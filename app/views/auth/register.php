<div class="auth-container">
    <div class="auth-card">
        <!-- Brand Section -->
        <div class="brand-section">
            <a href="<?php echo SITE_URL; ?>" class="brand-link">
                <div class="auth-logo-container">
                    <img src="<?php echo SITE_URL; ?>/public/images/logo/logo.png" alt="Logo" class="auth-logo">
                </div>
                <h1 class="brand-title">Little Bone</h1>
                <p class="brand-subtitle">Tạo tài khoản mới!</p>
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

        <!-- Register Form -->
        <form class="auth-form" method="POST" action="<?php echo SITE_URL; ?>/register">
            <div class="form-group">
                <div class="input-container">
                    <i class="bi bi-person input-icon"></i>
                    <input
                        type="text"
                        name="full_name"
                        placeholder="Họ và tên"
                        class="form-input"
                        required
                        value="<?php echo isset($data['full_name']) ? htmlspecialchars($data['full_name']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <div class="input-container">
                    <i class="bi bi-person-badge input-icon"></i>
                    <input
                        type="text"
                        name="username"
                        placeholder="Tên đăng nhập"
                        class="form-input"
                        required
                        value="<?php echo isset($data['username']) ? htmlspecialchars($data['username']) : ''; ?>">
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
                        required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-container">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Xác nhận mật khẩu"
                        class="form-input"
                        required>
                </div>
            </div>

            <button type="submit" class="auth-btn">
                <span>Đăng ký</span>
                <i class="bi bi-person-plus"></i>
            </button>

            <div class="form-links">
                <span>Đã có tài khoản?</span>
                <a href="<?php echo SITE_URL; ?>/login" class="link">Đăng nhập ngay</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.body.classList.add('register-page');
</script>

<link rel="stylesheet" href="<?php echo SITE_URL; ?>/public/css/auth.css">
<script src="<?php echo SITE_URL; ?>/public/js/auth.js"></script>