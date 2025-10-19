<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="text-primary">Ưu đãi đặc biệt</h1>
            <p class="lead text-muted">Khám phá các ưu đãi độc quyền và tiết kiệm chi phí cho kỳ nghỉ của bạn</p>
        </div>
    </div>

    <!-- Banner cho ưu đãi nổi bật -->
    <?php if (!empty($promotions)): ?>
        <?php $featuredPromo = $promotions[0];
        ?>
        <div class="featured-promotion mb-5">
            <div class="card shadow border-0 overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-6 position-relative">
                        <img src="<?php echo SITE_URL; ?>/public/images/promotions/<?php echo $featuredPromo['image']; ?>"
                            class="w-100 h-100 object-fit-cover"
                            alt="<?php echo htmlspecialchars($featuredPromo['title']); ?>"
                            onerror="this.src='<?php echo SITE_URL; ?>/public/images/promotions/default.jpg';">
                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge bg-danger fs-5 rounded-pill px-3 py-2 shadow-sm">
                                <?php if ($featuredPromo['discount_type'] === 'percentage'): ?>
                                    <i class="bi bi-percent me-1"></i> Giảm <?php echo $featuredPromo['discount_value']; ?>%
                                <?php else: ?>
                                    <i class="bi bi-currency-exchange me-1"></i> Giảm <?php echo number_format($featuredPromo['discount_value']); ?> VNĐ
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-body p-4 h-100 d-flex flex-column">
                            <div class="mb-4">
                                <h2 class="card-title mb-2"><?php echo htmlspecialchars($featuredPromo['title']); ?></h2>
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-tag-fill me-1"></i> Mã: <strong><?php echo $featuredPromo['code']; ?></strong>
                                    </span>
                                    <?php if (isset($featuredPromo['need_verification']) && $featuredPromo['need_verification'] == 1): ?>
                                        <span class="badge bg-info rounded-pill px-3 py-2 ms-2">
                                            <i class="bi bi-person-badge me-1"></i> Xác minh khi nhận phòng
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success rounded-pill px-3 py-2 ms-2">
                                            <i class="bi bi-lightning me-1"></i> Áp dụng ngay
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="card-text"><?php echo htmlspecialchars($featuredPromo['description']); ?></p>
                            </div>
                            <div class="mt-auto">
                                <div class="d-flex align-items-center text-muted mb-3">
                                    <i class="bi bi-calendar3 me-2"></i>
                                    <span>Hiệu lực từ: <?php echo date('d/m/Y', strtotime($featuredPromo['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($featuredPromo['end_date'])); ?></span>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/promotions/<?php echo $featuredPromo['id']; ?>" class="btn btn-primary">
                                    <i class="bi bi-info-circle me-1"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Danh sách ưu đãi nhận ngay -->
    <?php if (!empty($groupedPromotions['instant'])): ?>
        <div class="mb-5 promotion-section">
            <h2 class="text-primary mb-4"><i class="bi bi-lightning-charge me-2"></i>Nhận ưu đãi ngay</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                $totalInstant = count($groupedPromotions['instant']);
                foreach ($groupedPromotions['instant'] as $index => $promo):
                    if ($promo['id'] === $featuredPromo['id']) continue;
                    $hiddenClass = $index >= 4 ? 'promo-hidden instant-hidden' : '';
                ?>
                    <div class="col <?php echo $hiddenClass; ?>">
                        <div class="card h-100 promotion-card shadow-sm border-0">
                            <div class="position-relative">
                                <img src="<?php echo SITE_URL; ?>/public/images/promotions/<?php echo $promo['image']; ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($promo['title']); ?>"
                                    style="height: 180px; object-fit: cover;"
                                    onerror="this.src='<?php echo SITE_URL; ?>/public/images/promotions/default.jpg';">
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge <?php echo $promo['discount_type'] === 'percentage' ? 'bg-primary' : 'bg-success'; ?> rounded-pill fs-6">
                                        <?php if ($promo['discount_type'] === 'percentage'): ?>
                                            Giảm <?php echo $promo['discount_value']; ?>%
                                        <?php else: ?>
                                            Giảm <?php echo number_format($promo['discount_value']); ?> VNĐ
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-2"><?php echo htmlspecialchars($promo['title']); ?></h5>
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark">
                                        Mã: <strong><?php echo $promo['code']; ?></strong>
                                    </span>
                                </div>
                                <p class="card-text small" style="height: 60px; overflow: hidden;">
                                    <?php echo htmlspecialchars(substr($promo['description'], 0, 120)) . (strlen($promo['description']) > 120 ? '...' : ''); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <div class="d-flex align-items-center text-muted mb-3 small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <span>Đến: <?php echo date('d/m/Y', strtotime($promo['end_date'])); ?></span>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/promotions/<?php echo $promo['id']; ?>" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-info-circle me-1"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ($totalInstant > 3): ?>
                <div class="text-center mt-4">
                    <button class="btn btn-outline-primary show-more-btn" data-target="instant">
                        <i class="bi bi-plus-circle me-1"></i> Xem thêm <span class="remaining-count"><?php echo $totalInstant - 3; ?></span> ưu đãi
                    </button>
                    <button class="btn btn-outline-secondary show-less-btn" data-target="instant" style="display: none;">
                        <i class="bi bi-dash-circle me-1"></i> Thu gọn
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Danh sách ưu đãi khi nhận phòng (cần xác minh) -->
    <?php if (!empty($groupedPromotions['verification'])): ?>
        <div class="mb-5 promotion-section">
            <h2 class="text-primary mb-4"><i class="bi bi-person-badge me-2"></i>Ưu đãi trực tiếp khi nhận phòng</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php
                $totalVerification = count($groupedPromotions['verification']);
                foreach ($groupedPromotions['verification'] as $index => $promo):
                    if ($promo['id'] === $featuredPromo['id']) continue;
                    $hiddenClass = $index >= 3 ? 'promo-hidden verification-hidden' : '';
                ?>
                    <div class="col <?php echo $hiddenClass; ?>">
                        <div class="card h-100 promotion-card shadow-sm border-0">
                            <div class="position-relative">
                                <img src="<?php echo SITE_URL; ?>/public/images/promotions/<?php echo $promo['image']; ?>"
                                    class="card-img-top"
                                    alt="<?php echo htmlspecialchars($promo['title']); ?>"
                                    style="height: 180px; object-fit: cover;"
                                    onerror="this.src='<?php echo SITE_URL; ?>/public/images/promotions/default.jpg';">
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge <?php echo $promo['discount_type'] === 'percentage' ? 'bg-primary' : 'bg-success'; ?> rounded-pill fs-6">
                                        <?php if ($promo['discount_type'] === 'percentage'): ?>
                                            Giảm <?php echo $promo['discount_value']; ?>%
                                        <?php else: ?>
                                            Giảm <?php echo number_format($promo['discount_value']); ?> VNĐ
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-2"><?php echo htmlspecialchars($promo['title']); ?></h5>
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark">
                                        Mã: <strong><?php echo $promo['code']; ?></strong>
                                    </span>
                                </div>
                                <p class="card-text small" style="height: 60px; overflow: hidden;">
                                    <?php echo htmlspecialchars(substr($promo['description'], 0, 120)) . (strlen($promo['description']) > 120 ? '...' : ''); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <div class="d-flex align-items-center text-muted mb-3 small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <span>Đến: <?php echo date('d/m/Y', strtotime($promo['end_date'])); ?></span>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/promotions/<?php echo $promo['id']; ?>" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-info-circle me-1"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ($totalVerification > 3): ?>
                <div class="text-center mt-4">
                    <button class="btn btn-outline-primary show-more-btn" data-target="verification">
                        <i class="bi bi-plus-circle me-1"></i> Xem thêm <span class="remaining-count"><?php echo $totalVerification - 3; ?></span> ưu đãi
                    </button>
                    <button class="btn btn-outline-secondary show-less-btn" data-target="verification" style="display: none;">
                        <i class="bi bi-dash-circle me-1"></i> Thu gọn
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($promotions)): ?>
        <div class="alert alert-info text-center p-5">
            <i class="bi bi-tag-fill fs-1 d-block mb-3"></i>
            <h3>Hiện không có ưu đãi nào</h3>
            <p>Vui lòng quay lại sau để cập nhật các ưu đãi mới nhất</p>
            <a href="<?php echo SITE_URL; ?>/hotels" class="btn btn-primary mt-3">
                <i class="bi bi-building me-1"></i> Xem danh sách khách sạn
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý nút Xem thêm
        const showMoreBtns = document.querySelectorAll('.show-more-btn');
        showMoreBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                const hiddenItems = document.querySelectorAll('.' + target + '-hidden');

                hiddenItems.forEach(item => {
                    item.classList.remove(target + '-hidden');
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.style.opacity = '1';
                    }, 10);
                });

                this.style.display = 'none';

                const showLessBtn = document.querySelector('.show-less-btn[data-target="' + target + '"]');
                if (showLessBtn) {
                    showLessBtn.style.display = 'inline-block';
                }
            });
        });

        // Xử lý nút Thu gọn
        const showLessBtns = document.querySelectorAll('.show-less-btn');
        showLessBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                const itemsToHide = document.querySelectorAll('.promo-hidden:not(.' + target + '-hidden)');

                itemsToHide.forEach(item => {
                    if (item.classList.contains('promo-hidden')) {
                        item.classList.add(target + '-hidden');
                    }
                });

                this.style.display = 'none';

                const showMoreBtn = document.querySelector('.show-more-btn[data-target="' + target + '"]');
                if (showMoreBtn) {
                    showMoreBtn.style.display = 'inline-block';
                }

                const sectionElement = this.closest('.promotion-section');
                if (sectionElement) {
                    sectionElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>

<style>
    .promotion-card {
        transition: all 0.3s ease;
        overflow: hidden;
        border-radius: 12px;
    }

    .promotion-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .promotion-card .card-img-top {
        transition: transform 0.5s ease;
    }

    .promotion-card:hover .card-img-top {
        transform: scale(1.05);
    }

    .featured-promotion .card {
        border-radius: 16px;
    }

    .featured-promotion .card-title {
        font-size: 1.8rem;
    }

    /* Animation cho badges */
    .badge {
        transition: all 0.3s ease;
    }

    .promotion-card:hover .badge {
        transform: scale(1.05);
    }

    /* Kẻ viền nhẹ phân cách các phần */
    .promotion-section {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 2rem;
    }

    /* Style cho nút xem thêm/thu gọn */
    .show-more-btn,
    .show-less-btn {
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .show-more-btn:hover,
    .show-less-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Ẩn các ưu đãi sau ưu đãi thứ 3 */
    .promo-hidden {
        display: block;
        transition: opacity 0.5s ease;
    }

    .instant-hidden,
    .verification-hidden {
        display: none;
    }
</style>