<div class="container py-5">
    <div class="mb-4">
        <div class="card promotion-detail-card shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-9">
                        <h1 class="promotion-title"><?php echo $promotion['title']; ?></h1>
                        <div class="badge-wrapper mb-2">
                            <?php if ($promotion['discount_type'] == 'percentage'): ?>
                                <span class="badge bg-danger">Giảm <?php echo $promotion['discount_value']; ?>%</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Giảm <?php echo number_format($promotion['discount_value'], 0, ',', '.'); ?> VNĐ</span>
                            <?php endif; ?>
                            <span class="badge bg-secondary ms-2">Mã: <?php echo $promotion['code']; ?></span>
                        </div>
                        <p class="card-text"><?php echo nl2br($promotion['description']); ?></p>
                        <div class="promotion-dates text-muted">
                            <i class="bi bi-calendar-check"></i>
                            Có hiệu lực từ <?php echo date('d/m/Y', strtotime($promotion['start_date'])); ?>
                            đến <?php echo date('d/m/Y', strtotime($promotion['end_date'])); ?>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <?php if (!empty($promotion['image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/public/images/promotions/<?php echo $promotion['image']; ?>"
                                alt="<?php echo $promotion['title']; ?>" class="img-fluid rounded">
                        <?php else: ?>
                            <img src="<?php echo SITE_URL; ?>/public/images/promotions/default.jpg"
                                alt="Default promotion image" class="img-fluid rounded">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mb-3">Khách sạn áp dụng ưu đãi</h2>

    <?php if (empty($hotels)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Chưa có khách sạn nào áp dụng ưu đãi này.
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

                            <div class="main-image-container">
                                <?php
                                // Kiểm tra tồn tại của file main.jpg trong thư mục
                                $mainImagePath = $hotelImagesPath . 'main.jpg';
                                // URL hiển thị cho ảnh chính
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
                                    // Danh sách các hình ảnh thumbnail
                                    $imageFiles = [];

                                    // Kiểm tra thư mục hình ảnh khách sạn có tồn tại không
                                    if (is_dir($hotelImagesPath)) {
                                        // Quét tất cả file jpg, png trong thư mục
                                        $files = glob($hotelImagesPath . '*.{jpg,jpeg,png}', GLOB_BRACE);
                                        if (!empty($files)) {
                                            foreach ($files as $file) {
                                                // Tạo URL từ đường dẫn vật lý
                                                $imageFiles[] = $hotelImageUrl . basename($file);
                                            }
                                        }
                                    }

                                    // Nếu không có hình ảnh, sử dụng ảnh mặc định
                                    if (empty($imageFiles)) {
                                        $imageFiles = [SITE_URL . '/public/images/hotels/default.jpg'];
                                    }

                                    // Giới hạn tối đa 4 ảnh thumbnail
                                    $imageFiles = array_slice($imageFiles, 0, 4);

                                    // Hiển thị các thumbnail
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

                    <!-- Right column with hotel info -->
                    <div class="col-md-8">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title"><?php echo $hotel['name']; ?></h5>
                                    <p class="card-text"><i class="bi bi-geo-alt"></i> <?php echo $hotel['address']; ?></p>
                                </div>
                                <div class="hotel-rating">
                                    <?php
                                    // Get hotel rating from API or use a default
                                    $avgRating = isset($hotel['avg_rating']) ? $hotel['avg_rating'] : 0;
                                    for ($i = 0; $i < $avgRating; $i++):
                                    ?>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <!-- Brief description -->
                            <p class="card-text hotel-description">
                                <?php echo isset($hotel['description']) ?
                                    (mb_substr($hotel['description'], 0, 120) . (mb_strlen($hotel['description']) > 120 ? '...' : '')) :
                                    'Không có mô tả'; ?>
                            </p>

                            <!-- Limited amenities preview -->
                            <?php
                            // Get amenities from hotelModel if available
                            $amenities = isset($hotel['amenities']) ? $hotel['amenities'] : [];
                            error_log(print_r($amenities, true)); // Debugging line
                            if (!empty($amenities)):
                            ?>
                                <div class="amenities-preview mb-3">
                                    <?php
                                    $maxAmenities = 5;
                                    $count = 0;
                                    foreach ($amenities as $amenity):
                                        if ($count >= $maxAmenities) break;
                                    ?>
                                        <span class="badge bg-light text-dark me-2 mb-1">
                                            <i class="bi <?php echo $amenity['icon'] ?? 'bi-check'; ?>"></i>
                                            <?php echo $amenity['name']; ?>
                                        </span>
                                    <?php
                                        $count++;
                                    endforeach;

                                    if (count($amenities) > $maxAmenities): ?>
                                        <span class="badge bg-light text-dark me-2 mb-1">
                                            +<?php echo (count($amenities) - $maxAmenities); ?> tiện ích khác
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
                                    <div class="promotions-list d-flex align-items-center flex-wrap">
                                        <?php
                                        $count = 0;
                                        foreach ($hotel['promotions'] as $promo):
                                            if ($count >= 4) break; // Hiển thị tối đa 3 ưu đãi
                                            $count++;
                                        ?>
                                            <div class="promotion-item me-2 mb-2">
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="bi <?php echo isset($promo['icon']) ? $promo['icon'] : 'bi-tag'; ?>"></i>
                                                </span>
                                                <span class="promo-title ms-1">
                                                    <?php echo $promo['title']; ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>

                                        <?php
                                        $totalPromos = count($hotel['promotions']);
                                        if ($totalPromos > 3):
                                        ?>
                                            <button class="btn btn-sm btn-outline-success btn-sm show-more-promos"
                                                data-bs-toggle="modal"
                                                data-bs-target="#promotionsModal"
                                                data-hotel-id="<?php echo $hotel['id']; ?>"
                                                data-hotel-name="<?php echo htmlspecialchars($hotel['name']); ?>">
                                                <i class="bi bi-plus-circle"></i>
                                                +<?php echo $totalPromos - 3; ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Price and action buttons -->
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
                                        <a href="<?php echo SITE_URL . '/hotels/' . $hotel['id']; ?>" class="btn btn-primary">
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

<!-- Load CSS và JavaScript riêng biệt -->
<link rel="stylesheet" href="<?php echo SITE_URL; ?>/public/css/city_hotels.css?v=<?php echo time(); ?>">

<style>
    /* Cố định kích thước ảnh chính */
    .hotel-image-wrapper {
        position: relative;
        overflow: hidden;
    }

    .main-image-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 66.67%;
        /* Tỉ lệ 3:2 */
        overflow: hidden;
    }

    .hotel-main-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .hotel-main-image:hover {
        transform: scale(1.05);
    }

    /* Cố định kích thước thumbnail */
    .thumbnail-gallery .thumbnail {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 75%;
        /* Tỉ lệ 4:3 cho thumbnail */
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .thumbnail-gallery .thumbnail.active {
        border-color: #0d6efd;
    }

    .thumbnail-gallery .thumbnail img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<script>
    // Định nghĩa biến toàn cục cho JavaScript
    const SITE_URL = "<?php echo SITE_URL; ?>";

    // Hàm thay đổi ảnh chính khi click vào thumbnail
    function changeMainImage(thumbnail, hotelId, newImageSrc) {
        // Xóa active class từ tất cả thumbnails
        const thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(item => item.classList.remove('active'));

        // Thêm active class vào thumbnail được chọn
        thumbnail.classList.add('active');

        // Cập nhật ảnh chính
        const mainImage = document.getElementById('main-image-' + hotelId);
        if (mainImage) {
            mainImage.src = newImageSrc;
        }
    }

    <?php if (!isset($_SESSION['user_id'])): ?>
        // Ghi đè hàm toggleFavorite nếu chưa đăng nhập
        function toggleFavorite(button, hotelId) {
            window.location.href = '<?php echo SITE_URL; ?>/login';
            return;
        }
    <?php endif; ?>
</script>

<!-- Modal hiển thị tất cả khuyến mãi -->
<div class="modal fade" id="promotionsModal" tabindex="-1" aria-labelledby="promotionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="promotionsModalLabel">Tất cả ưu đãi của <span id="hotelNameInModal"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="promotionsContainer" class="row g-3">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Thêm code xử lý cho modal promotions
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý sự kiện click vào nút "Xem thêm" cho promotions
        document.querySelectorAll('.view-more-promos').forEach(button => {
            button.addEventListener('click', function() {
                const hotelId = this.getAttribute('data-hotel-id');
                const hotelName = this.getAttribute('data-hotel-name');

                // Cập nhật tên khách sạn trong modal
                document.getElementById('hotelNameInModal').textContent = hotelName;

                // Hiển thị loading
                document.getElementById('promotionsContainer').innerHTML = `
                    <div class="text-center w-100">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                `;

                // Lấy danh sách khuyến mãi từ API
                fetch(`${SITE_URL}/api/hotels/${hotelId}/promotions`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.promotions.length > 0) {
                            let promotionsHTML = '';

                            data.promotions.forEach(promo => {
                                promotionsHTML += `
                                    <div class="col-md-6">
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <h6 class="card-title">${promo.title}</h6>
                                                <div class="badge ${promo.discount_type === 'percentage' ? 'bg-danger' : 'bg-primary'} mb-2">
                                                    ${promo.discount_type === 'percentage' ? 
                                                        `Giảm ${promo.discount_value}%` : 
                                                        `Giảm ${new Intl.NumberFormat('vi-VN').format(promo.discount_value)}đ`}
                                                </div>
                                                <p class="card-text small">${promo.description || 'Không có mô tả chi tiết'}</p>
                                                <div class="text-muted small">
                                                    <i class="bi bi-calendar-event"></i> Hết hạn: 
                                                    ${new Date(promo.end_date).toLocaleDateString('vi-VN')}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            document.getElementById('promotionsContainer').innerHTML = promotionsHTML;
                        } else {
                            document.getElementById('promotionsContainer').innerHTML = `
                                <div class="col-12 text-center">
                                    <div class="alert alert-info">
                                        Không có ưu đãi nào hiện có cho khách sạn này.
                                    </div>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching promotions:', error);
                        document.getElementById('promotionsContainer').innerHTML = `
                            <div class="col-12 text-center">
                                <div class="alert alert-danger">
                                    Đã xảy ra lỗi khi tải thông tin ưu đãi. Vui lòng thử lại sau.
                                </div>
                            </div>
                        `;
                    });
            });
        });
    });

    // ...existing code...
</script>