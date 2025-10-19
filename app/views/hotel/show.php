<div class="container mt-4">
    <?php
    // Tính số đêm lưu trú
    $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    if ($nights < 1) $nights = 1;
    ?>

    <meta name="user-id" content="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>"><i class="bi bi-house"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/hotels">Khách sạn</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $hotel['name']; ?></li>
        </ol>
    </nav>

    <!-- Hotel Header Section -->
    <div class="hotel-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="hotel-title" data-hotel-id="<?php echo $hotel['id']; ?>"><?php echo $hotel['name']; ?></h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <?php for ($i = 0; $i < $rating; $i++): ?>
                            <i class="bi bi-star-fill text-warning"></i>
                        <?php endfor; ?>
                    </div>
                    <?php if (isset($rating) && $rating > 0): ?>
                        <div class="rating-badge">
                            <span class="rating-score"><?php echo number_format($rating, 1); ?></span>
                            <span class="rating-text">Tuyệt vời</span>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="hotel-address">
                    <i class="bi bi-geo-alt text-primary"></i>
                    <span><?php echo $hotel['address']; ?></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Thanh  cuộn -->
    <nav id="hotel-navbar" class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm mb-4">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav hotel-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#overview">
                            <i class="bi bi-info-circle me-1"></i> Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#rooms">
                            <i class="bi bi-door-closed me-1"></i> Phòng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#location">
                            <i class="bi bi-geo me-1"></i> Vị trí
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#reviews">
                            <i class="bi bi-star me-1"></i> Đánh giá
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="hotel-gallery mb-4 mt-4">
        <div class="row g-2">
            <div class="col-md-8">
                <div class="main-image-container">
                    <img src="<?php echo $images['main']; ?>" class="img-fluid rounded main-image" alt="<?php echo $hotel['name']; ?>" id="mainImage"
                        onerror="this.src='<?php echo SITE_URL; ?>/public/images/hotels/default.jpg'">
                </div>
            </div>

            <div class="col-md-4">
                <div class="row g-2">
                    <?php
                    $additionalImages = array_slice($images['additional'], 0, 6);
                    $actualThumbnails = count($additionalImages);

                    foreach ($additionalImages as $index => $image):
                    ?>
                        <div class="col-6">
                            <div class="thumbnail-container">
                                <img src="<?php echo $image; ?>" class="img-fluid rounded thumbnail"
                                    alt="<?php echo $hotel['name'] . ' - Ảnh ' . ($index + 1); ?>"
                                    onclick="changeMainImage(this.src)"
                                    onerror="this.src='<?php echo SITE_URL; ?>/public/images/hotels/default.jpg'">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Phần tổng quân -->
    <div class="row" id="overview">
        <div class="col-md-8">
            <div class="hotel-section mb-4">
                <h4 class="section-title">Giới thiệu</h4>
                <div class="section-content">
                    <?php echo nl2br($hotel['description']); ?>
                </div>
            </div>

            <div class="hotel-section mb-4">
                <h4 class="section-title">Tiện nghi khách sạn</h4>
                <div class="amenities-wrapper">
                    <div class="row">
                        <?php foreach ($hotel['amenities'] as $amenity): ?>
                            <div class="col-md-4 col-6 mb-2">
                                <div class="amenity-item">
                                    <i class="bi <?php echo !empty($amenity['icon']) ? $amenity['icon'] : 'bi-check-circle'; ?> text-primary"></i>
                                    <div class="amenity-content">
                                        <span class="amenity-name"><?php echo $amenity['name']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-3">
                        <button class="btn btn-outline-secondary" onclick="showAllAmenities()">
                            <i class="bi bi-info-circle"></i> Xem chi tiết tiện nghi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Liên hệ -->
            <div class="hotel-section mb-4">
                <h4 class="section-title">Thông tin liên hệ</h4>
                <ul class="contact-list">
                    <?php if (!empty($hotel['contact_phone'])): ?>
                        <li><i class="bi bi-telephone"></i> <?php echo $hotel['contact_phone']; ?></li>
                    <?php endif; ?>
                    <?php if (!empty($hotel['contact_email'])): ?>
                        <li><i class="bi bi-envelope"></i> <?php echo $hotel['contact_email']; ?></li>
                    <?php endif; ?>
                    <li><i class="bi bi-geo-alt"></i> <?php echo $hotel['address']; ?></li>
                </ul>
            </div>
        </div>

        <!-- Các ưu đãi -->
        <div class="col-md-4">
            <?php if (!empty($promotions)): ?>
                <div class="promo-section mb-4">
                    <h4 class="promo-section-title"><i class="bi bi-tag"></i> Khuyến mãi đặc biệt</h4>

                    <div class="promotion-list">
                        <?php foreach ($promotions as $promo): ?>
                            <div class="promotion-card mb-3"
                                <?php if (!empty($promo['description'])): ?>
                                data-bs-toggle="tooltip"
                                data-bs-html="true"
                                data-bs-placement="left"
                                title="<?php echo htmlspecialchars($promo['description']); ?>"
                                <?php endif; ?>>
                                <div class="promotion-header">
                                    <div class="promotion-title-wrapper">
                                        <h5 class="promotion-title">
                                            <i class="bi <?php echo !empty($promo['icon']) ? $promo['icon'] : 'bi-tag'; ?>"></i>
                                            <?php echo $promo['title']; ?>
                                        </h5>
                                        <?php if (!empty($promo['discount_value'])): ?>
                                            <div class="promotion-discount">
                                                <?php if ($promo['discount_type'] == 'percentage'): ?>
                                                    <span class="discount-badge">-<?php echo $promo['discount_value']; ?>%</span>
                                                <?php else: ?>
                                                    <span class="discount-badge">-<?php echo number_format($promo['discount_value']); ?>đ</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="promotion-code">Mã: <strong><?php echo $promo['code']; ?></strong></div>
                                </div>


                                <div class="promotion-footer">
                                    <div class="promotion-dates">
                                        <i class="bi bi-calendar3"></i>
                                        <?php echo date('d/m/Y', strtotime($promo['start_date'])); ?> -
                                        <?php echo date('d/m/Y', strtotime($promo['end_date'])); ?>
                                    </div>
                                    <?php if (!empty($promo['description'])): ?>
                                        <div class="view-details">
                                            <i class="bi bi-info-circle text-primary"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Add additional styles for the more compact promotion cards -->
                <style>
                    .promotion-card {
                        cursor: pointer;
                        padding: 12px;
                        transition: all 0.2s ease;
                    }

                    .promotion-card:hover {
                        background-color: #f8f9fa;
                        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
                    }

                    .promotion-footer {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-top: 8px;
                    }

                    .view-details {
                        font-size: 0.9rem;
                    }

                    /* Make tooltips bigger and better formatted */
                    .tooltip-inner {
                        max-width: 300px;
                        padding: 10px;
                        text-align: left;
                        font-size: 0.9rem;
                        line-height: 1.4;
                    }
                </style>
            <?php endif; ?>
        </div>
    </div>

    <div id="rooms" class="section-container my-5">
        <h3 class="section-title mb-4">Phòng</h3>
        <!-- Filter và sắp xếp -->
        <div class="filter-sort-container mb-4">
            <div class="card filter-card">
                <div class="card-body p-3">
                    <form id="roomFilterForm" class="row g-2" method="GET" action="<?php echo SITE_URL . '/hotels/' . $hotel['id']; ?>">
                        <div class="col-md-3">
                            <label class="form-label small">Ngày nhận phòng</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                <input type="date" class="form-control" id="checkInDate" name="check_in"
                                    value="<?php echo !empty($check_in) ? htmlspecialchars($check_in) : date('Y-m-d', strtotime('+1 day')); ?>"
                                    min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Ngày trả phòng</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                                <input type="date" class="form-control" id="checkOutDate" name="check_out"
                                    value="<?php echo !empty($check_out) ? htmlspecialchars($check_out) : date('Y-m-d', strtotime('+2 days')); ?>"
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Khách</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-people"></i></span>
                                <select class="form-select" id="guestCount" name="guests">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($_GET['guests']) && $_GET['guests'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Loại phòng</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-door-closed"></i></span>
                                <select class="form-select" id="roomType" name="room_type">
                                    <option value="">Tất cả</option>
                                    <option value="single" <?php echo (isset($_GET['room_type']) && $_GET['room_type'] == 'single') ? 'selected' : ''; ?>>Phòng đơn</option>
                                    <option value="double" <?php echo (isset($_GET['room_type']) && $_GET['room_type'] == 'double') ? 'selected' : ''; ?>>Phòng đôi</option>
                                    <option value="suite" <?php echo (isset($_GET['room_type']) && $_GET['room_type'] == 'suite') ? 'selected' : ''; ?>>Suite</option>
                                    <option value="family" <?php echo (isset($_GET['room_type']) && $_GET['room_type'] == 'family') ? 'selected' : ''; ?>>Phòng gia đình</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danh sách phòng -->
        <div class="room-list-container">
            <?php if (empty($rooms)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Không tìm thấy phòng phù hợp trong thời gian này. Vui lòng thử chọn ngày khác.
                </div>
                <div class="text-center mt-3">
                    <button type="button" id="viewAllRoomsBtn" class="btn btn-outline-primary">
                        <i class="bi bi-grid-3x3-gap"></i> Xem tất cả phòng
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($rooms as $room): ?>
                    <div class="room-card" data-room-id="<?php echo $room['id']; ?>" data-room-price="<?php echo $room['price']; ?>" data-room-name="<?php echo htmlspecialchars($room['name']); ?>">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <div class="room-image-container">
                                    <img src="<?php echo SITE_URL . '/public/images/rooms/' . $room['id'] . '.jpg'; ?>"
                                        class="room-image" alt="<?php echo $room['name']; ?>"
                                        onerror="this.src='<?php echo SITE_URL; ?>/public/images/rooms/default.jpg'">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="room-details">
                                    <h4 class="room-name"><?php echo $room['name']; ?></h4>

                                    <div class="room-type-badge mb-2">
                                        <?php
                                        $typeBadgeClass = 'secondary';
                                        switch ($room['room_type']) {
                                            case 'single':
                                                $typeBadgeClass = 'info';
                                                break;
                                            case 'double':
                                                $typeBadgeClass = 'primary';
                                                break;
                                            case 'suite':
                                                $typeBadgeClass = 'success';
                                                break;
                                            case 'family':
                                                $typeBadgeClass = 'warning';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $typeBadgeClass; ?>">
                                            <?php
                                            $roomTypeNames = [
                                                'single' => 'Phòng đơn',
                                                'double' => 'Phòng đôi',
                                                'suite' => 'Suite',
                                                'family' => 'Phòng gia đình'
                                            ];
                                            echo $roomTypeNames[$room['room_type']] ?? $room['room_type'];
                                            ?>
                                        </span>

                                        <?php if (!empty($room['area'])): ?>
                                            <span class="room-area ms-2">
                                                <i class="bi bi-rulers"></i> <?php echo $room['area']; ?> m²
                                            </span>
                                        <?php endif; ?>

                                        <span class="room-capacity ms-2">
                                            <i class="bi bi-people-fill"></i> Tối đa <?php echo $room['capacity']; ?> người
                                        </span>
                                    </div>

                                    <!-- Room Amenities -->
                                    <div class="room-amenities mb-3">
                                        <?php
                                        // Lấy tiện nghi phòng
                                        $roomModel = new \App\Models\Room();
                                        $amenities = $roomModel->getRoomAmenities($room['id']);
                                        if (!empty($amenities)):
                                            $maxAmenities = 5;
                                            $displayedCount = 0;
                                            foreach ($amenities as $amenity):
                                                if ($displayedCount++ < $maxAmenities):
                                        ?>
                                                    <span class="amenity-icon" data-bs-toggle="tooltip" title="<?php echo $amenity['name']; ?>">
                                                        <i class="bi <?php echo $amenity['icon']; ?> <?php echo $amenity['is_premium'] ? 'premium' : ''; ?>"></i>
                                                    </span>
                                                <?php
                                                endif;
                                            endforeach;

                                            // Hiển thị nút "thêm" nếu có nhiều hơn số lượng tối đa
                                            if (count($amenities) > $maxAmenities):
                                                ?>
                                                <span class="amenity-more" data-bs-toggle="tooltip"
                                                    title="<?php echo count($amenities) - $maxAmenities; ?> tiện nghi khác">
                                                    <i class="bi bi-plus-circle"></i>
                                                </span>
                                        <?php
                                            endif;
                                        endif;
                                        ?>
                                    </div>

                                    <div class="room-description">
                                        <?php echo mb_substr($room['description'] ?? '', 0, 120); ?>
                                        <?php if (mb_strlen($room['description'] ?? '') > 120): ?>...<?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="room-price-container">
                                    <div class="room-price">
                                        <span class="price-value"><?php echo number_format($room['price'], 0, ',', '.'); ?> VNĐ</span>
                                    </div>

                                    <div class="room-selection">
                                        <div class="form-check">
                                            <input class="form-check-input room-checkbox" type="checkbox"
                                                id="room-<?php echo $room['id']; ?>" data-room-id="<?php echo $room['id']; ?>">
                                            <label class="form-check-label select-room-label" for="room-<?php echo $room['id']; ?>">
                                                Thêm vào giỏ hàng
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="text-center mt-4">
                    <button type="button" id="viewAllRoomsBtn" class="btn btn-outline-primary">
                        <i class="bi bi-grid-3x3-gap"></i> Xem tất cả phòng
                    </button>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div id="location" class="section-container my-5">
        <h3 class="section-title mb-4">Vị trí</h3>

        <div class="row">
            <!-- Bản đồ Gg maps -->
            <div class="col-md-7 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body p-0 h-100">
                        <div id="hotelMap" class="h-100"></div>
                    </div>
                </div>
            </div>

            <!-- Địa điểm nổi tiếng gần đó -->
            <div class="col-md-5 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Địa điểm nổi tiếng gần đây</h5>
                    </div>
                    <div class="card-body">
                        <div id="loadingPlaces" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <p class="mt-2">Đang tìm kiếm địa điểm gần đó...</p>
                        </div>
                        <ul id="nearbyPlaces" class="list-group list-group-flush nearby-places"></ul>
                        <div id="noPlacesFound" class="alert alert-info text-center" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>Không tìm thấy địa điểm nổi tiếng gần đây
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Biến toàn cục để lưu thông tin khách sạn
        const hotelData = {
            id: <?php echo $hotel['id']; ?>,
            name: "<?php echo htmlspecialchars($hotel['name']); ?>",
            latitude: <?php echo $hotel['latitude'] ?? 'null'; ?>,
            longitude: <?php echo $hotel['longitude'] ?? 'null'; ?>,
            address: "<?php echo htmlspecialchars($hotel['address']); ?>"
        };

        // Khởi tạo bản đồ khi Google Maps API đã tải xong
        function initMap() {
            if (hotelData.latitude && hotelData.longitude) {
                renderMapWithLocation(hotelData.latitude, hotelData.longitude);
            } else {
                geocodeAddress(hotelData.address);
            }
        }

        // Hàm chuyển đổi địa chỉ sang tọa độ
        function geocodeAddress(address) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'address': address
            }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const lat = results[0].geometry.location.lat();
                    const lng = results[0].geometry.location.lng();
                    renderMapWithLocation(lat, lng);
                } else {
                    console.error('Geocode was not successful:', status);
                    document.getElementById('hotelMap').innerHTML =
                        '<div class="alert alert-warning m-3">Không tìm thấy vị trí trên bản đồ. Vui lòng thử lại sau.</div>';
                    document.getElementById('loadingPlaces').style.display = 'none';
                    document.getElementById('noPlacesFound').style.display = 'block';
                }
            });
        }

        // Hàm hiển thị bản đồ và tìm kiếm địa điểm gần đó
        function renderMapWithLocation(lat, lng) {
            const hotelPosition = {
                lat: parseFloat(lat),
                lng: parseFloat(lng)
            };

            const map = new google.maps.Map(document.getElementById('hotelMap'), {
                center: hotelPosition,
                zoom: 15,
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: true
            });

            const hotelMarker = new google.maps.Marker({
                position: hotelPosition,
                map: map,
                title: hotelData.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: '#3d7bb7',
                    fillOpacity: 1,
                    strokeColor: '#ffffff',
                    strokeWeight: 2,
                    scale: 10
                },
                animation: google.maps.Animation.DROP
            });

            const infowindow = new google.maps.InfoWindow({
                content: `<div class="p-2"><strong>${hotelData.name}</strong><br>${hotelData.address}</div>`
            });

            hotelMarker.addListener('click', function() {
                infowindow.open(map, hotelMarker);
            });

            infowindow.open(map, hotelMarker);

            searchNearbyPlaces(map, hotelPosition);
        }

        function searchNearbyPlaces(map, hotelPosition) {
            const service = new google.maps.places.PlacesService(map);

            document.getElementById('loadingPlaces').style.display = 'block';
            document.getElementById('noPlacesFound').style.display = 'none';
            document.getElementById('nearbyPlaces').innerHTML = '';

            const request = {
                location: hotelPosition,
                rankBy: google.maps.places.RankBy.DISTANCE, // Sắp xếp theo khoảng cách gần nhất
                keyword: 'tourist attraction', // Thêm từ khóa tìm kiếm chung
                fields: ['name', 'vicinity', 'rating', 'user_ratings_total', 'geometry', 'place_id', 'types']
            };




            console.time('Places API Request');

            service.nearbySearch(request, (results, status, pagination) => {
                console.timeEnd('Places API Request');

                // Log kết quả và trạng thái




                processSearchResults(results, status);
            });
        }

        // Xử lý kết quả tìm kiếm
        function processSearchResults(results, status) {
            const loadingElement = document.getElementById('loadingPlaces');
            const placesListElement = document.getElementById('nearbyPlaces');
            const noPlacesElement = document.getElementById('noPlacesFound');

            loadingElement.style.display = 'none';

            if (status === google.maps.places.PlacesServiceStatus.OK && results.length > 0) {
                const nearbyPlaces = results.slice(0, 5);

                placesListElement.innerHTML = '';

                nearbyPlaces.forEach(place => {
                    const distance = calculateDistance(
                        hotelData.latitude,
                        hotelData.longitude,
                        place.geometry.location.lat(),
                        place.geometry.location.lng()
                    );

                    const placeElement = createPlaceElement(place, distance);
                    placesListElement.appendChild(placeElement);
                });

                placesListElement.style.display = 'block';
            } else {
                placesListElement.style.display = 'none';
                noPlacesElement.style.display = 'block';
            }
        }

        // Tạo HTML hiển thị thông tin địa điểm
        function createPlaceElement(place, distance) {
            const li = document.createElement('li');
            li.className = 'list-group-item border-0 border-bottom py-2'; // Giảm padding từ py-3 thành py-2

            const rating = place.rating || 0;
            let starsHtml = '';
            for (let i = 0; i < 5; i++) {
                if (i < Math.floor(rating)) {
                    starsHtml += '<i class="bi bi-star-fill text-warning small"></i>'; // Thêm class small
                } else if (i < Math.ceil(rating) && rating % 1 !== 0) {
                    starsHtml += '<i class="bi bi-star-half text-warning small"></i>'; // Thêm class small
                } else {
                    starsHtml += '<i class="bi bi-star text-warning small"></i>'; // Thêm class small
                }
            }

            li.innerHTML = `
                <div class="d-flex align-items-start">
                    <div class="place-icon me-2 fs-5">  <!-- Giảm margin và kích thước icon -->
                        <i class="bi bi-geo-fill text-danger"></i>
                    </div>
                    <div class="place-info">
                        <h6 class="mb-0">${place.name}</h6>  <!-- Giảm từ h5 xuống h6 -->
                        <p class="mb-1 small text-muted">
                            <i class="bi bi-geo-alt"></i> ${place.vicinity || 'Không có địa chỉ'}
                        </p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="rating small">  <!-- Thêm class small -->
                                ${starsHtml}
                                <small class="ms-1 text-muted">(${place.user_ratings_total || 0})</small>
                            </div>
                            <div class="distance badge bg-light text-dark small">  <!-- Thêm class small -->
                                <i class="bi bi-signpost-2"></i> ${distance} km
                            </div>
                        </div>
                    </div>
                </div>
            `;

            return li;
        }

        // Tính khoảng cách giữa hai điểm (theo công thức Haversine)
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = deg2rad(lat2 - lat1);
            const dLon = deg2rad(lon2 - lon1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = R * c;
            return distance.toFixed(1);
        }

        function deg2rad(deg) {
            return deg * (Math.PI / 180);
        }
    </script>

    <!-- Tải Google Maps API với API key từ config -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&libraries=places&callback=initMap">
    </script>

    <style>
        .modern-header {
            position: relative;
        }

        .nearby-places .list-group-item {
            transition: all 0.2s ease;
            padding-top: 0.5rem;
            /* Giảm padding */
            padding-bottom: 0.5rem;
            /* Giảm padding */
        }

        .nearby-places .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .place-icon {
            width: 30px;
            /* Giảm kích thước từ 40px */
            text-align: center;
        }

        .place-info {
            width: calc(100% - 40px);
            /* Điều chỉnh chiều rộng theo place-icon mới */
        }

        /* Điều chỉnh thêm cho các địa điểm lân cận */
        .nearby-places h6 {
            font-size: 0.95rem;
            /* Giảm kích thước font */
            line-height: 1.3;
        }

        .nearby-places .rating {
            line-height: 1;
        }

        .nearby-places .distance {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Cập nhật chiều cao cho khu vực vị trí */
        #location .card {
            min-height: 400px;
        }

        @media (max-width: 768px) {
            #hotelMap {
                height: 300px;
            }

            #location .card {
                min-height: auto;
            }
        }
    </style>

    <!-- Reviews section - Layout mới toàn bộ chiều rộng và có bộ lọc -->
    <div id="reviews" class="section-container my-5">
        <h3 class="section-title mb-4">Đánh giá</h3>

        <!-- Review Summary Section - Thiết kế rút gọn -->
        <div class="review-summary mb-4">
            <div class="review-summary-container">
                <div class="review-summary-score">
                    <div class="rating-circle pulse-animation">
                        <span class="rating-number"><?php echo number_format($rating, 1); ?></span>
                        <span class="rating-max">/5</span>
                    </div>
                    <div class="d-flex justify-content-center mt-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star<?php echo ($i <= round($rating)) ? '-fill' : ''; ?> text-warning"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="review-count-text text-center">Dựa trên <?php echo isset($reviewCount) ? $reviewCount : 0; ?> đánh giá</p>
                </div>

                <div class="review-summary-distribution">
                    <h5 class="distribution-title">Phân bố đánh giá</h5>
                    <?php
                    // Mảng phân bố đánh giá
                    $ratingDistribution = isset($ratingDistribution) ? $ratingDistribution : [
                        5 => 65,
                        4 => 20,
                        3 => 10,
                        2 => 3,
                        1 => 2
                    ];

                    // Hiển thị thanh phân bố cho mỗi mức sao
                    for ($i = 5; $i >= 1; $i--):
                        $percentage = isset($ratingDistribution[$i]) ? $ratingDistribution[$i] : 0;
                    ?>
                        <div class="rating-bar">
                            <div class="d-flex align-items-center">
                                <div class="rating-label me-2">
                                    <?php echo $i; ?> <i class="bi bi-star-fill"></i>
                                </div>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%"
                                        aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="rating-percentage ms-2">
                                    <?php echo $percentage; ?>%
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Button viết đánh giá -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="text-center mt-3">
                    <button id="writeReviewBtn" class="btn btn-primary write-review-btn">
                        <i class="bi bi-pencil-square me-1"></i> Viết đánh giá
                    </button>
                </div>
            <?php else: ?>
                <div class="alert alert-info my-3">
                    <i class="bi bi-info-circle me-2"></i> Vui lòng <a href="<?php echo SITE_URL; ?>/login" class="alert-link">đăng nhập</a> để viết đánh giá.
                </div>
            <?php endif; ?>
        </div>

        <!-- Filter -->
        <div class="review-filter-sort">
            <div class="row w-100 align-items-center">
                <div class="col-md-7">
                    <div class="review-filters d-flex flex-wrap">
                        <div class="filter-label me-2">Lọc theo:</div>
                        <div class="filter-options">
                            <div class="star-filter" data-star="5">
                                5 <i class="bi bi-star-fill"></i>
                            </div>
                            <div class="star-filter" data-star="4">
                                4 <i class="bi bi-star-fill"></i>
                            </div>
                            <div class="star-filter" data-star="3">
                                3 <i class="bi bi-star-fill"></i>
                            </div>
                            <div class="star-filter" data-star="2">
                                2 <i class="bi bi-star-fill"></i>
                            </div>
                            <div class="star-filter" data-star="1">
                                1 <i class="bi bi-star-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="review-sorting d-flex justify-content-md-end justify-content-start mt-md-0 mt-3">
                        <div class="sort-label me-2">Sắp xếp theo:</div>
                        <div class="sort-options">
                            <select id="sortSelect" class="sort-select">
                                <option value="created_at">Thời gian</option>
                                <option value="rating">Điểm đánh giá</option>
                            </select>
                            <button id="sortDirectionBtn" class="sort-direction ms-2" aria-label="Thay đổi hướng sắp xếp">
                                <i class="bi bi-sort-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="reviewsContainer" class="reviews-container">
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tải đánh giá...</p>
            </div>
        </div>

        <!-- Nút tải thêm đánh giá -->
        <div class="text-center mt-4" id="loadMoreContainer" style="display: none;">
            <button class="btn btn-outline-primary load-more-btn" id="loadMoreBtn">
                <i class="bi bi-plus-circle"></i> Xem thêm đánh giá
            </button>
        </div>
    </div>

    <!-- Form viết đánh giá (Modal) -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div id="reviewFormModal" class="review-form-modal">
            <div class="review-form-container">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Viết đánh giá</h5>
                        <button type="button" class="btn-close btn-close-white" id="closeReviewForm" aria-label="Close"></button>
                    </div>
                    <div class="card-body p-4">
                        <form id="reviewForm" method="POST" action="<?php echo SITE_URL; ?>/api/reviews" enctype="multipart/form-data">
                            <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">

                            <div class="mb-4">
                                <label class="form-label fw-medium">Đánh giá tổng thể <span class="text-danger">*</span></label>
                                <div class="rating-input">
                                    <div class="stars">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo ($i == 5) ? 'checked' : ''; ?> required>
                                            <label for="star<?php echo $i; ?>" class="star-label"><i class="bi bi-star-fill"></i></label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="rating-text-indicator text-center mt-2">
                                    <span id="ratingText">Tuyệt vời</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="comment" class="form-label fw-medium">Nội dung đánh giá <span class="text-muted">(không bắt buộc)</span></label>
                                <div class="position-relative">
                                    <textarea class="form-control review-textarea" id="comment" name="comment" rows="4"
                                        placeholder="Chia sẻ trải nghiệm của bạn về khách sạn này"
                                        maxlength="500"></textarea>
                                    <div class="char-counter">
                                        <span id="charCount">0</span>/500
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="review_images" class="form-label fw-medium">Thêm hình ảnh <span class="text-muted">(không bắt buộc)</span></label>
                                <div class="image-upload-container">
                                    <div class="image-dropzone" id="imageDropzone">
                                        <i class="bi bi-cloud-arrow-up dropzone-icon"></i>
                                        <p class="mb-1">Kéo thả ảnh vào đây hoặc</p>
                                        <label for="review_images" class="btn btn-sm btn-outline-primary">Chọn ảnh</label>
                                        <input class="form-control" type="file" id="review_images" name="review_images[]" multiple accept="image/*">
                                    </div>
                                    <div class="form-text small">Tối đa 5 hình ảnh (JPG, PNG). Kích thước tối đa: 2MB/ảnh.</div>
                                </div>
                                <div class="image-preview mt-3 row g-2" id="imagePreviewContainer"></div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send"></i> Gửi đánh giá
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/public/css/hotel-reviews.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/public/css/hotel-detail.css">

    <script>
        window.currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
    </script>

    <!-- JavaScript files -->
    <script src="<?php echo SITE_URL; ?>/public/js/hotel-reviews.js"></script>
    <script src="<?php echo SITE_URL; ?>/public/js/hotel-detail.js"></script>
    <script src="<?php echo SITE_URL; ?>/public/js/cart.js" defer></script>
    <script src="<?php echo SITE_URL; ?>/public/js/room-booking.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Thêm CSS đơn giản cho navbar hiện đại
            const style = document.createElement('style');
            style.innerHTML = `
                #hotel-navbar {
                    box-shadow: 0 2px 10px rgba(0,0,0,0.05) !important;
                    padding: 0 !important;
                }
                #hotel-navbar .nav-link {
                    padding: 15px 20px;
                    color: #6c757d;
                    font-weight: 500;
                    transition: color 0.2s ease, border-bottom 0.3s ease;
                    position: relative;
                    border-bottom: 3px solid transparent;
                }
                #hotel-navbar .nav-link:hover {
                    color: #4285f4;
                }
                #hotel-navbar .nav-link.active {
                    color: #4285f4;
                    font-weight: 600;
                    border-bottom: 3px solid #4285f4;
                }
                #hotel-navbar .nav-link.active::after {
                    display: none; 
                }
                #hotel-navbar .nav-link.active i {
                    color: #4285f4;
                }
                #hotel-navbar .nav-link i {
                    margin-right: 6px;
                }
                
                /* Phiên bản mobile */
                @media (max-width: 768px) {
                    #hotel-navbar .nav-link {
                        padding: 10px 15px;
                        text-align: center;
                    }
                    #hotel-navbar .nav-link i {
                        display: block;
                        margin: 0 auto 5px;
                        font-size: 1.2rem;
                    }
                }
            `;
            document.head.appendChild(style);

            const sections = document.querySelectorAll('.section-container, #overview');
            const navItems = document.querySelectorAll('#hotel-navbar .nav-link');

            const navbarOffset = 100;

            // Cập nhật active nav item dựa trên vị trí cuộn
            function updateActiveNavItem() {
                let currentSection = null;

                sections.forEach(section => {
                    const sectionTop = section.getBoundingClientRect().top;
                    const sectionBottom = section.getBoundingClientRect().bottom;

                    if ((sectionTop <= navbarOffset && sectionBottom > navbarOffset) ||
                        (sectionTop > 0 && sectionTop < window.innerHeight / 3)) {
                        currentSection = section;
                    }
                });

                // Nếu không tìm thấy section nào trong viewport, lấy section gần nhất
                if (!currentSection && sections.length > 0) {
                    let closestDistance = Infinity;
                    let closestSection = null;

                    sections.forEach(section => {
                        const sectionTop = section.getBoundingClientRect().top;
                        const distance = Math.abs(sectionTop - navbarOffset);

                        if (distance < closestDistance) {
                            closestDistance = distance;
                            closestSection = section;
                        }
                    });

                    currentSection = closestSection;
                }

                // Cập nhật active nav item
                if (currentSection) {
                    const currentSectionId = currentSection.getAttribute('id');

                    navItems.forEach(navItem => {
                        navItem.classList.remove('active');

                        const href = navItem.getAttribute('href').substring(1);
                        if (href === currentSectionId) {
                            navItem.classList.add('active');

                            const navbarContainer = document.querySelector('#hotel-navbar .navbar-collapse');
                            if (navbarContainer) {
                                const containerRect = navbarContainer.getBoundingClientRect();
                                const itemRect = navItem.getBoundingClientRect();

                                if (itemRect.left < containerRect.left || itemRect.right > containerRect.right) {
                                    navItem.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'nearest',
                                        inline: 'center'
                                    });
                                }
                            }
                        }
                    });
                }
            }

            window.addEventListener('scroll', updateActiveNavItem);

            setTimeout(updateActiveNavItem, 100);

            navItems.forEach(navItem => {
                navItem.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);

                    navItems.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');

                    if (targetSection) {
                        window.scrollTo({
                            top: targetSection.offsetTop - 70, // Trừ chiều cao navbar
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo tooltips cho tất cả các phần tử có thuộc tính data-bs-toggle="tooltip"
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</div>

<!-- Thêm biểu tượng Messenger -->
<div class="hotel-messenger-icon">
    <div class="messenger-icon-container" onclick="location.reload()" data-bs-toggle="tooltip" data-bs-placement="left" title="Chat với nhân viên khách sạn">
        <i class="bi bi-chat-fill" id="messenger-icon"></i>
    </div>
</div>

<!-- All Rooms Modal -->
<div class="modal fade" id="allRoomsModal" tabindex="-1" aria-labelledby="allRoomsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allRoomsModalLabel">Tất cả phòng - <?php echo $hotel['name']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Search Form -->
            <div class="modal-search-form p-3 border-bottom">
                <form id="modalRoomSearchForm" class="row g-2">
                    <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">

                    <div class="col-md-3">
                        <label class="form-label small">Ngày nhận phòng</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            <input type="date" class="form-control" id="modalCheckInDate" name="check_in"
                                value="<?php echo !empty($check_in) ? htmlspecialchars($check_in) : date('Y-m-d', strtotime('+1 day')); ?>"
                                min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Ngày trả phòng</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            <input type="date" class="form-control" id="modalCheckOutDate" name="check_out"
                                value="<?php echo !empty($check_out) ? htmlspecialchars($check_out) : date('Y-m-d', strtotime('+2 days')); ?>"
                                min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Khách</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <select class="form-select" id="modalGuestCount" name="guests">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo (isset($_GET['guests']) && $_GET['guests'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Loại phòng</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-door-closed"></i></span>
                            <select class="form-select" id="modalRoomType" name="room_type">
                                <option value="">Tất cả</option>
                                <option value="single" <?php echo (isset($_GET['room_type']) && $_GET['room_type'] == 'single') ? 'selected' : ''; ?>>Phòng đơn</option>
                                <option value="double" <?php echo (isset($_GET['room_type']) && $_GET['room_type'] == 'double') ? 'selected' : ''; ?>>Phòng đôi</option>
                                <option value="suite" <?php echo (isset($_GET['room_type']) && $_GET['room_type'] == 'suite') ? 'selected' : ''; ?>>Suite</option>
                                <option value="family" <?php echo (isset($_GET['room_type']) && $_GET['room_type'] == 'family') ? 'selected' : ''; ?>>Phòng gia đình</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" id="searchModalRoomsBtn" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>

            <div class="modal-body">
                <div id="modalRoomsLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang tải danh sách phòng...</p>
                </div>

                <div id="modalRoomsList" class="room-list-container">
                </div>

                <div id="noRoomsFound" class="alert alert-info text-center" style="display: none;">
                    <i class="bi bi-info-circle me-2"></i> Không tìm thấy phòng phù hợp trong thời gian này. Vui lòng thử chọn ngày khác.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize variables
        const viewAllRoomsBtn = document.getElementById('viewAllRoomsBtn');
        const allRoomsModal = new bootstrap.Modal(document.getElementById('allRoomsModal'));
        const modalRoomSearchForm = document.getElementById('modalRoomSearchForm');
        const modalRoomsList = document.getElementById('modalRoomsList');
        const modalRoomsLoading = document.getElementById('modalRoomsLoading');
        const noRoomsFound = document.getElementById('noRoomsFound');

        viewAllRoomsBtn.addEventListener('click', function() {
            allRoomsModal.show();
            loadRooms();
        });

        modalRoomSearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadRooms();
        });

        function loadRooms() {
            modalRoomsLoading.style.display = 'block';
            modalRoomsList.style.display = 'none';
            noRoomsFound.style.display = 'none';

            const formData = new FormData(modalRoomSearchForm);
            formData.forEach((value, key) => {

            });
            fetch(`${SITE_URL}/hotels/getAvailableRooms`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {

                    // Hide loading indicator
                    modalRoomsLoading.style.display = 'none';

                    // Check if rooms were found
                    if (data.success && data.rooms && data.rooms.length > 0) {
                        // Display rooms
                        renderRooms(data.rooms);
                        modalRoomsList.style.display = 'block';
                    } else {
                        noRoomsFound.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading rooms:', error);
                    modalRoomsLoading.style.display = 'none';
                    noRoomsFound.textContent = 'Có lỗi xảy ra khi tải danh sách phòng. Vui lòng thử lại sau.';
                    noRoomsFound.style.display = 'block';
                });
            initRoomCheckboxes();
        }

        function renderRooms(rooms) {
            modalRoomsList.innerHTML = '';

            rooms.forEach(room => {
                let isSelected = false;

                if (window.cartManager && typeof window.cartManager.findRoomInCart === 'function') {
                    isSelected = window.cartManager.findRoomInCart(room.id) !== null;
                }

                const roomCard = createRoomCard(room, isSelected);
                modalRoomsList.appendChild(roomCard);
            });

            var tooltipTriggerList = [].slice.call(modalRoomsList.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            if (typeof window.initRoomCheckboxes === 'function') {
                window.initRoomCheckboxes();
            } else {
                console.error('initRoomCheckboxes is not available as a global function');
            }
        }

        function createRoomCard(room, isSelected = false) {
            const roomElement = document.createElement('div');
            roomElement.className = isSelected ? 'room-card selected' : 'room-card';
            roomElement.setAttribute('data-room-id', room.id);
            roomElement.setAttribute('data-room-price', room.price);
            roomElement.setAttribute('data-room-name', room.name);
            roomElement.setAttribute('data-room-type', room.room_type || 'standard');

            let typeBadgeClass = 'secondary';
            switch (room.room_type) {
                case 'single':
                    typeBadgeClass = 'info';
                    break;
                case 'double':
                    typeBadgeClass = 'primary';
                    break;
                case 'suite':
                    typeBadgeClass = 'success';
                    break;
                case 'family':
                    typeBadgeClass = 'warning';
                    break;
            }

            const roomTypeNames = {
                'single': 'Phòng đơn',
                'double': 'Phòng đôi',
                'suite': 'Suite',
                'family': 'Phòng gia đình'
            };
            const roomTypeName = roomTypeNames[room.room_type] || room.room_type;

            let amenitiesHtml = '';
            if (room.amenities && room.amenities.length > 0) {
                const maxAmenities = 5;
                const displayedCount = Math.min(maxAmenities, room.amenities.length);

                for (let i = 0; i < displayedCount; i++) {
                    const amenity = room.amenities[i];
                    amenitiesHtml += `
                        <span class="amenity-icon" data-bs-toggle="tooltip" title="${amenity.name}">
                            <i class="bi ${amenity.icon} ${amenity.is_premium ? 'premium' : ''}"></i>
                        </span>
                    `;
                }

                if (room.amenities.length > maxAmenities) {
                    amenitiesHtml += `
                        <span class="amenity-more" data-bs-toggle="tooltip" 
                            title="${room.amenities.length - maxAmenities} tiện nghi khác">
                            <i class="bi bi-plus-circle"></i>
                        </span>
                    `;
                }
            }

            roomElement.innerHTML = `
                <div class="row g-0">
                    <!-- Room Image -->
                    <div class="col-md-4">
                        <div class="room-image-container">
                            <img src="${SITE_URL}/public/images/rooms/${room.id}.jpg"
                                class="room-image" alt="${room.name}"
                                onerror="this.src='${SITE_URL}/public/images/rooms/default.jpg'">
                        </div>
                    </div>
                    <!-- Room Details -->
                    <div class="col-md-5">
                        <div class="room-details">
                            <h4 class="room-name">${room.name}</h4>

                            <div class="room-type-badge mb-2">
                                <span class="badge bg-${typeBadgeClass}">
                                    ${roomTypeName}
                                </span>

                                <!-- Room Area -->
                                ${room.area ? `
                                <span class="room-area ms-2">
                                    <i class="bi bi-rulers"></i> ${room.area} m²
                                </span>` : ''}

                                <!-- Max Occupancy -->
                                <span class="room-capacity ms-2">
                                    <i class="bi bi-people-fill"></i> Tối đa ${room.capacity} người
                                </span>
                            </div>

                            <!-- Room Amenities -->
                            <div class="room-amenities mb-3">
                                ${amenitiesHtml}
                            </div>

                            <!-- Room Description -->
                            <div class="room-description">
                                ${room.description ? (room.description.length > 120 ? 
                                    room.description.substring(0, 120) + '...' : 
                                    room.description) : ''}
                            </div>
                        </div>
                    </div>

                    <!-- Room Price and Selection -->
                    <div class="col-md-3">
                        <div class="room-price-container">
                            <div class="room-price">
                                <span class="price-value">${Number(room.price).toLocaleString('vi-VN')} VNĐ</span>
                            </div>

                            <div class="room-selection">
                                <div class="form-check">
                                    <input class="form-check-input room-checkbox" type="checkbox"
                                        id="modal-room-${room.id}" data-room-id="${room.id}" ${isSelected ? 'checked' : ''}>
                                    <label class="form-check-label select-room-label" for="modal-room-${room.id}">
                                        Thêm vào giỏ hàng
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            return roomElement;
        }

        const modalCheckInDate = document.getElementById('modalCheckInDate');
        const modalCheckOutDate = document.getElementById('modalCheckOutDate');

        modalCheckInDate.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            checkInDate.setDate(checkInDate.getDate() + 1);

            const minCheckOutDate = checkInDate.toISOString().split('T')[0];
            modalCheckOutDate.min = minCheckOutDate;

            if (modalCheckOutDate.value < minCheckOutDate) {
                modalCheckOutDate.value = minCheckOutDate;
            }
        });

        document.getElementById('checkInDate').addEventListener('change', function() {
            modalCheckInDate.value = this.value;
            modalCheckInDate.dispatchEvent(new Event('change'));
        });

        document.getElementById('checkOutDate').addEventListener('change', function() {
            modalCheckOutDate.value = this.value;
        });

        document.getElementById('guestCount').addEventListener('change', function() {
            document.getElementById('modalGuestCount').value = this.value;
        });

        document.getElementById('roomType').addEventListener('change', function() {
            document.getElementById('modalRoomType').value = this.value;
        });
    });
</script>

<style>
    #allRoomsModal .modal-xl {
        max-width: 90%;
    }

    #allRoomsModal .modal-search-form {
        background-color: #f8f9fa;
    }

    #modalRoomsList .room-card {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 16px;
        padding: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    #modalRoomsList .room-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    #modalRoomsLoading {
        padding: 40px 0;
    }

    #allRoomsModal .modal-body {
        padding-top: 10px;
    }

    @media (max-width: 768px) {
        #allRoomsModal .modal-xl {
            max-width: 100%;
            margin: 10px;
        }
    }
</style>

<!-- Thêm script khởi tạo tooltip cho giỏ hàng -->
<script>
    window.addEventListener('load', function() {
        const cartLink = document.getElementById('cart-link');
        if (cartLink) {
            cartLink.setAttribute('data-bs-toggle', 'tooltip');
            cartLink.setAttribute('data-bs-placement', 'left');
            cartLink.setAttribute('title', 'Xem giỏ hàng của bạn');

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });
</script>

<!-- Thêm modal hiển thị thông tin chi tiết của phòng nếu cần -->
<div class="modal fade" id="roomDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomDetailsTitle">Chi tiết phòng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="roomDetailsContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>