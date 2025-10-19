<div class="home-page">
    <!-- Link to Swiper.js CSS và JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <section class="hero py-0 mb-3">
        <div class="container">
            <div class="row align-items-stretch shadow-sm rounded overflow-hidden" style="min-height: 420px; background: linear-gradient(to right, #e6f0ff, #d0e6ff);">
                <div class="col-md-4 d-flex py-4 ps-4 pe-1">
                    <div class="destination-slider w-100">
                        <?php if (empty($socialMedia)): ?>
                            <div class="alert alert-info">Không có dữ liệu điểm đến</div>
                        <?php else: ?>
                            <div id="destinationCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                                <div class="carousel-inner h-100">
                                    <?php foreach ($socialMedia as $index => $image): ?>
                                        <div class="carousel-item h-100 <?php echo $index === 0 ? 'active' : ''; ?>">
                                            <img src="<?php echo $image; ?>"
                                                class="d-block w-100 h-100 object-fit-cover"
                                                alt="Social Media Image <?php echo $index + 1; ?>"
                                                onerror="this.src='<?php echo SITE_URL; ?>public\images\social-media\default.jpg';">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#destinationCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#destinationCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-8 d-flex p-4">
                    <div class="card-style-search-container w-100 border-0 shadow-none">
                        <div class="search-header mb-3">
                            <h3 class="fw-bold text-primary">Tìm kiếm trải nghiệm hoàn hảo</h3>
                            <p class="text-muted">Khám phá các điểm đến tuyệt vời cùng mức giá ưu đãi</p>
                        </div>

                        <form action="<?php echo SITE_URL . "/search"; ?>" method="GET" class="card-style-form">
                            <div class="search-grid">

                                <div class="search-card location">
                                    <label class="fw-medium">Điểm đến</label>
                                    <div class="input-container shadow-sm mb-0">
                                        <i class="bi bi-geo-alt-fill text-primary"></i>
                                        <input type="text" name="city" id="destinationInput" placeholder="Thành phố, khu vực"
                                            value="<?php echo $filters['city']; ?>" autocomplete="off">
                                        <div class="destination-suggestions" id="destinationSuggestions"></div>
                                    </div>
                                </div>

                                <div class="search-card price">
                                    <label class="fw-medium">Giá tối thiểu</label>
                                    <div class="input-container shadow-sm">
                                        <i class="bi bi-currency-dollar text-primary"></i>
                                        <input type="number" name="price_min" placeholder="500.000" min="0" step="100000"
                                            value="<?php echo $filters['price_min'] ?? ''; ?>">
                                    </div>
                                </div>

                                <div class="search-card price">
                                    <label class="fw-medium">Giá tối đa</label>
                                    <div class="input-container shadow-sm">
                                        <i class="bi bi-currency-dollar text-primary"></i>
                                        <input type="number" name="price_max" placeholder="5.000.000" min="0" step="100000"
                                            value="<?php echo $filters['price_max'] ?? ''; ?>">
                                    </div>
                                </div>

                                <div class="search-card guests mb-0">
                                    <label class="fw-medium">Số khách</label>
                                    <div class="input-container shadow-sm">
                                        <i class="bi bi-people-fill text-primary"></i>
                                        <select name="guests">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo $filters['guests'] == $i ? 'selected' : ''; ?>>
                                                    <?php echo $i; ?> khách
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="search-card date">
                                    <label class="fw-medium">Nhận phòng</label>
                                    <div class="input-container shadow-sm">
                                        <i class="bi bi-calendar-event-fill text-primary"></i>
                                        <input type="date" name="check_in" id="check_in" value="<?php echo $filters['check_in']; ?>">
                                    </div>
                                </div>

                                <div class="search-card date">
                                    <label class="fw-medium">Trả phòng</label>
                                    <div class="input-container shadow-sm">
                                        <i class="bi bi-calendar-event-fill text-primary"></i>
                                        <input type="date" name="check_out" id="check_out" value="<?php echo $filters['check_out']; ?>">
                                    </div>
                                </div>
                                <div class="search-card button ">
                                    <button type="submit" class="w-100 search-btn shadow-sm">
                                        <i class="bi bi-search"></i>
                                        Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Điểm đến Việt Nam -->
    <section class="py-5">
        <div class="container">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">Điểm đến nổi bật tại Việt Nam</h2>
                <a href="<?php echo SITE_URL; ?>/regions/<?php echo urlencode('Việt Nam'); ?>" class="btn btn-outline-primary btn-sm">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-4">
                <?php
                foreach ($vietnameseDestinations as $destination):
                ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="destination-card shadow-sm rounded overflow-hidden hover-scale">
                            <a href="<?php echo SITE_URL . '/search?city=' . urlencode($destination['name']); ?>" class="text-decoration-none d-block h-100">
                                <div class="destination-image position-relative h-100">
                                    <img src="<?php echo $destination['image']; ?>"
                                        alt="<?php echo $destination['name']; ?>"
                                        class="w-100 h-100 destination-img"
                                        onerror="this.src='<?php echo SITE_URL; ?>/public/images/destinations/default.jpg';">

                                    <div class="destination-overlay d-flex flex-column justify-content-end pb-2 ps-4 pe-4">
                                        <div class="text-overlay-container p-3 rounded" style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                            <div class="d-flex align-items-center">
                                                <h3 class="text-white fw-bold mb-2" style="font-size: 1.3rem;"><?php echo $destination['name']; ?></h3>

                                                <span class="ms-auto text-white">
                                                    <i class="bi bi-arrow-right-circle" style="font-size: 1.1rem;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ưu đãi-->
    <section class="hotel-promotions py-5">
        <div class="container">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">Ưu đãi hấp dẫn</h2>
                <a href="<?php echo SITE_URL; ?>/promotions" class="btn btn-outline-primary btn-sm">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            </div>

            <!-- Swiper -->
            <div class="swiper promotionsSwiper">
                <div class="swiper-wrapper">
                    <?php

                    foreach ($promotions as $promo):
                    ?>
                        <div class="swiper-slide">
                            <div class="promotion-card">
                                <img src="<?php echo SITE_URL . '/public/images/promotions/' . $promo['image']; ?>"
                                    alt="<?php echo $promo['title']; ?>"
                                    class="promotion-image"
                                    onerror="this.src='<?php echo SITE_URL; ?>/public/images/promotions/default.jpg';">
                                <span class="promotion-badge">
                                    <?php
                                    if ($promo['discount_type'] === 'percentage') {
                                        echo '-' . $promo['discount_value'] . '%';
                                    } elseif ($promo['discount_type'] === 'fixed') {
                                        echo '-' . number_format($promo['discount_value']) . 'đ';
                                    } else {
                                        echo $promo['discount_type'];
                                    }
                                    ?>
                                </span>
                                <a href="<?php echo SITE_URL; ?>/promotions/<?php echo $promo['id']; ?>" class="promotion-content">
                                    <div>
                                        <h3 class="promotion-title"><?php echo $promo['title']; ?></h3>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <?php if ($promo['end_date']): ?>
                                            <small class="text-danger">Hết hạn: <?php echo date('d/m/Y', strtotime($promo['end_date'])); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- Khách sạn nổi bật theo điểm đến -->
    <section class="featured-destinations-hotels mb-3">
        <div class="container">
            <div class="section-header d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title mb-0">Khách sạn nổi bật theo điểm đến</h2>
            </div>

            <!-- Tablist-->
            <ul class="nav nav-pills destination-tabs mb-4 gap-2" id="destinationTabs" role="tablist">
                <?php foreach ($featuredDestinationsWithHotels as $index => $destination): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>"
                            id="tab-<?php echo $destination['id']; ?>"
                            data-bs-toggle="tab"
                            data-bs-target="#hotels-<?php echo $destination['id']; ?>"
                            type="button"
                            role="tab"
                            aria-controls="hotels-<?php echo $destination['id']; ?>"
                            aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                            <?php echo $destination['name']; ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Tab content  -->
            <div class="tab-content" id="destinationTabContent">
                <?php foreach ($featuredDestinationsWithHotels as $index => $destination): ?>
                    <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>"
                        id="hotels-<?php echo $destination['id']; ?>"
                        role="tabpanel"
                        aria-labelledby="tab-<?php echo $destination['id']; ?>">

                        <?php if (empty($destination['hotels'])): ?>
                            <div class="alert alert-info">Không có khách sạn nào tại điểm đến này</div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($destination['hotels'] as $hotel): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card hotel-card border-0 shadow-sm h-100 animate__animated animate__fadeIn">
                                            <div class="hotel-image-container position-relative overflow-hidden" style="height: 160px;">
                                                <img src="<?php echo $hotel['image']; ?>" class="hotel-image w-100 h-100 object-fit-cover" alt="<?php echo $hotel['name']; ?>">
                                                <div class="hotel-badge position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-primary"><?php echo $hotel['avg_rating']; ?> <i class="bi bi-star-fill"></i></span>
                                                </div>
                                            </div>
                                            <a href="<?php echo SITE_URL . '/hotels/' . $hotel['id']; ?>" style="text-decoration: none;" class="hotel-info p-3">
                                                <h5 class="hotel-name fw-bold text-truncate"><?php echo $hotel['name']; ?></h5>
                                                <div class="hotel-location d-flex align-items-center text-muted mb-2">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    <span class="small text-truncate"><?php echo $hotel['address']; ?></span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                                                    <p class="price mb-0 fw-bold text-primary">Từ <?php echo number_format($hotel['min_price']); ?> VND</p>

                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Điểm đến nước ngoài -->
    <section class="py-5">
        <div class="container">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">Du lịch nước ngoài cùng Little Bone</h2>
            </div>

            <div class="row g-4">
                <?php
                foreach ($foreignDestinations as $destination):
                ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="destination-card shadow-sm rounded overflow-hidden hover-scale">
                            <a href="<?php echo SITE_URL . '/search?city=' . urlencode($destination['name']); ?>" class="text-decoration-none d-block h-100">
                                <div class="destination-image position-relative h-100">
                                    <img src="<?php echo $destination['image']; ?>"
                                        alt="<?php echo $destination['name']; ?>"
                                        class="w-100 h-100 destination-img"
                                        onerror="this.src='<?php echo SITE_URL; ?>/public/images/destinations/default.jpg';">

                                    <div class="destination-overlay d-flex flex-column justify-content-end pb-2 ps-4 pe-4">
                                        <div class="text-overlay-container p-3 rounded" style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                            <div class="d-flex align-items-center">
                                                <h3 class="text-white fw-bold mb-2" style="font-size: 1.3rem;"><?php echo $destination['name']; ?></h3>

                                                <span class="ms-auto text-white">
                                                    <i class="bi bi-arrow-right-circle" style="font-size: 1.1rem;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var destinationCarousel = document.querySelector('#destinationCarousel');
            if (destinationCarousel) {
                try {
                    var carousel = new bootstrap.Carousel(destinationCarousel, {
                        interval: 5000,
                        keyboard: true,
                        pause: 'hover',
                        wrap: true,
                        touch: true
                    });
                } catch (e) {
                    console.error("Error initializing carousel:", e);
                }
            }

            var swiperScript = document.createElement('script');
            swiperScript.src = 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js';
            document.body.appendChild(swiperScript);

            swiperScript.onload = function() {
                var swiper = new Swiper(".promotionsSwiper", {
                    effect: "coverflow",
                    grabCursor: true,
                    centeredSlides: true,
                    slidesPerView: "auto",
                    loop: true,
                    coverflowEffect: {
                        rotate: 0,
                        stretch: 0,
                        depth: 100,
                        modifier: 2,
                        slideShadows: true,
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    }
                });
            };

            document.querySelectorAll('.destination-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.querySelector('.destination-image img').style.transform = 'scale(1.05)';
                    this.querySelector('.destination-image img').style.transition = 'transform 0.5s ease';
                });

                card.addEventListener('mouseleave', function() {
                    this.querySelector('.destination-image img').style.transform = 'scale(1)';
                });
            });

            document.querySelectorAll('.hotel-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.querySelector('.hotel-image').style.transform = 'scale(1.05)';
                    this.querySelector('.hotel-image').style.transition = 'transform 0.5s ease';
                });

                card.addEventListener('mouseleave', function() {
                    this.querySelector('.hotel-image').style.transform = 'scale(1)';
                });
            });

            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');

            const today = new Date().toISOString().split('T')[0];

            checkInInput.min = today;

            function updateCheckOutMin() {
                if (checkInInput.value) {
                    const checkInDate = new Date(checkInInput.value);
                    checkInDate.setDate(checkInDate.getDate() + 1);
                    checkOutInput.min = checkInDate.toISOString().split('T')[0];
                } else {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    checkOutInput.min = tomorrow.toISOString().split('T')[0];
                }
            }

            checkInInput.addEventListener('change', function() {
                updateCheckOutMin();

                if (checkOutInput.value && checkOutInput.value <= this.value) {
                    const nextDay = new Date(this.value);
                    nextDay.setDate(nextDay.getDate() + 1);
                    checkOutInput.value = nextDay.toISOString().split('T')[0];
                }
            });

            checkOutInput.addEventListener('change', function() {
                if (checkInInput.value && this.value <= checkInInput.value) {
                    const nextDay = new Date(checkInInput.value);
                    nextDay.setDate(nextDay.getDate() + 1);
                    this.value = nextDay.toISOString().split('T')[0];

                    alert('Ngày trả phòng phải sau ngày nhận phòng. Đã tự động điều chỉnh.');
                }
            });

            updateCheckOutMin();

            if (checkInInput.value && !checkOutInput.value) {
                const nextDay = new Date(checkInInput.value);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutInput.value = nextDay.toISOString().split('T')[0];
            }
        });
    </script>
</div>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>/public/css/home.css">