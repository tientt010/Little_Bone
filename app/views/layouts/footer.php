<footer class="site-footer mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="footer-brand d-flex align-items-center mb-3">
                    <img src="<?php echo SITE_URL; ?>/public/images/logo/logo.png" alt="Little Bone Logo" class="footer-logo">
                    <span class="brand-text ms-2">LITTLE BONE</span>
                </div>
                <p class="footer-about">
                    Little Bone là trang web đặt phòng khách sạn hàng đầu, cung cấp dịch vụ đặt phòng nhanh chóng và tiện lợi với giá tốt nhất.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4">
                <h5 class="footer-heading">Tìm kiếm</h5>
                <ul class="footer-links">
                    <li><a href="<?php echo SITE_URL; ?>/search">Khách sạn</a></li>
                    <li><a href="<?php echo SITE_URL; ?>">Điểm đến</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/promotions">Ưu đãi</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/reviews">Đánh giá</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-4">
                <h5 class="footer-heading">Về chúng tôi</h5>
                <ul class="footer-links">
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Liên hệ</a></li>
                    <li><a href="#">Chính sách</a></li>
                    <li><a href="#">Điều khoản</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="footer-heading">Nhận thông tin mới nhất</h5>
                <p class="footer-newsletter-text">Đăng ký nhận thông báo về các ưu đãi mới nhất từ chúng tôi</p>
                <form class="footer-newsletter mt-3">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Email của bạn" aria-label="Email">
                        <button class="btn btn-primary" type="submit">Đăng ký</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="footer-divider"></div>
        <div class="footer-bottom">
            <p class="copyright">© <?php echo date('Y'); ?> Little Bone. All rights reserved.</p>
            <div class="payment-methods">
                <i class="bi bi-credit-card payment-icon"></i>
                <i class="bi bi-paypal payment-icon"></i>
                <i class="bi bi-wallet2 payment-icon"></i>
                <i class="bi bi-bank payment-icon"></i>
            </div>
        </div>
    </div>
</footer>

<div class="floating-cart">
    <a href="<?php echo SITE_URL; ?>/booking/cart" id="cart-link">
        <div class="cart-icon-container">
            <i class="bi bi-building" id="cart-icon"></i>
            <div class="cart-badge" id="cart-badge">0</div>
        </div>
    </a>
</div>

<script>
    window.SITE_URL = "<?php echo SITE_URL; ?>";
</script>

<script>
    // Định nghĩa hàm xử lý khi click vào giỏ hàng
    function handleCartClick(event) {
        if (window.cartManager) {
            window.cartManager.saveCart();
            window.cartManager.updateCartDisplay();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const cartIcon = document.getElementById('cart-icon');
        const cartLink = document.getElementById('cart-link');

        if (cartIcon) {
            cartIcon.addEventListener('click', handleCartClick);

        } else {
            console.warn('Không tìm thấy phần tử có ID "cart-icon"');
        }

        if (cartLink) {
            cartLink.addEventListener('click', handleCartClick);

        } else {
            console.warn('Không tìm thấy phần tử có ID "cart-link"');
        }
    });
</script>

</body>