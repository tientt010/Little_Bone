<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center">
                <i class="bi bi-door-open me-2"></i> Quản lý Phòng
            </h1>
        </div>
        <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            <i class="bi bi-plus-circle me-1"></i> Thêm phòng mới
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-door-closed fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Tổng số phòng</h6>
                        <h3 class="mb-0" id="totalRooms"><?php echo $stats['total'] ?? 0; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-check-circle fs-4 text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Phòng trống</h6>
                        <h3 class="mb-0" id="availableRooms"><?php echo $stats['available'] ?? 0; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="bi bi-calendar-check fs-4 text-danger"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Phòng đã đặt</h6>
                        <h3 class="mb-0" id="bookedRooms"><?php echo $stats['booked'] ?? 0; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="bi bi-tools fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Đang bảo trì</h6>
                        <h3 class="mb-0" id="maintenanceRooms"><?php echo $stats['maintenance'] ?? 0; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i> Lọc phòng</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-4">
                    <label for="roomType" class="form-label fw-semibold">
                        <i class="bi bi-house-door me-1 text-primary"></i> Loại phòng
                    </label>
                    <select class="form-select shadow-sm" id="roomType" name="type">
                        <option value="">Tất cả loại phòng</option>
                        <option value="single" <?php echo isset($_GET['type']) && $_GET['type'] == 'single' ? 'selected' : ''; ?>>Phòng đơn</option>
                        <option value="double" <?php echo isset($_GET['type']) && $_GET['type'] == 'double' ? 'selected' : ''; ?>>Phòng đôi</option>
                        <option value="suite" <?php echo isset($_GET['type']) && $_GET['type'] == 'suite' ? 'selected' : ''; ?>>Suite</option>
                        <option value="family" <?php echo isset($_GET['type']) && $_GET['type'] == 'family' ? 'selected' : ''; ?>>Phòng gia đình</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="roomStatus" class="form-label fw-semibold">
                        <i class="bi bi-bookmark-check me-1 text-success"></i> Trạng thái
                    </label>
                    <select class="form-select shadow-sm" id="roomStatus" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="available" <?php echo isset($_GET['status']) && $_GET['status'] == 'available' ? 'selected' : ''; ?>>Trống</option>
                        <option value="booked" <?php echo isset($_GET['status']) && $_GET['status'] == 'booked' ? 'selected' : ''; ?>>Đã đặt</option>
                        <option value="maintenance" <?php echo isset($_GET['status']) && $_GET['status'] == 'maintenance' ? 'selected' : ''; ?>>Đang bảo trì</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="priceRange" class="form-label fw-semibold">
                        <i class="bi bi-currency-dollar me-1 text-danger"></i> Khoảng giá
                    </label>
                    <select class="form-select shadow-sm" id="priceRange" name="price">
                        <option value="">Tất cả mức giá</option>
                        <option value="0-500000" <?php echo isset($_GET['price']) && $_GET['price'] == '0-500000' ? 'selected' : ''; ?>>
                            < 500,000 VNĐ</option>
                        <option value="500000-1000000" <?php echo isset($_GET['price']) && $_GET['price'] == '500000-1000000' ? 'selected' : ''; ?>>500,000 - 1,000,000 VNĐ</option>
                        <option value="1000000-2000000" <?php echo isset($_GET['price']) && $_GET['price'] == '1000000-2000000' ? 'selected' : ''; ?>>1,000,000 - 2,000,000 VNĐ</option>
                        <option value="2000000-100000000" <?php echo isset($_GET['price']) && $_GET['price'] == '2000000-0' ? 'selected' : ''; ?>>> 2,000,000 VNĐ</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="text-muted">Hiển thị <?php echo count($rooms ?? []); ?> phòng</span>
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary active" id="tableViewBtn">
                <i class="bi bi-table"></i> Dạng bảng
            </button>
            <button type="button" class="btn btn-outline-primary" id="cardViewBtn">
                <i class="bi bi-grid-3x3-gap"></i> Dạng thẻ
            </button>
        </div>
    </div>

    <!-- Table View (Default) -->
    <div id="tableView" class="card shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="80">Số phòng</th>
                        <th scope="col">Tên phòng</th>
                        <th scope="col">Loại phòng</th>
                        <th scope="col" width="100" class="text-end">Giá (VNĐ)</th>
                        <th scope="col" width="80" class="text-center">Sức chứa</th>
                        <th scope="col" width="120" class="text-center">Trạng thái</th>
                        <th scope="col" width="120" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rooms)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-exclamation-circle text-muted fs-3 d-block mb-2"></i>
                                Không tìm thấy phòng nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rooms as $room): ?>
                            <?php
                            // Xác định class trạng thái
                            $statusClass = '';
                            $statusText = '';

                            switch ($room['status']) {
                                case 'available':
                                    $statusClass = 'success';
                                    $statusText = 'Trống';
                                    break;
                                case 'booked':
                                    $statusClass = 'danger';
                                    $statusText = 'Đã đặt';
                                    break;
                                case 'maintenance':
                                    $statusClass = 'warning';
                                    $statusText = 'Bảo trì';
                                    break;
                                default:
                                    $statusClass = 'secondary';
                                    $statusText = 'Không xác định';
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($room['name']); ?></strong>
                                    <?php if (!empty($room['description'])): ?>
                                        <span class="d-block small text-muted text-truncate" style="max-width: 250px;"><?php echo htmlspecialchars($room['description']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $roomTypeLabels = [
                                        'single' => 'Phòng đơn',
                                        'double' => 'Phòng đôi',
                                        'suite' => 'Suite',
                                        'family' => 'Phòng gia đình'
                                    ];
                                    echo $roomTypeLabels[$room['room_type']] ?? $room['room_type'];
                                    ?>
                                </td>
                                <td class="text-end fw-bold"><?php echo number_format($room['price']); ?></td>
                                <td class="text-center"><?php echo $room['capacity']; ?> người</td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary edit-room-btn" data-room-id="<?php echo $room['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger delete-room-btn" data-room-id="<?php echo $room['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Card View (Hidden by default) -->
    <div id="cardView" class="mb-4" style="display: none;">
        <div class="row g-4">
            <?php if (empty($rooms)): ?>
                <div class="col-12 text-center py-4">
                    <i class="bi bi-exclamation-circle text-muted fs-3 d-block mb-2"></i>
                    Không tìm thấy phòng nào
                </div>
            <?php else: ?>
                <?php foreach ($rooms as $room): ?>
                    <?php
                    // Xác định class trạng thái
                    $statusClass = '';
                    $statusText = '';
                    $statusIcon = '';

                    switch ($room['status']) {
                        case 'available':
                            $statusClass = 'success';
                            $statusText = 'Trống';
                            $statusIcon = 'check-circle';
                            break;
                        case 'booked':
                            $statusClass = 'danger';
                            $statusText = 'Đã đặt';
                            $statusIcon = 'calendar-check';
                            break;
                        case 'maintenance':
                            $statusClass = 'warning';
                            $statusText = 'Bảo trì';
                            $statusIcon = 'tools';
                            break;
                        default:
                            $statusClass = 'secondary';
                            $statusText = 'Không xác định';
                            $statusIcon = 'question-circle';
                    }

                    $roomTypeLabels = [
                        'single' => 'Phòng đơn',
                        'double' => 'Phòng đôi',
                        'suite' => 'Suite',
                        'family' => 'Phòng gia đình'
                    ];
                    $roomTypeText = $roomTypeLabels[$room['room_type']] ?? $room['room_type'];
                    ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm h-100">
                            <div class="position-relative">
                                <img src="<?php echo SITE_URL; ?>/public/images/rooms/<?php echo $room['id']; ?>.jpg"
                                    class="card-img-top" alt="<?php echo htmlspecialchars($room['name']); ?>"
                                    onerror="this.src='<?php echo SITE_URL; ?>/public/images/rooms/default.jpg'" style="height: 160px; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                        <i class="bi bi-<?php echo $statusIcon; ?>"></i> <?php echo $statusText; ?>
                                    </span>
                                </div>
                                <div class="position-absolute bottom-0 start-0 m-2">
                                    <span class="badge bg-dark"><?php echo htmlspecialchars($room['room_number']); ?></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($room['name']); ?></h5>
                                <p class="card-text text-muted small mb-2"><?php echo $roomTypeText; ?></p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fs-5 fw-bold text-primary"><?php echo number_format($room['price']); ?> VNĐ</span>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-people"></i> <?php echo $room['capacity']; ?> người
                                    </span>
                                </div>
                                <?php if (!empty($room['description'])): ?>
                                    <p class="card-text small mb-0 text-truncate" title="<?php echo htmlspecialchars($room['description']); ?>"><?php echo htmlspecialchars($room['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="btn-group w-100">
                                    <button type="button" class="btn btn-outline-primary edit-room-btn" data-room-id="<?php echo $room['id']; ?>">
                                        <i class="bi bi-pencil"></i> Sửa
                                    </button>
                                    <button type="button" class="btn btn-outline-danger delete-room-btn" data-room-id="<?php echo $room['id']; ?>">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoomModalLabel">Thêm phòng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addRoomForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Cột trái -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="room_number" class="form-label">Số phòng <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="room_number" name="room_number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên phòng <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="room_type" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                                    <select class="form-select" id="room_type" name="room_type" required>
                                        <option value="">Chọn loại phòng</option>
                                        <option value="single">Phòng đơn</option>
                                        <option value="double">Phòng đôi</option>
                                        <option value="suite">Suite</option>
                                        <option value="family">Phòng gia đình</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Giá phòng (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" min="0" step="1000" required>
                                </div>
                            </div>

                            <!-- Cột phải -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Sức chứa (người) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="capacity" name="capacity" min="1" max="10" value="2" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="available">Trống</option>
                                        <option value="maintenance">Bảo trì</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="area" class="form-label">Diện tích (m²)</label>
                                    <input type="number" class="form-control" id="area" name="area" min="0" step="0.1">
                                </div>
                                <div class="mb-3">
                                    <label for="room_image" class="form-label">Hình ảnh phòng</label>
                                    <input type="file" class="form-control" id="room_image" name="room_image" accept="image/*">
                                </div>
                            </div>

                            <!-- Mô tả -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="addRoomSubmitBtn">
                            <span class="spinner-border spinner-border-sm d-none" id="addRoomSpinner" role="status" aria-hidden="true"></span>
                            Thêm phòng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Room Modal -->
    <div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoomModalLabel">Chỉnh sửa thông tin phòng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editRoomForm" enctype="multipart/form-data">
                    <input type="hidden" name="room_id" id="edit_room_id">
                    <div class="modal-body">
                        <!-- Form content similar to addRoomModal with id prefixed with 'edit_' -->
                        <!-- This will be populated with AJAX -->
                        <div id="editRoomContent">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <p class="mt-2">Đang tải thông tin phòng...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle between table and card view
            document.getElementById('tableViewBtn').addEventListener('click', function() {
                document.getElementById('tableView').style.display = 'block';
                document.getElementById('cardView').style.display = 'none';
                this.classList.add('active');
                document.getElementById('cardViewBtn').classList.remove('active');
            });

            document.getElementById('cardViewBtn').addEventListener('click', function() {
                document.getElementById('tableView').style.display = 'none';
                document.getElementById('cardView').style.display = 'block';
                this.classList.add('active');
                document.getElementById('tableViewBtn').classList.remove('active');
            });

            // Edit Room Button Click
            const editButtons = document.querySelectorAll('.edit-room-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const roomId = this.getAttribute('data-room-id');
                    loadRoomDetails(roomId);
                });
            });

            // Delete Room Button Click
            const deleteButtons = document.querySelectorAll('.delete-room-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const roomId = this.getAttribute('data-room-id');
                    confirmDeleteRoom(roomId);
                });
            });

            // Function to load room details for editing
            function loadRoomDetails(roomId) {
                // Show the edit modal
                const editModal = new bootstrap.Modal(document.getElementById('editRoomModal'));
                editModal.show();

                // Set the room ID in the form
                document.getElementById('edit_room_id').value = roomId;

                // Load room details via AJAX
                fetch(`${SITE_URL}/hotel_staff/getRoomDetails/${roomId}`)
                    .then(response => response.json())
                    .then(data => {

                        if (data.success) {
                            // Populate the form with room data
                            const room = data.room;
                            document.getElementById('editRoomContent').innerHTML = `
                        <div class="row g-3">
                            <!-- Cột trái -->
                            <div class="col-md-6">
                                
                                <div class="mb-3">
                                    <label for="edit_room_number" class="form-label">Số phòng <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_room_number" name="room_number" required value="${room.room_number}">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label">Tên phòng <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required value="${room.name}">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_room_type" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_room_type" name="room_type" required>
                                        <option value="single" ${room.room_type === 'single' ? 'selected' : ''}>Phòng đơn</option>
                                        <option value="double" ${room.room_type === 'double' ? 'selected' : ''}>Phòng đôi</option>
                                        <option value="suite" ${room.room_type === 'suite' ? 'selected' : ''}>Suite</option>
                                        <option value="family" ${room.room_type === 'family' ? 'selected' : ''}>Phòng gia đình</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_price" class="form-label">Giá phòng (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_price" name="price" min="0" step="1000" required value="${room.price}">
                                </div>
                            </div>
                            
                            <!-- Cột phải -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_capacity" class="form-label">Sức chứa (người) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_capacity" name="capacity" min="1" max="10" required value="${room.capacity}">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select" id="edit_status" name="status" required>
                                        <option value="available" ${room.status === 'available' ? 'selected' : ''}>Trống</option>
                                        <option value="booked" ${room.status === 'booked' ? 'selected' : ''}>Đã đặt</option>
                                        <option value="maintenance" ${room.status === 'maintenance' ? 'selected' : ''}>Bảo trì</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_area" class="form-label">Diện tích (m²)</label>
                                    <input type="number" class="form-control" id="edit_area" name="area" min="0" step="0.1" value="${room.area || ''}">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_room_image" class="form-label">Hình ảnh phòng</label>
                                    <input type="file" class="form-control" id="edit_room_image" name="room_image" accept="image/*">
                                    ${room.has_image ? '<p class="small mt-1">Phòng đã có hình ảnh. Tải lên hình mới để thay thế.</p>' : ''}
                                </div>
                            </div>
                            
                            <!-- Current Image -->
                            ${room.has_image ? `
                            <div class="col-12 mb-3">
                                <label class="form-label">Hình ảnh hiện tại</label>
                                <div class="text-center">
                                    <img src="${SITE_URL}/public/images/rooms/${room.id}.jpg?v=${new Date().getTime()}" 
                                        class="img-fluid rounded" style="max-height: 200px;" 
                                        alt="Room Image">
                                </div>
                            </div>
                            ` : ''}
                            
                            <!-- Mô tả -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">Mô tả</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="3">${room.description || ''}</textarea>
                                </div>
                            </div>
                        </div>
                    `;
                        } else {
                            // Show error
                            document.getElementById('editRoomContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Không thể tải thông tin phòng. Vui lòng thử lại sau.
                        </div>
                    `;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('editRoomContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Đã xảy ra lỗi khi tải thông tin phòng. Vui lòng thử lại sau.
                    </div>
                `;
                    });
            }

            // Function to confirm and delete room
            function confirmDeleteRoom(roomId) {
                if (confirm('Bạn có chắc muốn xóa phòng này? Hành động này không thể hoàn tác.')) {
                    window.location.href = `${SITE_URL}/hotel_staff/deleteRoom/${roomId}`;
                }
            }

            document.getElementById('editRoomForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Ngăn chặn form submit mặc định

                // Hiển thị spinner
                const submitBtn = document.querySelector('#editRoomModal button[type="submit"]');
                const spinner = document.createElement('span');
                spinner.className = 'spinner-border spinner-border-sm me-1';
                submitBtn.disabled = true;
                submitBtn.appendChild(spinner);

                // Lấy dữ liệu từ form
                const formData = new FormData(this);
                // Gửi request bằng fetch API
                fetch(`${SITE_URL}/hotel_staff/editRoom`, {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {

                        // Ẩn spinner
                        submitBtn.disabled = false;
                        spinner.remove();

                        if (data.success) {
                            // Hiển thị thông báo thành công
                            showToast('Cập nhật phòng thành công', 'success');

                            // Đóng modal
                            const editRoomModal = bootstrap.Modal.getInstance(document.getElementById('editRoomModal'));
                            editRoomModal.hide();

                            // Cập nhật UI bằng cách cập nhật phòng trong bảng và card view
                            updateRoomItem(formData);
                            updateStats();
                        } else {
                            // Hiển thị lỗi
                            showToast(data.message || 'Có lỗi xảy ra khi cập nhật phòng', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        submitBtn.disabled = false;
                        spinner.remove();
                        showToast('Đã xảy ra lỗi khi kết nối với máy chủ', 'danger');
                    });
            });
            // Xử lý form thêm phòng bằng AJAX
            document.getElementById('addRoomForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Ngăn chặn form submit mặc định

                // Hiển thị spinner
                const submitBtn = document.getElementById('addRoomSubmitBtn');
                const spinner = document.getElementById('addRoomSpinner');
                submitBtn.disabled = true;
                spinner.classList.remove('d-none');

                // Lấy dữ liệu từ form
                const formData = new FormData(this);
                // Gửi request bằng fetch API
                fetch(`${SITE_URL}/hotel_staff/addRoom`, {
                        method: 'POST',
                        body: formData,
                        // Không cần thiết lập Content-Type vì FormData tự xử lý
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Ẩn spinner
                        submitBtn.disabled = false;
                        spinner.classList.add('d-none');

                        if (data.success) {
                            // Hiển thị thông báo thành công
                            showToast('Thêm phòng thành công', 'success');

                            // Đóng modal
                            const addRoomModal = bootstrap.Modal.getInstance(document.getElementById('addRoomModal'));
                            addRoomModal.hide();

                            // Cập nhật UI bằng cách thêm phòng mới vào bảng và card view
                            addRoomItem(data.room);
                            // Cập nhật thống kê
                            updateStats();

                            // Reset form
                            document.getElementById('addRoomForm').reset();
                        } else {
                            // Hiển thị lỗi
                            showToast(data.message || 'Có lỗi xảy ra khi thêm phòng', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        submitBtn.disabled = false;
                        spinner.classList.add('d-none');
                        showToast('Đã xảy ra lỗi khi kết nối với máy chủ', 'danger');
                    });
            });

            // Hàm hiển thị toast message
            function showToast(message, type = 'info') {
                // Tạo một toast element
                const toastEl = document.createElement('div');
                toastEl.className = `toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3`;
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');

                toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

                // Thêm vào body
                document.body.appendChild(toastEl);

                // Khởi tạo và hiển thị toast
                const toast = new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000
                });
                toast.show();

                // Xóa toast element sau khi ẩn
                toastEl.addEventListener('hidden.bs.toast', function() {
                    document.body.removeChild(toastEl);
                });
            }

            // Hàm cập nhật danh sách phòng
            function addRoomItem(room) {
                // Tạo các biến cần thiết
                const statusMap = {
                    'available': {
                        class: 'success',
                        text: 'Trống',
                        icon: 'check-circle'
                    },
                    'booked': {
                        class: 'danger',
                        text: 'Đã đặt',
                        icon: 'calendar-check'
                    },
                    'maintenance': {
                        class: 'warning',
                        text: 'Bảo trì',
                        icon: 'tools'
                    }
                };
                const roomTypeMap = {
                    'single': 'Phòng đơn',
                    'double': 'Phòng đôi',
                    'suite': 'Suite',
                    'family': 'Phòng gia đình'
                };

                const status = statusMap[room.status] || {
                    class: 'secondary',
                    text: 'Không xác định',
                    icon: 'question-circle'
                };
                const roomType = roomTypeMap[room.room_type] || room.room_type;

                // 1. Cập nhật table view
                const tableBody = document.querySelector('#tableView table tbody');
                const noRoomsRow = tableBody.querySelector('tr td[colspan="7"]');

                // Nếu không có phòng, xóa hàng "Không tìm thấy phòng nào"
                if (noRoomsRow) {
                    tableBody.innerHTML = '';
                }

                // Thêm hàng mới
                const newTableRow = document.createElement('tr');
                newTableRow.innerHTML = `
                <td>${room.room_number}</td>
                <td>
                    <strong>${room.name}</strong>
                    ${room.description ? `<span class="d-block small text-muted text-truncate" style="max-width: 250px;">${room.description}</span>` : ''}
                </td>
                <td>${roomType}</td>
                <td class="text-end fw-bold">${new Intl.NumberFormat('vi-VN').format(room.price)}</td>
                <td class="text-center">${room.capacity} người</td>
                <td class="text-center">
                    <span class="badge bg-${status.class}">${status.text}</span>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary edit-room-btn" data-room-id="${room.id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger delete-room-btn" data-room-id="${room.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            `;
                tableBody.prepend(newTableRow);

                // 2. Cập nhật card view
                const cardContainer = document.querySelector('#cardView .row');
                const noRoomsCard = cardContainer.querySelector('.col-12.text-center');

                // Nếu không có phòng, xóa thông báo "Không tìm thấy phòng nào"
                if (noRoomsCard) {
                    cardContainer.innerHTML = '';
                }

                // Thêm card mới
                const newCard = document.createElement('div');
                newCard.className = 'col-md-6 col-xl-4';
                newCard.innerHTML = `
                <div class="card shadow-sm h-100">
                    <div class="position-relative">
                        <img src="${SITE_URL}/public/images/rooms/${room.id}.jpg"
                            class="card-img-top" alt="${room.name}"
                            onerror="this.src='${SITE_URL}/public/images/rooms/default.jpg'" style="height: 160px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-${status.class}">
                                <i class="bi bi-${status.icon}"></i> ${status.text}
                            </span>
                        </div>
                        <div class="position-absolute bottom-0 start-0 m-2">
                            <span class="badge bg-dark">${room.room_number}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-1">${room.name}</h5>
                        <p class="card-text text-muted small mb-2">${roomType}</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-5 fw-bold text-primary">${new Intl.NumberFormat('vi-VN').format(room.price)} VNĐ</span>
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-people"></i> ${room.capacity} người
                            </span>
                        </div>
                        ${room.description ? `<p class="card-text small mb-0 text-truncate" title="${room.description}">${room.description}</p>` : ''}
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-primary edit-room-btn" data-room-id="${room.id}">
                                <i class="bi bi-pencil"></i> Sửa
                            </button>
                            <button type="button" class="btn btn-outline-danger delete-room-btn" data-room-id="${room.id}">
                                <i class="bi bi-trash"></i> Xóa
                            </button>
                        </div>
                    </div>
                </div>
            `;
                cardContainer.prepend(newCard);

                // 3. Cập nhật số phòng hiển thị
                const countDisplay = document.querySelector('.d-flex.justify-content-between.align-items-center.mb-3 .text-muted');
                if (countDisplay) {
                    const currentCount = parseInt(countDisplay.textContent.match(/\d+/)[0]) || 0;
                    countDisplay.textContent = `Hiển thị ${currentCount + 1} phòng`;
                }

                // 4. Gắn event listener cho các nút mới
                newTableRow.querySelector('.edit-room-btn').addEventListener('click', function() {
                    loadRoomDetails(room.id);
                });

                newTableRow.querySelector('.delete-room-btn').addEventListener('click', function() {
                    confirmDeleteRoom(room.id);
                });


                // Tương tự cho card view
                newCard.querySelector('.edit-room-btn').addEventListener('click', function() {
                    loadRoomDetails(room.id);
                });

                newCard.querySelector('.delete-room-btn').addEventListener('click', function() {
                    confirmDeleteRoom(room.id);
                });
            }

            function updateRoomItem(formData) {
                const roomId = formData.get('room_id');
                const roomNumber = formData.get('room_number');
                const roomName = formData.get('name');
                const roomType = formData.get('room_type');
                const price = formData.get('price');
                const capacity = formData.get('capacity');
                const status = formData.get('status');
                const area = formData.get('area') || '';
                const description = formData.get('description') || '';

                // Định nghĩa status class và text dựa trên giá trị status
                const statusInfo = {
                    'available': {
                        class: 'success',
                        text: 'Trống',
                        icon: 'check-circle'
                    },
                    'booked': {
                        class: 'danger',
                        text: 'Đã đặt',
                        icon: 'calendar-check'
                    },
                    'maintenance': {
                        class: 'warning',
                        text: 'Bảo trì',
                        icon: 'tools'
                    }
                } [status] || {
                    class: 'secondary',
                    text: 'Không xác định',
                    icon: 'question-circle'
                };

                // Định nghĩa tên loại phòng
                const roomTypeMap = {
                    'single': 'Phòng đơn',
                    'double': 'Phòng đôi',
                    'suite': 'Suite',
                    'family': 'Phòng gia đình'
                };
                const roomTypeName = roomTypeMap[roomType] || roomType;

                // Tìm kiếm row dựa trên nút edit có data-room-id
                const editButton = document.querySelector(`#tableView table tbody button.edit-room-btn[data-room-id="${roomId}"]`);
                const tableRow = editButton?.closest('tr');

                if (tableRow) {
                    tableRow.innerHTML = `
                    <td>${roomNumber}</td>
                    <td>
                        <strong>${roomName}</strong>
                        ${description ? `<span class="d-block small text-muted text-truncate" style="max-width: 250px;">${description}</span>` : ''}
                    </td>
                    <td>${roomTypeName}</td>
                    <td class="text-end fw-bold">${new Intl.NumberFormat('vi-VN').format(price)}</td>
                    <td class="text-center">${capacity} người</td>
                    <td class="text-center">
                        <span class="badge bg-${statusInfo.class}">${statusInfo.text}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary edit-room-btn" data-room-id="${roomId}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger delete-room-btn" data-room-id="${roomId}">
                                <i class="bi bi-trash"></i>
                            </button>

                        </div>
                    </td>
                `;

                    // Gắn lại sự kiện lắng nghe cho các nút
                    tableRow.querySelector('.edit-room-btn').addEventListener('click', function() {
                        loadRoomDetails(roomId);
                    });

                    tableRow.querySelector('.delete-room-btn').addEventListener('click', function() {
                        confirmDeleteRoom(roomId);
                    });
                } else {
                    // Nếu không tìm thấy hàng, có thể là do đã xóa
                    console.error('Không tìm thấy hàng phòng để cập nhật');
                }
            }

            // Hàm cập nhật thống kê
            function updateStats() {
                fetch(`${SITE_URL}/hotel_staff/getRoomStats`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const stats = data.stats;
                            document.getElementById('totalRooms').textContent = stats.total;
                            document.getElementById('availableRooms').textContent = stats.available;
                            document.getElementById('bookedRooms').textContent = stats.booked;
                            document.getElementById('maintenanceRooms').textContent = stats.maintenance;
                        } else {
                            console.error('Không thể cập nhật thống kê phòng');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            // Thêm event listener cho các dropdown để lọc ngay khi thay đổi
            document.getElementById('roomType').addEventListener('change', applyFilter);
            document.getElementById('roomStatus').addEventListener('change', applyFilter);
            document.getElementById('priceRange').addEventListener('change', applyFilter);


            function applyFilter() {
                // Hiển thị trạng thái đang tải
                const tableBody = document.querySelector('#tableView table tbody');
                const cardContainer = document.querySelector('#cardView .row');

                tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <p class="mt-2">Đang lọc dữ liệu...</p>
                    </td>
                </tr>
            `;

                cardContainer.innerHTML = `
                <div class="col-12 text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang lọc dữ liệu...</p>
                </div>
            `;

                // Lấy dữ liệu từ form filter
                const formData = new FormData(document.getElementById('filterForm'));

                // Gửi yêu cầu lọc đến server
                formData.forEach((value, key) => {

                });
                fetch(`${SITE_URL}/hotel_staff/filterRooms`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.success) {
                            // Cập nhật UI với kết quả lọc
                            updateRoomsUI(data.rooms);

                            // Cập nhật thống kê
                            if (data.stats) {
                                updateStats(data.stats);
                            }

                            // Cập nhật số lượng hiển thị
                            const countDisplay = document.querySelector('.d-flex.justify-content-between.align-items-center.mb-3 .text-muted');
                            if (countDisplay) {
                                countDisplay.textContent = `Hiển thị ${data.rooms.length} phòng`;
                            }
                        } else {
                            showToast(data.message || 'Có lỗi xảy ra khi lọc dữ liệu', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Đã xảy ra lỗi khi kết nối với máy chủ', 'danger');

                        // Hiển thị thông báo lỗi trong UI
                        tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-3 d-block mb-2"></i>
                                Không thể tải dữ liệu. Vui lòng thử lại sau.
                            </td>
                        </tr>
                    `;
                        cardContainer.innerHTML = `
                        <div class="col-12 text-center py-4">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-3 d-block mb-2"></i>
                            Không thể tải dữ liệu. Vui lòng thử lại sau.
                        </div>
                    `;
                    });
            }

            // Hàm cập nhật giao diện phòng từ dữ liệu AJAX
            function updateRoomsUI(rooms) {
                const tableBody = document.querySelector('#tableView table tbody');
                const cardContainer = document.querySelector('#cardView .row');

                // Xóa nội dung hiện tại
                tableBody.innerHTML = '';
                cardContainer.innerHTML = '';

                if (rooms.length === 0) {
                    // Hiển thị thông báo không có phòng
                    tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-search text-muted fs-3 d-block mb-2"></i>
                            Không tìm thấy phòng nào phù hợp với điều kiện lọc
                        </td>
                    </tr>
                `;

                    cardContainer.innerHTML = `
                    <div class="col-12 text-center py-4">
                        <i class="bi bi-search text-muted fs-3 d-block mb-2"></i>
                        Không tìm thấy phòng nào phù hợp với điều kiện lọc
                    </div>
                `;
                    return;
                }

                // Thêm từng phòng vào UI
                rooms.forEach(room => {
                    // Sử dụng cùng logic từ hàm addRoomItem để thêm phòng vào table view và card view
                    addRoomItem(room);
                });

                // Gắn lại event listeners cho các nút
                addEventListenersToRoomButtons();
            }



            // Gắn lại event listeners cho các nút sau khi cập nhật UI
            function addEventListenersToRoomButtons() {
                // Gắn lại event listeners cho các nút chỉnh sửa
                document.querySelectorAll('.edit-room-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const roomId = this.getAttribute('data-room-id');
                        loadRoomDetails(roomId);
                    });
                });

                // Gắn lại event listeners cho các nút xóa
                document.querySelectorAll('.delete-room-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const roomId = this.getAttribute('data-room-id');
                        confirmDeleteRoom(roomId);
                    });
                });
            }
        });
    </script>