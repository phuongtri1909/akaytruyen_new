@extends('admin.layouts.sidebar')

@section('title', 'Quản lý quyền')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Quyền</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-key icon-title"></i>
                    <h5>Danh sách quyền</h5>
                </div>
               
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.permissions.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-6">
                            <label for="name_filter">Tên quyền</label>
                            <input type="text" id="name_filter" name="name" class="filter-input"
                                placeholder="Tìm theo tên quyền" value="{{ request('name') }}">
                        </div>
                        <div class="col-6">
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
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.permissions.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('name') || request('role'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('name'))
                            <span class="filter-tag">
                                <span>Tên: {{ request('name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}"
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
                    </div>
                @endif

                @if ($permissions->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        @if (request('name') || request('role'))
                            <h4>Không tìm thấy quyền nào</h4>
                            <p>Không có quyền nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.permissions.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có quyền nào</h4>
                            <p>Hãy đồng bộ quyền từ hệ thống để bắt đầu.</p>
                            <button type="button" class="action-button" id="syncPermissionsEmpty">
                                <i class="fas fa-sync"></i> Đồng bộ quyền
                            </button>
                        @endif
                    </div>
                @else
                    <div class="permissions-stats">
                        <div class="stat-item">
                            <span class="stat-label">Tổng quyền:</span>
                            <span class="stat-value">{{ $totalPermissions }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Đã gán vai trò:</span>
                            <span class="stat-value">{{ $assignedPermissions }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Chưa gán vai trò:</span>
                            <span class="stat-value">{{ $unassignedPermissions }}</span>
                        </div>
                    </div>

                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên quyền</th>
                                    <th class="column-large">Vai trò được gán</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissions as $index => $permission)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($permissions->currentPage() - 1) * $permissions->perPage() + $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $permission->name }}</strong>
                                        </td>
                                        <td>
                                            <div class="roles-list">
                                                @if ($permission->roles->count() > 0)
                                                    @foreach ($permission->roles->take(3) as $role)
                                                        <span class="role-badge">{{ $role->name }}</span>
                                                    @endforeach
                                                    @if ($permission->roles->count() > 3)
                                                        <span class="more-roles">+{{ $permission->roles->count() - 3 }} vai trò khác</span>
                                                    @endif
                                                @else
                                                    <span class="no-roles">Chưa gán vai trò</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="permission-date">
                                            {{ $permission->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                @can('gan_quyen_cho_vai_tro')
                                                    <button type="button" class="action-icon edit-icon" 
                                                        onclick="assignRoles({{ $permission->id }}, '{{ $permission->name }}')" 
                                                        title="Gán vai trò">
                                                        <i class="fas fa-users"></i>
                                                    </button>
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền gán vai trò">
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
                            Hiển thị {{ $permissions->firstItem() ?? 0 }} đến {{ $permissions->lastItem() ?? 0 }} của
                            {{ $permissions->total() }} quyền
                        </div>
                        <div class="pagination-controls">
                            {{ $permissions->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Assign Roles -->
    <div class="modal fade" id="assignRolesModal" tabindex="-1" aria-labelledby="assignRolesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignRolesModalLabel">Gán vai trò cho quyền</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignRolesForm">
                        @csrf
                        <input type="hidden" id="permissionId" name="permission_id">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Quyền: <strong id="permissionName"></strong>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-label-custom">Chọn vai trò:</label>
                            <div class="roles-container">
                                <div class="roles-actions">
                                    <button type="button" class="select-all-roles-btn" id="selectAllRoles">
                                        <i class="fas fa-check-square"></i> Chọn tất cả
                                    </button>
                                    <button type="button" class="deselect-all-roles-btn" id="deselectAllRoles">
                                        <i class="fas fa-square"></i> Bỏ chọn tất cả
                                    </button>
                                </div>
                                <div class="roles-grid" id="rolesGrid">
                                    <!-- Roles will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="saveRoles">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .permissions-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .roles-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .role-badge {
            display: inline-block;
            background: #e8f5e9;
            color: #2e7d32;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        .more-roles {
            display: inline-block;
            background: #f5f5f5;
            color: #757575;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-style: italic;
        }

        .no-roles {
            color: #757575;
            font-style: italic;
            font-size: 12px;
        }

        .permission-actions {
            display: flex;
            gap: 10px;
        }

        .roles-container {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
        }

        .roles-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .select-all-roles-btn, .deselect-all-roles-btn {
            padding: 8px 16px;
            border: 1px solid #007bff;
            background: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .deselect-all-roles-btn {
            background: #6c757d;
            border-color: #6c757d;
        }

        .select-all-roles-btn:hover {
            background: #0056b3;
            border-color: #0056b3;
        }

        .deselect-all-roles-btn:hover {
            background: #545b62;
            border-color: #545b62;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
        }

        .role-item {
            padding: 8px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .role-item:hover {
            background: #e9ecef;
            border-color: #007bff;
        }

        .role-label {
            font-size: 13px;
            color: #333;
            cursor: pointer;
            margin: 0;
            padding-left: 25px;
            position: relative;
        }

        .role-label::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #ced4da;
            border-radius: 3px;
            background: white;
            transition: all 0.3s ease;
        }

        .role-item input[type="checkbox"]:checked + .role-label::before {
            background: #007bff;
            border-color: #007bff;
        }

        .role-item input[type="checkbox"]:checked + .role-label::after {
            content: '✓';
            position: absolute;
            left: 2px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .role-item input[type="checkbox"] {
            display: none;
        }

        .disabled-icon {
            color: #6c757d !important;
            cursor: not-allowed !important;
            opacity: 0.5;
        }

        .disabled-icon:hover {
            color: #6c757d !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .permissions-stats {
                flex-direction: column;
                gap: 10px;
            }

            .roles-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Tự động submit form khi thay đổi bộ lọc
            document.getElementById('role_filter').addEventListener('change', function() {
                document.querySelector('.filter-form').submit();
            });
        });

        function assignRoles(permissionId, permissionName) {
            $('#permissionId').val(permissionId);
            $('#permissionName').text(permissionName);
            
            // Load roles for this permission
            $.ajax({
                url: "{{ route('admin.permissions.get-roles') }}",
                type: 'GET',
                data: {
                    permission_id: permissionId
                },
                success: function(response) {
                    let rolesHtml = '';
                    response.roles.forEach(function(role) {
                        const isChecked = response.assignedRoles.includes(role.id) ? 'checked' : '';
                        rolesHtml += `
                            <div class="role-item">
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="role_${role.id}" 
                                        name="roles[]" value="${role.id}" ${isChecked}>
                                    <label for="role_${role.id}" class="role-label">
                                        ${role.name}
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                    $('#rolesGrid').html(rolesHtml);
                    var modal = new bootstrap.Modal(document.getElementById('assignRolesModal'));
                    modal.show();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Không thể tải danh sách vai trò.'
                    });
                }
            });
        }

        // Select all roles
        $(document).on('click', '#selectAllRoles', function() {
            $('#rolesGrid input[type="checkbox"]').prop('checked', true);
        });

        // Deselect all roles
        $(document).on('click', '#deselectAllRoles', function() {
            $('#rolesGrid input[type="checkbox"]').prop('checked', false);
        });

        // Save roles assignment
        $(document).on('click', '#saveRoles', function() {
            const permissionId = $('#permissionId').val();
            const selectedRoles = $('#rolesGrid input[type="checkbox"]:checked').map(function() {
                return $(this).val();
            }).get();

            $.ajax({
                url: "{{ route('admin.permissions.assign-roles') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    permission_id: permissionId,
                    roles: selectedRoles
                },
                success: function(response) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('assignRolesModal'));
                    modal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: response.message || 'Gán vai trò thành công!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Có lỗi xảy ra khi gán vai trò.'
                    });
                }
            });
        });
    </script>
@endpush
