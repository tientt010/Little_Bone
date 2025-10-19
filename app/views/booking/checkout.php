<div class="container py-5">
    <h1 class="mb-4">Thanh toán</h1>

    <div class="row">
        <!-- Left column: Payment Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form id="checkoutForm" class="needs-validation" novalidate>
                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3"><i class="bi bi-credit-card me-2"></i>Phương thức thanh toán</h5>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="payNow" value="pay_now" checked>
                                <label class="form-check-label" for="payNow">
                                    <strong>Thanh toán ngay</strong>
                                </label>
                                <div class="payment-benefits ms-4 mt-2">
                                    <p class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i> Nhanh gọn, thủ tục đơn giản</p>
                                    <p class="mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Chính sách hoàn tiền trong vòng 24 giờ</p>
                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="payLater" value="pay_later">
                                <label class="form-check-label" for="payLater">
                                    <strong>Thanh toán khi nhận phòng</strong>
                                </label>
                                <div class="payment-benefits ms-4 mt-2">
                                    <p class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i> Không sợ rủi ro</p>
                                    <p class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i> Không phải thanh toán hôm nay</p>
                                    <?php
                                    $cancellationDate = date('d/m/Y', strtotime('+2 days'));
                                    ?>
                                    <p class="mb-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Huỷ miễn phí trước 0:00 <?php echo $cancellationDate; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Information (shown regardless of payment method) -->
                        <div id="cardInfoSection" class="mb-4">
                            <h5 class="card-title mb-3"><i class="bi bi-credit-card-2-front me-2"></i>Thông tin thẻ (Visa/MasterCard/Amex/JCB)</h5>

                            <div class="alert alert-info mb-3 py-2">
                                <i class="bi bi-shield-lock me-2"></i> Tất cả thông tin thẻ đều được mã hóa hoàn toàn, an toàn và được bảo vệ.
                            </div>

                            <div id="payLaterNotice" class="alert alert-warning mb-3 py-2" style="display: none;">
                                <i class="bi bi-info-circle me-2"></i> Thẻ của quý khách sẽ không bị tính phí ngày hôm nay. Chỉ yêu cầu thẻ của quý khách để xác nhận việc đặt phòng.
                            </div>

                            <div class="mb-3">
                                <label for="cardName" class="form-label">Tên trên thẻ</label>
                                <input type="text" class="form-control" id="cardName" name="cardName" value="<?php echo $user['full_name'] ?? ''; ?>" required>
                                <div class="invalid-feedback">Vui lòng nhập tên trên thẻ</div>
                            </div>

                            <div class="mb-3">
                                <label for="cardNumber" class="form-label">Số thẻ</label>
                                <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="XXXX XXXX XXXX XXXX" required>
                                <div class="invalid-feedback">Vui lòng nhập số thẻ hợp lệ</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiryDate" class="form-label">Ngày hết hạn (MM/YY)</label>
                                    <input type="text" class="form-control" id="expiryDate" name="expiryDate" placeholder="MM/YY" required>
                                    <div class="invalid-feedback">Vui lòng nhập ngày hết hạn</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">Mã bảo mật CVC</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="XXX" required>
                                    <div class="invalid-feedback">Vui lòng nhập mã CVC</div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3"><i class="bi bi-person me-2"></i>Thông tin người đặt phòng</h5>

                            <div class="mb-3">
                                <label for="customerName" class="form-label">Họ tên</label>
                                <input type="text" class="form-control" id="customerName" name="customerName" value="<?php echo $user['full_name'] ?? ''; ?>" required>
                                <div class="invalid-feedback">Vui lòng nhập họ tên</div>
                            </div>

                            <div class="mb-3">
                                <label for="customerPhone" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="customerPhone" name="customerPhone" value="<?php echo $user['phone'] ?? ''; ?>" required>
                                <div class="invalid-feedback" id="phoneValidationMessage">Vui lòng nhập số điện thoại hợp lệ</div>
                            </div>

                            <div class="mb-3">
                                <label for="customerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="customerEmail" name="customerEmail" value="<?php echo $user['email'] ?? ''; ?>" required>
                                <div class="invalid-feedback" id="emailValidationMessage">Vui lòng nhập email hợp lệ</div>
                            </div>

                            <div class="mb-3">
                                <label for="customerNotes" class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="customerNotes" name="customerNotes" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                <label class="form-check-label" for="termsCheck">
                                    Tôi đồng ý với <a href="#" class="text-primary">các điều khoản của Little Bone</a>
                                </label>
                                <div class="invalid-feedback">Bạn phải đồng ý với các điều khoản để tiếp tục</div>
                            </div>
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Chúng tôi sẽ gửi thông tin chi tiết đến quý khách thông qua email (<?php echo $user['email'] ?? ''; ?>)
                        </div>

                        <!-- Submit button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="bookNowBtn">
                                <i class="bi bi-check-circle me-2"></i>Đặt phòng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right column: Booking Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Tóm tắt đặt phòng</h5>
                </div>
                <div class="card-body p-3">
                    <!-- Hotels in cart -->
                    <div class="hotels-summary mb-4">
                        <h6 class="text-muted mb-3">Danh sách khách sạn</h6>
                        <div id="hotelsList">
                            <!-- Hotels will be inserted here by JavaScript -->
                        </div>
                    </div>

                    <hr>

                    <!-- Order summary -->
                    <div class="order-summary">
                        <h6 class="text-muted mb-3">Chi tiết hóa đơn</h6>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng tiền gốc:</span>
                            <span id="originalPrice" class="text-muted text-decoration-line-through">0 VNĐ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2" id="discountRow">
                            <span>Chiết khấu ưu đãi:</span>
                            <span id="totalDiscount" class="text-success">-0 VNĐ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Thuế VAT (10%):</span>
                            <span id="taxAmount">0 VNĐ</span>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Tổng thanh toán:</span>
                            <span id="finalTotal" class="fs-5 text-primary fw-bold">0 VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get cart data from session
        const cartData = <?php echo isset($_POST['cart_data']) ? $_POST['cart_data'] : 'null'; ?>;

        if (!cartData) {
            window.location.href = '<?php echo SITE_URL; ?>/booking/cart';
            return;
        }

        // Render booking summary using pre-calculated values
        renderBookingSummary(cartData);

        // Set up payment method toggle
        const payNowRadio = document.getElementById('payNow');
        const payLaterRadio = document.getElementById('payLater');
        const cardInfoSection = document.getElementById('cardInfoSection');
        const payLaterNotice = document.getElementById('payLaterNotice');

        function toggleCardInfoSection() {
            if (payNowRadio.checked) {
                cardInfoSection.style.display = 'block';
                payLaterNotice.style.display = 'none';
                // Enable card validations
                document.querySelectorAll('#cardInfoSection input').forEach(input => {
                    input.setAttribute('required', '');
                });
            } else {
                cardInfoSection.style.display = 'block'; // Luôn hiển thị, không ẩn nữa
                payLaterNotice.style.display = 'block';
                // Keep card fields required
                document.querySelectorAll('#cardInfoSection input').forEach(input => {
                    input.setAttribute('required', '');
                });
            }
        }

        payNowRadio.addEventListener('change', toggleCardInfoSection);
        payLaterRadio.addEventListener('change', toggleCardInfoSection);

        // Initialize card info section visibility
        toggleCardInfoSection();

        // Real-time validation for phone number
        const phoneInput = document.getElementById('customerPhone');
        phoneInput.addEventListener('input', function() {
            const phoneRegex = /^(0|\+84)[3|5|7|8|9][0-9]{8}$/;
            const isValid = phoneRegex.test(this.value);

            if (this.value && !isValid) {
                phoneInput.classList.add('is-invalid');
                document.getElementById('phoneValidationMessage').textContent = 'Số điện thoại không hợp lệ (phải có 10 số và bắt đầu bằng 03, 05, 07, 08, 09)';
            } else {
                phoneInput.classList.remove('is-invalid');
            }
        });

        // Real-time validation for email
        const emailInput = document.getElementById('customerEmail');
        emailInput.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isValid = emailRegex.test(this.value);

            if (this.value && !isValid) {
                emailInput.classList.add('is-invalid');
                document.getElementById('emailValidationMessage').textContent = 'Email không hợp lệ';
            } else {
                emailInput.classList.remove('is-invalid');
            }
        });

        // Card number formatting
        const cardNumberInput = document.getElementById('cardNumber');
        cardNumberInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            // Add space after every 4 digits
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            // Limit to 19 characters (16 digits + 3 spaces)
            this.value = value.substring(0, 19);
        });

        // Expiry date formatting
        const expiryDateInput = document.getElementById('expiryDate');
        expiryDateInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value.substring(0, 5);
        });

        // CVV formatting
        const cvvInput = document.getElementById('cvv');
        cvvInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            this.value = value.substring(0, 4); // Most cards have 3 digits, Amex has 4
        });

        // Form submission
        const form = document.getElementById('checkoutForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            // Hiển thị loading
            const bookNowBtn = document.getElementById('bookNowBtn');
            const originalBtnText = bookNowBtn.innerHTML;
            bookNowBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
            bookNowBtn.disabled = true;

            // Thu thập dữ liệu từ form
            const formData = {
                payment_method: document.querySelector('input[name="paymentMethod"]:checked').value,
                customer_name: document.getElementById('customerName').value,
                customer_phone: document.getElementById('customerPhone').value,
                customer_email: document.getElementById('customerEmail').value,
                customer_notes: document.getElementById('customerNotes').value,
                notes: document.getElementById('customerNotes').value,
                cart_data: cartData
            };

            // Gửi dữ liệu đến server
            fetch('<?php echo SITE_URL; ?>/booking/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {

                    if (data.success) {
                        // Hiển thị thông báo thành công
                        Swal.fire({
                            title: 'Đặt phòng thành công!',
                            text: data.message || 'Cảm ơn bạn đã đặt phòng. Thông tin đặt phòng đã được gửi đến email của bạn.',
                            icon: 'success',
                            confirmButtonText: 'Quay lại trang chủ'
                        }).then(() => {
                            // Xóa giỏ hàng
                            if (window.cartManager) {
                                window.cartManager.clearCart();
                            }

                            // Chuyển hướng đến trang chủ
                            window.location.href = '<?php echo SITE_URL; ?>';
                        });
                    } else {
                        // Hiển thị thông báo lỗi
                        Swal.fire({
                            title: 'Có lỗi xảy ra',
                            text: data.message || 'Không thể hoàn tất đặt phòng. Vui lòng thử lại.',
                            icon: 'error',
                            confirmButtonText: 'Đóng'
                        });

                        // Khôi phục nút đặt phòng
                        bookNowBtn.innerHTML = originalBtnText;
                        bookNowBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error processing booking:', error);

                    // Hiển thị thông báo lỗi
                    Swal.fire({
                        title: 'Có lỗi xảy ra',
                        text: 'Không thể kết nối đến máy chủ. Vui lòng thử lại sau.',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });

                    // Khôi phục nút đặt phòng
                    bookNowBtn.innerHTML = originalBtnText;
                    bookNowBtn.disabled = false;
                });
        });

        // Function to render booking summary using pre-calculated values
        function renderBookingSummary(cart) {
            // Render hotels list
            const hotelsList = document.getElementById('hotelsList');
            hotelsList.innerHTML = '';

            if (cart && cart.hotels) {
                Object.values(cart.hotels).forEach(hotel => {
                    const hotelElement = document.createElement('div');
                    hotelElement.className = 'hotel-summary mb-3';
                    hotelElement.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${hotel.name}</h6>
                                <small class="text-muted">${Object.keys(hotel.rooms).length} phòng</small>
                            </div>
                            <div class="text-end">
                                <div class="text-muted text-decoration-line-through small">${formatCurrency(hotel.totalPrice || 0)}</div>
                                <div class="fw-bold">${formatCurrency(hotel.finalPrice || 0)}</div>
                            </div>
                        </div>
                    `;
                    hotelsList.appendChild(hotelElement);
                });
            }

            // Update order summary using pre-calculated values
            document.getElementById('originalPrice').textContent = formatCurrency(cart.originalTotal || 0);
            document.getElementById('totalDiscount').textContent = `-${formatCurrency(cart.totalDiscount || 0)}`;
            document.getElementById('taxAmount').textContent = formatCurrency(cart.taxes || 0);
            document.getElementById('finalTotal').textContent = formatCurrency(cart.finalTotal || 0);

            // Show/hide discount row
            if (cart.totalDiscount > 0) {
                document.getElementById('discountRow').style.display = 'flex';
            } else {
                document.getElementById('discountRow').style.display = 'none';
            }
        }

        // Helper function to format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Remove unnecessary calculation functions
        // calculateHotelTotal, calculateHotelDiscount, calculateNights can be removed
    });
</script>

<!-- Thêm SweetAlert2 để hiển thị thông báo đẹp hơn -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
    }

    .card-header {
        border-bottom: none;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    /* Animated focus for card inputs */
    #cardInfoSection .form-control:focus {
        border-color: #0d6efd;
        transition: all 0.3s ease;
    }

    .hotel-summary {
        padding: 12px;
        border-radius: 8px;
        background-color: #f8f9fa;
    }

    .form-control {
        border-radius: 0.375rem;
        padding: 0.6rem 0.75rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    #bookNowBtn {
        padding: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    #bookNowBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }
</style>