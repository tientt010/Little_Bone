<div class="container mt-4">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="city-title">Kết quả tìm kiếm</h1>
            </div>
            <!-- Sắp xếp -->
            <div class="col-md-4">
                <form id="sortForm" class="row g-2">
                    <div class="col-6">
                        <label class="form-label small">Sắp xếp theo</label>
                        <select class="form-select form-select-sm" id="sort_by" name="sort_by">
                            <option value="avg_rating" <?php echo ($sort['field'] == 'avg_rating') ? 'selected' : ''; ?>>Đánh giá</option>
                            <option value="min_price" <?php echo ($sort['field'] == 'min_price') ? 'selected' : ''; ?>>Giá</option>
                            <option value="name" <?php echo ($sort['field'] == 'name') ? 'selected' : ''; ?>>Tên</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Thứ tự</label>
                        <select class="form-select form-select-sm" id="sort_dir" name="sort_dir">
                            <option value="DESC" <?php echo ($sort['direction'] == 'DESC') ? 'selected' : ''; ?>>Giảm dần</option>
                            <option value="ASC" <?php echo ($sort['direction'] == 'ASC') ? 'selected' : ''; ?>>Tăng dần</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bộ lọc -->
        <div class="col-md-3 mb-4 sidebar-container">
            <div class="card sticky-filter shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo SITE_URL; ?>/search" method="GET" id="filterForm">
                        <!-- Hidden sort fields -->
                        <input type="hidden" name="sort_by" value="<?php echo $sort['field']; ?>">
                        <input type="hidden" name="sort_dir" value="<?php echo $sort['direction']; ?>">
                        <input type="hidden" name="page" value="1" id="page_input">

                        <!-- Điểm đến -->
                        <div class="mb-3">
                            <label>Điểm đến</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <select class="form-select" name="city">
                                    <option value="">Tất cả điểm đến</option>
                                    <?php foreach ($destinations as $destination): ?>
                                        <option value="<?php echo $destination['name']; ?>"
                                            <?php echo ($filters['city'] == $destination['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($destination['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Check-in date -->
                        <div class="mb-3">
                            <label>Nhận phòng</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                <input type="date" class="form-control" name="check_in" id="search_check_in" value="<?php echo $filters['check_in']; ?>">
                            </div>
                        </div>

                        <!-- Check-out date -->
                        <div class="mb-3">
                            <label>Trả phòng</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                <input type="date" class="form-control" name="check_out" id="search_check_out" value="<?php echo $filters['check_out']; ?>">
                            </div>
                        </div>

                        <!-- Khách -->
                        <div class="mb-3">
                            <label>Số khách</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-people"></i></span>
                                <select class="form-select" name="guests">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($filters['guests'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?> khách</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Star rating -->
                        <div class="mb-3">
                            <label>Đánh giá</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-star"></i></span>
                                <select class="form-select" name="avg_rating">
                                    <option value="">Tất cả</option>
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($filters['avg_rating'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?> sao</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Khoảng giá -->
                        <div class="mb-3">
                            <label>Giá tối thiểu</label>
                            <div class="input-group">
                                <span class="input-group-text">VNĐ</span>
                                <input type="number" class="form-control" name="price_min" value="<?php echo $filters['price_min'] ?? ''; ?>" placeholder="500.000">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Giá tối đa</label>
                            <div class="input-group">
                                <span class="input-group-text">VNĐ</span>
                                <input type="number" class="form-control" name="price_max" value="<?php echo $filters['price_max'] ?? ''; ?>" placeholder="5.000.000">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Áp dụng
                        </button>

                        <a href="<?php echo SITE_URL; ?>/search" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-x-circle"></i> Đặt lại
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách khách sạn -->
        <div class="col-md-9">
            <?php if (empty($hotels)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Không tìm thấy khách sạn phù hợp với điều kiện tìm kiếm.
                </div>
            <?php else: ?>
                <?php foreach ($hotels as $hotel): ?>
                    <div class="card mb-4 hotel-card shadow-sm">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <div class="hotel-image-wrapper">
                                    <?php
                                    $hotelImagesPath = ROOT_PATH . '/public/images/hotels/' . $hotel['id'] . '/';
                                    $hotelImageUrl = SITE_URL . '/public/images/hotels/' . $hotel['id'] . '/';
                                    ?>

                                    <!-- Hiển thị ảnh chính -->
                                    <div class="main-image-container">
                                        <?php
                                        $mainImagePath = $hotelImagesPath . 'main.jpg';
                                        $mainImageUrl = file_exists($mainImagePath) ?
                                            $hotelImageUrl . 'main.jpg' :
                                            SITE_URL . '/public/images/hotels/default.jpg';
                                        ?>
                                        <img id="main-image-<?php echo $hotel['id']; ?>"
                                            src="<?php echo $mainImageUrl; ?>"
                                            class="img-fluid rounded-top hotel-main-image"
                                            alt="<?php echo $hotel['name']; ?>">
                                    </div>

                                    <!-- Hiển thị thumbnails -->
                                    <div class="thumbnail-gallery">
                                        <div class="row g-1 mt-1">
                                            <?php
                                            $imageFiles = [];
                                            if (is_dir($hotelImagesPath)) {
                                                $files = glob($hotelImagesPath . '*.{jpg,jpeg,png}', GLOB_BRACE);
                                                if (!empty($files)) {
                                                    foreach ($files as $file) {
                                                        $imageFiles[] = $hotelImageUrl . basename($file);
                                                    }
                                                }
                                            }
                                            // Nếu không có hình ảnh, sử dụng ảnh mặc định
                                            if (empty($imageFiles)) {
                                                $imageFiles = [SITE_URL . '/public/images/hotels/default.jpg'];
                                            }

                                            $imageFiles = array_slice($imageFiles, 0, 4);
                                            foreach ($imageFiles as $index => $imgSrc):
                                            ?>
                                                <div class="col-3">
                                                    <div class="thumbnail <?php echo ($index === 0) ? 'active' : ''; ?>"
                                                        onclick="changeMainImage(this, '<?php echo $hotel['id']; ?>', '<?php echo $imgSrc; ?>')">
                                                        <img src="<?php echo $imgSrc; ?>" class="img-fluid" alt="">
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title"><?php echo $hotel['name']; ?></h5>
                                            <p class="card-text"><i class="bi bi-geo-alt"></i> <?php echo $hotel['address']; ?></p>
                                        </div>
                                        <div class="hotel-rating">
                                            <?php for ($i = 0; $i < $hotel['avg_rating']; $i++): ?>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <p class="card-text hotel-description">
                                        <?php echo mb_substr($hotel['description'], 0, 120) . (mb_strlen($hotel['description']) > 120 ? '...' : ''); ?>
                                    </p>

                                    <?php if (isset($hotel['amenities']) && !empty($hotel['amenities'])): ?>
                                        <div class="amenities-preview mb-3">
                                            <?php
                                            $maxAmenities = 4;
                                            $count = 0;
                                            foreach ($hotel['amenities'] as $amenity):
                                                if ($count >= $maxAmenities) break;
                                            ?>
                                                <span class="badge bg-light text-dark me-2 mb-1">
                                                    <i class="bi <?php echo $amenity['icon'] ?? 'bi-check'; ?>"></i>
                                                    <?php echo $amenity['name']; ?>
                                                </span>
                                            <?php
                                                $count++;
                                            endforeach;

                                            if (count($hotel['amenities']) > $maxAmenities): ?>
                                                <span class="badge bg-light text-dark me-2 mb-1">
                                                    +<?php echo (count($hotel['amenities']) - $maxAmenities); ?> tiện ích khác
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Hiển thị khuyến mãi -->
                                    <?php if (isset($hotel['promotions']) && !empty($hotel['promotions'])): ?>
                                        <div class="promotions-section mb-3">
                                            <h6 class="text-success mb-2">
                                                <i class="bi bi-tags-fill"></i> Ưu đãi đặc biệt:
                                            </h6>
                                            <div class="promotions-list d-flex align-items-center">
                                                <div class="row w-100">
                                                    <?php
                                                    $counter = 0;
                                                    foreach ($hotel['promotions'] as $promo):
                                                        if ($counter >= 3) break;
                                                    ?>
                                                        <div class="col-4">
                                                            <div class="promotion-item d-flex align-items-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                                                <span class="badge bg-success rounded-pill">
                                                                    <i class="bi <?php echo $promo['icon'] ?: 'bi-tag'; ?>"></i>
                                                                </span>
                                                                <span class="promo-title ms-1 text-truncate">
                                                                    <?php echo $promo['title']; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    <?php
                                                        $counter++;
                                                    endforeach;

                                                    if ($hotel['total_promotions'] > 3):
                                                    ?>
                                                        <div class="col-auto ms-auto">
                                                            <button class="btn btn-sm btn-outline-success show-more-promos"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#promotionsModal"
                                                                data-hotel-id="<?php echo $hotel['id']; ?>"
                                                                data-hotel-name="<?php echo htmlspecialchars($hotel['name']); ?>">
                                                                <i class="bi bi-plus-circle"></i>
                                                                +<?php echo $hotel['total_promotions'] - 3; ?>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row align-items-end mt-3">
                                        <div class="col-md-6">
                                            <?php if (isset($hotel['min_price']) && $hotel['min_price'] > 0): ?>
                                                <div class="price">
                                                    <span class="text-muted">Chỉ từ</span>
                                                    <span class="price-value"><?php echo number_format($hotel['min_price'], 0, ',', '.'); ?> VNĐ</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                            <div class="btn-group">
                                                <?php if (isset($_SESSION['user_id'])): ?>
                                                    <button class="btn btn-outline-secondary favorite-btn me-2 <?php echo isset($favorites[$hotel['id']]) ? 'active' : ''; ?>"
                                                        onclick="toggleFavorite(this, <?php echo $hotel['id']; ?>)">
                                                        <i class="bi <?php echo isset($favorites[$hotel['id']]) ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <a href="<?php echo SITE_URL . '/hotels/' . $hotel['id'] .
                                                                '?check_in=' . urlencode($filters['check_in']) .
                                                                '&check_out=' . urlencode($filters['check_out']) .
                                                                '&guests=' . urlencode($filters['guests']); ?>" class="btn btn-primary">
                                                    <i class="bi bi-info-circle"></i> Chi tiết
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal hiển thị tất cả khuyến mãi -->
<div class="modal fade" id="promotionsModal" tabindex="-1" aria-labelledby="promotionsModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="promotionsModalLabel">Tất cả ưu đãi của <span id="hotelNameInModal"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="promotionsContainer" class="row">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Đóng</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo SITE_URL; ?>/public/css/searchview.css?v=<?php echo time(); ?>">
<script>
    // Định nghĩa biến toàn cục cho JavaScript
    const SITE_URL = "<?php echo SITE_URL; ?>";
    <?php if (!isset($_SESSION['user_id'])): ?>

        function toggleFavorite(button, hotelId) {
            window.location.href = '<?php echo SITE_URL; ?>/login';
            return;
        }
    <?php endif; ?>
</script>
<script src="<?php echo SITE_URL; ?>/public/js/search.js?v=<?php echo time(); ?>"></script>