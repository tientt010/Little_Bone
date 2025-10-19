<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Little Bone</title>

    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/public/images/favicon.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/public/css/main.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/public/css/header.css?v=<?php echo time(); ?>">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <?php if (isset($extraStyles)) echo $extraStyles; ?>
    <script src="<?php echo SITE_URL; ?>/public/js/debug.js"></script>
</head>

<body>
    <!-- Page Preloader -->
    <div id="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
            <div class="preloader-text">Loading...</div>
        </div>
    </div>

    <div class="bg-shape shape1"></div>
    <div class="bg-shape shape2"></div>
    <div class="bg-shape shape3"></div>
    <div class="bg-shape shape4"></div>

    <?php require_once __DIR__ . '/header.php'; ?>

    <main class="container-fluid">
        <?php echo $content; ?>
    </main>

    <?php require_once __DIR__ . '/footer.php'; ?>


    <script>
        window.SITE_URL = '<?php echo SITE_URL; ?>';
    </script>

    <!-- Thêm CartManager trước các script khác -->
    <script src="<?php echo SITE_URL; ?>/public/js/cart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo carousel khi trang đã tải xong
            var carouselElement = document.querySelector('#destinationCarousel');
            if (carouselElement) {
                try {
                    var carousel = new bootstrap.Carousel(carouselElement, {
                        interval: 5000,
                        wrap: true,
                        pause: 'hover',
                        touch: true,
                        keyboard: true
                    });

                    carouselElement.addEventListener('slide.bs.carousel', function(event) {
                        var currentSlide = event.from !== undefined ?
                            carouselElement.querySelector('.carousel-item.active') :
                            null;
                        var nextSlide = event.relatedTarget;

                        if (currentSlide) {
                            var currentCaption = currentSlide.querySelector('.carousel-caption');
                            if (currentCaption) {
                                currentCaption.style.opacity = 0;
                            }
                        }

                        var nextCaption = nextSlide.querySelector('.carousel-caption');
                        if (nextCaption) {
                            nextCaption.style.opacity = 0;
                            nextCaption.style.transform = 'translateY(20px)';

                            setTimeout(function() {
                                nextCaption.style.opacity = 1;
                                nextCaption.style.transform = 'translateY(0)';
                            }, 500);
                        }
                    });


                } catch (e) {
                    console.error("Error initializing carousel:", e);
                }
            } else {
                console.warn("Carousel element not found");
            }

            setTimeout(function() {
                const preloader = document.getElementById('preloader');
                if (preloader) {
                    preloader.style.opacity = '0';
                    setTimeout(function() {
                        preloader.style.display = 'none';
                    }, 300);
                }
            }, 500);
        });
    </script>

    <script src="<?php echo SITE_URL; ?>/public/js/main.js"></script>
    <script src="<?php echo SITE_URL; ?>/public/js/debug-cart.js"></script>
    <script src="<?php echo SITE_URL; ?>/public/js/date-validation.js"></script>
</body>

</html>