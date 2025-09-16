@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý vai trò')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Vai trò</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-users-cog icon-title"></i>
                    <h5>Danh sách vai trò</h5>
                </div>

                @can('them_vai_tro')
                    <a href="{{ route('admin.roles.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm vai trò
                    </a>
                @endcan
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.roles.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-6">
                            <label for="name_filter">Tên vai trò</label>
                            <input type="text" id="name_filter" name="name" class="filter-input"
                                placeholder="Tìm theo tên vai trò" value="{{ request('name') }}">
                        </div>
                        <div class="col-6">
                            <label for="permission_filter">Quyền</label>
                            <select id="permission_filter" name="permission" class="filter-input">
                                <option value="">Tất cả quyền</option>
                                @foreach ($permissions as $permission)
                                    <option value="{{ $permission->name }}"
                                        {{ request('permission') == $permission->name ? 'selected' : '' }}>
                                        {{ $permission->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('name') || request('permission'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('name'))
                            <span class="filter-tag">
                                <span>Tên: {{ request('name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('permission'))
                            <span class="filter-tag">
                                <span>Quyền: {{ request('permission') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('permission')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($roles->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        @if (request('name') || request('permission'))
                            <h4>Không tìm thấy vai trò nào</h4>
                            <p>Không có vai trò nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.roles.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có vai trò nào</h4>
                            <p>Bắt đầu bằng cách thêm vai trò đầu tiên.</p>
                            <a href="{{ route('admin.roles.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm vai trò mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên vai trò</th>
                                    <th class="column-large">Quyền</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $index => $role)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($roles->currentPage() - 1) * $roles->perPage() + $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $role->name }}</strong>
                                        </td>
                                        <td>
                                            <div class="permissions-list">
                                                @if ($role->permissions->count() > 0)
                                                    @foreach ($role->permissions->take(3) as $permission)
                                                        <span class="permission-badge">{{ $permission->name }}</span>
                                                    @endforeach
                                                    @if ($role->permissions->count() > 3)
                                                        <span
                                                            class="more-permissions">+{{ $role->permissions->count() - 3 }}
                                                            quyền khác</span>
                                                    @endif
                                                @else
                                                    <span class="no-permissions">Chưa có quyền</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="role-date">
                                            {{ $role->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                @if ($role->name !== 'Admin' && auth()->user()->can('sua_vai_tro'))
                                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                                        class="action-icon edit-icon text-decoration-none"
                                                        title="{{ $role->protected ? 'Chỉnh sửa quyền (tên không thể thay đổi)' : 'Chỉnh sửa' }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @elseif($role->name === 'Admin')
                                                    <span class="admin-badge"
                                                        title="Vai trò Admin - Full quyền, không thể chỉnh sửa">
                                                        <i class="fas fa-crown"></i>
                                                    </span>
                                                @else
                                                    <span class="action-icon disabled-icon"
                                                        title="Không có quyền chỉnh sửa">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endif

                                                @if (!$role->protected && auth()->user()->can('xoa_vai_tro'))
                                                    @include('components.delete-form', [
                                                        'id' => $role->id,
                                                        'route' => route('admin.roles.destroy', $role),
                                                        'message' => "Bạn có chắc chắn muốn xóa vai trò '{$role->name}'?",
                                                    ])
                                                @elseif($role->protected)
                                                    <span class="protected-badge" title="Không thể xóa vai trò được bảo vệ">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </span>
                                                @else
                                                    <span class="action-icon disabled-icon"
                                                        title="Không có quyền xóa">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $roles->firstItem() ?? 0 }} đến {{ $roles->lastItem() ?? 0 }} của
                            {{ $roles->total() }} vai trò
                        </div>
                        <div class="pagination-controls">
                            {{ $roles->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .permissions-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .permission-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        .more-permissions {
            display: inline-block;
            background: #f5f5f5;
            color: #757575;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-style: italic;
        }

        .no-permissions {
            color: #757575;
            font-style: italic;
            font-size: 12px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('permission_filter').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });
    </script>
@endpush
