@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý người dùng')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Người dùng</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-users icon-title"></i>
                    <h5>Danh sách người dùng</h5>
                </div>
                <div class="card-actions">
                    @can('xoa_nguoi_dung')
                        <button type="button" class="action-button danger-button" id="bulk-delete-btn" style="display: none;">
                            <i class="fas fa-trash"></i> Xóa đã chọn
                        </button>
                    @endcan
                    @can('them_nguoi_dung')
                        <a href="{{ route('admin.users.create') }}" class="action-button">
                            <i class="fas fa-plus"></i> Thêm người dùng
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.users.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="user_id_filter">ID người dùng</label>
                            <input type="number" id="user_id_filter" name="user_id" class="filter-input"
                                placeholder="Nhập ID" value="{{ request('user_id') }}">
                        </div>
                        <div class="col-3">
                            <label for="search_filter">Tìm kiếm</label>
                            <input type="text" id="search_filter" name="search" class="filter-input"
                                placeholder="Tên hoặc email" value="{{ request('search') }}">
                        </div>
                        <div class="col-3">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đã xác thực
                                </option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Chưa xác
                                    thực</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="role_filter">Vai trò</label>
                            <select id="role_filter" name="role" class="filter-input">
                                <option value="">Tất cả vai trò</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <label for="ban_filter">Trạng thái ban</label>
                            <select id="ban_filter" name="ban" class="filter-input">
                                <option value="">Tất cả</option>
                                <option value="no_ban" {{ request('ban') == 'no_ban' ? 'selected' : '' }}>Không bị ban</option>
                                <option value="ban_login" {{ request('ban') == 'ban_login' ? 'selected' : '' }}>Ban đăng nhập</option>
                                <option value="ban_comment" {{ request('ban') == 'ban_comment' ? 'selected' : '' }}>Ban bình luận</option>
                                <option value="ban_rate" {{ request('ban') == 'ban_rate' ? 'selected' : '' }}>Ban đánh giá</option>
                                <option value="ban_read" {{ request('ban') == 'ban_read' ? 'selected' : '' }}>Ban đọc truyện</option>
                                <option value="ban_ip" {{ request('ban') == 'ban_ip' ? 'selected' : '' }}>Ban IP</option>
                                <option value="any_ban" {{ request('ban') == 'any_ban' ? 'selected' : '' }}>Bị ban (bất kỳ loại nào)</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('user_id') || request('search') || request('status') || request('role') || request('ban'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('user_id'))
                            <span class="filter-tag">
                                <span>ID: {{ request('user_id') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('user_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('search'))
                            <span class="filter-tag">
                                <span>Tìm kiếm: {{ request('search') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('search')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('status'))
                            <span class="filter-tag">
                                <span>Trạng thái:
                                    {{ request('status') == 'active' ? 'Đã xác thực' : 'Chưa xác thực' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('role'))
                            <span class="filter-tag">
                                <span>Vai trò: {{ request('role') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('role')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('ban'))
                            <span class="filter-tag">
                                <span>Ban: 
                                    @switch(request('ban'))
                                        @case('no_ban') Không bị ban @break
                                        @case('ban_login') Ban đăng nhập @break
                                        @case('ban_comment') Ban bình luận @break
                                        @case('ban_rate') Ban đánh giá @break
                                        @case('ban_read') Ban đọc truyện @break
                                        @case('ban_ip') Ban IP @break
                                        @case('any_ban') Bị ban (bất kỳ loại nào) @break
                                        @default {{ request('ban') }} @break
                                    @endswitch
                                </span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('ban')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($users->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        @if (request('user_id') || request('search') || request('status') || request('role') || request('ban'))
                            <h4>Không tìm thấy người dùng nào</h4>
                            <p>Không có người dùng nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.users.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có người dùng nào</h4>
                            <p>Bắt đầu bằng cách thêm người dùng đầu tiên.</p>
                            <a href="{{ route('admin.users.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm người dùng mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @can('xoa_nguoi_dung')
                                        <th class="column-small text-center">
                                            <input type="checkbox" id="select-all" title="Chọn tất cả">
                                        </th>
                                    @endcan
                                    <th class="column-small">STT</th>
                                    <th class="column-small">ID</th>
                                    <th class="column-large">Tên</th>
                                    <th class="column-large">Email</th>
                                    <th class="column-medium">IP Address</th>
                                    <th class="column-medium">Vai trò</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-medium">Đăng nhâp lần cuối</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr>
                                        @can('xoa_nguoi_dung')
                                            <td class="text-center">
                                                @php
                                                    $smtpSetting = \App\Models\SMTPSetting::first();
                                                    $isSuperAdmin = $smtpSetting && $smtpSetting->admin_email === auth()->user()->email;
                                                    $isUserSuperAdmin = $smtpSetting && $smtpSetting->admin_email === $user->email;
                                                    $canDelete = !$isUserSuperAdmin && !($user->hasRole('Admin') && !$isSuperAdmin);
                                                @endphp
                                                @if($canDelete)
                                                    <input type="checkbox" class="user-checkbox" value="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                                @else
                                                    <span class="text-muted" title="Không thể xóa">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endif
                                            </td>
                                        @endcan
                                        <td class="text-center">
                                            {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                                        <td class="text-center">{{ $user->id }}</td>
                                        <td class="item-title">
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    @if ($user->avatar)
                                                        <img class="img-fluid rounded-circle"
                                                            style="width: 32px; height: 32px; object-fit: cover;"
                                                            src="{{ Storage::url($user->avatar) }}"
                                                            alt="{{ $user->name }}">
                                                    @else
                                                        <i class="fas fa-user rounded-circle"></i>
                                                    @endif
                                                </div>
                                                <span class="user-name">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="ip-address">{{ $user->ip_address ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div class="role-badges">
                                                @forelse($user->roles as $role)
                                                    <span class="role-badge role-{{ strtolower($role->name) }}">
                                                        {{ $role->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted">Chưa có vai trò</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($user->isBanned() || $user->banIp)
                                                @if($user->userBan->login)
                                                    <span class="status-badge status-banned">
                                                        <i class="fas fa-ban"></i>
                                                        ban Login
                                                    </span>
                                                @endif
                                                @if($user->userBan->comment)
                                                    <span class="status-badge status-banned">
                                                        <i class="fas fa-ban"></i>
                                                        ban Comment
                                                    </span>
                                                @endif
                                                @if($user->userBan->rate)
                                                    <span class="status-badge status-banned">
                                                        <i class="fas fa-ban"></i>
                                                        ban Rate
                                                    </span>
                                                @endif
                                                @if($user->userBan->read)
                                                    <span class="status-badge status-banned">
                                                        <i class="fas fa-ban"></i>
                                                        ban Read
                                                    </span>
                                                @endif
                                                @if($user->banIp)
                                                    <span class="status-badge status-banned">
                                                        <i class="fas fa-ban"></i>
                                                        ban IP
                                                    </span>
                                                @endif
                                            @elseif($user->active == 'active')
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-check-circle"></i>
                                                    Hoạt động
                                                </span>
                                            @else
                                                <span class="status-badge status-inactive">
                                                    <i class="fas fa-clock"></i>
                                                    Chờ xác thực
                                                </span>
                                            @endif
                                        </td>
                                        <td class="user-date">
                                            @if ($user->last_login_time)
                                                {{ $user->last_login_time->format('d/m/Y H:i') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                      
                                        <td>
                                            <div class="action-buttons-grid">
                                                @php
                                                    $smtpSetting = \App\Models\SMTPSetting::first();
                                                    $isSuperAdmin =
                                                        $smtpSetting &&
                                                        $smtpSetting->admin_email === auth()->user()->email;
                                                    $isUserSuperAdmin =
                                                        $smtpSetting && $smtpSetting->admin_email === $user->email;
                                                @endphp

                                                <!-- Dòng 1: Xem + Chỉnh sửa -->
                                                <div class="action-row">
                                                    <a href="{{ route('admin.users.show', $user) }}"
                                                        class="action-icon view-icon text-decoration-none"
                                                        title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if ($isUserSuperAdmin && !$isSuperAdmin)
                                                        <span class="action-icon disabled-icon"
                                                            title="Không có quyền chỉnh sửa Super Admin">
                                                            <i class="fas fa-lock"></i>
                                                        </span>
                                                    @elseif(auth()->user()->can('sua_nguoi_dung'))
                                                        <a href="{{ route('admin.users.edit', $user) }}"
                                                            class="action-icon edit-icon text-decoration-none"
                                                            title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @else
                                                        <span class="action-icon disabled-icon" title="Không có quyền">
                                                            <i class="fas fa-lock"></i>
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Dòng 2: Ban + Xóa -->
                                                <div class="action-row">
                                                    @if ($isUserSuperAdmin)
                                                        <span class="action-icon disabled-icon"
                                                            title="Không thể ban Super Admin">
                                                            <i class="fas fa-shield-alt"></i>
                                                        </span>
                                                    @elseif(auth()->user()->can('sua_nguoi_dung'))
                                                        <button class="action-icon ban-icon"
                                                            onclick="banUser({{ $user->id }})"
                                                            title="Quản lý ban người dùng">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    @else
                                                        <span class="action-icon disabled-icon" title="Không có quyền">
                                                            <i class="fas fa-lock"></i>
                                                        </span>
                                                    @endif

                                                    @if ($isUserSuperAdmin)
                                                        <span class="action-icon disabled-icon"
                                                            title="Không thể xóa Super Admin">
                                                            <i class="fas fa-shield-alt"></i>
                                                        </span>
                                                    @elseif($user->hasRole('Admin') && !$isSuperAdmin)
                                                        <span class="action-icon disabled-icon"
                                                            title="Không có quyền xóa admin khác">
                                                            <i class="fas fa-lock"></i>
                                                        </span>
                                                    @elseif(auth()->user()->can('xoa_nguoi_dung'))
                                                        @include('components.delete-form', [
                                                            'id' => $user->id,
                                                            'route' => route('admin.users.destroy', $user),
                                                            'message' => "Bạn có chắc chắn muốn xóa người dùng '{$user->name}'?",
                                                        ])
                                                    @else
                                                        <span class="action-icon disabled-icon" title="Không có quyền">
                                                            <i class="fas fa-lock"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $users->firstItem() ?? 0 }} đến {{ $users->lastItem() ?? 0 }} của
                            {{ $users->total() }} người dùng
                        </div>
                        <div class="pagination-controls">
                            {{ $users->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .role-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            background: #e9ecef;
            color: #495057;
        }

        .role-badge.role-admin {
            background: #dc3545;
            color: white;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-inactive {
            background-color: #f5f5f5;
            color: #757575;
        }

        .status-banned {
            background-color: #ffebee;
            color: #c62828;
        }

        .ban-icon {
            color: #dc3545;
        }

        .unban-icon {
            color: #28a745;
        }

        .user-date {
            font-size: 14px;
            color: #6c757d;
        }

        .ip-address {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #6c757d;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .action-buttons-grid {
            display: flex;
            flex-direction: column;
            gap: 4px;
            align-items: center;
        }

        .action-row {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-icon {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .danger-button {
            background-color: #dc3545;
            color: white;
            border: 1px solid #dc3545;
        }

        .danger-button:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .user-checkbox {
            transform: scale(1.2);
        }

        #select-all {
            transform: scale(1.2);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Khi thay đổi bộ lọc, tự động submit form
        document.getElementById('status_filter').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });

        document.getElementById('role_filter').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });

        document.getElementById('ban_filter').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });

        // Bulk delete functionality
        $(document).ready(function() {
            const selectAllCheckbox = $('#select-all');
            const userCheckboxes = $('.user-checkbox');
            const bulkDeleteBtn = $('#bulk-delete-btn');

            // Select all functionality
            selectAllCheckbox.on('change', function() {
                const isChecked = $(this).is(':checked');
                userCheckboxes.prop('checked', isChecked);
                toggleBulkDeleteButton();
            });

            // Individual checkbox change
            userCheckboxes.on('change', function() {
                const totalCheckboxes = userCheckboxes.length;
                const checkedCheckboxes = userCheckboxes.filter(':checked').length;
                
                selectAllCheckbox.prop('checked', totalCheckboxes === checkedCheckboxes);
                selectAllCheckbox.prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
                
                toggleBulkDeleteButton();
            });

            // Toggle bulk delete button visibility
            function toggleBulkDeleteButton() {
                const checkedCount = userCheckboxes.filter(':checked').length;
                if (checkedCount > 0) {
                    bulkDeleteBtn.show();
                    bulkDeleteBtn.html(`<i class="fas fa-trash"></i> Xóa đã chọn (${checkedCount})`);
                } else {
                    bulkDeleteBtn.hide();
                }
            }

            // Bulk delete action
            bulkDeleteBtn.on('click', function() {
                const selectedUsers = userCheckboxes.filter(':checked');
                const userIds = selectedUsers.map(function() {
                    return $(this).val();
                }).get();
                const userNames = selectedUsers.map(function() {
                    return $(this).data('user-name');
                }).get();

                if (userIds.length === 0) {
                    Swal.fire('Cảnh báo', 'Vui lòng chọn ít nhất một người dùng để xóa!', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận xóa hàng loạt',
                    html: `
                        <p>Bạn có chắc chắn muốn xóa <strong>${userIds.length}</strong> người dùng sau?</p>
                        <div style="max-height: 200px; overflow-y: auto; text-align: left; margin: 10px 0;">
                            ${userNames.map(name => `<div>• ${name}</div>`).join('')}
                        </div>
                        <p style="color: #dc3545; font-weight: bold;">Hành động này không thể hoàn tác!</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Xóa ngay',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Đang xóa...',
                            text: 'Vui lòng chờ trong giây lát',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send bulk delete request
                        $.ajax({
                            url: '{{ route("admin.users.bulk-delete") }}',
                            type: 'POST',
                            data: {
                                user_ids: userIds,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                const message = xhr.responseJSON?.message || 'Có lỗi xảy ra khi xóa người dùng';
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: message,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });

        // Ban/Unban user function
        function banUser(userId) {
            // Lấy thông tin ban hiện tại
            $.ajax({
                url: `/admin/users/${userId}/ban-info`,
                type: 'GET',
                success: function(response) {
                    const banInfo = response.ban;

                    Swal.fire({
                        title: 'Quản lý ban người dùng',
                        html: `
                            <div class="form-group">
                                <label>Chọn loại ban:</label>
                                <div style="margin: 10px 0;">
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" id="ban_login" value="login" ${banInfo.login ? 'checked' : ''}> 
                                        Ban đăng nhập
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" id="ban_comment" value="comment" ${banInfo.comment ? 'checked' : ''}> 
                                        Ban bình luận
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" id="ban_rate" value="rate" ${banInfo.rate ? 'checked' : ''}> 
                                        Ban đánh giá
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" id="ban_read" value="read" ${banInfo.read ? 'checked' : ''}> 
                                        Ban đọc truyện
                                    </label>
                                    <label style="display: block; margin: 5px 0;">
                                        <input type="checkbox" id="ban_ip" value="ip" ${banInfo.ip ? 'checked' : ''}> 
                                        Ban IP Address
                                    </label>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Cập nhật',
                        cancelButtonText: 'Hủy',
                        confirmButtonColor: '#dc3545',
                        preConfirm: () => {
                            const banTypes = [];
                            if (document.getElementById('ban_login').checked) banTypes.push('login');
                            if (document.getElementById('ban_comment').checked) banTypes.push('comment');
                            if (document.getElementById('ban_rate').checked) banTypes.push('rate');
                            if (document.getElementById('ban_read').checked) banTypes.push('read');
                            if (document.getElementById('ban_ip').checked) banTypes.push('ip');

                            return {
                                banTypes: banTypes // Luôn gửi mảng, có thể rỗng
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/users/${userId}/ban`,
                                type: 'POST',
                                data: {
                                    ban_types: result.value.banTypes,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire('Thành công!', response.message, 'success')
                                        .then(() => {
                                            location.reload();
                                        });
                                },
                                error: function(xhr) {
                                    const message = xhr.responseJSON?.message ||
                                        'Có lỗi xảy ra';
                                    Swal.fire('Lỗi!', message, 'error');
                                }
                            });
                        }
                    });
                },
                error: function(xhr) {
                    Swal.fire('Lỗi!', 'Không thể lấy thông tin ban', 'error');
                }
            });
        }
    </script>
@endpush
