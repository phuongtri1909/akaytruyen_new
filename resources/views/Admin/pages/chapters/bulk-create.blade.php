@extends('Admin.layouts.sidebar')

@section('title', 'Tạo nhiều chương')

@section('main-content')
    <div class="chapter-bulk-create-container">
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.index') }}">Truyện</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.show', $story) }}">{{ $story->name }}</a></li>
                <li class="breadcrumb-item current">Tạo nhiều chương</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-copy icon-title"></i>
                    <h5>Tạo nhiều chương - {{ $story->name }}</h5>
                </div>
            </div>

            <div class="form-body">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle"></i>
                    Nội dung sẽ được tự động phân tích theo định dạng: <strong>"Chương [số]: [tên chương]"</strong>
                </div>

                <form id="bulkCreateForm" method="POST" novalidate>
                    @csrf

                    <div class="form-group mb-4">
                        <label class="form-label-custom">Nội dung từ file mẫu</label>
                        <textarea class="custom-input" id="sampleContent" rows="25" placeholder="Paste nội dung từ file mẫu vào đây..."></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label-custom">Ngày công bố đầu tiên</label>
                                <input type="datetime-local" class="custom-input" id="globalPublishedAt">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label-custom">Thời gian giữa các chương</label>
                                <div class="d-flex gap-2">
                                    <input type="number" class="custom-input" id="globalIntervalValue" value="5" min="1" style="width: 80px;">
                                    <select class="custom-input" id="globalIntervalUnit" style="flex: 1;">
                                        <option value="hours">Giờ</option>
                                        <option value="days">Ngày</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="analysisResults" class="mb-4" style="display: none;">
                        <h5>Kết quả phân tích:</h5>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Số chương</th>
                                        <th>Tên chương</th>
                                        <th>Ngày công bố</th>
                                        <th>Xuất bản luôn</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-actions d-flex gap-2">
                        <button type="button" class="save-button" id="detectChapters">
                            <i class="fas fa-search"></i> Phân tích chương
                        </button>
                        <button type="submit" class="save-button" id="createChapters" style="display: none;">
                            <i class="fas fa-plus"></i> Tạo tất cả chương
                        </button>
                        <a href="{{ route('admin.stories.show', $story) }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let detectedChapters = [];
    const storyId = {{ $story->id }};
    const bulkStoreUrl = "{{ route('admin.chapters.bulk-store', $story) }}";
    const checkExistingUrl = "{{ route('admin.chapters.check-existing', $story) }}";
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    fetch('{{ route('admin.get-server-time') }}')
        .then(r => r.json())
        .then(data => { $('#globalPublishedAt').val(data.time ? data.time.slice(0, 16) : new Date().toISOString().slice(0, 16)); })
        .catch(() => { $('#globalPublishedAt').val(new Date().toISOString().slice(0, 16)); });

    function detectChapters() {
        const content = $('#sampleContent').val().trim();
        if (!content) {
            Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Vui lòng nhập nội dung mẫu' });
            return;
        }

        const chapters = [];
        const lines = content.split('\n');
        let currentChapter = null;
        let chapterContent = [];

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i].trim();
            const match = line.match(/^Chương\s*(\d+)(?:\s*:\s*(.*))?$/);

            if (match) {
                if (currentChapter) {
                    chapters.push({
                        number: currentChapter.number,
                        name: currentChapter.name,
                        content: chapterContent.join('\n').trim()
                    });
                }
                const num = parseInt(match[1]);
                const name = match[2] ? match[2].trim() : 'Chương ' + num;
                currentChapter = { number: num, name };
                chapterContent = [];
            } else if (currentChapter) {
                chapterContent.push(line);
            }
        }
        if (currentChapter) {
            chapters.push({
                number: currentChapter.number,
                name: currentChapter.name,
                content: chapterContent.join('\n').trim()
            });
        }

        if (chapters.length === 0) {
            Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không tìm thấy chương nào' });
            return;
        }

        $.ajax({
            url: checkExistingUrl,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                chapter_numbers: chapters.map(c => c.number),
                _token: csrfToken
            }),
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            success: function(data) {
                const existing = data.existing || [];
                const baseDateStr = $('#globalPublishedAt').val();
                const intervalVal = parseInt($('#globalIntervalValue').val()) || 5;
                const intervalUnit = $('#globalIntervalUnit').val();

                detectedChapters = chapters.map((ch, idx) => {
                    const baseDate = new Date(baseDateStr + (baseDateStr.length === 16 ? '' : ''));
                    const d = new Date(baseDate.getTime() + idx * intervalVal * (intervalUnit === 'hours' ? 3600000 : 86400000));
                    const publishedAt = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0') + 'T' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
                    return {
                        ...ch,
                        chapter: ch.number,
                        existing: existing.includes(ch.number),
                        publish_now: false,
                        published_at: publishedAt,
                        content_type: 'plain'
                    };
                });
                displayChapters();
            }
        });
    }

    function formatPublishDate(isoStr) {
        if (!isoStr) return '-';
        const d = new Date(isoStr);
        if (isNaN(d.getTime())) return isoStr;
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        const h = String(d.getHours()).padStart(2, '0');
        const m = String(d.getMinutes()).padStart(2, '0');
        return day + '/' + month + '/' + year + ' ' + h + ':' + m;
    }

    function displayChapters() {
        const tbody = $('#previewTableBody');
        tbody.empty();
        detectedChapters.forEach(ch => {
            const row = $('<tr>').addClass(ch.existing ? 'table-warning' : '');
            const displayDate = ch.publish_now ? 'Ngay lập tức' : formatPublishDate(ch.published_at);
            row.html(`
                <td><strong>Chương ${ch.chapter}</strong> ${ch.existing ? '<span class="badge bg-warning">Đã tồn tại</span>' : ''}</td>
                <td>${ch.name} <small class="d-block text-muted">${ch.content.substring(0, 80)}...</small></td>
                <td>${displayDate}</td>
                <td>${ch.existing ? '-' : '<input type="checkbox" class="publish-now-cb" data-idx="' + detectedChapters.indexOf(ch) + '">'}</td>
            `);
            tbody.append(row);
        });
        $('#analysisResults').show();
        $('#createChapters').show();

        $('.publish-now-cb').on('change', function() {
            const idx = $(this).data('idx');
            detectedChapters[idx].publish_now = $(this).is(':checked');
        });
    }

    $('#globalPublishedAt, #globalIntervalValue, #globalIntervalUnit').on('change', function() {
        if (detectedChapters.length > 0) {
            const baseDateStr = $('#globalPublishedAt').val();
            const intervalVal = parseInt($('#globalIntervalValue').val()) || 5;
            const intervalUnit = $('#globalIntervalUnit').val();
            detectedChapters.forEach((ch, idx) => {
                if (!ch.publish_now && !ch.existing) {
                    const baseDate = new Date(baseDateStr);
                    const d = new Date(baseDate.getTime() + idx * intervalVal * (intervalUnit === 'hours' ? 3600000 : 86400000));
                    ch.published_at = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0') + 'T' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
                }
            });
            displayChapters();
        }
    });

    $('#detectChapters').on('click', detectChapters);

    $('#bulkCreateForm').on('submit', function(e) {
        e.preventDefault();
        const newChapters = detectedChapters.filter(ch => !ch.existing);
        if (newChapters.length === 0) {
            Swal.fire({ icon: 'error', title: 'Lỗi', text: 'Không có chương nào có thể tạo' });
            return;
        }

        const chaptersData = newChapters.map(ch => ({
            chapter: ch.chapter,
            name: ch.name,
            content: ch.content,
            content_type: 'plain',
            publish_now: ch.publish_now,
            published_at: ch.publish_now ? null : ch.published_at
        }));

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('chapters', JSON.stringify(chaptersData));

        $('#createChapters').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');

        $.ajax({
            url: bulkStoreUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: res.message || 'Đã tạo chương thành công'
                }).then(() => {
                    window.location.href = "{{ route('admin.stories.show', $story) }}";
                });
            },
            error: function(xhr) {
                $('#createChapters').prop('disabled', false).html('<i class="fas fa-plus"></i> Tạo tất cả chương');
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: xhr.responseJSON?.message || 'Có lỗi xảy ra'
                });
            }
        });
    });
});
</script>
@endpush
