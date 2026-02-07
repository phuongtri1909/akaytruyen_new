@php
    $tetPetalImages = [
        asset('images/tet/hoa1.png'),
        asset('images/tet/hoa2.png'),
        asset('images/tet/hoa3.webp'),
        asset('images/tet/hoa4.png'),
        asset('images/tet/hoa5.png'),
    ];
@endphp
<div id="tet-effects-overlay" class="tet-effects-overlay" aria-hidden="true">
    <div class="tet-petals" id="tet-petals" data-images='@json($tetPetalImages)'></div>
    <canvas id="tet-fireworks-canvas" class="tet-fireworks-canvas"></canvas>
</div>

@push('styles')
<style>
.tet-effects-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 9998;
    overflow: hidden;
}
.tet-fireworks-canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: block;
}
.tet-petals { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
.tet-petal {
    position: absolute;
    opacity: 0.9;
    animation: tet-fall linear infinite;
    pointer-events: none;
}
.tet-petal.tet-petal-img {
    width: 24px;
    height: 24px;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
}
@if(empty($tetPetalImages))
.tet-petal {
    width: 12px;
    height: 16px;
    background: linear-gradient(135deg, #f4d03f 0%, #e8b923 50%, #d4a017 100%);
    border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
}
.tet-petal.tet-petal-2 { background: linear-gradient(135deg, #f9e79f 0%, #f4d03f 100%); }
.tet-petal.tet-petal-3 { background: linear-gradient(135deg, #eb984e 0%, #e67e22 100%); }
@endif
@keyframes tet-fall {
    0% { transform: translateY(-10vh) translateX(0) rotate(0deg); opacity: 0.9; }
    25% { transform: translateY(30vh) translateX(15px) rotate(180deg); opacity: 0.85; }
    50% { transform: translateY(60vh) translateX(-10px) rotate(360deg); opacity: 0.75; }
    75% { transform: translateY(85vh) translateX(8px) rotate(540deg); opacity: 0.5; }
    100% { transform: translateY(110vh) translateX(0) rotate(720deg); opacity: 0.3; }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    var petalCount = 28;
    var petalEl = document.getElementById('tet-petals');
    if (petalEl) {
        var images = [];
        try {
            var raw = petalEl.getAttribute('data-images');
            if (raw) images = JSON.parse(raw);
        } catch (e) {}
        for (var i = 0; i < petalCount; i++) {
            var dur = 8 + Math.random() * 8;
            var p = document.createElement('div');
            p.className = 'tet-petal';
            if (images.length > 0) {
                p.classList.add('tet-petal-img');
                p.style.backgroundImage = "url('" + images[Math.floor(Math.random() * images.length)] + "')";
            } else {
                p.classList.add('tet-petal-' + (i % 3 + 1));
            }
            p.style.left = Math.random() * 100 + '%';
            p.style.animationDuration = dur + 's';
            p.style.animationDelay = -Math.random() * dur + 's';
            petalEl.appendChild(p);
        }
    }

    var canvas = document.getElementById('tet-fireworks-canvas');
    if (!canvas) return;

    var ctx = canvas.getContext('2d');
    var W = 0, H = 0;
    var particles = [];
    var fireworks = [];
    var lastLaunch = 0;
    var launchGap = 1800;

    function resize() {
        W = canvas.width = window.innerWidth;
        H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    function random(min, max) {
        return min + Math.random() * (max - min);
    }

    function Firework(sx, sy, tx, ty) {
        this.x = sx;
        this.y = sy;
        this.tx = tx;
        this.ty = ty;
        this.dist = Math.sqrt((tx - sx) * (tx - sx) + (ty - sy) * (ty - sy));
        this.angle = Math.atan2(ty - sy, tx - sx);
        this.v = 2 + Math.random() * 2;
        this.exploded = false;
        this.hue = Math.floor(Math.random() * 60) + 20;
    }
    Firework.prototype.update = function() {
        if (this.exploded) return;
        this.v *= 1.02;
        this.x += Math.cos(this.angle) * this.v;
        this.y += Math.sin(this.angle) * this.v;
        if (Math.sqrt((this.tx - this.x) ** 2 + (this.ty - this.y) ** 2) < 15) {
            this.exploded = true;
            explode(this.x, this.y, this.hue);
        }
    };
    Firework.prototype.draw = function() {
        if (this.exploded) return;
        ctx.beginPath();
        ctx.arc(this.x, this.y, 2, 0, Math.PI * 2);
        ctx.fillStyle = 'hsla(' + this.hue + ', 100%, 60%, 1)';
        ctx.fill();
    };

    function Particle(x, y, hue) {
        this.x = x;
        this.y = y;
        this.vx = random(-6, 6);
        this.vy = random(-8, 2);
        this.hue = hue;
        this.life = 1;
        this.decay = random(0.01, 0.025);
        this.size = random(1.5, 3);
    }
    Particle.prototype.update = function() {
        this.x += this.vx;
        this.y += this.vy;
        this.vy += 0.15;
        this.vx *= 0.98;
        this.vy *= 0.98;
        this.life -= this.decay;
    };
    Particle.prototype.draw = function() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = 'hsla(' + this.hue + ', 100%, 60%, ' + this.life + ')';
        ctx.fill();
    };

    function explode(x, y, hue) {
        var count = 60 + Math.floor(Math.random() * 40);
        for (var i = 0; i < count; i++) {
            particles.push(new Particle(x, y, hue + random(-15, 15)));
        }
    }

    function launchFirework() {
        var sx = W * 0.5;
        var sy = H;
        var tx = random(100, W - 100);
        var ty = random(50, H * 0.6);
        fireworks.push(new Firework(sx, sy, tx, ty));
    }

    function loop(ts) {
        requestAnimationFrame(loop);
        ctx.clearRect(0, 0, W, H);

        if (ts - lastLaunch > launchGap) {
            launchFirework();
            lastLaunch = ts;
        }

        for (var i = fireworks.length - 1; i >= 0; i--) {
            fireworks[i].update();
            fireworks[i].draw();
            if (fireworks[i].exploded) fireworks.splice(i, 1);
        }

        for (var j = particles.length - 1; j >= 0; j--) {
            particles[j].update();
            particles[j].draw();
            if (particles[j].life <= 0) particles.splice(j, 1);
        }
    }
    requestAnimationFrame(loop);
})();
</script>
@endpush
