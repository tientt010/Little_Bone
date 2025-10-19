<div class="profile-container">
    <?php if ($flash = $this->getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" data-bs-autohide="true" data-bs-delay="3000">
            <?php echo $flash; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($flash = $this->getFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <?php echo $flash; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Thông tin người dùng -->
        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <ul class="nav nav-tabs card-header-tabs justify-content-between" id="profileTabs" role="tablist">
                        <li class="nav-item flex-grow-1 text-center" role="presentation">
                            <button class="nav-link active w-100" id="view-tab" data-bs-toggle="tab" data-bs-target="#profile-view-mode" type="button" role="tab" aria-controls="profile-view-mode" aria-selected="true">
                                <i class="bi bi-person me-1"></i>Thông tin
                            </button>
                        </li>
                        <li class="nav-item flex-grow-1 text-center" role="presentation">
                            <button class="nav-link w-100" id="edit-tab" data-bs-toggle="tab" data-bs-target="#profile-edit-mode" type="button" role="tab" aria-controls="profile-edit-mode" aria-selected="false">
                                <i class="bi bi-pencil-square me-1"></i>Chỉnh sửa
                            </button>
                        </li>
                        <li class="nav-item flex-grow-1 text-center" role="presentation">
                            <button class="nav-link w-100" id="password-tab" data-bs-toggle="tab" data-bs-target="#change-password-mode" type="button" role="tab" aria-controls="change-password-mode" aria-selected="false">
                                <i class="bi bi-lock me-1"></i>Đổi mật khẩu
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="profileTabsContent">
                        <div class="tab-pane fade show active" id="profile-view-mode" role="tabpanel" aria-labelledby="view-tab">
                            <div class="text-center mb-4">
                                <div class="avatar-wrapper">
                                    <img src="<?php echo SITE_URL; ?>/public/images/avatars/<?php echo $user['avatar']; ?>"
                                        class="rounded-circle avatar-lg" alt="<?php echo $user['full_name']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#avatarModal">
                                    <div class="change-avatar-icon" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                                        <i class="bi bi-camera"></i>
                                    </div>
                                </div>
                                <h4 class="mt-3"><?php echo $user['full_name']; ?></h4>

                                <div class="user-info-container mt-4">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="bi bi-envelope me-2"></i>
                                        <span><?php echo $user['email']; ?></span>
                                    </div>

                                    <?php if (!empty($user['phone'])): ?>
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="bi bi-telephone me-2"></i>
                                            <span><?php echo $user['phone']; ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($user['address'])): ?>
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="bi bi-geo-alt me-2"></i>
                                            <span><?php echo $user['address']; ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($user['birth_date'])): ?>
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="bi bi-calendar me-2"></i>
                                            <span><?php echo date('d/m/Y', strtotime($user['birth_date'])); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($user['gender'])): ?>
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="bi bi-person me-2"></i>
                                            <span>
                                                <?php
                                                $genders = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                                echo $genders[$user['gender']] ?? $user['gender'];
                                                ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Chỉnh sửa thông tin -->
                        <div class="tab-pane fade" id="profile-edit-mode" role="tabpanel" aria-labelledby="edit-tab">
                            <h5 class="card-title mb-3 text-center">Chỉnh sửa thông tin</h5>
                            <form id="edit-profile-form" method="POST" action="<?php echo SITE_URL; ?>/user/update-profile">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Họ tên</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                        value="<?php echo $user['full_name']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo $user['email']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Điện thoại</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="<?php echo $user['phone']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Địa chỉ</label>
                                    <textarea class="form-control" id="address" name="address" rows="2"><?php echo $user['address']; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Ngày sinh</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date"
                                        value="<?php echo $user['birth_date'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giới tính</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="gender-male"
                                                value="male" <?php echo ($user['gender'] === 'male') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="gender-male">Nam</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="gender-female"
                                                value="female" <?php echo ($user['gender'] === 'female') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="gender-female">Nữ</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="gender-other"
                                                value="other" <?php echo ($user['gender'] === 'other') ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="gender-other">Khác</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                </div>
                            </form>
                        </div>

                        <!-- Đổi mật khẩu -->
                        <div class="tab-pane fade" id="change-password-mode" role="tabpanel" aria-labelledby="password-tab">
                            <h5 class="card-title mb-3 text-center">Đổi mật khẩu</h5>
                            <form id="change-password-form" method="POST" action="<?php echo SITE_URL; ?>/user/update-password" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <div class="invalid-feedback">Vui lòng nhập mật khẩu hiện tại</div>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password"
                                        required pattern=".{6,}" title="Mật khẩu phải có ít nhất 6 ký tự">
                                    <div class="invalid-feedback">Mật khẩu phải có ít nhất 6 ký tự</div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <div class="invalid-feedback" id="password-match-feedback">Mật khẩu xác nhận phải trùng khớp</div>
                                </div>
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs custom-tabs justify-content-around">
                        <li class="nav-item flex-grow-1 text-center">
                            <a class="nav-link active" data-bs-toggle="tab" href="#bookings">
                                <i class="bi bi-calendar-check"></i> Đặt phòng gần đây
                            </a>
                        </li>
                        <li class="nav-item flex-grow-1 text-center">
                            <a class="nav-link" data-bs-toggle="tab" href="#favorites">
                                <i class="bi bi-heart"></i> Khách sạn yêu thích
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content p-3">
                        <!-- Recent Bookings -->
                        <div class="tab-pane active" id="bookings">
                            <?php if (!empty($recentBookings)): ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <?php
                                    $bookingDate = new DateTime($booking['booking_date']);
                                    $currentDate = new DateTime();
                                    $daysDifference = $currentDate->diff($bookingDate)->days;
                                    $canCancel = $daysDifference <= 2 && ($booking['payment_status'] !== 'cancelled');
                                    ?>
                                    <div class="booking-item mb-3 p-3 border rounded">
                                        <div class="row">
                                            <div class="col-md-4 mb-2 mb-md-0">
                                                <img src="<?php echo isset($booking['hotel_image']) ? $booking['hotel_image'] : SITE_URL . '/public/images/hotels/' . $booking['hotel_id'] . '/main.jpg'; ?>"
                                                    class="img-fluid rounded" alt="<?php echo $booking['hotel_name']; ?>"
                                                    onerror="this.src='<?php echo SITE_URL; ?>/public/images/hotels/default.jpg'">
                                            </div>
                                            <div class="col-md-8 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <h5 class="mb-0"><?php echo $booking['hotel_name']; ?></h5>
                                                        <div class="badge bg-<?php echo $this->getStatusBadgeClass($booking['payment_status'] ?? 'unknown'); ?>">
                                                            <?php
                                                            $statusText = [
                                                                'paid' => 'Đã thanh toán',
                                                                'pending' => 'Chờ thanh toán',
                                                                'cancelled' => 'Đã hủy'
                                                            ];
                                                            echo $statusText[$booking['payment_status']] ?? 'Chưa thanh toán';
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <p class="mb-1 text-muted">
                                                        <i class="bi bi-geo-alt-fill"></i>
                                                        <?php echo $booking['hotel_address']; ?>
                                                    </p>
                                                    <p class="mb-1">
                                                        <i class="bi bi-door-closed"></i>
                                                        <span class="badge bg-light text-dark"><?php echo $booking['room_count']; ?> phòng</span>
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <p class="mb-1 text-muted">
                                                            <small>
                                                                <i class="bi bi-calendar3"></i>
                                                                Đặt ngày: <?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?>
                                                            </small>
                                                        </p>
                                                        <div class="text-end mb-1">
                                                            <?php if ($canCancel): ?>
                                                                <button class="btn btn-sm btn-outline-danger cancel-booking"
                                                                    data-booking-id="<?php echo $booking['booking_id']; ?>"
                                                                    data-hotel-name="<?php echo htmlspecialchars($booking['hotel_name']); ?>">
                                                                    <i class="bi bi-x-circle"></i> Hủy đặt
                                                                </button>
                                                            <?php endif; ?>
                                                            <a href="<?php echo SITE_URL . '/hotels/' . $booking['hotel_id']; ?>"
                                                                class="btn btn-sm btn-outline-primary me-2">
                                                                <i class="bi bi-chat-dots"></i> Bình luận
                                                            </a>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x h1 text-muted"></i>
                                    <p class="text-muted mt-3">Chưa có đơn đặt phòng nào</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Favorite Hotels -->
                        <div class="tab-pane" id="favorites">
                            <?php if (!empty($favoriteHotels)): ?>
                                <div class="row g-3">
                                    <?php foreach ($favoriteHotels as $hotel): ?>
                                        <div class="col-12">
                                            <div class="card hover-shadow">
                                                <div class="row g-0">
                                                    <div class="col-md-4">
                                                        <img src="<?php echo SITE_URL . '/public/images/hotels/' . ($hotel['image'] ?? 'default.jpg'); ?>"
                                                            class="img-fluid rounded-start" alt="<?php echo $hotel['name']; ?>">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between">
                                                                <h5 class="card-title text-primary"><?php echo $hotel['name']; ?></h5> <!-- Add text-primary class -->

                                                            </div>
                                                            <p class="card-text">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-geo-alt"></i> <?php echo $hotel['address']; ?>
                                                                </small>
                                                            </p>
                                                            <div class="mt-3">
                                                                <a href="<?php echo SITE_URL . '/hotels/' . $hotel['id']; ?>"
                                                                    class="btn btn-sm btn-primary">
                                                                    <i class="bi bi-search"></i> Xem chi tiết
                                                                </a>
                                                                <button class="btn btn-sm btn-danger ms-2 remove-favorite"
                                                                    data-hotel-id="<?php echo $hotel['id']; ?>">
                                                                    <i class="bi bi-heart-fill"></i> Bỏ yêu thích
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-heart h1 text-muted"></i>
                                    <p class="text-muted mt-3">Chưa có khách sạn yêu thích</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade avatar-modal" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Ảnh đại diện</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="<?php echo SITE_URL; ?>/public/images/avatars/<?php echo $user['avatar']; ?>"
                        alt="<?php echo $user['full_name']; ?>" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeAvatarModal" tabindex="-1" aria-labelledby="changeAvatarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeAvatarModalLabel">Đổi ảnh đại diện</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="avatar-form" method="POST" action="<?php echo SITE_URL; ?>/user/update-avatar" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Chọn ảnh mới</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" required>
                            <div class="form-text text-light">Hỗ trợ các định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</div>
                        </div>
                        <div class="mt-4 text-center">
                            <img id="avatar-preview" src="#" alt="Preview" style="max-width: 200px; max-height: 200px; display: none;" class="rounded-circle mb-3">
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">Cập nhật ảnh đại diện</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelBookingModalLabel">Xác nhận hủy đặt phòng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center">Bạn có chắc chắn muốn hủy đặt phòng tại <strong id="hotelNameToCancel"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i>
                        <strong>Lưu ý:</strong> Việc hủy đặt phòng có thể không được hoàn tiền tùy theo chính sách của khách sạn.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Giữ lại</button>
                    <button type="button" class="btn btn-danger" id="confirmCancelBooking">
                        <i class="bi bi-x-circle"></i> Xác nhận hủy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto hide success alerts
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(function() {
                const closeBtn = successAlert.querySelector('.btn-close');
                if (closeBtn) closeBtn.click();
            }, 3000);
        }

        // Password validation
        const form = document.getElementById('change-password-form');
        if (form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (newPassword !== confirmPassword) {
                    event.preventDefault();
                    document.getElementById('confirm_password').setCustomValidity('Mật khẩu xác nhận không khớp');
                    document.getElementById('confirm_password').classList.add('is-invalid');
                } else {
                    document.getElementById('confirm_password').setCustomValidity('');
                }

                form.classList.add('was-validated');
            });

            // Reset validation when typing
            document.getElementById('confirm_password').addEventListener('input', function() {
                this.setCustomValidity('');
            });
        }

        // Avatar preview - Cải thiện xử lý
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatar-preview');

        if (avatarInput && avatarPreview) {
            avatarInput.addEventListener('change', function(event) {
                const file = event.target.files[0];

                // Kiểm tra xem có chọn file hợp lệ không
                if (file && file.type.match('image.*')) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                        avatarPreview.style.display = 'block';
                    }

                    reader.readAsDataURL(file);
                } else if (file) {
                    // Hiển thị thông báo lỗi nếu file không phải là ảnh
                    alert('Vui lòng chọn một file hình ảnh (JPG, PNG, GIF)');
                    avatarInput.value = ''; // Reset input
                    avatarPreview.style.display = 'none';
                }
            });
        }

        // Thêm animation cho avatar khi hover
        const avatarImg = document.querySelector('.avatar-lg');
        if (avatarImg) {
            avatarImg.addEventListener('mouseenter', function() {
                this.classList.add('animate__animated', 'animate__pulse');
            });

            avatarImg.addEventListener('mouseleave', function() {
                this.classList.remove('animate__animated', 'animate__pulse');
            });
        }

        // Xử lý nút bỏ yêu thích
        const removeFavButtons = document.querySelectorAll('.remove-favorite');
        if (removeFavButtons.length > 0) {
            removeFavButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const hotelId = this.dataset.hotelId;

                    // Hiển thị hiệu ứng loading
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
                    this.disabled = true;

                    // Gửi yêu cầu xóa đến server
                    fetch(`${SITE_URL}/user/remove_favorite`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: `hotel_id=${hotelId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Xóa phần tử khỏi giao diện sau khi xử lý thành công
                                const card = this.closest('.col-12');
                                card.classList.add('animate__animated', 'animate__fadeOut');

                                setTimeout(() => {
                                    card.remove();

                                    // Kiểm tra nếu không còn khách sạn yêu thích nào
                                    const remainingFavorites = document.querySelectorAll('#favorites .col-12');
                                    if (remainingFavorites.length === 0) {
                                        document.querySelector('#favorites .row').innerHTML = `
                                        <div class="text-center py-5 w-100">
                                            <i class="bi bi-heart h1 text-muted"></i>
                                            <p class="text-muted mt-3">Chưa có khách sạn yêu thích</p>
                                        </div>
                                    `;
                                    }
                                }, 500);
                            } else {
                                // Hiển thị lỗi nếu có
                                alert(data.message || 'Có lỗi xảy ra. Vui lòng thử lại sau.');
                                // Khôi phục nút
                                this.innerHTML = '<i class="bi bi-heart-fill"></i> Bỏ yêu thích';
                                this.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Có lỗi xảy ra khi xử lý yêu cầu');
                            // Khôi phục nút
                            this.innerHTML = '<i class="bi bi-heart-fill"></i> Bỏ yêu thích';
                            this.disabled = false;
                        });
                });
            });
        }

        // Handle cancel booking functionality
        const cancelButtons = document.querySelectorAll('.cancel-booking');
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
        const confirmCancelBtn = document.getElementById('confirmCancelBooking');
        const hotelNameElement = document.getElementById('hotelNameToCancel');
        let currentBookingId = null;

        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                currentBookingId = this.dataset.bookingId;
                const hotelName = this.dataset.hotelName;

                hotelNameElement.textContent = hotelName;
                cancelModal.show();
            });
        });

        if (confirmCancelBtn) {
            confirmCancelBtn.addEventListener('click', function() {
                if (!currentBookingId) return;

                // Show loading state
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang hủy...';
                this.disabled = true;

                // Send cancel request
                fetch(`${SITE_URL}/user/cancel-booking`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `booking_id=${currentBookingId}`
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.success) {
                            // Update the booking status in the UI
                            const bookingItem = document.querySelector(`[data-booking-id="${currentBookingId}"]`).closest('.booking-item');
                            const statusBadge = bookingItem.querySelector('.badge');
                            const cancelButton = bookingItem.querySelector('.cancel-booking');

                            // Update status badge
                            statusBadge.className = 'badge bg-secondary';
                            statusBadge.textContent = 'Đã hủy';

                            // Remove cancel button
                            if (cancelButton) {
                                cancelButton.remove();
                            }

                            // Show success message
                            const alertContainer = document.querySelector('.profile-container');
                            const successAlert = document.createElement('div');
                            successAlert.className = 'alert alert-success alert-dismissible fade show mb-4';
                            successAlert.innerHTML = `
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                            alertContainer.insertBefore(successAlert, alertContainer.firstChild);

                            // Auto hide alert after 3 seconds
                            setTimeout(() => {
                                const closeBtn = successAlert.querySelector('.btn-close');
                                if (closeBtn) closeBtn.click();
                            }, 3000);

                            cancelModal.hide();
                        } else {
                            alert(data.message || 'Có lỗi xảy ra khi hủy đặt phòng');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi xử lý yêu cầu');
                    })
                    .finally(() => {
                        // Reset button state
                        confirmCancelBtn.innerHTML = '<i class="bi bi-x-circle"></i> Xác nhận hủy';
                        confirmCancelBtn.disabled = false;
                        currentBookingId = null;
                    });
            });
        }

        // Thêm biến SITE_URL để JavaScript có thể sử dụng
        const SITE_URL = "<?php echo SITE_URL; ?>";
    });
</script>