<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use App\Models\User;
use App\Models\Setting;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Mews\Purifier\Facades\Purifier;
use Artesaos\SEOTools\Facades\JsonLd;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Support\Facades\Cookie;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Storage;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\TwitterCard;

class Helper
{
   
    public static function getImagePath($imagePath): string
    {
        return Storage::exists('public/' . $imagePath) ? asset('storage/' . $imagePath) : asset('images/default_image.jpg');
    }

    public static function getCategoies() {
        static $categories = null;
        if ($categories === null) {
            $categories = Category::query()->get();
        }
        return $categories;
    }

    public static function getCachedCategories() {
        return Cache::remember('app:categories', now()->addMinutes(10), function () {
            return self::getCategoies();
        });
    }

    static function setSEO($objectSEO)
    {
        $args = [
            'title'         => $objectSEO->name ?? env('APP_NAME') ?? '',
            'description'   => $objectSEO->description ?? '',
            'keywords'      => $objectSEO->keywords ?? '',
            'no_index'      => $objectSEO->no_index,
            'type'          => $objectSEO->meta_type ?? 'website',
            'url_canonical' => $objectSEO->url_canonical ?? route('home'),
            'image'         => $objectSEO->image,
            'site_name'     => $objectSEO->site_name ?? '',
        ];

        OpenGraph::addProperty('locale', 'vi_VN');
        OpenGraph::addProperty('type', $args['type']);
        JsonLdMulti::setType($args['type']);
        TwitterCard::setType('summary');

        if ($args['site_name']) {
            OpenGraph::setSiteName($args['site_name']);
            TwitterCard::addValue('domain', $args['site_name']);
        }
        if ($args['title']) {
            SEOTools::setTitle($args['title']);
        }
        if ($args['description']) {
            SEOTools::setDescription($args['description']);
        }
        if ($args['keywords']) {
            SEOMeta::setKeywords($args['keywords']);
        }
        if ($args['url_canonical']) {
            SEOTools::setCanonical($args['url_canonical']);
        }
        if ($args['image']) {
            SEOTools::addImages($args['image']);
        }

        if (!empty($objectSEO->article)) {
            foreach ($objectSEO->article as $_key => $_value) {
                SEOMeta::addMeta('article:' . $_key, $_value, 'property');
            }
        }

        if (config('app.env') == 'local') {
            SEOMeta::setRobots('noindex,nofollow');
        } else {
            SEOMeta::setRobots($args['no_index'] ? 'noindex,nofollow' : 'index,follow');
        }
    }

    public static function parseLinks($text)
    {
        if (empty($text)) return '';

        // Sanitize text first
        $text = self::sanitizeComment($text);
        
        // Parse links
        $text = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-500 underline hover:text-blue-700">$1</a>',
            $text
        );

        // Parse emojis
        $emojiPattern = '/[\x{1F000}-\x{1FFFF}|\x{2600}-\x{27BF}|\x{1F900}-\x{1F9FF}|\x{2B50}|\x{2705}]/u';
        $text = preg_replace_callback($emojiPattern, fn($m) => '<span class="emoji">'.$m[0].'</span>', $text);

        return nl2br($text);
    }
    
    /**
     * Sanitize comment content to prevent XSS
     */
    public static function sanitizeComment($text)
    {
        if (empty($text)) return '';
        
        // Remove dangerous HTML tags
        $dangerousTags = [
            'script', 'iframe', 'object', 'embed', 'form', 'input', 'textarea', 
            'select', 'button', 'meta', 'link', 'style', 'title', 'head', 'html', 
            'body', 'base', 'bgsound', 'xml', 'xmp', 'plaintext', 'listing', 
            'marquee', 'blink', 'keygen', 'isindex', 'nextid', 'spacer', 'wbr', 
            'acronym', 'applet', 'basefont', 'big', 'center', 'dir', 'font', 
            'hgroup', 'kbd', 'noframes', 's', 'strike', 'tt', 'u', 'nobr', 
            'noembed', 'noscript', 'param', 'q', 'rb', 'rbc', 'rp', 'rt', 'rtc', 
            'ruby', 'samp', 'small', 'span', 'strong', 'sub', 'sup', 'table', 
            'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'ul', 'var', 'video', 'xml', 'xmp'
        ];
        
        foreach ($dangerousTags as $tag) {
            $text = preg_replace('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', '', $text);
            $text = preg_replace('/<' . $tag . '[^>]*\/?>/is', '', $text);
        }
        
        // Remove dangerous attributes
        $dangerousAttributes = [
            'onload', 'onerror', 'onclick', 'onmouseover', 'onmouseout', 'onfocus', 
            'onblur', 'onchange', 'onsubmit', 'onreset', 'onselect', 'onunload', 
            'javascript:', 'vbscript:', 'data:', 'mocha:', 'livescript:'
        ];
        
        foreach ($dangerousAttributes as $attr) {
            $text = preg_replace('/\s*' . preg_quote($attr) . '\s*=\s*["\'][^"\']*["\']/i', '', $text);
            $text = preg_replace('/\s*' . preg_quote($attr) . '\s*=\s*[^\s>]+/i', '', $text);
        }
        
        // Remove script content
        $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);
        
        // Remove javascript: URLs
        $text = preg_replace('/javascript\s*:/i', '', $text);
        $text = preg_replace('/vbscript\s*:/i', '', $text);
        $text = preg_replace('/data\s*:/i', '', $text);
        
        // Remove CSS expressions
        $text = preg_replace('/expression\s*\(/i', '', $text);
        
        // Remove comments
        $text = preg_replace('/<!--.*?-->/s', '', $text);
        
        // Remove null bytes
        $text = str_replace("\0", '', $text);
        
        // Remove control characters
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // HTML encode special characters
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $text;
    }
    
    /**
     * Sanitize CKEditor content - cho phép một số HTML tags an toàn
     */
    public static function sanitizeCKEditorContent($text)
    {
        if (empty($text)) return '';
        
        // Remove dangerous HTML tags
        $dangerousTags = [
            'script', 'iframe', 'object', 'embed', 'form', 'input', 'textarea', 
            'select', 'button', 'meta', 'link', 'style', 'title', 'head', 'html', 
            'body', 'base', 'bgsound', 'xml', 'xmp', 'plaintext', 'listing', 
            'marquee', 'blink', 'keygen', 'isindex', 'nextid', 'spacer', 'wbr', 
            'acronym', 'applet', 'basefont', 'big', 'center', 'dir', 'font', 
            'hgroup', 'kbd', 'noframes', 's', 'strike', 'tt', 'u', 'nobr', 
            'noembed', 'noscript', 'param', 'q', 'rb', 'rbc', 'rp', 'rt', 'rtc', 
            'ruby', 'samp', 'small', 'span', 'strong', 'sub', 'sup', 'table', 
            'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'ul', 'var', 'video', 'xml', 'xmp'
        ];
        
        foreach ($dangerousTags as $tag) {
            $text = preg_replace('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', '', $text);
            $text = preg_replace('/<' . $tag . '[^>]*\/?>/is', '', $text);
        }
        
        // Remove dangerous attributes
        $dangerousAttributes = [
            'onload', 'onerror', 'onclick', 'onmouseover', 'onmouseout', 'onfocus', 
            'onblur', 'onchange', 'onsubmit', 'onreset', 'onselect', 'onunload', 
            'onabort', 'onbeforeunload', 'onerror', 'onhashchange', 'onmessage', 
            'onoffline', 'ononline', 'onpagehide', 'onpageshow', 'onpopstate', 
            'onresize', 'onstorage', 'oncontextmenu', 'onkeydown', 'onkeypress', 
            'onkeyup', 'onmousedown', 'onmousemove', 'onmouseup', 'onwheel', 
            'oncopy', 'oncut', 'onpaste', 'onbeforecopy', 'onbeforecut', 
            'onbeforepaste', 'onsearch', 'onselectionchange', 'onselectstart', 
            'onstart', 'onstop', 'onbeforeprint', 'onafterprint', 'onbeforeeditfocus', 
            'onblur', 'onchange', 'oncontextmenu', 'oncontrolselect', 'oncopy', 
            'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 
            'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 
            'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerrorupdate', 
            'onfilterchange', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 
            'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 
            'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 
            'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 
            'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 
            'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 
            'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 
            'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 
            'onsubmit', 'onunload', 'javascript:', 'vbscript:', 'data:', 'mocha:', 'livescript:'
        ];
        
        foreach ($dangerousAttributes as $attr) {
            $text = preg_replace('/\s*' . preg_quote($attr) . '\s*=\s*["\'][^"\']*["\']/i', '', $text);
            $text = preg_replace('/\s*' . preg_quote($attr) . '\s*=\s*[^\s>]+/i', '', $text);
        }
        
        // Remove script content
        $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);
        
        // Remove javascript: URLs
        $text = preg_replace('/javascript\s*:/i', '', $text);
        $text = preg_replace('/vbscript\s*:/i', '', $text);
        $text = preg_replace('/data\s*:/i', '', $text);
        
        // Remove CSS expressions
        $text = preg_replace('/expression\s*\(/i', '', $text);
        
        // Remove comments
        $text = preg_replace('/<!--.*?-->/s', '', $text);
        
        // Remove null bytes
        $text = str_replace("\0", '', $text);
        
        // Remove control characters
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Allow safe HTML tags but sanitize attributes
        $allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a', 'img', 'div', 'span'];
        
        // Strip all tags except allowed ones
        $text = strip_tags($text, '<' . implode('><', $allowedTags) . '>');
        
        return $text;
    }
    
    /**
     * Sanitize Chapter Content - cho phép HTML tags và CSS styling
     */
    public static function sanitizeChapterContent($text)
    {
        if (empty($text)) return '';
        
        // Remove dangerous HTML tags
        $dangerousTags = [
            'script', 'iframe', 'object', 'embed', 'form', 'input', 'textarea', 
            'select', 'button', 'meta', 'link', 'style', 'title', 'head', 'html', 
            'body', 'base', 'bgsound', 'xml', 'xmp', 'plaintext', 'listing', 
            'marquee', 'blink', 'keygen', 'isindex', 'nextid', 'spacer', 'wbr', 
            'acronym', 'applet', 'basefont', 'big', 'center', 'dir', 'font', 
            'hgroup', 'kbd', 'noframes', 's', 'strike', 'tt', 'u', 'nobr', 
            'noembed', 'noscript', 'param', 'q', 'rb', 'rbc', 'rp', 'rt', 'rtc', 
            'ruby', 'samp', 'small', 'span', 'strong', 'sub', 'sup', 'table', 
            'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'ul', 'var', 'video', 'xml', 'xmp'
        ];
        
        foreach ($dangerousTags as $tag) {
            $text = preg_replace('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', '', $text);
            $text = preg_replace('/<' . $tag . '[^>]*\/?>/is', '', $text);
        }
        
        // Remove dangerous attributes
        $dangerousAttributes = [
            'onload', 'onerror', 'onclick', 'onmouseover', 'onmouseout', 'onfocus', 
            'onblur', 'onchange', 'onsubmit', 'onreset', 'onselect', 'onunload', 
            'onabort', 'onbeforeunload', 'onerror', 'onhashchange', 'onmessage', 
            'onoffline', 'ononline', 'onpagehide', 'onpageshow', 'onpopstate', 
            'onresize', 'onstorage', 'oncontextmenu', 'onkeydown', 'onkeypress', 
            'onkeyup', 'onmousedown', 'onmousemove', 'onmouseup', 'onwheel', 
            'oncopy', 'oncut', 'onpaste', 'onbeforecopy', 'onbeforecut', 
            'onbeforepaste', 'onsearch', 'onselectionchange', 'onselectstart', 
            'onstart', 'onstop', 'onbeforeprint', 'onafterprint', 'onbeforeeditfocus', 
            'onblur', 'onchange', 'oncontextmenu', 'oncontrolselect', 'oncopy', 
            'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 
            'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 
            'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerrorupdate', 
            'onfilterchange', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 
            'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 
            'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 
            'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 
            'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 
            'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 
            'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 
            'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 
            'onsubmit', 'onunload', 'javascript:', 'vbscript:', 'data:', 'mocha:', 'livescript:'
        ];
        
        foreach ($dangerousAttributes as $attr) {
            $text = preg_replace('/\s*' . preg_quote($attr) . '\s*=\s*["\'][^"\']*["\']/i', '', $text);
            $text = preg_replace('/\s*' . preg_quote($attr) . '\s*=\s*[^\s>]+/i', '', $text);
        }
        
        // Remove script content
        $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);
        
        // Remove javascript: URLs
        $text = preg_replace('/javascript\s*:/i', '', $text);
        $text = preg_replace('/vbscript\s*:/i', '', $text);
        $text = preg_replace('/data\s*:/i', '', $text);
        
        // Remove CSS expressions
        $text = preg_replace('/expression\s*\(/i', '', $text);
        
        // Remove comments
        $text = preg_replace('/<!--.*?-->/s', '', $text);
        
        // Remove null bytes
        $text = str_replace("\0", '', $text);
        
        // Remove control characters
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Allow safe HTML tags for chapter content
        $allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'pre', 'code', 'a', 'img', 'div', 'span'];
        
        // Strip all tags except allowed ones
        $text = strip_tags($text, '<' . implode('><', $allowedTags) . '>');
        
        // Clean up any remaining dangerous content
        $text = preg_replace('/<[^>]*javascript[^>]*>/i', '', $text);
        $text = preg_replace('/<[^>]*on\w+\s*=/i', '', $text);
        
        return $text;
    }
    
    public static function sanitizeAdvancedContent($text)
    {
        if (empty($text)) return '';
        
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);
        $text = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $text);
        $text = preg_replace('/<object[^>]*>.*?<\/object>/is', '', $text);
        $text = preg_replace('/<embed[^>]*>.*?<\/embed>/is', '', $text);
        $text = preg_replace('/<form[^>]*>.*?<\/form>/is', '', $text);
        $text = preg_replace('/<input[^>]*\/?>/is', '', $text);
        $text = preg_replace('/<textarea[^>]*>.*?<\/textarea>/is', '', $text);
        $text = preg_replace('/<select[^>]*>.*?<\/select>/is', '', $text);
        $text = preg_replace('/<button[^>]*>.*?<\/button>/is', '', $text);
        
        $text = preg_replace('/javascript\s*:/i', '', $text);
        $text = preg_replace('/vbscript\s*:/i', '', $text);
        $text = preg_replace('/data\s*:/i', '', $text);
        $text = preg_replace('/mocha\s*:/i', '', $text);
        $text = preg_replace('/livescript\s*:/i', '', $text);
        
        $text = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $text);
        $text = preg_replace('/on\w+\s*=\s*[^\s>]+/i', '', $text);
        
        $text = preg_replace('/expression\s*\(/i', '', $text);
        $text = preg_replace('/<!--.*?-->/s', '', $text);
        
        $text = str_replace("\0", '', $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        $text = Purifier::clean($text);
        
        if (preg_match('/<script[^>]*>.*?<\/script>/is', $text) || 
            preg_match('/javascript\s*:/i', $text) ||
            preg_match('/on\w+\s*=/i', $text)) {
            $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);
            $text = preg_replace('/javascript\s*:/i', '', $text);
            $text = preg_replace('/on\w+\s*=/i', '', $text);
        }
        
        return $text;
    }

    public static function processTextareaContent($text)
    {
        if (empty($text)) return '';
        
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);
        $text = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $text);
        $text = preg_replace('/<object[^>]*>.*?<\/object>/is', '', $text);
        $text = preg_replace('/<embed[^>]*>.*?<\/embed>/is', '', $text);
        $text = preg_replace('/<form[^>]*>.*?<\/form>/is', '', $text);
        $text = preg_replace('/<input[^>]*\/?>/is', '', $text);
        $text = preg_replace('/<textarea[^>]*>.*?<\/textarea>/is', '', $text);
        $text = preg_replace('/<select[^>]*>.*?<\/select>/is', '', $text);
        $text = preg_replace('/<button[^>]*>.*?<\/button>/is', '', $text);
        
        $text = preg_replace('/javascript\s*:/i', '', $text);
        $text = preg_replace('/vbscript\s*:/i', '', $text);
        $text = preg_replace('/data\s*:/i', '', $text);
        $text = preg_replace('/mocha\s*:/i', '', $text);
        $text = preg_replace('/livescript\s*:/i', '', $text);
        
        $text = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $text);
        $text = preg_replace('/on\w+\s*=\s*[^\s>]+/i', '', $text);
        
        return trim($text);
    }

    public static function generateChapterSlug($chapterNumber, $chapterName, $storyId, $excludeId = null)
    {
        $baseSlug = $chapterNumber . '-' . \Str::slug($chapterName);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = \App\Models\Chapter::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            $exists = $query->exists();
            
            if (!$exists) {
                break;
            }
            
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
    
    public static function validateImageFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'])
    {
        if (!$file || !$file->isValid()) {
            return ['valid' => false, 'message' => 'File không hợp lệ'];
        }
        
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        
        if (!in_array($extension, $allowedTypes)) {
            return ['valid' => false, 'message' => "Chỉ chấp nhận file ảnh (" . strtoupper(implode(', ', $allowedTypes)) . "). File của bạn có định dạng: $extension"];
        }
        
        $allowedMimes = [
            'image/jpeg',
            'image/png', 
            'image/gif',
            'image/webp'
        ];
        
        if (!in_array($mimeType, $allowedMimes)) {
            return ['valid' => false, 'message' => "Chỉ chấp nhận file ảnh (" . strtoupper(implode(', ', $allowedTypes)) . "). File của bạn có định dạng: $mimeType"];
        }
        
        $fileContent = file_get_contents($file->getPathname());
        $allowedImageHeaders = [
            "\xFF\xD8\xFF", // JPEG
            "\x89PNG\r\n\x1a\n", // PNG
            "GIF87a", // GIF87a
            "GIF89a", // GIF89a
        ];
        
        $isValidImage = false;
        foreach ($allowedImageHeaders as $header) {
            if (strpos($fileContent, $header) === 0) {
                $isValidImage = true;
                break;
            }
        }
        
        if (!$isValidImage && strpos($fileContent, "RIFF") === 0 && strpos($fileContent, "WEBP") !== false) {
            $isValidImage = true;
        }
        
        if (!$isValidImage) {
            return ['valid' => false, 'message' => 'File không phải là hình ảnh hợp lệ. Vui lòng chọn file ảnh thực sự.'];
        }
        
        return ['valid' => true, 'message' => 'File hợp lệ'];
    }
    
    /**
     * Sanitize JavaScript content for safe display
     */
    public static function sanitizeForJS($text)
    {
        if (empty($text)) return '';
        
        // Remove dangerous HTML tags
        $dangerousTags = [
            'script', 'iframe', 'object', 'embed', 'form', 'input', 'textarea', 
            'select', 'button', 'meta', 'link', 'style', 'title', 'head', 'html', 
            'body', 'base', 'bgsound', 'xml', 'xmp', 'plaintext', 'listing', 
            'marquee', 'blink', 'keygen', 'isindex', 'nextid', 'spacer', 'wbr', 
            'acronym', 'applet', 'basefont', 'big', 'center', 'dir', 'font', 
            'hgroup', 'kbd', 'noframes', 's', 'strike', 'tt', 'u', 'nobr', 
            'noembed', 'noscript', 'param', 'q', 'rb', 'rbc', 'rp', 'rt', 'rtc', 
            'ruby', 'samp', 'small', 'span', 'strong', 'sub', 'sup', 'table', 
            'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'ul', 'var', 'video', 'xml', 'xmp'
        ];
        
        foreach ($dangerousTags as $tag) {
            $text = preg_replace('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', '', $text);
            $text = preg_replace('/<' . $tag . '[^>]*\/?>/is', '', $text);
        }
        
        // Remove dangerous attributes
        $dangerousAttributes = [
            'onload', 'onerror', 'onclick', 'onmouseover', 'onmouseout', 'onfocus', 
            'onblur', 'onchange', 'onsubmit', 'onreset', 'onselect', 'onunload', 
            'onabort', 'onbeforeunload', 'onerror', 'onhashchange', 'onmessage', 
            'onoffline', 'ononline', 'onpagehide', 'onpageshow', 'onpopstate', 
            'onresize', 'onstorage', 'oncontextmenu', 'onkeydown', 'onkeypress', 
            'onkeyup', 'onmousedown', 'onmousemove', 'onmouseup', 'onwheel', 
            'oncopy', 'oncut', 'onpaste', 'onbeforecopy', 'onbeforecut', 
            'onbeforepaste', 'onsearch', 'onselectionchange', 'onselectstart', 
            'onstart', 'onstop', 'onbeforeprint', 'onafterprint', 'onbeforeeditfocus', 
            'onblur', 'onchange', 'oncontextmenu', 'oncontrolselect', 'oncopy', 
            'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 
            'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 
            'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerrorupdate', 
            'onfilterchange', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 
            'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 
            'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 
            'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 
            'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 
            'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 
            'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 
            'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 
            'onsubmit', 'onunload', 'javascript:', 'vbscript:', 'data:', 'mocha:', 'livescript:'
        ];
        
        foreach ($dangerousAttributes as $attr) {
            $text = preg_replace('/\s*' . preg_quote($attr) . '\s*=\s*["\'][^"\']*["\']/i', '', $text);
            $text = preg_replace('/\s*' . preg_quote($attr) . '\s*=\s*[^\s>]+/i', '', $text);
        }
        
        // Remove script content
        $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $text);
        
        // Remove javascript: URLs
        $text = preg_replace('/javascript\s*:/i', '', $text);
        $text = preg_replace('/vbscript\s*:/i', '', $text);
        $text = preg_replace('/data\s*:/i', '', $text);
        
        // Remove CSS expressions
        $text = preg_replace('/expression\s*\(/i', '', $text);
        
        // Remove comments
        $text = preg_replace('/<!--.*?-->/s', '', $text);
        
        // Remove null bytes
        $text = str_replace("\0", '', $text);
        
        // Remove control characters
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // HTML encode for JavaScript
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $text;
    }

    public static function getVietnameseMonth($month) {
        $months = [
            '01' => 'T1', '02' => 'T2', '03' => 'T3', '04' => 'T4',
            '05' => 'T5', '06' => 'T6', '07' => 'T7', '08' => 'T8',
            '09' => 'T9', '10' => 'T10', '11' => 'T11', '12' => 'T12'
        ];
        return $months[$month] ?? 'T1';
    }

    /**
     * Get story image URL
     */
    public static function getStoryImageUrl($imagePath)
    {
        if (!$imagePath) {
            return asset('assets/frontend/images/default-story.jpg');
        }

        $url = '';
        $filePath = '';

        // Nếu là đường dẫn cũ (public/images/stories/)
        if (str_starts_with($imagePath, '/images/stories/')) {
            $url = asset($imagePath);
            $filePath = public_path($imagePath);
        } else {
            // Nếu là đường dẫn storage mới
            $url = asset('storage/' . $imagePath);
            $filePath = storage_path('app/public/' . $imagePath);
        }

        // Thêm cache busting parameter dựa trên file modification time
        $version = file_exists($filePath) ? filemtime($filePath) : time();
        $url .= (str_contains($url, '?') ? '&' : '?') . 'v=' . $version;

        return $url;
    }

    public static function cleanDescription($content, $limit = 150)
    {
        $content = Purifier::clean($content, 'default');
        $text = strip_tags($content);

        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        $text = preg_replace('/\s+/', ' ', $text);

        $text = trim($text);

        return Str::limit($text, $limit);
    }
}
