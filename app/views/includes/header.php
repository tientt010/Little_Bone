<!-- Header -->
<header class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
            <img src="<?php echo SITE_URL; ?>/public/images/logo.png" alt="Logo" height="40">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/hotels">Khách sạn</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/destinations">Điểm đến</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo SITE_URL; ?>/contact">Liên hệ</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> Tài khoản
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/profile">Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/user/bookings">Đặt phòng của tôi</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/logout">Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/register">Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</header>



<script>
    var SITE_URL = "<?php echo SITE_URL; ?>";

    // Xử lý khi click vào giỏ hàng
    function handleCartClick(event) {
        event.preventDefault();
        if (window.cartManager) {
            window.cartManager.processCart();
        } else {
            window.location.href = SITE_URL + "/booking/cart";
        }
        return false;
    }

    // Đảm bảo CartManager được tải
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof CartManager === 'function' && !window.cartManager) {
            window.cartManager = new CartManager();
            window.cartManager.init();
        }

        const cartIcon = document.getElementById('cart-icon');
        const cartLink = document.getElementById('cart-link');

        if (cartIcon) {
            cartIcon.addEventListener('click', handleCartClick);
        }

        if (cartLink) {
            cartLink.addEventListener('click', handleCartClick);
        }
    });
</script>