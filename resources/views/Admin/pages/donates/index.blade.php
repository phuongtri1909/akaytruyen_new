@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý thông tin donate - ' . $story->name)

@section('main-content')
    <div class="donate-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.index') }}">Truyện</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.show', $story->id) }}">{{ $story->name }}</a></li>
                <li class="breadcrumb-item current">Thông tin donate</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-heart text-danger icon-title"></i>
                    <h5>Quản lý thông tin donate - {{ $story->name }}</h5>
                </div>

                <div>
                    <a href="{{ route('admin.stories.show', $story->id) }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    @can('them_thong_tin_donate')
                        <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addDonateModal">
                            <i class="fas fa-plus"></i> Thêm thông tin donate
                        </button>
                    @endcan
                </div>
            </div>

            <div class="card-content">
                @if (session()->has('success'))
                    <div class="alert alert-success p-1">
                        {{ session()->get('success') }}
                    </div>
                @endif

                @if($story->donates->count() > 0)
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên ngân hàng/dịch vụ</th>
                                    <th class="column-large">Thông tin donate</th>
                                    <th class="column-medium">Hình ảnh QR</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($story->donates as $index => $donate)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="item-title">
                                            <strong>{{ $donate->bank_name }}</strong>
                                        </td>
                                        <td>
                                            <div class="donate-info-text">
                                                {{ $donate->donate_info ?: 'Không có thông tin' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($donate->image)
                                                <img src="{{ Storage::url($donate->image) }}" alt="QR Code" class="qr-image">
                                            @else
                                                <span class="text-muted">Không có ảnh</span>
                                            @endif
                                        </td>
                                        <td class="donate-date">
                                            {{ $donate->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                @can('sua_thong_tin_donate')
                                                    <button class="action-icon edit-icon edit-donate" 
                                                            data-donate-id="{{ $donate->id }}"
                                                            data-bank-name="{{ $donate->bank_name }}"
                                                            data-donate-info="{{ $donate->donate_info }}"
                                                            title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endcan
                                                @can('xoa_thong_tin_donate')
                                                    @include('components.delete-form', [
                                                        'id' => $donate->id,
                                                        'route' => route('admin.donate.destroy', $donate),
                                                        'message' => "Bạn có chắc chắn muốn xóa thông tin donate '{$donate->bank_name}'?",
                                                    ])
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4>Chưa có thông tin donate nào</h4>
                        <p>Bắt đầu bằng cách thêm thông tin donate đầu tiên.</p>
                        @can('them_thong_tin_donate')
                            <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addDonateModal">
                                <i class="fas fa-plus"></i> Thêm thông tin donate
                            </button>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Add Donate -->
    <div class="modal fade" id="addDonateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm thông tin donate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDonateForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="bank_name" class="form-label">Tên ngân hàng/dịch vụ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="donate_info" class="form-label">Thông tin donate (STK, gmail, etc.)</label>
                            <input type="text" class="form-control" id="donate_info" name="donate_info">
                        </div>
                        <div class="form-group mb-3">
                            <label for="image" class="form-label">Hình ảnh QR code</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-outline-primary">Thêm donate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Donate -->
    <div class="modal fade" id="editDonateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa thông tin donate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editDonateForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_donate_id" name="donate_id">
                        <div class="form-group mb-3">
                            <label for="edit_bank_name" class="form-label">Tên ngân hàng/dịch vụ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_bank_name" name="bank_name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_donate_info" class="form-label">Thông tin donate (STK, gmail, etc.)</label>
                            <input type="text" class="form-control" id="edit_donate_info" name="donate_info">
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_image" class="form-label">Hình ảnh QR code</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-outline-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .donate-info-text {
            font-size: 14px;
            color: #6c757d;
            line-height: 1.4;
        }

        .qr-image {
            max-width: 60px;
            max-height: 60px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .donate-date {
            font-size: 14px;
            color: #6c757d;
        }

        .disabled-icon {
            color: #6c757d !important;
            cursor: not-allowed !important;
            opacity: 0.5;
        }

        .disabled-icon:hover {
            color: #6c757d !important;
        }
    </style>
@endpush

@push('scripts')
<script>
// Route helpers
const routes = {
    donateUpdate: (id) => `/admin/donate/${id}`,
};
$(document).ready(function() {
    // Thêm donate mới
    $('#addDonateForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("admin.donate.store", $story->id) }}', {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (res.ok) {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Thêm thông tin donate thành công',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Có lỗi xảy ra khi thêm donate',
                showConfirmButton: false,
                timer: 2000
            });
        });
    });

    // Mở modal edit
    $('.edit-donate').on('click', function() {
        const donateId = $(this).data('donate-id');
        const bankName = $(this).data('bank-name');
        const donateInfo = $(this).data('donate-info');

        $('#edit_donate_id').val(donateId);
        $('#edit_bank_name').val(bankName);
        $('#edit_donate_info').val(donateInfo);

        $('#editDonateModal').modal('show');
    });

    // Cập nhật donate
    $('#editDonateForm').on('submit', function(e) {
        e.preventDefault();

        const donateId = $('#edit_donate_id').val();
        const formData = new FormData(this);
        const url = routes.donateUpdate(donateId);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-HTTP-Method-Override': 'PUT',
            },
            body: formData
        })
        .then(res => {
            if (res.ok) {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Cập nhật thông tin donate thành công',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Có lỗi xảy ra khi cập nhật donate',
                showConfirmButton: false,
                timer: 2000
            });
        });
    });
});
</script>
@endpush
