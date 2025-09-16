@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết người dùng')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                <li class="breadcrumb-item current">Chi tiết</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-user icon-title"></i>
                    <h5>Chi tiết người dùng</h5>
                </div>
                <div class="form-actions">
                    @php
                        $smtpSetting = \App\Models\SMTPSetting::first();
                        $isSuperAdmin = $smtpSetting && $smtpSetting->admin_email === auth()->user()->email;
                        $isUserSuperAdmin = $smtpSetting && $smtpSetting->admin_email === $user->email;
                    @endphp
                    
                    @if($isUserSuperAdmin && !$isSuperAdmin)
                        <span class="action-button disabled-button" title="Không có quyền chỉnh sửa Super Admin">
                            <i class="fas fa-lock"></i> Không có quyền
                        </span>
                    @else
                        <a href="{{ route('admin.users.edit', $user) }}" class="action-button">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                    @endif
                    
                    <a href="{{ route('admin.users.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="form-body">
                <div class="user-detail-container">
                    <!-- User Info -->
                    <div class="user-info-section">
                        <div class="user-avatar-large">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-basic-info">
                            <h4 class="user-name">{{ $user->name }}</h4>
                            <p class="user-email">{{ $user->email }}</p>
                            <div class="user-status">
                                @php
                                    $smtpSetting = \App\Models\SMTPSetting::first();
                                    $isUserSuperAdmin = $smtpSetting && $smtpSetting->admin_email === $user->email;
                                @endphp
                                
                                @if($isUserSuperAdmin)
                                    <span class="status-badge status-super-admin">
                                        <i class="fas fa-crown"></i>
                                        Super Admin
                                    </span>
                                @elseif($user->active == 'active')
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle"></i>
                                        Đã xác thực
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-times-circle"></i>
                                        Chưa xác thực
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- User Details -->
                    <div class="user-details-grid">
                        <div class="detail-card">
                            <div class="detail-header">
                                <i class="fas fa-info-circle"></i>
                                <h6>Thông tin cơ bản</h6>
                            </div>
                            <div class="detail-content">
                                <div class="detail-item">
                                    <span class="detail-label">ID:</span>
                                    <span class="detail-value">{{ $user->id }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tên:</span>
                                    <span class="detail-value">{{ $user->name }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Email:</span>
                                    <span class="detail-value">{{ $user->email }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">IP Address:</span>
                                    <span class="detail-value">{{ $user->ip_address ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Đăng nhâp lần cuối:</span>
                                    <span class="detail-value">{{ $user->last_login_time->format('d/m/Y H:i:s') }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Ngày tạo:</span>
                                    <span class="detail-value">{{ $user->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Cập nhật lần cuối:</span>
                                    <span class="detail-value">{{ $user->updated_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-header">
                                <i class="fas fa-user-tag"></i>
                                <h6>Vai trò</h6>
                            </div>
                            <div class="detail-content">
                                @forelse($user->roles as $role)
                                    <div class="role-item">
                                        <span class="role-name">{{ $role->name }}</span>
                                        <span class="role-permissions-count">
                                            {{ $role->permissions->count() }} quyền
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-muted">Chưa có vai trò nào</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-header">
                                <i class="fas fa-key"></i>
                                <h6>Quyền hạn</h6>
                            </div>
                            <div class="detail-content">
                                @php
                                    $permissions = $user->getAllPermissions();
                                @endphp
                                @forelse($permissions as $permission)
                                    <div class="permission-item">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span>{{ $permission->name }}</span>
                                    </div>
                                @empty
                                    <p class="text-muted">Chưa có quyền hạn nào</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-header">
                                <i class="fas fa-chart-line"></i>
                                <h6>Thống kê</h6>
                            </div>
                            <div class="detail-content">
                                <div class="detail-item">
                                    <span class="detail-label">Số vai trò:</span>
                                    <span class="detail-value">{{ $user->roles->count() }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tổng quyền:</span>
                                    <span class="detail-value">{{ $permissions->count() }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Trạng thái:</span>
                                    <span class="detail-value">
                                        @if($user->active == 'active')
                                            <span class="text-success">Hoạt động</span>
                                        @else
                                            <span class="text-warning">Chờ xác thực</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .user-detail-container {
            padding: 20px;
        }

        .user-info-section {
            display: flex;
            align-items: center;
            gap: 30px;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            color: white;
            margin-bottom: 30px;
        }

        .user-avatar-large {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            backdrop-filter: blur(10px);
        }

        .user-basic-info {
            flex: 1;
        }

        .user-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .user-email {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .user-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-active {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-inactive {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .status-super-admin {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .disabled-button {
            background-color: #6c757d !important;
            color: white !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }

        .user-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .detail-card {
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .detail-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: #495057;
        }

        .detail-header i {
            color: #007bff;
        }

        .detail-content {
            padding: 20px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #6c757d;
        }

        .detail-value {
            font-weight: 600;
            color: #495057;
        }

        .role-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .role-item:last-child {
            border-bottom: none;
        }

        .role-name {
            font-weight: 600;
            color: #495057;
        }

        .role-permissions-count {
            font-size: 12px;
            color: #6c757d;
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .permission-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .permission-item:last-child {
            border-bottom: none;
        }

        .permission-item i {
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .user-info-section {
                flex-direction: column;
                text-align: center;
            }
            
            .user-details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
