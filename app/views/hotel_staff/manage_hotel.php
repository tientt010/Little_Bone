<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center">
                <i class="bi bi-building me-2"></i> <?php echo htmlspecialchars($hotel['name']); ?>
            </h1>
        </div>
        <div class="col-auto">
            <div class="status-toggle-wrapper">
                <div class="form-check form-switch">
                    <input class="form-check-input status-toggle-input" type="checkbox" id="statusToggle"
                        data-hotel-id="<?php echo $hotel['id']; ?>"
                        <?php echo $hotel['status'] == 'active' ? 'checked' : ''; ?>>
                    <label class="form-check-label status-label" for="statusToggle">
                        <span class="status-text"><?php echo $hotel['status'] == 'active' ? 'Đang hoạt động' : 'Ngừng hoạt động'; ?></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin cơ bản và mô tả -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin & Mô tả khách sạn</h5>
        </div>
        <div class="card-body">
            <form id="hotelBasicForm">
                <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">

                <div class="row">
                    <div class="col-lg-6">
                        <h6 class="mb-3 border-bottom pb-2"><i class="bi bi-buildings me-2"></i>Thông tin cơ bản</h6>

                        <div class="mb-3">
                            <label for="name" class="form-label">Tên khách sạn <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required
                                value="<?php echo htmlspecialchars($hotel['name']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="2" required><?php echo htmlspecialchars($hotel['address']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destination_id" class="form-label">Điểm đến</label>
                                    <select class="form-select" id="destination_id" name="destination_id">
                                        <option value="">-- Chọn điểm đến --</option>
                                        <?php foreach ($destinations as $destination): ?>
                                            <option value="<?php echo $destination['id']; ?>"
                                                <?php echo ($hotel['destination_id'] == $destination['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($destination['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tổng số phòng</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?php echo $hotel['total_room'] ?? 0; ?>" readonly>
                                        <a href="<?php echo SITE_URL; ?>/hotel_staff/manage_rooms/<?php echo $hotel['id']; ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-plus-circle"></i> Quản lý phòng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mô tả và đánh giá -->
                    <div class="col-lg-6">
                        <h6 class="mb-3 border-bottom pb-2"><i class="bi bi-card-text me-2"></i>Mô tả & Đánh giá</h6>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả khách sạn</label>
                            <textarea class="form-control" id="description" name="description" rows="6"><?php echo htmlspecialchars($hotel['description']); ?></textarea>
                            <div class="form-text text-muted">
                                Mô tả chi tiết về khách sạn, dịch vụ và tiện ích cung cấp.
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Điểm đánh giá trung bình</label>
                            <div class="d-flex align-items-center">
                                <div class="rating-stars me-2">
                                    <?php
                                    $avgRating = round($hotel['avg_rating'] ?? 0, 1);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= floor($avgRating)) {
                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                        } elseif ($i - 0.5 <= $avgRating) {
                                            echo '<i class="bi bi-star-half text-warning"></i>';
                                        } else {
                                            echo '<i class="bi bi-star text-warning"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <span class="fw-bold"><?php echo $avgRating; ?>/5</span>
                                <span class="ms-2 text-muted small">(Dựa trên đánh giá của khách hàng)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top mt-3 pt-3">
                    <button type="submit" class="btn btn-primary" id="saveHotelInfoBtn">
                        <i class="bi bi-save"></i> Lưu thông tin
                    </button>

                </div>
            </form>
        </div>
    </div>

    <!-- Vị trí -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Vị trí</h5>
            <button type="button" class="btn btn-sm btn-outline-primary toggle-section" data-target="locationSection">
                <i class="bi bi-chevron-down"></i> Hiển thị
            </button>
        </div>
        <div id="locationSection" class="card-body collapsible-section" style="display: none;">
            <form id="locationForm" action="<?php echo SITE_URL; ?>/hotel_staff/updateLocation" method="post">
                <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="latitude" class="form-label">Vĩ độ</label>
                            <input type="text" class="form-control" id="latitude" name="latitude"
                                value="<?php echo htmlspecialchars($hotel['latitude'] ?? ''); ?>" placeholder="Nhập vĩ độ (ví dụ: 21.028511)">
                            <div class="form-text text-muted small">Giá trị vĩ độ địa lý (latitude)</div>
                        </div>

                        <div class="mb-3">
                            <label for="longitude" class="form-label">Kinh độ</label>
                            <input type="text" class="form-control" id="longitude" name="longitude"
                                value="<?php echo htmlspecialchars($hotel['longitude'] ?? ''); ?>" placeholder="Nhập kinh độ (ví dụ: 105.804817)">
                            <div class="form-text text-muted small">Giá trị kinh độ địa lý (longitude)</div>
                        </div>

                        <div class="mb-3">
                            <button type="button" id="saveLocationBtn" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Lưu vị trí
                            </button>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>Bạn có thể click vào bản đồ bên phải để chọn vị trí.</small>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div id="map" style="height: 400px; width: 100%; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hình ảnh -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-images me-2"></i>Hình ảnh khách sạn</h5>
            <button type="button" class="btn btn-sm btn-outline-primary toggle-section" data-target="imagesSection">
                <i class="bi bi-chevron-down"></i> Hiển thị
            </button>
        </div>
        <div id="imagesSection" class="card-body collapsible-section ps-0 pe-0" style="display: none;">
            <div class="hotel-gallery my-0">
                <div class="row g-2">
                    <!-- Ảnh chính -->
                    <div class="col-md-8 mb-3">
                        <div class="gallery-item main-image" data-filename="main.jpg">
                            <img src="<?php echo SITE_URL; ?>/public/images/hotels/<?php echo $hotel['id']; ?>/main.jpg?v=<?php echo time(); ?>"
                                onerror="this.src='<?php echo SITE_URL; ?>/public/images/hotels/default.jpg'"
                                alt="Ảnh chính" class="img-fluid rounded">
                            <div class="image-overlay">
                                <button class="btn btn-light btn-upload">
                                    <i class="bi bi-cloud-arrow-up-fill me-1"></i>Thay đổi ảnh chính
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 6 ảnh phụ -->
                    <div class="col-md-4">
                        <div class="gallery-grid">
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <div class="gallery-item thumbnail-item" data-filename="<?php echo $i; ?>.jpg">
                                    <div class="thumbnail-container">
                                        <img src="<?php echo SITE_URL; ?>/public/images/hotels/<?php echo $hotel['id']; ?>/<?php echo $i; ?>.jpg?v=<?php echo time(); ?>"
                                            onerror="this.src='<?php echo SITE_URL; ?>/public/images/default.jpg'"
                                            alt="Ảnh phòng khách sạn" class="img-fluid rounded">
                                    </div>
                                    <div class="image-overlay">
                                        <button class="btn btn-sm btn-light btn-upload">
                                            <i class="bi bi-camera-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <div class="gallery-info mt-0">
                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> Nhấp vào ảnh để thay đổi. Ảnh đầu tiên sẽ được sử dụng làm ảnh chính.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tiện ích -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Tiện ích khách sạn</h5>
            <div>
                <button type="button" class="btn btn-sm btn-primary" id="saveAmenitiesBtn" style="display: none;">
                    <i class="bi bi-save"></i> Lưu tiện ích
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary toggle-section" data-target="amenitiesSection">
                    <i class="bi bi-chevron-down"></i> Hiển thị
                </button>
            </div>
        </div>
        <div id="amenitiesSection" class="card-body collapsible-section" style="display: none;">
            <div class="amenities-loading text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tải danh sách tiện ích...</p>
            </div>

            <div class="amenities-container" style="display: none;">
                <form id="amenitiesForm">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Các tiện ích phải sát với thực tế và được cập nhật thường xuyên.
                            </div>
                        </div>
                    </div>

                    <div id="amenitiesList" class="row g-4">
                        <!-- Danh sách tiện ích t -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ưu đãi -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Ưu đãi và khuyến mãi</h5>
            <div>
                <button type="button" class="btn btn-sm btn-primary" id="savePromotionsBtn" style="display: none;">
                    <i class="bi bi-save"></i> Lưu ưu đãi
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary toggle-section" data-target="promotionsSection">
                    <i class="bi bi-chevron-down"></i> Hiển thị
                </button>
            </div>
        </div>
        <div id="promotionsSection" class="card-body collapsible-section" style="display: none;">
            <div class="promotions-loading text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tải danh sách ưu đãi...</p>
            </div>

            <div class="promotions-container" style="display: none;">
                <form id="promotionsForm">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Chọn các ưu đãi muốn áp dụng cho khách sạn.
                            </div>
                        </div>
                    </div>

                    <div id="promotionsList">
                        <!-- Danh sách ưu đãi -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form ẩn để upload file -->
    <form id="imageUploadForm" style="display: none;">
        <input type="file" id="imageInput" name="photo" accept="image/*">
        <input type=" hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
        <input type="hidden" id="imageFilename" name="filename">
    </form>
</div>

<style>
    /* Card styling */
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        border: none;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        padding: 15px 20px;
    }

    .collapsible-section {
        overflow: hidden;
        transition: all 0.3s ease-in-out;
    }

    /* Hiệu ứng khi nút được nhấn */
    .toggle-section {
        transition: all 0.3s ease;
    }

    .toggle-section.active {
        background-color: #0d6efd;
        color: white;
    }

    .image-container {
        position: relative;
        height: 180px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .main-image-container {
        height: 300px;
    }

    .hotel-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .image-container:hover .image-overlay {
        opacity: 1;
    }

    .image-container:hover .hotel-image {
        transform: scale(1.05);
    }

    .form-check-amenity {
        padding: 10px 15px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        flex-basis: calc(20% - 8px);
        flex-grow: 1;
        min-width: 180px;
    }

    .form-check-amenity:hover {
        background-color: #f8f9fa;
    }

    .form-check-amenity.checked {
        background-color: #e8f4ff;
        border-color: #a9d0f5;
    }

    .form-check-amenity .form-check-input:checked~.form-check-label {
        font-weight: 500;
    }

    .hotel-gallery {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        cursor: pointer;
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    /* Ảnh chính */
    .main-image {
        height: 400px;
        width: 100%;
    }

    /* Anh phụ */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(3, 1fr);
        gap: 8px;
        height: 400px;
    }

    .thumbnail-item {
        height: calc((400px - 16px) / 3);
    }

    .thumbnail-container {
        width: 100%;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    /* Đảm bảo mọi ảnh đều sẽ fill đầy container */
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.05);
    }

    .gallery-item:hover .image-overlay {
        opacity: 1;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 10;
    }

    .btn-upload {
        backdrop-filter: blur(3px);
        font-weight: 500;
        border: none;
    }

    .gallery-info {
        text-align: center;
    }

    @media (max-width: 767px) {
        .main-image {
            height: 300px;
        }

        .gallery-grid {
            height: auto;
            grid-template-rows: repeat(2, 1fr);
            grid-template-columns: repeat(3, 1fr);
        }

        .thumbnail-item {
            height: 120px;
        }
    }

    @media (max-width: 576px) {
        .main-image {
            height: 250px;
        }

        .gallery-grid {
            grid-template-rows: repeat(3, 1fr);
            grid-template-columns: repeat(2, 1fr);
        }

        .thumbnail-item {
            height: 100px;
        }
    }

    .amenities-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
        justify-content: flex-start;
    }

    .form-check-amenity {
        width: 180px;
        height: 40px;
        padding: 0 10px;
        margin-bottom: 6px;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        transition: all 0.2s ease;
        flex-grow: 0;
        flex-shrink: 0;
        display: flex;
        align-items: center;
    }

    .form-check-amenity .form-check {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .form-check-amenity .form-check-label {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: calc(100% - 25px);
        display: inline-flex;
        align-items: center;
        font-size: 0.9rem;
        line-height: 1.2;
        height: 100%;
    }

    .form-check-amenity .amenity-name {
        display: inline-block;
        vertical-align: middle;
        line-height: normal;
        max-width: calc(100% - 40px);
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .form-check-amenity:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .form-check-amenity.checked {
        background-color: #e8f4ff;
        border-color: #a9d0f5;
    }

    @media (max-width: 992px) {
        .form-check-amenity {
            width: 160px;
        }
    }

    @media (max-width: 576px) {
        .form-check-amenity {
            width: calc(50% - 5px);
            /* Hai cột trên mobile */
            font-size: 0.85rem;
        }

        .amenities-row {
            gap: 8px;
        }
    }


    .status-indicator {
        font-size: 0.6em;
        vertical-align: middle;
        margin-right: 4px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Styling cho switch trạng thái - đã bỏ các hiệu ứng highlight */
    .status-toggle-wrapper {
        background: linear-gradient(to right, #f8f9fa, #ffffff);
        border-radius: 50px;
        padding: 8px 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .form-check.form-switch {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 24px;
        margin-bottom: 0;
        padding-left: 0;
    }

    .form-check-input.status-toggle-input {
        width: 45px;
        height: 22px;
        margin-left: 0;
        cursor: pointer;
    }

    .status-toggle-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }

    .status-toggle-input:focus {
        box-shadow: none;
    }

    .status-toggle-input:not(:checked) {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .status-label {
        margin-bottom: 0;
        font-weight: 500;
        padding-left: 10px;
    }

    .status-text {
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .form-check-input:checked~.status-label .status-text {
        color: #28a745;
    }

    .form-check-input:not(:checked)~.status-label .status-text {
        color: #dc3545;
    }

    /* CSS cho khuyến mãi dạng thẻ hình ảnh */
    .promotion-card-compact {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.2s ease, background-color 0.3s ease;
        /* Thêm transition cho background */
        height: 170px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        margin-bottom: 15px;
    }

    .promotion-image {
        height: 140px;
        overflow: hidden;
        position: relative;
    }

    .promotion-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .promotion-title-compact {
        margin-top: 3px;
        display: flex;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        font-size: 14px;
        text-align: center;
        align-items: center;
        justify-content: center;
    }

    .promotion-card-compact.selected {
        border: 2px solid #0d6efd;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
        transform: translateY(-3px);
        background-color: rgb(33, 129, 239);
        /* Thêm màu nền nhẹ cho trạng thái đã chọn */
    }

    .promotion-card-compact:before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        display: none;
        border-style: solid;
        border-width: 0 30px 30px 0;
        border-color: transparent #0d6efd transparent transparent;
        z-index: 1;
    }

    .promotion-card-compact.selected:before {
        display: block;
    }

    .promotion-card-compact.selected:after {
        content: '✓';
        position: absolute;
        top: 3px;
        right: 7px;
        font-size: 12px;
        color: white;
        font-weight: bold;
        z-index: 2;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý hiển thị/ẩn các phần
        document.querySelectorAll('.toggle-section').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetSection = document.getElementById(targetId);

                if (targetSection.style.display === 'none') {
                    // Hiển thị section
                    targetSection.style.display = 'block';
                    this.innerHTML = '<i class="bi bi-chevron-up"></i> Ẩn';
                    this.classList.add('active');

                    // Nếu là phần tiện ích, hiển thị nút lưu và load dữ liệu
                    if (targetId === 'amenitiesSection') {
                        document.getElementById('saveAmenitiesBtn').style.display = 'inline-block';
                        loadAmenities();
                    }

                    // Nếu là phần ưu đãi, hiển thị nút lưu và load dữ liệu
                    if (targetId === 'promotionsSection') {
                        document.getElementById('savePromotionsBtn').style.display = 'inline-block';
                        loadPromotions();
                    }

                    // Nếu là phần vị trí, khởi tạo bản đồ Google
                    if (targetId === 'locationSection') {
                        setTimeout(() => {
                            initMap();
                        }, 200);
                    }
                } else {
                    targetSection.style.display = 'none';
                    this.innerHTML = '<i class="bi bi-chevron-down"></i> Hiển thị';
                    this.classList.remove('active');

                    if (targetId === 'amenitiesSection') {
                        document.getElementById('saveAmenitiesBtn').style.display = 'none';
                    }

                    if (targetId === 'promotionsSection') {
                        document.getElementById('savePromotionsBtn').style.display = 'none';
                    }
                }
            });
        });

        // Xử lý upload hình ảnh
        const imageContainers = document.querySelectorAll('.gallery-item');
        const imageInput = document.getElementById('imageInput');
        const imageFilename = document.getElementById('imageFilename');
        const imageUploadForm = document.getElementById('imageUploadForm');

        let activeContainer = null;

        // Xử lý click vào container ảnh
        imageContainers.forEach(container => {
            container.addEventListener('click', function() {
                activeContainer = this;
                const filename = this.dataset.filename;
                imageFilename.value = filename;
                imageInput.click();
            });
        });

        // Xử lý khi chọn file
        imageInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                uploadImage();
            }
        });

        // Hàm upload ảnh
        function uploadImage() {
            const formData = new FormData(imageUploadForm);

            // Hiển thị loading trên container đang active
            if (activeContainer) {
                activeContainer.classList.add('uploading');
                const uploadBtn = activeContainer.querySelector('.upload-btn');
                if (uploadBtn) {
                    uploadBtn.textContent = 'Đang tải...';
                    uploadBtn.disabled = true;
                }
            }

            fetch('<?php echo SITE_URL; ?>/hotel_staff/uploadHotelPhotos', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật hình ảnh nếu thành công
                        if (activeContainer) {
                            const img = activeContainer.querySelector('img');
                            if (img) {
                                const timestamp = data.timestamp || new Date().getTime();
                                img.src = `${data.image_url}?v=${timestamp}`;
                            }

                            // Thông báo thành công
                            showToast('success', 'Cập nhật ảnh thành công!');
                        }
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra khi tải ảnh lên.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Đã xảy ra lỗi khi tải ảnh lên.');
                })
                .finally(() => {
                    if (activeContainer) {
                        activeContainer.classList.remove('uploading');
                        const uploadBtn = activeContainer.querySelector('.upload-btn');
                        if (uploadBtn) {
                            uploadBtn.textContent = 'Thay đổi ảnh';
                            uploadBtn.disabled = false;
                        }
                    }
                    imageUploadForm.reset();
                });
        }

        // Hàm load tiện ích
        function loadAmenities() {
            const hotelId = <?php echo $hotel['id']; ?>;
            const amenitiesContainer = document.querySelector('.amenities-container');
            const loadingElement = document.querySelector('.amenities-loading');
            const amenitiesListContainer = document.getElementById('amenitiesList'); // Thay đổi tên biến

            if (amenitiesContainer.classList.contains('loaded')) {
                amenitiesContainer.style.display = 'block';
                loadingElement.style.display = 'none';
                return;
            }

            loadingElement.style.display = 'block';
            amenitiesContainer.style.display = 'none';

            // Lấy tất cả các tiện ích có sẵn
            fetch(`<?php echo SITE_URL; ?>/hotel_staff/getAmenities`)
                .then(response => response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Không thể parse JSON response:', text);
                        throw new Error('Không thể parse JSON response');
                    }
                }))
                .then(allAmenitiesData => {
                    if (!allAmenitiesData.success) {
                        throw new Error('Không thể tải danh sách tiện ích');
                    }
                    return fetch(`<?php echo SITE_URL; ?>/hotel_staff/getAppliedAmenities/${hotelId}`)
                        .then(response => response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Không thể parse JSON response:', text);
                                throw new Error('Không thể parse JSON response');
                            }
                        }))
                        .then(appliedAmenitiesData => {
                            if (!appliedAmenitiesData.success) {
                                throw new Error('Không thể tải danh sách tiện ích đã áp dụng');
                            }

                            // Xóa nội dung cũ
                            amenitiesListContainer.innerHTML = ''; // Thay đổi tên biến

                            const appliedAmenityMap = {};
                            if (Array.isArray(appliedAmenitiesData.amenities)) {
                                appliedAmenitiesData.amenities.forEach(amenity => {
                                    if (amenity && amenity.id) {
                                        appliedAmenityMap[amenity.id] = true;
                                    }
                                });
                            }

                            // Kiểm tra và hiển thị danh sách tiện ích
                            if (allAmenitiesData.amenities && Array.isArray(allAmenitiesData.amenities)) {
                                const amenities = allAmenitiesData.amenities;

                                if (amenities.length === 0) {
                                    const noDataElement = document.createElement('div');
                                    noDataElement.className = 'col-12 text-center py-4';
                                    noDataElement.innerHTML = `
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Không có tiện ích nào
                                        </div>`;
                                    amenitiesListContainer.appendChild(noDataElement); // Thay đổi tên biến
                                } else {
                                    // Tạo container chính
                                    const mainColumn = document.createElement('div');
                                    mainColumn.className = 'col-12';

                                    // Tính số dòng cần thiết (mỗi dòng 5 tiện ích)
                                    const rowCount = Math.ceil(amenities.length / 5);
                                    let amenitiesHtml = '';

                                    // Tạo từng dòng tiện ích
                                    for (let i = 0; i < rowCount; i++) {
                                        amenitiesHtml += `<div class="amenities-row d-flex flex-wrap justify-content-start">`;
                                        const startIdx = i * 5;
                                        const endIdx = Math.min(startIdx + 5, amenities.length);

                                        // Tạo từng tiện ích trong dòng
                                        for (let j = startIdx; j < endIdx; j++) {
                                            const amenity = amenities[j];
                                            if (amenity && amenity.id) {
                                                const isSelected = appliedAmenityMap[amenity.id] || false;
                                                const isChecked = isSelected ? 'checked' : '';
                                                const checkedClass = isSelected ? 'checked' : '';
                                                const tooltipDescription = amenity.description || 'Không có mô tả cho tiện ích này';

                                                amenitiesHtml += `
                                                    <div class="form-check-amenity ${checkedClass}" onclick="toggleAmenity(this, ${amenity.id})" 
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipDescription}">
                                                        <div class="form-check">
                                                            <input class="form-check-input amenity-checkbox" type="checkbox" 
                                                                id="amenity_${amenity.id}" name="amenities[]" value="${amenity.id}" ${isChecked} style="display:none;">
                                                            <label class="form-check-label d-flex align-items-center w-100" for="amenity_${amenity.id}">
                                                                <i class="${amenity.icon || 'bi bi-check-circle'} me-2"></i>
                                                                <span class="amenity-name">${amenity.name}</span>
                                                                <i class="bi bi-check check-icon ms-auto"></i>
                                                            </label>
                                                        </div>
                                                    </div>`;
                                            }
                                        }

                                        amenitiesHtml += `</div>`;
                                    }

                                    mainColumn.innerHTML = amenitiesHtml;
                                    amenitiesListContainer.appendChild(mainColumn); // Thay đổi tên biến

                                    // Khởi tạo tooltips cho các tiện ích
                                    setTimeout(() => {
                                        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                                        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
                                    }, 100);
                                }
                            } else {
                                // Không có dữ liệu tiện ích
                                const noDataElement = document.createElement('div');
                                noDataElement.className = 'col-12 text-center py-4';
                                noDataElement.innerHTML = `
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Không có dữ liệu tiện ích
                                    </div>`;
                                amenitiesListContainer.appendChild(noDataElement); // Thay đổi tên biến
                            }

                            amenitiesContainer.classList.add('loaded');

                            loadingElement.style.display = 'none';
                            amenitiesContainer.style.display = 'block';
                        });
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', error.message || 'Đã xảy ra lỗi khi tải danh sách tiện ích');
                    loadingElement.style.display = 'none';
                });
        }

        // Hàm load ưu đãi
        function loadPromotions() {
            const hotelId = <?php echo $hotel['id']; ?>;
            const promotionsContainer = document.querySelector('.promotions-container');
            const loadingElement = document.querySelector('.promotions-loading');
            const promotionsList = document.getElementById('promotionsList');

            if (promotionsContainer.classList.contains('loaded')) {
                promotionsContainer.style.display = 'block';
                loadingElement.style.display = 'none';
                return;
            }

            // Hiển thị loading
            loadingElement.style.display = 'block';
            promotionsContainer.style.display = 'none';

            // Tạo một đối tượng để lưu trữ các khuyến mãi đã áp dụng
            const appliedPromotionsMap = {};
            Promise.all([
                    fetch(`<?php echo SITE_URL; ?>/hotel_staff/getPromotions`).then(res => res.json()),
                    fetch(`<?php echo SITE_URL; ?>/hotel_staff/getAppliedPromotions/${hotelId}`).then(res => res.json())
                ])
                .then(([allPromotionsData, appliedPromotionsData]) => {
                    if (!allPromotionsData.success) {
                        throw new Error('Không thể tải danh sách khuyến mãi');
                    }

                    if (!appliedPromotionsData.success) {
                        throw new Error('Không thể tải danh sách khuyến mãi đã áp dụng');
                    }

                    // Tạo map các khuyến mãi đã áp dụng để dễ dàng kiểm tra
                    const appliedPromotions = appliedPromotionsData.appliedPromotions;

                    if (Array.isArray(appliedPromotions)) {
                        appliedPromotions.forEach(promotion => {
                            if (promotion && promotion.id) {
                                appliedPromotionsMap[promotion.id] = true;
                            }
                        });
                    }

                    // Render danh sách khuyến mãi
                    promotionsList.innerHTML = '';

                    if (!allPromotionsData.promotions || allPromotionsData.promotions.length === 0) {
                        promotionsList.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-tags fs-1 text-muted"></i>
                            <p class="mt-3">Hiện tại không có ưu đãi nào khả dụng.</p>
                        </div>`;
                    } else {
                        // Tạo container chính cho dòng mới
                        let currentRow = document.createElement('div');
                        currentRow.className = 'row mb-4';
                        promotionsList.appendChild(currentRow);

                        let count = 0;

                        allPromotionsData.promotions.forEach(promotion => {
                            // Tạo dòng mới sau mỗi 4 khuyến mãi
                            if (count > 0 && count % 4 === 0) {
                                currentRow = document.createElement('div');
                                currentRow.className = 'row mb-4';
                                promotionsList.appendChild(currentRow);
                            }
                            count++;

                            const isSelected = appliedPromotionsMap[promotion.id] ? true : false;
                            const isChecked = isSelected ? 'checked' : '';
                            const selectedClass = isSelected ? 'selected' : '';

                            const col = document.createElement('div');
                            col.className = 'col-md-3'; // 4 khuyến mãi mỗi dòng

                            // Chuẩn bị nội dung tooltip
                            const tooltipContent = `
                                ${promotion.description || 'Không có mô tả'}
                                <br>
                                <strong>Thời gian:</strong> ${formatDate(promotion.start_date)} - ${formatDate(promotion.end_date)}
                                <br>
                                <strong>Mã:</strong> ${promotion.code}
                                <br>
                                <strong>Giảm giá:</strong> ${promotion.discount_type === 'percentage' ? `${promotion.discount_value}%` : formatCurrency(promotion.discount_value)}
                            `;

                            // Banner image - sử dụng placeholder nếu không có ảnh
                            const bannerImg = '<?php echo SITE_URL . '/public/images/promotions/'; ?>' + promotion.id + '.png';
                            col.innerHTML = `
                                <div class="promotion-card-compact ${selectedClass}" data-id="${promotion.id}" 
                                    data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="top" title="${tooltipContent}">
                                    <div class="promotion-image">
                                        <img src="${bannerImg}" alt="${promotion.title}" class="img-fluid" onerror="this.src='<?php echo SITE_URL; ?>/public/images/promotions/default.jpg'">
                                        <div class="promotion-overlay">
                                            <!-- Bỏ form-check hiển thị ở đây -->
                                        </div>
                                    </div>
                                    <!-- Giữ checkbox nhưng ẩn hoàn toàn -->
                                    <input class="promotion-checkbox" type="checkbox" 
                                        id="promotion_${promotion.id}" name="promotions[]" value="${promotion.id}" ${isChecked} 
                                        style="position: absolute; opacity: 0; width: 0; height: 0;">
                                    <div class="promotion-title-compact">
                                        ${promotion.title}
                                    </div>
                                </div>
                            `;

                            currentRow.appendChild(col);
                        });

                        // Thêm event listener cho các checkbox
                        document.querySelectorAll('.promotion-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                const card = this.closest('.promotion-card-compact');
                                if (this.checked) {
                                    card.classList.add('selected');
                                } else {
                                    card.classList.remove('selected');
                                }
                            });

                            // Ngăn chặn sự kiện click trên checkbox lan ra ngoài card
                            checkbox.addEventListener('click', function(e) {
                                e.stopPropagation();
                            });
                        });

                        // Cho phép click vào card để chọn/bỏ chọn khuyến mãi
                        document.querySelectorAll('.promotion-card-compact').forEach(card => {
                            card.addEventListener('click', function() {
                                const checkbox = this.querySelector('.promotion-checkbox');
                                checkbox.checked = !checkbox.checked;

                                if (checkbox.checked) {
                                    this.classList.add('selected');
                                } else {
                                    this.classList.remove('selected');
                                }
                            });
                        });

                        // Khởi tạo tooltips
                        setTimeout(() => {
                            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
                                html: true
                            }));
                        }, 100);
                    }

                    // Đánh dấu đã load thành công
                    promotionsContainer.classList.add('loaded');

                    // Hiển thị container, ẩn loading
                    loadingElement.style.display = 'none';
                    promotionsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', error.message || 'Đã xảy ra lỗi khi tải danh sách ưu đãi');
                    loadingElement.style.display = 'none';
                });
        }

        // Xử lý nút lưu tiện ích
        document.getElementById('saveAmenitiesBtn').addEventListener('click', function() {
            const form = document.getElementById('amenitiesForm');
            const formData = new FormData(form);
            const hotelId = <?php echo $hotel['id']; ?>;

            // Hiển thị loading
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';

            // Lấy tất cả các tiện ích đã chọn
            const amenities = [];
            document.querySelectorAll('input[name="amenities[]"]:checked').forEach(checkbox => {
                amenities.push(checkbox.value);
            });

            // Gửi request lưu tiện ích
            fetch('<?php echo SITE_URL; ?>/hotel_staff/updateAmenities', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        hotel_id: hotelId,
                        amenities: amenities
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Lưu tiện ích thành công');
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra khi lưu tiện ích');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Đã xảy ra lỗi khi lưu tiện ích');
                })
                .finally(() => {
                    // Reset trạng thái nút
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-save"></i> Lưu tiện ích';
                });
        });



        // Xử lý nút lưu ưu đãi
        document.getElementById('savePromotionsBtn').addEventListener('click', function() {
            const form = document.getElementById('promotionsForm');
            const formData = new FormData(form);
            const hotelId = <?php echo $hotel['id']; ?>;

            // Hiển thị loading
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';

            // Lấy tất cả các ưu đãi đã chọn
            const promotions = [];
            document.querySelectorAll('input[name="promotions[]"]:checked').forEach(checkbox => {
                promotions.push(checkbox.value);
            });

            // Gửi request lưu ưu đãi
            fetch('<?php echo SITE_URL; ?>/hotel_staff/updatePromotions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        hotel_id: hotelId,
                        promotions: promotions
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Lưu ưu đãi thành công');
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra khi lưu ưu đãi');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Đã xảy ra lỗi khi lưu ưu đãi');
                })
                .finally(() => {
                    // Reset trạng thái nút
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-save"></i> Lưu ưu đãi';
                });
        });

        // Hàm hiển thị thông báo
        function showToast(type, message) {
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }

            // Tạo Toast
            const toastId = 'toast-' + Date.now();
            const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';

            const toastHTML = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

            toastContainer.insertAdjacentHTML('beforeend', toastHTML);

            // Khởi tạo và hiển thị toast
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                delay: 5000
            });
            toast.show();
        }

        // Tiện ích format
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Xử lý chuyển đổi trạng thái hoạt động
        const statusToggle = document.querySelector('.status-toggle-input');
        if (statusToggle) {
            statusToggle.addEventListener('click', function() {
                const hotelId = this.dataset.hotelId;
                const currentStatus = this.checked ? 'inactive' : 'active';
                const newStatus = this.checked ? 'active' : 'inactive';

                // Hiển thị trạng thái đang xử lý
                this.parentNode.classList.add('processing');

                // Gửi yêu cầu AJAX để cập nhật trạng thái
                fetch('<?php echo SITE_URL; ?>/hotel_staff/updateStatus', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            hotel_id: hotelId,
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Cập nhật UI
                            this.dataset.currentStatus = newStatus;

                            const statusText = this.closest('.form-check').querySelector('.status-text');

                            if (newStatus === 'active') {
                                this.classList.remove('bg-danger');
                                this.classList.add('bg-success');
                                if (statusText) statusText.textContent = 'Đang hoạt động';
                                const statusCheckbox = document.getElementById('status');
                                if (statusCheckbox) statusCheckbox.checked = true;
                            } else {
                                this.classList.remove('bg-success');
                                this.classList.add('bg-danger');
                                if (statusText) statusText.textContent = 'Ngừng hoạt động';
                                // Cập nhật trạng thái cho input checkbox nếu có
                                const statusCheckbox = document.getElementById('status');
                                if (statusCheckbox) statusCheckbox.checked = false;
                            }

                        } else {
                            showToast('error', data.message || 'Có lỗi xảy ra khi cập nhật trạng thái');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Đã xảy ra lỗi khi cập nhật trạng thái');
                    })
                    .finally(() => {
                        this.parentNode.classList.remove('processing'); // Xóa trạng thái đang xử lý
                    });
            });
        }

        // Xử lý cập nhật vị trí
        const saveLocationBtn = document.getElementById('saveLocationBtn');
        if (saveLocationBtn) {
            saveLocationBtn.addEventListener('click', function() {
                const form = document.getElementById('locationForm');
                const hotelId = form.querySelector('input[name="hotel_id"]').value;
                const latitude = form.querySelector('input[name="latitude"]').value;
                const longitude = form.querySelector('input[name="longitude"]').value;

                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';

                // Gửi yêu cầu AJAX để cập nhật vị trí
                fetch('<?php echo SITE_URL; ?>/hotel_staff/updateLocation', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            hotel_id: hotelId,
                            latitude: latitude,
                            longitude: longitude
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('success', 'Cập nhật vị trí thành công');
                        } else {
                            showToast('error', data.message || 'Có lỗi xảy ra khi cập nhật vị trí');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Đã xảy ra lỗi khi cập nhật vị trí');
                    })
                    .finally(() => {
                        // Khôi phục trạng thái nút sau khi hoàn thành
                        this.disabled = false;
                        this.innerHTML = originalText;
                    });
            });
        }

        // Xử lý lưu thông tin khách sạn
        const hotelBasicForm = document.getElementById('hotelBasicForm');
        if (hotelBasicForm) {
            hotelBasicForm.addEventListener('submit', function(e) {
                event.preventDefault(); //
                const formData = new FormData(this);
                const saveBtn = document.getElementById('saveHotelInfoBtn');

                saveBtn.disabled = true;
                const originalBtnText = saveBtn.innerHTML;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';

                formData.forEach((value, key) => {

                });
                fetch(`${SITE_URL}/hotel_staff/updateHotel`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.success) {
                            showToast('success', 'Cập nhật thông tin khách sạn thành công');
                        } else {
                            showToast('error', data.message || 'Có lỗi xảy ra khi cập nhật thông tin khách sạn');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Có lỗi xảy ra khi cập nhật thông tin khách sạn');
                    })
                    .finally(() => {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalBtnText;
                    });
            });
        }
    });

    function initMap() {
        const defaultLat = <?php echo $hotel['latitude'] ?? 21.028511; ?>;
        const defaultLng = <?php echo $hotel['longitude'] ?? 105.804817; ?>;

        const mapOptions = {
            zoom: 15,
            center: {
                lat: defaultLat,
                lng: defaultLng
            }
        };

        const map = new google.maps.Map(document.getElementById('map'), mapOptions);

        // Đặt marker ban đầu
        let marker = new google.maps.Marker({
            position: {
                lat: defaultLat,
                lng: defaultLng
            },
            map: map,
            draggable: true
        });

        // Cập nhật vị trí khi kéo marker
        google.maps.event.addListener(marker, 'dragend', function(event) {
            document.getElementById('latitude').value = event.latLng.lat().toFixed(6);
            document.getElementById('longitude').value = event.latLng.lng().toFixed(6);
        });

        // Cập nhật vị trí khi click vào map
        google.maps.event.addListener(map, 'click', function(event) {
            marker.setPosition(event.latLng);
            document.getElementById('latitude').value = event.latLng.lat().toFixed(6);
            document.getElementById('longitude').value = event.latLng.lng().toFixed(6);
        });

        // Cập nhật marker khi thay đổi giá trị tọa độ
        function updateMarkerPosition() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            const newPosition = {
                lat: lat,
                lng: lng
            };

            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setPosition(newPosition);
                map.setCenter(newPosition);
            }
        }

        document.getElementById('latitude').addEventListener('change', updateMarkerPosition);
        document.getElementById('longitude').addEventListener('change', updateMarkerPosition);
    }

    // Thêm hàm toggleAmenity để xử lý việc chọn/bỏ chọn tiện ích
    function toggleAmenity(element, amenityId) {
        // Tìm checkbox trong phần tử được click
        const checkbox = document.getElementById('amenity_' + amenityId);
        if (!checkbox) return;

        // Đảo ngược trạng thái checkbox
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            element.classList.add('checked');
        } else {
            element.classList.remove('checked');
        }
    }
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap"></script>