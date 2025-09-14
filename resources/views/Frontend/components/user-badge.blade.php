@if($user)
    @php
        $role = $user->getRoleNames()->first();
        $email = $user->email;

        $bgMap = [
            'Admin' => 'admin.gif',
            'Mod' => 'vip.gif',
            'Content' => 'vip.gif',
            'vip' => 'mod.gif',
            'VIP PRO' => 'vip_pro.gif',
            'VIP PRO MAX' => 'vip_pro_max.webp',
            'VIP SIÊU VIỆT' => 'vip-sieu-viet.gif',
        ];

        $bgImage = isset($bgMap[$role]) ? asset('images/roles/' . $bgMap[$role]) : null;
        $colorMap = [
            'Admin' => 'red',
            'Mod' => 'green',
            'Content' => 'yellow',
            'vip' => 'blue',
            'VIP PRO' => 'purple',
            'VIP PRO MAX' => '#f37200',
            'VIP SIÊU VIỆT' => '',
        ];
        $textColor = $colorMap[$role] ?? 'black';
    @endphp

    @if ($role === 'VIP SIÊU VIỆT')
        <div class="vip-sieu-viet-badge">
            <span>{{ $user->name }}</span>
        </div>
    @elseif ($bgImage)
        <div style="
            display: inline-block;
            padding: 4px 8px;
            color: {{ $textColor }};
            background-image: url('{{ $bgImage }}');
            background-size: cover;
            background-repeat: no-repeat;
            border-radius: 4px;
            font-weight: bold;
        ">
            {{ $user->name }}
        </div>
    @else
        <strong>{{ $user->name }}</strong>
    @endif

    {{-- Special badges based on email --}}
    @if($email === 'khaicybers@gmail.com')
        <span class="tooltip-icon">
            <img src="https://cdn3.emoji.gg/emojis/64012-management.png" width="24px" height="24px" style="margin-left:5px;" alt="Dev">
            <span class="tooltip-text">Hỗ Trợ</span>
        </span>
    @elseif($email === 'nguyenphuochau12t2@gmail.com')
        <span class="tooltip-icon">
            <img src="https://cdn3.emoji.gg/emojis/65928-owner.png" width="30px" style="margin-left:5px;margin-top:-10px;" alt="tac gia">
            <span class="tooltip-text">Tác Giả</span>
        </span>
        <div class="custom-badge-tooltip">
            <img src="{{ asset('images/roles/kaybage.png') }}" alt="user" style="width: 60px; height: 60px; margin-left: 5px;">
            <div class="custom-badge-tooltiptext">
                <img src="{{ asset('images/roles/kaybage.png') }}" alt="user" style="width: 70px; height: 70px; display: block; margin: 0 auto;">
                <div class="custom-badge-name">Tác Giả</div>
            </div>
        </div>
    @elseif($user->hasRole('Admin'))
        <span class="tooltip-icon">
            <img src="https://cdn3.emoji.gg/emojis/39760-owner.png" width="30px" style="margin-left:5px;margin-top:-10px;" alt="Admin">
            <span class="tooltip-text">Quản Trị Viên</span>
        </span>

        @php
            $badgeEmail = $user->email;
            $badgeName = 'Tông Chủ';
            $badgeImage = asset('images/roles/tongchu.gif');

            if ($badgeEmail === 'nguyenphuochau12t2@gmail.com') {
                $badgeName = 'Tác Giả';
                $badgeImage = asset('images/roles/akay.png');
            } elseif ($badgeEmail === 'nang2025@gmail.com') {
                $badgeName = 'Thánh Nữ';
                $badgeImage = asset('images/roles/thanhnu.gif');
            }
        @endphp

        <div class="custom-badge-tooltip">
            <img src="{{ $badgeImage }}" alt="user" style="width: 50px; height: 50px; margin-left: 5px;">
            <div class="custom-badge-tooltiptext">
                <img src="{{ $badgeImage }}" alt="user" style="width: 60px; height: 60px; display: block; margin: 0 auto;">
                <div class="custom-badge-name">{{ $badgeName }}</div>
            </div>
        </div>
    @endif

    {{-- Role-based badges --}}
    @if($user->hasRole('Mod'))
        <span class="tooltip-icon">
            <img src="https://cdn3.emoji.gg/emojis/80156-developer.png" width="30px" style="margin-left:5px;margin-top:-10px;" alt="Mod">
            <span class="tooltip-text">Mod kiểm duyệt</span>
        </span>
        <div class="custom-badge-tooltip">
            <img src="{{ asset('images/roles/chapphap.gif') }}" alt="user" style="width: 40px; height: 40px; margin-left: 5px;">
            <div class="custom-badge-tooltiptext">
                <img src="{{ asset('images/roles/chapphap.gif') }}" alt="user" style="width: 50px; height: 50px; display: block; margin: 0 auto;">
                <div class="custom-badge-name">Chấp Pháp</div>
            </div>
        </div>
    @endif

    @if ($user->hasRole('vip'))
        <span class="tooltip-icon">
            <img src="https://cdn3.emoji.gg/emojis/45918-msp-super-vip.png" width="30px" style="margin-left:5px;margin-top:-10px;" alt="vip1">
            <span class="tooltip-text">VIP Bậc I</span>
        </span>
        <div class="custom-badge-tooltip">
            <img src="{{ asset('images/roles/tinhanh.gif') }}" alt="user" style="width: 40px; height: 40px; margin-left: 5px;">
            <div class="custom-badge-tooltiptext">
                <img src="{{ asset('images/roles/tinhanh.gif') }}" alt="user" style="width: 50px; height: 50px; display: block; margin: 0 auto;">
                <div class="custom-badge-name">Tinh Anh Bậc I</div>
            </div>
        </div>
    @endif

    @if ($user->hasRole('VIP PRO'))
        <span class="tooltip-icon">
            <img src="https://cdn3.emoji.gg/emojis/44014-msp-elite-vip.png" width="30px" style="margin-left:5px;margin-top:-10px;" alt="vip2">
            <span class="tooltip-text">VIP Bậc II</span>
        </span>
        <div class="custom-badge-tooltip">
            <img src="{{ asset('images/roles/hophap.gif') }}" alt="user" style="width: 40px; height: 40px; margin-left: 5px;">
            <div class="custom-badge-tooltiptext">
                <img src="{{ asset('images/roles/hophap.gif') }}" alt="user" style="width: 50px; height: 50px; display: block; margin: 0 auto;">
                <div class="custom-badge-name">Hộ Pháp Bậc II</div>
            </div>
        </div>
    @endif

    @if ($user->hasRole('VIP PRO MAX'))
        <span class="tooltip-icon">
            <img src="https://cdn3.emoji.gg/emojis/55280-msp-star-vip.png" width="30px" style="margin-left:5px;margin-top:-10px;" alt="vip3">
            <span class="tooltip-text">VIP Bậc III</span>
        </span>
        <div class="custom-badge-tooltip">
            <div class="crossed-swords">
                <img src="{{ asset('images/roles/huyet-kiem-vip.gif') }}" alt="kiem xanh" class="sword-left">
                <img src="{{ asset('images/roles/kiemdo.gif') }}" alt="kiem do" class="sword-right">
            </div>
            <div class="custom-badge-tooltiptext">
                <img src="{{ asset('images/roles/huyet-kiem-vip.gif') }}" alt="kiem xanh" style="width: 50px; height: 50px; display: block; margin: 0 auto;transform: rotate(-85deg);">
                <img src="{{ asset('images/roles/kiemdo.gif') }}" alt="kiem do" style="width: 50px; height: 60px; display: block; margin: 0 auto;">
                <div class="custom-badge-name">Trưởng Lão Bậc III</div>
            </div>
        </div>
    @endif

    @if ($user->hasRole('User'))
        <div class="custom-badge-tooltip">
            <img src="{{ asset('images/roles/detu.gif') }}" alt="user" style="width: 30px;height: 30px;margin-left: -4px;margin-top: -22px;">
            <div class="custom-badge-tooltiptext">
                <img src="{{ asset('images/roles/detu.gif') }}" alt="user" style="width: 50px; height: 50px; display: block; margin: 0 auto;">
                <div class="custom-badge-name">Đệ Tử</div>
            </div>
        </div>
    @endif

    @if ($user->hasRole('VIP SIÊU VIỆT'))
        <span class="tooltip-icon">
            <img src="https://cdn3.emoji.gg/emojis/2336-vipgif.gif" width="30px" style="margin-left:5px;margin-top:-10px;" alt="vipmax">
            <span class="tooltip-text">VIP MAX</span>
        </span>
        <div class="custom-badge-tooltip">
            <img src="{{ asset('images/roles/bage_vipsieuviet.png') }}" alt="user" style="width: 70px;height: 50px;margin-left: -4px;margin-top: -22px;">
            <div class="custom-badge-tooltiptext">
                <img src="{{ asset('images/roles/bage_vipsieuviet.png') }}" alt="user" style="width: 70px; height: 60px; display: block; margin: 0 auto;">
                <div class="custom-badge-name">Thái Thượng</div>
            </div>
        </div>
    @endif
@else
    <span class="text-muted">Người dùng không tồn tại</span>
@endif
