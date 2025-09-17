@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý donation - ' . $story->name)

@section('main-content')
    <div class="donation-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.index') }}">Truyện</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.show', $story->id) }}">{{ $story->name }}</a>
                </li>
                <li class="breadcrumb-item current">Donation</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-heart text-danger icon-title"></i>
                    <h5>Quản lý Donation - {{ $story->name }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.stories.show', $story->id) }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    @can('them_thanh_vien_donate')
                        <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#addDonationModal">
                            <i class="fas fa-plus"></i> Thêm donation
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

                @if ($donations->count() > 0)
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên người donate</th>
                                    <th class="column-medium">Số tiền (Linh Thạch)</th>
                                    <th class="column-medium">Ngày donate</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($donations as $index => $donation)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($donations->currentPage() - 1) * $donations->perPage() + $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $donation->name }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="amount-text">{{ number_format($donation->amount, 0) }}</span>
                                        </td>
                                        <td class="donation-date">
                                            {{ \Carbon\Carbon::parse($donation->donated_at)->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                @can('sua_thanh_vien_donate')
                                                    <button class="action-icon edit-icon edit-donation"
                                                        data-id="{{ $donation->id }}" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endcan
                                                @can('xoa_thanh_vien_donate')
                                                    @include('components.delete-form', [
                                                        'id' => $donation->id,
                                                        'route' => route('admin.donations.destroy', $donation),
                                                        'message' => "Bạn có chắc chắn muốn xóa donation '{$donation->name}'?",
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

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $donations->firstItem() ?? 0 }} đến {{ $donations->lastItem() ?? 0 }} của
                            {{ $donations->total() }} donation
                        </div>
                        <div class="pagination-controls">
                            {{ $donations->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4>Chưa có donation nào</h4>
                        <p>Bắt đầu bằng cách thêm donation đầu tiên.</p>
                        @can('them_thanh_vien_donate')
                            <button type="button" class="action-button" data-bs-toggle="modal"
                                data-bs-target="#addDonationModal">
                                <i class="fas fa-plus"></i> Thêm donation
                            </button>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Add Donation -->
    <div class="modal fade" id="addDonationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm donation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDonationForm">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Tên người donate <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="amount" class="form-label">Số tiền (Linh Thạch) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1000"
                                required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="donated_at" class="form-label">Ngày donate</label>
                            <input type="datetime-local" class="form-control" id="donated_at" name="donated_at">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-outline-primary">Thêm donation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Donation -->
    <div class="modal fade" id="editDonationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa donation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editDonationForm">
                        <input type="hidden" id="edit_donation_id">
                        <div class="form-group mb-3">
                            <label for="edit_name" class="form-label">Tên người donate <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_amount" class="form-label">Số tiền (Linh Thạch) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_amount" name="amount" min="1000"
                                required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_donated_at" class="form-label">Ngày donate</label>
                            <input type="datetime-local" class="form-control" id="edit_donated_at" name="donated_at">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-outline-primary" id="saveEditDonation">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .amount-text {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .donation-date {
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const storyId = {{ $story->id }};
            const routes = {
                store: '{{ route('admin.donations.store', $story->id) }}',
                update: '{{ route('admin.donations.update', ':id') }}',
            };

            $('#addDonationForm').on('submit', function(e) {
                e.preventDefault();

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.text();

                submitBtn.prop('disabled', true).text('Đang thêm...');

                const formData = new FormData(this);

                fetch(routes.store, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire('Thành công!', 'Thêm donation thành công!', 'success');
                            location.reload();
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Lỗi!', 'Có lỗi xảy ra khi thêm donation.', 'error');
                    })
                    .finally(() => {
                        submitBtn.prop('disabled', false).text(originalText);
                    });
            });

            $(document).on('click', '.edit-donation', function() {
                const donationId = $(this).data('id');
                const row = $(this).closest('tr');

                $('#edit_donation_id').val(donationId);
                $('#edit_name').val(row.find('td:eq(1)').text().trim());
                $('#edit_amount').val(row.find('td:eq(2)').text().replace(/[^\d]/g, ''));

                // Format datetime for input
                const donatedAt = row.find('td:eq(3)').text().trim(); 
                const parts = donatedAt.split(' ');
                const datePart = parts[0].split('/');
                const timePart = parts[1] || '00:00';

                const formattedDate =
                    `${datePart[2]}-${datePart[1].padStart(2, '0')}-${datePart[0].padStart(2, '0')}T${timePart}`;
                $('#edit_donated_at').val(formattedDate);

                $('#editDonationModal').modal('show');
            });

            $('#saveEditDonation').on('click', function() {
                const saveBtn = $(this);
                const originalText = saveBtn.text();

                saveBtn.prop('disabled', true).text('Đang lưu...');

                const donationId = $('#edit_donation_id').val();
                const formData = new FormData($('#editDonationForm')[0]);

                fetch(routes.update.replace(':id', donationId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-HTTP-Method-Override': 'PUT'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire('Thành công!', 'Cập nhật donation thành công!', 'success');
                            $('#editDonationModal').modal('hide');
                            location.reload();
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Lỗi!', 'Có lỗi xảy ra khi cập nhật donation.', 'error');
                    })
                    .finally(() => {
                        saveBtn.prop('disabled', false).text(originalText);
                    });
            });
        });
    </script>
@endpush
