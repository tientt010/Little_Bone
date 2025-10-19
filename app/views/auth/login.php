<div class="auth-container">
    <div class="auth-card">
        <!-- Brand Section -->
        <div class="brand-section">
            <a href="<?php echo SITE_URL; ?>" class="brand-link">
                <div class="auth-logo-container">
                    <img src="<?php echo SITE_URL; ?>/public/images/logo/logo.png" alt="Logo" class="auth-logo">
                </div>
                <h1 class="brand-title">Little Bone</h1>
                <p class="brand-subtitle">Chào mừng trở lại!</p>
            </a>
        </div>
        <?php if ($error = $this->getFlash('error')): ?>
            <div class="alert alert-error">
                <i class="bi bi-exclamation-triangle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success = $this->getFlash('success')): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="<?php echo SITE_URL; ?>/login">
            <div class="form-group">
                <div class="input-container">
                    <i class="bi bi-person input-icon"></i>
                    <input
                        type="text"
                        name="login_id"
                        placeholder="Email hoặc tên đăng nhập"
                        class="form-input"
                        required
                        autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <div class="input-container">
                    <i class="bi bi-lock input-icon"></i>
                    <input
                        type="password"
                        name="password"
                        placeholder="Mật khẩu"
                        class="form-input"
                        required
                        autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="auth-btn">
                <span>Đăng nhập</span>
                <i class="bi bi-arrow-right"></i>
            </button>

            <div class="form-links">
                <a href="<?php echo SITE_URL; ?>/forgot-password" class="link">Quên mật khẩu?</a>
                <span class="divider">•</span>
                <a href="<?php echo SITE_URL; ?>/register" class="link">Đăng ký tài khoản</a>
            </div>
        </form>

        <div class="features">
            <div class="feature">
                <i class="bi bi-shield-check"></i>
                <span>Bảo mật</span>
            </div>
            <div class="feature">
                <i class="bi bi-star-fill"></i>
                <span>Uy tín</span>
            </div>
            <div class="feature">
                <i class="bi bi-lightning-fill"></i>
                <span>Nhanh chóng</span>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo SITE_URL; ?>/public/css/auth.css">


<script>
    document.body.classList.add('auth-page');

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.auth-form');
        const authBtn = document.querySelector('.auth-btn');
        const inputs = document.querySelectorAll('.form-input');

        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        form.addEventListener('submit', function() {
            authBtn.classList.add('loading');

            setTimeout(() => {
                authBtn.classList.remove('loading');
            }, 3000);
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.classList.contains('form-input')) {
                const currentIndex = Array.from(inputs).indexOf(e.target);
                const nextInput = inputs[currentIndex + 1];

                if (nextInput) {
                    nextInput.focus();
                } else {
                    form.submit();
                }
            }
        });
    });
</script>
</body>

</html>