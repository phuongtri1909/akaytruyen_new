<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;


class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    const ROLE_ADMIN       = 'Admin';
    const ROLE_ADMIN_BRAND = 'Admin Brand';
    const ROLE_SEO         = 'SEO';
    const ROLE_CONTENT     = 'Content';

    const STATUS_ACTIVE   = 1;
    const STATUS_INACTIVE = 2;

    const STATUS_TEXT = [
        self::STATUS_ACTIVE   => 'Hoạt động',
        self::STATUS_INACTIVE => 'Ngừng hoạt động',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'avatar',
        'role',
        'status',
        'active',
        'key_active',
        'key_reset_password',
        'reset_password_at',
        'ip_address',
        'last_login_time',
        'email_verified_at',
        'remember_token',
        'created_at',
        'ip_address',
        'rating',
        'created_by',
        'google_id',
        'donate_amount'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'last_login_time'   => 'datetime',
    ];

    public function ban()
    {
        return $this->hasOne(UserBan::class);
    }

    public function getBanAttribute()
    {
        return $this->ban()->first() ?? new UserBan([
            'user_id' => $this->id,
            'login' => false,
            'comment' => false,
            'rate' => false,
            'read' => false,
        ]);
    }

    public function banIps()
    {
        return $this->hasMany(BanIp::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function stories()
    {
        return $this->hasMany(Story::class, 'author_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function routeNotificationForDatabase()
    {
        return $this->notifications();
    }

    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')
            ->newQuery()
            ->from('user_notifications');
    }

    /**
     * Get avatar URL (compatible with old and new paths)
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return asset('images/defaults/avatar_default.jpg');
        }

        // Nếu là đường dẫn cũ (uploads/images/avatar/)
        if (str_starts_with($this->avatar, 'uploads/images/avatar/')) {
            return asset($this->avatar);
        }

        // Nếu là đường dẫn storage mới
        return asset('storage/' . $this->avatar);
    }
}
