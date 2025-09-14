<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\Chapter;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
class DownloadController extends Controller
{
    public function downloadEpub($storySlug)
    {
        $user = auth()->user();

        // Kiểm tra quyền (chỉ VIP, Mod hoặc Admin mới được tải)
        if (!$user || (!$user->hasRole('vip') && !$user->hasRole('Mod') && !$user->hasRole('Admin') && !$user->hasRole('VIP PRO') && !$user->hasRole('VIP PRO MAX'))) {
            abort(403, 'Bạn không có quyền tải xuống.');
        }

        // Tìm truyện theo slug
        $story = Story::where('slug', $storySlug)->firstOrFail();

        // Đường dẫn file EPUB (giả sử lưu trong storage/app/epubs/)
        $filePath = "epubs/{$storySlug}.epub";

        if (!Storage::exists($filePath)) {
            return response()->json(['error' => 'File không tồn tại.'], Response::HTTP_NOT_FOUND);
        }

        return response()->download(storage_path("app/$filePath"), "{$story->title}.epub");
    }

    public function generateEpub($storySlug, $chapterSlug)
    {
        $user = auth()->user();
    
        // Kiểm tra quyền tải EPUB
        if (!$user || (!$user->hasRole('vip') && !$user->hasRole('Mod') && !$user->hasRole('Admin') && !$user->hasRole('VIP PRO') && !$user->hasRole('VIP PRO MAX'))) {
            abort(403, 'Bạn không có quyền tải xuống.');
        }
    
        // Lấy dữ liệu truyện và chương
        $story = Story::where('slug', $storySlug)->firstOrFail();
        $chapter = Chapter::where('slug', $chapterSlug)->where('story_id', $story->id)->firstOrFail();
    
        // Làm sạch nội dung chương
        $chapter->content = html_entity_decode(htmlspecialchars_decode($chapter->content), ENT_QUOTES, 'UTF-8');
    
        $replace_pairs = [
            '&ldquo;' => '"', '&rdquo;' => '"', '&lsquo;' => "'", '&rsquo;' => "'",
            '&hellip;' => '...', '&ndash;' => '-', '&mdash;' => '-',
            '‐' => '-', '‑' => '-', '‒' => '-', '–' => '-', '—' => '-',
            "\xe2\x80\x93" => '-', "\xe2\x80\x94" => '-', "\xe2\x80\xa6" => '...',
            '“' => '"', '”' => '"', '‘' => "'", '’' => "'"
        ];
        $chapter->content = str_replace(array_keys($replace_pairs), array_values($replace_pairs), $chapter->content);
    
        // Loại bỏ khoảng trắng không mong muốn
        $chapter->content = str_replace(['&nbsp;', "\xc2\xa0"], ' ', $chapter->content);
    
        // Chuyển đổi xuống dòng thành <p>
        $chapter->content = preg_replace('/\s*[\r\n]+\s*/', "</p><p>", trim($chapter->content));
        $chapter->content = "<p>" . $chapter->content . "</p>";
        $chapter->content = preg_replace('/<p>\s*<\/p>/', '', $chapter->content); // Xóa <p> rỗng
    
        // Định dạng tên file EPUB
        $fileName = "{$chapterSlug}.epub";
        $filePath = storage_path("app/epubs/{$fileName}");
    
        // Xóa file EPUB cũ nếu tồn tại
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    
        // Tạo thư mục tạm
        $tempDir = storage_path("app/epubs/temp_" . uniqid());
        mkdir($tempDir, 0777, true);
    
        // Tạo file mimetype
        file_put_contents("$tempDir/mimetype", "application/epub+zip");
    
        // Tạo thư mục META-INF và file container.xml
        mkdir("$tempDir/META-INF");
        file_put_contents("$tempDir/META-INF/container.xml", '<?xml version="1.0" encoding="UTF-8"?>
        <container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">
            <rootfiles>
                <rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>
            </rootfiles>
        </container>');
    
        // Tạo thư mục OEBPS và file content.opf
        mkdir("$tempDir/OEBPS");
    
        $contentOpf = '<?xml version="1.0" encoding="UTF-8"?>
        <package xmlns="http://www.idpf.org/2007/opf" version="2.0" unique-identifier="BookId">
            <metadata xmlns:dc="http://purl.org/dc/elements/1.1/">
                <dc:title>' . htmlspecialchars($story->title) . '</dc:title>
                <dc:creator>' . htmlspecialchars($story->author) . '</dc:creator>
                <dc:language>vi</dc:language>
            </metadata>
            <manifest>
                <item id="ncx" href="toc.ncx" media-type="application/x-dtbncx+xml"/>
                <item id="content" href="chapter.html" media-type="application/xhtml+xml"/>
            </manifest>
            <spine toc="ncx">
                <itemref idref="content"/>
            </spine>
        </package>';
        file_put_contents("$tempDir/OEBPS/content.opf", $contentOpf);
    
        // Tạo file toc.ncx (Mục lục)
        $tocNcx = '<?xml version="1.0" encoding="UTF-8"?>
        <ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
            <head>
                <meta name="dtb:uid" content="BookId"/>
                <meta name="dtb:depth" content="1"/>
                <meta name="dtb:totalPageCount" content="0"/>
                <meta name="dtb:maxPageNumber" content="0"/>
            </head>
            <docTitle><text>' . htmlspecialchars($story->title) . '</text></docTitle>
            <navMap>
                <navPoint id="chapter" playOrder="1">
                    <navLabel><text>' . htmlspecialchars($chapter->title) . '</text></navLabel>
                    <content src="chapter.html"/>
                </navPoint>
            </navMap>
        </ncx>';
        file_put_contents("$tempDir/OEBPS/toc.ncx", $tocNcx);
    
        // Tạo file HTML cho chương
        $chapterContent = '<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta charset="UTF-8"/>
            <title>' . htmlspecialchars($chapter->title, ENT_QUOTES, 'UTF-8') . '</title>
        </head>
        <body>
            <h1>' . htmlspecialchars($chapter->title, ENT_QUOTES, 'UTF-8') . '</h1>
            ' . $chapter->content . '
        </body>
        </html>';
        file_put_contents("$tempDir/OEBPS/chapter.html", $chapterContent);
    
        // Nén thành file EPUB
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile("$tempDir/mimetype", "mimetype");
            $zip->addEmptyDir("META-INF");
            $zip->addFile("$tempDir/META-INF/container.xml", "META-INF/container.xml");
            $zip->addEmptyDir("OEBPS");
            $zip->addFile("$tempDir/OEBPS/content.opf", "OEBPS/content.opf");
            $zip->addFile("$tempDir/OEBPS/toc.ncx", "OEBPS/toc.ncx");
            $zip->addFile("$tempDir/OEBPS/chapter.html", "OEBPS/chapter.html");
            $zip->close();
        } else {
            return response()->json(['error' => 'Không thể tạo file EPUB'], 500);
        }
    
        // Xóa thư mục tạm
        $this->deleteDir($tempDir);
    
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
    

    // Hàm xóa thư mục tạm
    private function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) return;
        foreach (scandir($dirPath) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = "$dirPath/$item";
            is_dir($path) ? $this->deleteDir($path) : unlink($path);
        }
        rmdir($dirPath);
    }
}

