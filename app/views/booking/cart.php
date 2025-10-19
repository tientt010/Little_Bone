<div class="container py-5">
    <h1 class="mb-4">Giỏ hàng của bạn</h1>

    <!-- Loading -->
    <div id="cartLoading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-3">Đang tải thông tin giỏ hàng...</p>
    </div>

    <!-- Giỏ hàng trống -->
    <div id="emptyCartMessage" class="alert alert-info text-center py-5" style="display: none;">
        <i class="bi bi-cart-x fs-1 d-block mb-3"></i>
        <h4>Giỏ hàng của bạn đang trống</h4>
        <p>Hãy thêm phòng từ các khách sạn để tiếp tục đặt phòng.</p>
        <a href="<?php echo SITE_URL; ?>/hotels" class="btn btn-primary mt-3">
            <i class="bi bi-search"></i> Tìm khách sạn
        </a>
    </div>

    <!-- Cart content -->
    <div id="cartContent" style="display: none;">
        <div id="hotelsContainer" class="mb-4">
        </div>

        <!-- Tóm tắt đặt phòng -->
        <div class="card booking-summary shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="bi bi-receipt me-2"></i> Tóm tắt đặt phòng
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <div class="price-summary">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tổng phòng:</span>
                                <span id="totalRooms" class="fw-bold">0 phòng</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tổng tiền phòng:</span>
                                <span id="totalRoomPrice">0 VNĐ</span>
                            </div>
                            <!-- Thêm dòng hiển thị tổng tiền giảm giá -->
                            <div class="d-flex justify-content-between mb-3 discount-row" id="discountRow" style="display: none;">
                                <span>Giảm giá:</span>
                                <span id="totalDiscount" class="text-success">-0 VNĐ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Thuế và phí (10%):</span>
                                <span id="taxesFees">0 VNĐ</span>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between mb-0 fw-bold">
                                <span class="fs-5">Tổng thanh toán:</span>
                                <span id="finalTotal" class="text-primary fs-5">0 VNĐ</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="payment-info bg-light p-3 rounded">
                            <p class="small mb-2"><i class="bi bi-info-circle me-1"></i> Thanh toán an toàn và bảo mật</p>
                            <div class="payment-methods mb-3">
                                <i class="bi bi-credit-card me-2 fs-4"></i>
                                <i class="bi bi-paypal me-2 fs-4"></i>
                                <i class="bi bi-bank me-2 fs-4"></i>
                            </div>
                            <button id="checkoutButton" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-credit-card me-1"></i> Tiến hành thanh toán
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template cho mỗi khách sạn -->
<template id="hotelItemTemplate">
    <div class="hotel-booking-section card shadow-sm mb-4" data-hotel-id="">
        <div class="card-header bg-white py-3 d-flex align-items-center">
            <img src="" class="hotel-thumbnail me-3" alt="Hotel thumbnail"
                onerror="this.src='<?php echo SITE_URL; ?>/public/images/hotels/default.jpg'">
            <div>
                <h5 class="mb-0 hotel-name"></h5>
                <div class="hotel-address small text-muted"></div>
            </div>
        </div>
        <div class="row g-0">
            <div class="col-lg-9">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 240px;">Phòng</th>
                                    <th class="text-center" style="width: 100px;">Giá</th>
                                    <th class="text-center">Thời gian</th>
                                    <th class="text-end">Tổng</th>
                                    <th class="text-center" style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody class="room-items-container">
                                <!-- Room items -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- promotions -->
            <div class="col-lg-3 bg-light p-0 d-flex flex-column">
                <div class="promotions-sidebar p-3 border-bottom">
                    <div class="promotions-header mb-2">
                        <h6 class="mb-0"><i class="bi bi-tag-fill me-1 text-danger"></i>Ưu đãi</h6>
                    </div>
                    <div class="hotel-promotions" data-hotel-id="">
                        <div class="promotion-list mb-3">
                            <!-- Promotions -->
                        </div>
                        <div class="no-promotions text-center py-2 d-none">
                            <p class="text-muted mb-0 small">Không có ưu đãi</p>
                        </div>
                        <div class="promotions-loading text-center py-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <p class="text-muted mb-0 small">Đang tải...</p>
                        </div>
                    </div>
                </div>

                <!-- Tóm tắt giá -->
                <div class="p-3 flex-grow-1">
                    <h6 class="mb-3"><i class="bi bi-calculator me-2"></i>Tóm tắt giá</h6>
                    <div class="hotel-price-summary">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Tổng tiền phòng:</span>
                            <span class="hotel-total-price-before-discount fw-bold">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 hotel-discount-row" style="display: none;">
                            <span>Ưu đãi:</span>
                            <span class="hotel-discount text-success">-0 VNĐ</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Phải thanh toán:</span>
                            <span class="hotel-total-price fw-bold">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Template cho phòng -->
<template id="roomItemTemplate">
    <tr class="room-item" data-room-id="">
        <td>
            <div class="d-flex align-items-center">
                <img src="" class="room-thumbnail me-3 rounded" alt="Room thumbnail"
                    onerror="this.src='<?php echo SITE_URL; ?>/public/images/rooms/default.jpg'">
                <div>
                    <h6 class="mb-1 room-name"></h6>
                    <div class="room-type small text-muted"></div>
                </div>
            </div>
        </td>
        <td class="text-center room-price"></td>
        <td class="text-center">
            <div class="room-dates" data-room-id="" data-price="">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="dates-display small">
                        <input type="date" class="form-control form-control-sm check-in-date" placeholder="Nhận phòng">
                        <div class="date-separator text-center my-1">→</div>
                        <input type="date" class="form-control form-control-sm check-out-date" placeholder="Trả phòng">
                    </div>
                </div>
                <div class="nights-display text-center mt-1">
                    <span class="nights-count badge bg-light text-dark">1 đêm</span>
                </div>
            </div>
        </td>
        <td class="text-end room-total" data-room-id=""></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-room-btn">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<!-- Template cho mỗi ưu đãi-->
<template id="promotionItemTemplate">
    <div class="promotion-card mb-2">
        <div class="d-flex align-items-center">
            <div class="promotion-info flex-grow-1">
                <div class="mb-1">
                    <span class="promotion-title fw-bold small d-block"></span>
                    <div class="d-flex align-items-center mt-1">
                        <span class="discount-badge me-2"></span>
                        <span class="promotion-code small"></span>
                    </div>
                </div>
                <p class="promotion-description small text-muted mb-0 d-none"></p>
            </div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof CartManager === 'undefined') {
            console.error('CartManager chưa được tải!');
            return;
        }

        if (!window.cartManager) {
            window.cartManager = new CartManager();
        }

        renderCart();

        function renderCart() {
            // Lấy dữ liệu từ CartManager
            const cart = window.cartManager.getCart();


            setTimeout(() => {
                document.getElementById('cartLoading').style.display = 'none';

                // Kiểm tra giỏ hàng có trống không
                if (!cart.hotels || Object.keys(cart.hotels).length === 0) {
                    document.getElementById('emptyCartMessage').style.display = 'block';
                    return;
                }

                document.getElementById('cartContent').style.display = 'block';

                // Render từng khách sạn
                const hotelsContainer = document.getElementById('hotelsContainer');
                hotelsContainer.innerHTML = '';

                let grandTotal = 0;
                let grandDiscount = 0;
                let originalTotal = 0;
                Object.values(cart.hotels).forEach(hotel => {
                    const hotelElement = renderHotelItem(hotel);
                    hotelsContainer.appendChild(hotelElement);

                    // Tính tổng tiền của khách sạn
                    let hotelTotal = 0;
                    Object.values(hotel.rooms).forEach(room => {
                        const roomNights = room.nights || calculateNights(room.checkInDate, room.checkOutDate);
                        const roomPrice = room.price * room.quantity * roomNights;
                        hotelTotal += roomPrice;
                    });

                    const hotelSection = hotelsContainer.querySelector(`.hotel-booking-section[data-hotel-id="${hotel.id}"]`);

                    // Cập nhật tổng tiền khách sạn trước khi áp dụng ưu đãi
                    const hotelTotalBeforeDiscountElement = hotelSection.querySelector('.hotel-total-price-before-discount');
                    if (hotelTotalBeforeDiscountElement) {
                        hotelTotalBeforeDiscountElement.textContent = formatCurrency(hotelTotal);
                    }
                    originalTotal += hotelTotal;

                    fetchHotelPromotions(hotel.id, hotelSection)
                        .then(promotions => {
                            // Tính tổng giảm giá từ tất cả các ưu đãi thay vì chỉ áp dụng ưu đãi tốt nhất
                            let totalDiscountAmount = 0;
                            let appliedPromotions = [];

                            if (promotions && promotions.length > 0) {
                                // Áp dụng tất cả các ưu đãi
                                promotions.forEach(promotion => {
                                    const discountAmount = calculateDiscount(promotion, hotelTotal);
                                    totalDiscountAmount += discountAmount;

                                    if (discountAmount > 0) {
                                        appliedPromotions.push({
                                            ...promotion,
                                            appliedAmount: discountAmount
                                        });
                                    }
                                });

                                grandDiscount += totalDiscountAmount;

                                // Hiển thị thông tin giảm giá
                                const hotelDiscountRow = hotelSection.querySelector('.hotel-discount-row');
                                const hotelDiscount = hotelSection.querySelector('.hotel-discount');

                                if (hotelDiscountRow && hotelDiscount && totalDiscountAmount > 0) {
                                    hotelDiscountRow.style.display = 'flex';
                                    hotelDiscount.textContent = `-${formatCurrency(totalDiscountAmount)}`;

                                    if (appliedPromotions.length > 0) {
                                        let tooltipContent = 'Các ưu đãi đã áp dụng:<br>';
                                        appliedPromotions.forEach(p => {
                                            tooltipContent += `- ${p.title || p.code}: ${formatCurrency(p.appliedAmount)}<br>`;
                                        });

                                        hotelDiscount.setAttribute('title', tooltipContent);
                                        hotelDiscount.setAttribute('data-bs-toggle', 'tooltip');
                                        hotelDiscount.setAttribute('data-bs-html', 'true');
                                    }
                                }
                            }

                            // Tính giá sau khi giảm
                            const finalHotelTotal = hotelTotal - totalDiscountAmount;
                            const hotelTotalElement = hotelSection.querySelector('.hotel-total-price');
                            if (hotelTotalElement) {
                                hotelTotalElement.textContent = formatCurrency(finalHotelTotal);
                            }

                            grandTotal += finalHotelTotal;

                            // Hiển thị tổng giảm giá nếu có
                            const discountRow = document.getElementById('discountRow');
                            const totalDiscount = document.getElementById('totalDiscount');
                            if (discountRow && totalDiscount && grandDiscount > 0) {
                                discountRow.style.display = 'flex';
                                totalDiscount.textContent = `-${formatCurrency(grandDiscount)}`;
                            }

                            updateCartSummary(cart, grandTotal, grandDiscount, originalTotal);

                            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                                tooltips.forEach(el => new bootstrap.Tooltip(el));
                            }
                        });
                });

                // Gắn sự kiện cho nút thanh toán
                document.getElementById('checkoutButton').addEventListener('click', proceedToCheckout);
            }, 500);
        }

        function renderHotelItem(hotel) {
            const template = document.getElementById('hotelItemTemplate');
            const hotelElement = template.content.cloneNode(true);

            // Cập nhật thông tin khách sạn
            const hotelSection = hotelElement.querySelector('.hotel-booking-section');
            hotelSection.dataset.hotelId = hotel.id;

            const hotelImg = hotelElement.querySelector('.hotel-thumbnail');
            hotelImg.src = `<?php echo SITE_URL; ?>/public/images/hotels/${hotel.id}.jpg`;
            hotelImg.alt = hotel.name;

            hotelElement.querySelector('.hotel-name').textContent = hotel.name;
            hotelElement.querySelector('.hotel-address').textContent = hotel.address;

            // Render từng phòng của khách sạn
            const roomsContainer = hotelElement.querySelector('.room-items-container');

            Object.values(hotel.rooms).forEach(room => {
                renderRoomItem(room, roomsContainer, hotel.id);
            });

            // Cập nhật phần promotions
            const promotionsSection = hotelElement.querySelector('.hotel-promotions');
            promotionsSection.dataset.hotelId = hotel.id;

            return hotelElement;
        }

        function renderRoomItem(room, container, hotelId) {
            const template = document.getElementById('roomItemTemplate');
            const roomElement = template.content.cloneNode(true);

            // Cập nhật thông tin phòng
            const roomItem = roomElement.querySelector('.room-item');
            roomItem.dataset.roomId = room.id;
            roomItem.dataset.hotelId = hotelId;

            const roomImg = roomElement.querySelector('.room-thumbnail');
            roomImg.src = `<?php echo SITE_URL; ?>/public/images/rooms/${room.id}.jpg`;
            roomImg.alt = room.name;

            roomElement.querySelector('.room-name').textContent = room.name;

            // Xác định và hiển thị loại phòng
            const roomTypeNames = {
                'single': 'Phòng đơn',
                'double': 'Phòng đôi',
                'suite': 'Suite',
                'family': 'Phòng gia đình'
            };
            roomElement.querySelector('.room-type').textContent = roomTypeNames[room.type] || room.type || 'Phòng tiêu chuẩn';
            roomElement.querySelector('.room-price').textContent = formatCurrency(room.price);

            // Cập nhật ngày nhận/trả phòng
            const roomDates = roomElement.querySelector('.room-dates');
            roomDates.dataset.roomId = room.id;
            roomDates.dataset.price = room.price;
            roomDates.dataset.hotelId = hotelId;

            const checkInInput = roomDates.querySelector('.check-in-date');
            const checkOutInput = roomDates.querySelector('.check-out-date');

            checkInInput.value = room.checkInDate || '';
            checkOutInput.value = room.checkOutDate || '';

            // Hiển thị số đêm
            const nights = room.nights || calculateNights(room.checkInDate, room.checkOutDate);
            roomDates.querySelector('.nights-count').textContent = `${nights} đêm`;

            // Cập nhật tổng giá phòng (giá * số lượng * số đêm)
            const roomTotal = roomElement.querySelector('.room-total');
            const totalPrice = room.price * room.quantity * nights;
            roomTotal.textContent = formatCurrency(totalPrice);
            roomTotal.dataset.roomId = room.id;
            roomTotal.dataset.originalPrice = totalPrice;
            setupDateEvents(roomElement, room, hotelId);
            setupRemoveButton(roomElement, room, hotelId);
            container.appendChild(roomElement);
        }

        function setupDateEvents(element, room, hotelId) {
            const roomDates = element.querySelector('.room-dates');
            const checkInInput = element.querySelector('.check-in-date');
            const checkOutInput = element.querySelector('.check-out-date');
            const roomDatesDisplay = element.querySelector('.room-dates');
            const nightsDisplay = roomDates ? roomDates.querySelector('.nights-count') : null;

            function updateDates() {
                if (checkInInput && checkOutInput && checkInInput.value && checkOutInput.value) {
                    const checkIn = new Date(checkInInput.value);
                    const checkOut = new Date(checkOutInput.value);

                    // Kiểm tra ngày hợp lệ
                    if (checkOut <= checkIn) {
                        const nextDay = new Date(checkIn);
                        nextDay.setDate(nextDay.getDate() + 1);
                        checkOutInput.value = nextDay.toISOString().split('T')[0];
                    }

                    // Cập nhật hiển thị số đêm
                    const nights = calculateNights(checkInInput.value, checkOutInput.value);

                    if (nightsDisplay) {
                        nightsDisplay.textContent = `${nights} đêm`;
                        nightsDisplay.classList.add('updated');
                        setTimeout(() => {
                            if (nightsDisplay) {
                                nightsDisplay.classList.remove('updated');
                            }
                        }, 1000);
                    }

                    // Cập nhật giỏ hàng
                    if (window.cartManager) {
                        window.cartManager.updateRoomDates(hotelId, room.id, checkInInput.value, checkOutInput.value);

                        // Cập nhật tổng giá phòng
                        const roomId = room.id;
                        const roomTotal = document.querySelector(`.room-total[data-room-id="${roomId}"]`);
                        const roomPrice = parseFloat(roomDates ? roomDates.dataset.price : 0);
                        const quantity = 1;
                        const totalPrice = roomPrice * quantity * nights;
                        if (roomTotal) {
                            // Hiệu ứng khi thay đổi giá
                            roomTotal.classList.add('value-changed');
                            setTimeout(() => {
                                if (roomTotal) {
                                    roomTotal.classList.remove('value-changed');
                                }
                            }, 1500);

                            roomTotal.textContent = formatCurrency(totalPrice);
                            roomTotal.dataset.originalPrice = totalPrice; // Cập nhật giá gốc
                        } else {

                        }

                        updateHotelAndCartTotals();
                    }
                }
            }
            if (checkInInput) {
                checkInInput.addEventListener('change', updateDates);
            }

            if (checkOutInput) {
                checkOutInput.addEventListener('change', updateDates);
            }

            function updateHotelAndCartTotals() {
                // Lấy thông tin về khách sạn và tất cả các phòng trong khách sạn
                let hotelSection;

                if (!hotelSection && roomDates && roomDates.dataset && roomDates.dataset.hotelId) {
                    hotelSection = document.querySelector(`.hotel-booking-section[data-hotel-id="${roomDates.dataset.hotelId}"]`);
                }

                if (!hotelSection) {
                    console.warn('Không tìm thấy phần tử khách sạn để cập nhật giá');
                    return;
                }

                const hotelId = hotelSection.dataset.hotelId;
                const roomRows = hotelSection.querySelectorAll('.room-item');

                // Tính tổng tiền cho khách sạn này
                let hotelTotal = 0;
                roomRows.forEach(roomRow => {
                    const roomTotalElement = roomRow.querySelector('.room-total');
                    if (roomTotalElement && roomTotalElement.dataset.originalPrice) {
                        const roomPrice = parseFloat(roomTotalElement.dataset.originalPrice);
                        hotelTotal += roomPrice;
                    }
                });

                // Cập nhật tổng tiền khách sạn trước khi áp dụng ưu đãi
                const hotelTotalBeforeDiscountElement = hotelSection.querySelector('.hotel-total-price-before-discount');
                if (hotelTotalBeforeDiscountElement) {
                    hotelTotalBeforeDiscountElement.textContent = formatCurrency(hotelTotal);
                    hotelTotalBeforeDiscountElement.classList.add('value-changed');
                    setTimeout(() => {
                        if (hotelTotalBeforeDiscountElement) {
                            hotelTotalBeforeDiscountElement.classList.remove('value-changed');
                        }
                    }, 1500);
                }

                // Lấy danh sách ưu đãi và áp dụng tất cả
                fetch(`<?php echo SITE_URL; ?>/api/hotels/${hotelId}/promotions`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.success && data.promotions && data.promotions.length > 0) {
                            // Tính tổng giảm giá từ tất cả các ưu đãi
                            let totalDiscountAmount = 0;
                            let appliedPromotions = [];

                            data.promotions.forEach(promotion => {
                                const discountAmount = calculateDiscount(promotion, hotelTotal);
                                totalDiscountAmount += discountAmount;

                                // Thêm vào danh sách ưu đãi đã áp dụng
                                if (discountAmount > 0) {
                                    appliedPromotions.push({
                                        ...promotion,
                                        appliedAmount: discountAmount
                                    });
                                }
                            });

                            // Hiển thị thông tin giảm giá
                            const hotelDiscountRow = hotelSection.querySelector('.hotel-discount-row');
                            const hotelDiscount = hotelSection.querySelector('.hotel-discount');

                            if (hotelDiscountRow && hotelDiscount && totalDiscountAmount > 0) {
                                hotelDiscountRow.style.display = 'flex';
                                hotelDiscount.textContent = `-${formatCurrency(totalDiscountAmount)}`;

                                // Thêm tooltip hiển thị chi tiết các ưu đãi đã áp dụng
                                if (appliedPromotions.length > 0) {
                                    let tooltipContent = 'Các ưu đãi đã áp dụng:<br>';
                                    appliedPromotions.forEach(p => {
                                        tooltipContent += `- ${p.title || p.code}: ${formatCurrency(p.appliedAmount)}<br>`;
                                    });

                                    hotelDiscount.setAttribute('title', tooltipContent);
                                    hotelDiscount.setAttribute('data-bs-toggle', 'tooltip');
                                    hotelDiscount.setAttribute('data-bs-html', 'true');
                                }
                            } else if (hotelDiscountRow) {
                                hotelDiscountRow.style.display = 'none';
                            }

                            // Cập nhật tổng thanh toán cho khách sạn
                            const hotelFinalTotal = hotelTotal - totalDiscountAmount;
                            const hotelTotalElement = hotelSection.querySelector('.hotel-total-price');
                            if (hotelTotalElement) {
                                hotelTotalElement.textContent = formatCurrency(hotelFinalTotal);
                                hotelTotalElement.classList.add('value-changed');
                                setTimeout(() => {
                                    if (hotelTotalElement) {
                                        hotelTotalElement.classList.remove('value-changed');
                                    }
                                }, 1500);
                            }

                            // Khởi tạo tooltips sau khi cập nhật DOM
                            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                                const tooltips = hotelSection.querySelectorAll('[data-bs-toggle="tooltip"]');
                                tooltips.forEach(el => new bootstrap.Tooltip(el));
                            }
                        } else {
                            // Không có ưu đãi, xóa hiển thị giảm giá nếu có
                            const hotelDiscountRow = hotelSection.querySelector('.hotel-discount-row');
                            if (hotelDiscountRow) {
                                hotelDiscountRow.style.display = 'none';
                            }

                            const hotelTotalElement = hotelSection.querySelector('.hotel-total-price');
                            if (hotelTotalElement) {
                                hotelTotalElement.textContent = formatCurrency(hotelTotal);
                                hotelTotalElement.classList.add('value-changed');
                                setTimeout(() => {
                                    if (hotelTotalElement) {
                                        hotelTotalElement.classList.remove('value-changed');
                                    }
                                }, 1500);
                            }
                        }

                        updateCartGrandTotal();
                    })
                    .catch(error => {
                        console.error('Error fetching promotions:', error);
                        updateCartGrandTotal();
                    });
            }

            // Hàm mới để cập nhật tổng thể cho giỏ hàng
            function updateCartGrandTotal() {
                // Tính toán lại tất cả các tổng
                let originalTotal = 0;
                let grandTotal = 0;
                let grandDiscount = 0;
                let totalRoomCount = 0;

                // Lấy tất cả các khách sạn
                const hotelSections = document.querySelectorAll('.hotel-booking-section');

                hotelSections.forEach(hotelSection => {
                    // Đếm số lượng phòng
                    const roomItems = hotelSection.querySelectorAll('.room-item');
                    totalRoomCount += roomItems.length;

                    // Tổng giá trước giảm giá
                    const hotelTotalBeforeElement = hotelSection.querySelector('.hotel-total-price-before-discount');
                    if (hotelTotalBeforeElement) {
                        originalTotal += parseNumberFromCurrency(hotelTotalBeforeElement.textContent);
                    }

                    // Số tiền giảm giá
                    const hotelDiscountElement = hotelSection.querySelector('.hotel-discount');
                    if (hotelDiscountElement && hotelDiscountElement.textContent) {
                        grandDiscount += parseNumberFromCurrency(hotelDiscountElement.textContent);
                    }

                    // Tổng thanh toán sau giảm giá
                    const hotelFinalTotalElement = hotelSection.querySelector('.hotel-total-price');
                    if (hotelFinalTotalElement) {
                        grandTotal += parseNumberFromCurrency(hotelFinalTotalElement.textContent);
                    }
                });

                // Cập nhật hiển thị tổng số phòng
                const totalRoomsElement = document.getElementById('totalRooms');
                if (totalRoomsElement) {
                    totalRoomsElement.textContent = `${totalRoomCount} phòng`;
                }

                // Cập nhật tổng giá trước giảm giá
                const totalRoomPriceElement = document.getElementById('totalRoomPrice');
                if (totalRoomPriceElement) {
                    totalRoomPriceElement.textContent = formatCurrency(originalTotal);
                    totalRoomPriceElement.classList.add('value-changed');
                    setTimeout(() => {
                        if (totalRoomPriceElement) {
                            totalRoomPriceElement.classList.remove('value-changed');
                        }
                    }, 1500);
                }

                // Hiển thị tổng giảm giá nếu có
                const discountRowElement = document.getElementById('discountRow');
                const totalDiscountElement = document.getElementById('totalDiscount');
                if (discountRowElement && totalDiscountElement) {
                    if (grandDiscount > 0) {
                        discountRowElement.style.display = 'flex';
                        totalDiscountElement.textContent = `-${formatCurrency(Math.abs(grandDiscount))}`;
                        totalDiscountElement.classList.add('value-changed');
                        setTimeout(() => {
                            if (totalDiscountElement) {
                                totalDiscountElement.classList.remove('value-changed');
                            }
                        }, 1500);
                    } else {
                        discountRowElement.style.display = 'none';
                    }
                }

                // Tính thuế và phí (10% của giá sau giảm giá)
                const taxes = grandTotal * 0.1;
                const taxesFeesElement = document.getElementById('taxesFees');
                if (taxesFeesElement) {
                    taxesFeesElement.textContent = formatCurrency(taxes);
                    taxesFeesElement.classList.add('value-changed');
                    setTimeout(() => {
                        if (taxesFeesElement) {
                            taxesFeesElement.classList.remove('value-changed');
                        }
                    }, 1500);
                }

                // Tính tổng thanh toán
                const finalTotal = grandTotal + taxes;
                const finalTotalElement = document.getElementById('finalTotal');
                if (finalTotalElement) {
                    finalTotalElement.textContent = formatCurrency(finalTotal);
                    finalTotalElement.classList.add('value-changed');
                    setTimeout(() => {
                        if (finalTotalElement) {
                            finalTotalElement.classList.remove('value-changed');
                        }
                    }, 1500);
                }
            }
        }

        function setupRemoveButton(element, room, hotelId) {
            const removeBtn = element.querySelector('.remove-room-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    if (window.cartManager) {
                        window.cartManager.removeFromCart(hotelId, room.id);
                        const roomElement = removeBtn.closest('.room-item');
                        if (roomElement) {
                            roomElement.classList.add('removing');
                            setTimeout(() => {
                                renderCart();
                            }, 300);
                        } else {
                            renderCart();
                        }
                    }
                });
            }
        }

        async function fetchHotelPromotions(hotelId, hotelSection) {


            const promotionSection = hotelSection.querySelector('.hotel-promotions');
            if (!promotionSection) return [];

            const promotionList = promotionSection.querySelector('.promotion-list');
            const noPromotionsMsg = promotionSection.querySelector('.no-promotions');
            const loadingMsg = promotionSection.querySelector('.promotions-loading');

            loadingMsg.classList.remove('d-none');
            noPromotionsMsg.classList.add('d-none');
            promotionList.innerHTML = '';

            const timestamp = new Date().getTime();

            try {
                // Gọi API để lấy danh sách ưu đãi của khách sạn
                const response = await fetch(`<?php echo SITE_URL; ?>/api/hotels/${hotelId}/promotions?_=${timestamp}`);

                if (!response.ok) {
                    throw new Error(`API response error: ${response.status}`);
                }

                const data = await response.json();
                loadingMsg.classList.add('d-none');

                // Xử lý dữ liệu ưu đãi
                if (data && data.success && data.promotions && data.promotions.length > 0) {
                    data.promotions.forEach(promo => {
                        const promotionItem = createPromotionElement(promo);
                        promotionList.appendChild(promotionItem);
                    });
                    return data.promotions;
                } else {
                    noPromotionsMsg.classList.remove('d-none');
                    return [];
                }
            } catch (error) {
                console.error('Error fetching promotions:', error);

                // Ẩn loading và hiển thị thông báo lỗi
                loadingMsg.classList.add('d-none');
                noPromotionsMsg.classList.remove('d-none');
                noPromotionsMsg.querySelector('p').textContent = 'Không thể tải ưu đãi. Vui lòng thử lại sau.';

                return [];
            }
        }

        function createPromotionElement(promo) {
            const template = document.getElementById('promotionItemTemplate');
            const promotionElement = template.content.cloneNode(true);

            promotionElement.querySelector('.promotion-title').textContent = promo.title || 'Ưu đãi đặc biệt';

            const discountBadge = promotionElement.querySelector('.discount-badge');
            if (promo.discount_type === 'percentage') {
                discountBadge.textContent = `-${promo.discount_value}%`;
                discountBadge.classList.add('text-success');
            } else {
                discountBadge.textContent = `-${formatCurrency(promo.discount_value)}`;
                discountBadge.classList.add('text-success');
            }

            promotionElement.querySelector('.promotion-code').textContent = promo.code;

            return promotionElement;
        }


        // Tính toán giảm giá
        function calculateDiscount(promotion, amount) {
            if (!promotion) return 0;

            let discountAmount = 0;

            if (promotion.discount_type === 'percentage') {
                discountAmount = (amount * promotion.discount_value) / 100;
            } else {
                discountAmount = promotion.discount_value;
            }

            return Math.min(discountAmount, amount);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }

        function calculateNights(checkInDate, checkOutDate) {
            if (!checkInDate || !checkOutDate) return 1;

            const checkIn = new Date(checkInDate);
            const checkOut = new Date(checkOutDate);

            checkIn.setHours(0, 0, 0, 0);
            checkOut.setHours(0, 0, 0, 0);

            const timeDiff = checkOut.getTime() - checkIn.getTime();
            const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
            return nights > 0 ? nights : 1;
        }

        function updateCartSummary(cart, grandTotal, totalDiscount, originalTotal) {
            // Hiển thị tổng số phòng
            document.getElementById('totalRooms').textContent = `${cart.totalCount} phòng`;

            // Hiển thị tổng giá trước giảm giá
            document.getElementById('totalRoomPrice').textContent = formatCurrency(originalTotal);
            document.getElementById('totalRoomPrice').classList.add('value-changed');
            setTimeout(() => document.getElementById('totalRoomPrice').classList.remove('value-changed'), 1500);

            // Tính thuế và phí (10% của giá sau giảm giá)
            const taxes = grandTotal * 0.1;
            document.getElementById('taxesFees').textContent = formatCurrency(taxes);
            document.getElementById('taxesFees').classList.add('value-changed');
            setTimeout(() => document.getElementById('taxesFees').classList.remove('value-changed'), 1500);

            // Tính tổng thanh toán
            const finalTotal = grandTotal + taxes;
            document.getElementById('finalTotal').textContent = formatCurrency(finalTotal);
            document.getElementById('finalTotal').classList.add('value-changed');
            setTimeout(() => document.getElementById('finalTotal').classList.remove('value-changed'), 1500);
        }

        function proceedToCheckout() {
            // Tạo form động để submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo SITE_URL; ?>/booking/checkout';

            const cart = window.cartManager.getCart();

            // Tính toán tổng hợp cho giỏ hàng
            let summaryData = {
                hotels: cart.hotels,
                totalCount: cart.totalCount || 0,
                originalTotal: 0,
                totalDiscount: 0,
                grandTotal: 0,
                taxes: 0,
                finalTotal: 0
            };
            // Tổng tiền gốc
            const originalPriceElement = document.getElementById('totalRoomPrice');
            summaryData.originalTotal = parseNumberFromCurrency(originalPriceElement.textContent);

            // Tổng giảm giá
            const totalDiscountElement = document.getElementById('totalDiscount');
            if (totalDiscountElement && document.getElementById('discountRow').style.display !== 'none') {
                summaryData.totalDiscount = Math.abs(parseNumberFromCurrency(totalDiscountElement.textContent));
            }

            // Tổng thanh toán trước thuế
            summaryData.grandTotal = summaryData.originalTotal - summaryData.totalDiscount;

            // Thuế (10%)
            const taxesElement = document.getElementById('taxesFees');
            summaryData.taxes = parseNumberFromCurrency(taxesElement.textContent);

            // Tổng thanh toán cuối cùng
            const finalTotalElement = document.getElementById('finalTotal');
            summaryData.finalTotal = parseNumberFromCurrency(finalTotalElement.textContent);

            // Thu thập thông tin chi tiết từng khách sạn
            const hotelSections = document.querySelectorAll('.hotel-booking-section');
            hotelSections.forEach(section => {
                const hotelId = section.dataset.hotelId;
                if (hotelId && summaryData.hotels[hotelId]) {
                    // Lấy thông tin giảm giá và tổng tiền của khách sạn
                    const hotelTotal = parseNumberFromCurrency(section.querySelector('.hotel-total-price-before-discount').textContent);
                    const hotelDiscountElement = section.querySelector('.hotel-discount');
                    const hotelDiscount = hotelDiscountElement ? Math.abs(parseNumberFromCurrency(hotelDiscountElement.textContent)) : 0;
                    const hotelFinalPrice = hotelTotal - hotelDiscount;

                    summaryData.hotels[hotelId].totalPrice = hotelTotal;
                    summaryData.hotels[hotelId].totalDiscount = hotelDiscount;
                    summaryData.hotels[hotelId].finalPrice = hotelFinalPrice;
                }
            });

            // Thêm dữ liệu cart vào form
            const cartData = document.createElement('input');
            cartData.type = 'hidden';
            cartData.name = 'cart_data';
            cartData.value = JSON.stringify(summaryData);
            form.appendChild(cartData);

            // Submit form
            document.body.appendChild(form);
            form.submit();
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Hàm mới để trích xuất số từ chuỗi tiền tệ 
        function parseNumberFromCurrency(currencyString) {
            if (!currencyString) return 0;
            let numStr = currencyString.replace(/[^0-9,.-]/g, '');
            numStr = numStr.replace(/,/g, '.');

            numStr = numStr.replace(/[.,]/g, '');

            let number = parseFloat(numStr);
            if (currencyString.includes('-')) {
                number = -Math.abs(number);
            }

            return isNaN(number) ? 0 : number;
        }
    });
</script>

<style>
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    }

    .hotel-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .room-thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 600;
        border-top: none;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.03rem;
    }

    .table td {
        vertical-align: middle;
        padding: 0.75rem;
    }

    /* Style cho ngày nhận và trả phòng */
    .check-in-date,
    .check-out-date {
        border-radius: 6px;
        border-color: #e9ecef;
        text-align: center;
        font-size: 0.85rem;
        width: 130px;
    }

    .date-separator {
        font-size: 1.1rem;
        color: #adb5bd;
    }

    .nights-count {
        background-color: #e9ecef !important;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.8rem;
    }

    .nights-count.updated {
        background-color: #28a745 !important;
        color: white !important;
    }

    .remove-room-btn {
        border-radius: 50%;
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .remove-room-btn:hover {
        background-color: #dc3545;
        color: white;
    }

    .booking-summary {
        border-radius: 12px;
        overflow: hidden;
    }

    .price-summary {
        font-size: 1rem;
    }

    .payment-info {
        border-radius: 8px;
    }

    .payment-methods {
        color: #6c757d;
    }

    #checkoutButton {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 123, 255, 0.11);
    }

    #checkoutButton:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(0, 123, 255, 0.2);
    }

    /* Animation khi giá thay đổi */
    @keyframes highlight {

        0%,
        100% {
            background-color: transparent;
        }

        50% {
            background-color: rgba(40, 167, 69, 0.1);
        }
    }

    .value-changed {
        animation: highlight 1.5s ease-in-out;
    }

    .promotions-sidebar {
        height: 100%;
        border-left: 1px solid #eee;
    }

    .promotions-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }

    .promotion-card {
        background-color: #f8fff8;
        border-left: 3px solid #28a745;
        border-radius: 6px;
        padding: 10px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .discount-badge {
        font-weight: bold;
        font-size: 0.9rem;
        color: #28a745;
    }

    /* Hiển thị nổi bật dòng giảm giá */
    .discount-row {
        color: #28a745;
        font-weight: 500;
    }

    .hotel-price-summary>div {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    @media (max-width: 991.98px) {
        .promotions-sidebar {
            border-left: none;
            border-top: 1px solid #eee;
            padding-top: 1rem;
        }
    }

    /* tiêu đề khuyến mãi */
    .promotion-title {
        color: #333;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>