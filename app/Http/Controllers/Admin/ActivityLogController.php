<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:xem_log_hoat_dong')->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20);

        $actions = ActivityLog::distinct()->pluck('action')->sort();
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->filter()->sort();
        $users = User::whereIn('id', ActivityLog::distinct()->pluck('user_id'))
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('Admin.pages.activity-logs.index', compact('logs', 'actions', 'modelTypes', 'users'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        
        $html = '
        <div class="log-detail-section">
            <h6><i class="fas fa-user"></i> Thông tin người thực hiện</h6>
            <div class="log-detail-content">
                <div class="log-detail-row">
                    <span class="log-detail-label">Tên:</span>
                    <span class="log-detail-value">' . $log->user->name . '</span>
                </div>
                <div class="log-detail-row">
                    <span class="log-detail-label">Email:</span>
                    <span class="log-detail-value">' . $log->user->email . '</span>
                </div>
                <div class="log-detail-row">
                    <span class="log-detail-label">IP Address:</span>
                    <span class="log-detail-value">' . $log->ip_address . '</span>
                </div>
                <div class="log-detail-row">
                    <span class="log-detail-label">User Agent:</span>
                    <span class="log-detail-value">' . $log->user_agent . '</span>
                </div>
            </div>
        </div>

        <div class="log-detail-section">
            <h6><i class="fas fa-cog"></i> Thông tin hoạt động</h6>
            <div class="log-detail-content">
                <div class="log-detail-row">
                    <span class="log-detail-label">Hành động:</span>
                    <span class="log-detail-value">' . $log->formatted_action . '</span>
                </div>
                <div class="log-detail-row">
                    <span class="log-detail-label">Đối tượng:</span>
                    <span class="log-detail-value">' . $log->formatted_model . '</span>
                </div>
                <div class="log-detail-row">
                    <span class="log-detail-label">Mô tả:</span>
                    <span class="log-detail-value">' . $log->description . '</span>
                </div>
                <div class="log-detail-row">
                    <span class="log-detail-label">Thời gian:</span>
                    <span class="log-detail-value">' . $log->created_at->format('d/m/Y H:i:s') . '</span>
                </div>
                <div class="log-detail-row">
                    <span class="log-detail-label">Route Name:</span>
                    <span class="log-detail-value">' . ($log->route_name ?? 'N/A') . '</span>
                </div>
                <div class="log-detail-row">
                    <span class="log-detail-label">Route URL:</span>
                    <span class="log-detail-value">' . ($log->route_url ?? 'N/A') . '</span>
                </div>
            </div>
        </div>';

        if ($log->old_values || $log->new_values) {
            $html .= '
            <div class="log-detail-section">
                <h6><i class="fas fa-exchange-alt"></i> Thay đổi dữ liệu</h6>
                <div class="log-detail-content">';
            
            if ($log->old_values) {
                $html .= '
                <div class="log-detail-row">
                    <span class="log-detail-label">Dữ liệu cũ:</span>
                    <div class="log-detail-value">
                        <div class="json-content">' . json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</div>
                    </div>
                </div>';
            }
            
            if ($log->new_values) {
                $html .= '
                <div class="log-detail-row">
                    <span class="log-detail-label">Dữ liệu mới:</span>
                    <div class="log-detail-value">
                        <div class="json-content">' . json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</div>
                    </div>
                </div>';
            }
            
            $html .= '
                </div>
            </div>';
        }

        return $html;
    }
}