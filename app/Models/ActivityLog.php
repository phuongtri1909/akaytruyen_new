<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Event;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
        'route_name',
        'route_url'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getFormattedActionAttribute(): string
    {
        $actions = [
            'create' => 'Tạo mới',
            'update' => 'Cập nhật',
            'delete' => 'Xóa',
            'login' => 'Đăng nhập',
            'logout' => 'Đăng xuất',
            'bulk_delete' => 'Xóa hàng loạt',
            'toggle_pin' => 'Ghim/Bỏ ghim',
            'ban' => 'Cấm',
            'unban' => 'Bỏ cấm',
            'upload' => 'Tải lên',
            'download' => 'Tải xuống'
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }

    public function getFormattedModelAttribute(): string
    {
        if (!$this->model_type) {
            return 'Hệ thống';
        }

        $models = [
            'App\Models\Story' => 'Truyện',
            'App\Models\Category' => 'Danh mục',
            'App\Models\Chapter' => 'Chương',
            'App\Models\User' => 'Người dùng',
            'App\Models\Comment' => 'Bình luận',
            'App\Models\Rating' => 'Đánh giá',
            'App\Models\SeoSetting' => 'SEO',
            'App\Models\Donation' => 'Donation',
            'App\Models\Donate' => 'Donate'
        ];

        $modelName = $models[$this->model_type] ?? class_basename($this->model_type);
        
        if ($this->model_name) {
            return $modelName . ': ' . $this->model_name;
        }

        return $modelName;
    }
}
