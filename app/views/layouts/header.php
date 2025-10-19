<header class="modern-header">
    <div class="container">
        <nav class="main-nav">
            <!-- Logo -->
            <a href="<?php echo SITE_URL; ?>" class="logo">
                <img src="<?php echo SITE_URL; ?>/public/images/logo/logo.png" alt="Logo">
                <span>LITTLE BONE</span>
            </a>

            <!-- Menu chính -->
            <div class="nav-menu" id="mainMenu">
                <ul class="nav-links">
                    <?php
                    $currentUri = $_SERVER['REQUEST_URI'];
                    $currentPath = parse_url($currentUri, PHP_URL_PATH);
                    $currentPath = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $currentPath);
                    ?>

                    <li><a href="<?php echo SITE_URL; ?>" class="<?php echo ($currentPath == '/' || $currentPath == '') ? 'active' : ''; ?>">Trang chủ</a></li>

                    <li class="has-dropdown">
                        <a href="#" class="<?php echo (strpos($currentPath, '/regions') === 0) ? 'active' : ''; ?>">Địa điểm </a>
                        <ul class="dropdown">
                            <?php
                            // Lấy các region từ Destination
                            $destination = new \App\Models\Destination();
                            $regions = array_unique(array_column($destination->getAllDestinations(), 'region'));

                            foreach ($regions as $region):
                                if (!empty($region)):
                                    $regionActive = (strpos($currentPath, '/regions/' . urlencode($region)) === 0) ? 'active' : '';
                            ?>
                                    <li><a href="<?php echo SITE_URL; ?>/regions/<?php echo urlencode($region); ?>" class="<?php echo $regionActive; ?>"><?php echo $region; ?></a></li>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </ul>
                    </li>

                    <li><a href="<?php echo SITE_URL; ?>/search" class="<?php echo ($currentPath == '/search' && !isset($_GET['city'])) ? 'active' : ''; ?>">Khách sạn</a></li>

                    <li><a href="<?php echo SITE_URL; ?>/promotions" class="<?php echo (strpos($currentPath, '/promotions') === 0) ? 'active' : ''; ?>">Ưu đãi</a></li>
                    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'hotel_staff'): ?>
                        <li class="has-dropdown">
                            <a href="#" class="<?php echo (strpos($currentPath, '/hotel_staff') === 0) ? 'active' : ''; ?>">Quản lý</a>
                            <ul class="dropdown">
                                <li><a href="<?php echo SITE_URL; ?>/hotel_staff/dashboard" class="<?php echo ($currentPath == '/hotel_staff/dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
                                <li><a href="<?php echo SITE_URL; ?>/hotel_staff/manage_hotel/<?php echo $_SESSION['hotel_id']; ?>" class="<?php echo (strpos($currentPath, '/hotel_staff/manage_hotel') === 0) ? 'active' : ''; ?>">Quản lý khách sạn</a></li>
                                <li><a href="<?php echo SITE_URL; ?>/hotel_staff/manage_rooms" class="<?php echo (strpos($currentPath, '/hotel_staff/manage_rooms') === 0) ? 'active' : ''; ?>">Quản lý phòng</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- Phần tài khoản -->
                <div class="user-actions">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-profile">
                            <a href="<?php echo SITE_URL; ?>/user/profile" class="<?php echo (strpos($currentPath, '/user/profile') === 0) ? 'active' : ''; ?>">
                                <img src="<?php echo SITE_URL; ?>/public/images/avatars/<?php echo isset($_SESSION['user_avatar']) ? $_SESSION['user_avatar'] : 'default.jpg'; ?>" alt="Avatar">
                                <span><?php echo $_SESSION['user_name']; ?></span>
                            </a>
                            <a href="<?php echo SITE_URL; ?>/logout" class="logout-btn" title="Đăng xuất">
                                <i class="bi bi-box-arrow-right"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="auth-links">
                            <a href="<?php echo SITE_URL; ?>/login" class="auth-btn <?php echo (strpos($currentPath, '/login') === 0) ? 'active' : ''; ?>">Đăng nhập</a>
                            <a href="<?php echo SITE_URL; ?>/register" class="auth-btn <?php echo (strpos($currentPath, '/register') === 0) ? 'active' : ''; ?>">Đăng ký</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
</header>

<script>
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', function() {
            document.getElementById('mainMenu').classList.remove('active');
        });
    });

    document.querySelectorAll('.has-dropdown').forEach(item => {
        item.addEventListener('click', function(e) {
            if (window.innerWidth < 992) {
                e.preventDefault();
                this.classList.toggle('open');
            }
        });
    });
</script>