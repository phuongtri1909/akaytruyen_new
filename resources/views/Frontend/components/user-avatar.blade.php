@php
    $avatar = $user->avatar_url ?? asset('images/defaults/avatar_default.jpg');
    $role = $user->getRoleNames()->first();
    $email = $user->email;

    $borderMap = [
        'Admin' => 'admin-vip-8.png',
        'Mod' => 'vien_mod.png',
        'Content' => 'avt_content.png',
        'vip' => 'avt_admin.png',
        'VIP PRO' => 'avt_pro_vip.png',
        'VIP PRO MAX' => 'avt_vip_pro_max.gif',
        'VIP SIÊU VIỆT' => 'khung-sieu-viet.png',
    ];

    $border = null;
    $borderStyle = '';

    if ($role === 'Admin' && $email === 'nang2025@gmail.com') {
        $border = asset('images/roles/vien-thanh-nu.png');
    } elseif ($role === 'Admin' && $email === 'nguyenphuochau12t2@gmail.com') {
        $border = asset('images/roles/akay.png');
        $borderStyle = 'width: 200%; height: 200%; top: 31%;';
    } else {
        $border = isset($borderMap[$role]) ? asset('images/roles/' . $borderMap[$role]) : null;
    }
@endphp

<div class="avatar-wrapper" style="position: relative; width: 40px; height: 40px; display: inline-block; flex-shrink: 0;">
    <img src="{{ $avatar }}"
        class="avatar rounded-circle border border-3"
        alt="{{ $user->name }}"
        style="width: 100%; height: 100%; object-fit: cover;"
        loading="lazy">

    @if ($border)
        <img src="{{ $border }}"
            class="rounded-circle"
            alt="Border {{ $role }}"
            style="
                position: absolute;
                top: 50%;
                left: 50%;
                {{ $borderStyle ?: 'width: 135%; height: 135%;' }}
                transform: translate(-50%, -50%);
                object-fit: cover;
                pointer-events: none;
                z-index: 2;
            "
            loading="lazy">
    @endif
</div>
