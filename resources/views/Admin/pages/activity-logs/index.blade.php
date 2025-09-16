@extends('Admin.layouts.sidebar')

@section('title', 'Log hoạt động')

@section('main-content')
<div class="category-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item current">Log hoạt động</li>
        </ol>
    </div>

    <div class="content-card">
        <div class="card-top">
            <div class="card-title">
                <i class="fas fa-history icon-title"></i>
                <h5>Log hoạt động Admin</h5>
                <small class="text-muted">Theo dõi tất cả thao tác của admin</small>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="action">Hành động:</label>
                        <select name="action" id="action" class="filter-select">
                            <option value="">Tất cả</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="model_type">Loại:</label>
                        <select name="model_type" id="model_type" class="filter-select">
                            <option value="">Tất cả</option>
                            @foreach($modelTypes as $modelType)
                                <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                    {{ class_basename($modelType) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="user_id">Người dùng:</label>
                        <select name="user_id" id="user_id" class="filter-select">
                            <option value="">Tất cả</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="date_from">Từ ngày:</label>
                        <input type="date" name="date_from" id="date_from" class="filter-input" 
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="filter-group">
                        <label for="date_to">Đến ngày:</label>
                        <input type="date" name="date_to" id="date_to" class="filter-input" 
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="filter-group">
                        <label for="search">Tìm kiếm:</label>
                        <input type="text" name="search" id="search" class="filter-input" 
                               placeholder="Tìm trong mô tả..." value="{{ request('search') }}">
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-content">
            @if($logs->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h4>Không có log hoạt động nào</h4>
                    <p>Chưa có hoạt động nào được ghi lại.</p>
                </div>
            @else
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="column-small">STT</th>
                                <th class="column-medium">Người thực hiện</th>
                                <th class="column-small">Hành động</th>
                                <th class="column-medium">Đối tượng</th>
                                <th class="column-large">Mô tả</th>
                                <th class="column-medium">Route</th>
                                <th class="column-small">IP</th>
                                <th class="column-small">Thời gian</th>
                                <th class="column-small text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $index => $log)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $log->user->id) }}" class="user-info text-decoration-none ">
                                            <div class="user-avatar">
                                                @if($log->user->avatar)
                                                    <img src="{{ Storage::url($log->user->avatar) }}" alt="{{ $log->user->name }}" class="avatar-img">
                                                @else
                                                    <div class="avatar-placeholder">
                                                        {{ substr($log->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">{{ $log->user->name }}</div>
                                                <div class="user-email text-muted">{{ $log->user->email }}</div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="action-badge action-{{ $log->action }}">
                                            {{ $log->formatted_action }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="model-info">
                                            <div class="model-name">{{ $log->formatted_model }}</div>
                                            @if($log->model_id)
                                                <div class="model-id text-muted">ID: {{ $log->model_id }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="description">
                                            {{ $log->description }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="route-info">
                                            <div class="route-name">{{ $log->route_name ?? 'N/A' }}</div>
                                            <div class="route-url text-muted">{{ $log->route_url ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="ip-address">{{ $log->ip_address }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="datetime">
                                            <div class="date">{{ $log->created_at->format('d/m/Y') }}</div>
                                            <div class="time text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons-wrapper">
                                            <button type="button" class="action-icon detail-icon" 
                                                    onclick="showLogDetail({{ $log->id }})" 
                                                    title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal xem chi tiết -->
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-labelledby="logDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logDetailModalLabel">Chi tiết hoạt động</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="logDetailContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.filter-section {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 20px;
}

.filter-form {
    width: 100%;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.filter-group label {
    font-size: 12px;
    font-weight: 600;
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-select,
.filter-input {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    background: white;
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.filter-btn,
.clear-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s;
}

.filter-btn {
    background: #007bff;
    color: white;
}

.filter-btn:hover {
    background: #0056b3;
}

.clear-btn {
    background: #6c757d;
    color: white;
}

.clear-btn:hover {
    background: #545b62;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

.user-details {
    min-width: 0;
}

.user-name {
    font-weight: 600;
    color: #333;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-email {
    font-size: 11px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.action-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.action-create {
    background: #d4edda;
    color: #155724;
}

.action-update {
    background: #fff3cd;
    color: #856404;
}

.action-delete {
    background: #f8d7da;
    color: #721c24;
}

.action-bulk_delete {
    background: #f8d7da;
    color: #721c24;
}

.action-login {
    background: #d1ecf1;
    color: #0c5460;
}

.action-logout {
    background: #e2e3e5;
    color: #383d41;
}

.model-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.model-name {
    font-weight: 500;
    color: #333;
    font-size: 13px;
}

.model-id {
    font-size: 11px;
}

.description {
    font-size: 13px;
    color: #495057;
    line-height: 1.4;
    max-width: 300px;
    word-wrap: break-word;
}

.route-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.route-name {
    font-weight: 500;
    color: #333;
    font-size: 12px;
    font-family: monospace;
}

.route-url {
    font-size: 11px;
    word-break: break-all;
    max-width: 200px;
}

.ip-address {
    font-family: monospace;
    font-size: 12px;
    color: #6c757d;
}

.datetime {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.date {
    font-weight: 500;
    color: #333;
    font-size: 12px;
}

.time {
    font-size: 11px;
}

.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.detail-icon {
    background: #17a2b8;
    color: white;
}

.detail-icon:hover {
    background: #138496;
}

.log-detail-section {
    margin-bottom: 20px;
}

.log-detail-section h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 5px;
}

.log-detail-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}

.log-detail-row {
    display: flex;
    margin-bottom: 8px;
}

.log-detail-label {
    font-weight: 600;
    min-width: 120px;
    color: #495057;
}

.log-detail-value {
    flex: 1;
    color: #333;
}

.json-content {
    background: #f1f3f4;
    padding: 10px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
    white-space: pre-wrap;
    max-height: 200px;
    overflow-y: auto;
}

.no-data {
    color: #6c757d;
    font-style: italic;
}
</style>

<script>
function showLogDetail(logId) {
    $('#logDetailModal').modal('show');
    
    $.ajax({
        url: '{{ route("admin.activity-logs.show", ":id") }}'.replace(':id', logId),
        method: 'GET',
        success: function(response) {
            $('#logDetailContent').html(response);
        },
        error: function() {
            $('#logDetailContent').html('<div class="alert alert-danger">Không thể tải chi tiết hoạt động.</div>');
        }
    });
}
</script>
@endsection
