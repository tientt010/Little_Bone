<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="container">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center py-3 mb-4 border-bottom">
        <div class="d-flex align-items-center">
            <h1 class="h3 mb-0"><?= htmlspecialchars($hotel['name'] ?? 'Khách sạn') ?></h1>
            <span class="ms-3 badge bg-<?= ($hotel['status'] ?? 'inactive') == 'active' ? 'success' : 'secondary' ?>">
                <?= ($hotel['status'] ?? 'inactive') == 'active' ? 'Đang hoạt động' : 'Không hoạt động' ?>
            </span>
        </div>
        <!-- Chỉnh thời gian -->
        <div class="position-relative">
            <button type="button" class="btn btn-sm filter-btn" data-bs-toggle="modal" data-bs-target="#dateFilterModal">
                <i class="bi bi-gear-fill me-1"></i>
            </button>
        </div>
    </header>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4" id="stats-cards">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Tổng đơn đặt phòng</h6>
                            <h2 class="card-title mb-0" id="total-bookings"><?= $stats['total_bookings'] ?? 0 ?></h2>
                            <div class="small" id="booking-trend-container">
                                <span class="<?= ($stats['booking_trend'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <i class="bi bi-arrow-<?= ($stats['booking_trend'] ?? 0) >= 0 ? 'up' : 'down' ?>"></i>
                                    <span id="booking-trend"><?= abs($stats['booking_trend'] ?? 0) ?></span>% so với kỳ trước
                                </span>
                            </div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-check text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Doanh thu</h6>
                            <h2 class="card-title mb-0" id="total-revenue"><?= number_format(($stats['total_revenue'] ?? 0) / 1000000, 1) ?>M</h2>
                            <div class="small" id="revenue-trend-container">
                                <span class="<?= ($stats['revenue_trend'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <i class="bi bi-arrow-<?= ($stats['revenue_trend'] ?? 0) >= 0 ? 'up' : 'down' ?>"></i>
                                    <span id="revenue-trend"><?= abs($stats['revenue_trend'] ?? 0) ?></span>% so với kỳ trước
                                </span>
                            </div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-cash-stack text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Tỉ lệ lấp đầy</h6>
                            <h2 class="card-title mb-0" id="occupancy-rate"><?= $stats['occupancy_rate'] ?? 0 ?>%</h2>
                            <div class="small" id="occupancy-trend-container">
                                <span class="<?= ($stats['occupancy_trend'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <i class="bi bi-arrow-<?= ($stats['occupancy_trend'] ?? 0) >= 0 ? 'up' : 'down' ?>"></i>
                                    <span id="occupancy-trend"><?= abs($stats['occupancy_trend'] ?? 0) ?></span>% so với kỳ trước
                                </span>
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-bar-chart-fill text-info fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Đánh giá trung bình</h6>
                            <h2 class="card-title mb-0" id="avg-rating"><?= number_format($stats['avg_rating'] ?? 0, 1) ?>/5</h2>
                            <div class="small" id="rating-trend-container">
                                <span class="<?= ($stats['rating_trend'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <i class="bi bi-arrow-<?= ($stats['rating_trend'] ?? 0) >= 0 ? 'up' : 'down' ?>"></i>
                                    <span id="rating-trend"><?= abs($stats['rating_trend'] ?? 0) ?></span> điểm so với kỳ trước
                                </span>
                            </div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-star-fill text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <!-- Revenue Chart -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Doanh thu theo thời gian</h5>
                </div>
                <div class="card-body pb-2">
                    <div id="revenueChart" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>

        <!-- Occupancy Charts -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Tỉ lệ lấp đầy</h5>
                </div>
                <div class="card-body pb-2">
                    <div id="occupancyTrend" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <!-- Ratings Chart -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Đánh giá khách hàng theo thời gian</h5>
                </div>
                <div class="card-body pb-2">
                    <div id="ratingsChart" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>

        <!-- Room Type Distribution Chart -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Phân bố đặt phòng theo loại</h5>
                </div>
                <div class="card-body pb-2">
                    <div id="roomTypeChart" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách các đơn đặt phòng -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Đơn đặt phòng gần đây</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="bookingsTable">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Số phòng</th>
                                    <th>Thanh toán</th>
                                    <th>Ngày đặt</th>
                                    <th>Check in</th>
                                    <th>Check out</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody id="bookings-data">
                                <?php if (empty($recent_bookings)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Không có đơn đặt phòng nào gần đây</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_bookings as $booking): ?>
                                        <tr>
                                            <td data-order="<?= $booking['booking_item_id'] ?>">#<?= $booking['booking_item_id'] ?></td>
                                            <td><?= $booking['room_number'] ?></td>
                                            <td data-order="<?= $booking['total_price'] ?>"><?= number_format($booking['total_price']) ?> ₫</td>
                                            <td data-order="<?= strtotime($booking['booking_date']) ?>"><?= date('d/m/Y', strtotime($booking['booking_date'])) ?></td>
                                            <td data-order="<?= strtotime($booking['check_in']) ?>"><?= date('d/m/Y', strtotime($booking['check_in'])) ?></td>
                                            <td data-order="<?= strtotime($booking['check_out']) ?>"><?= date('d/m/Y', strtotime($booking['check_out'])) ?></td>
                                            <td>
                                                <?php
                                                $statusClasses = [
                                                    'pending' => 'warning text-dark',
                                                    'confirmed' => 'success',
                                                    'checked_in' => 'info',
                                                    'completed' => 'primary',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusTexts = [
                                                    'pending' => 'Chờ xác nhận',
                                                    'confirmed' => 'Đã xác nhận',
                                                    'checked_in' => 'Đang check-in',
                                                    'completed' => 'Hoàn thành',
                                                    'cancelled' => 'Đã hủy'
                                                ];
                                                $statusClass = $statusClasses[$booking['status']] ?? 'secondary';
                                                $statusText = $statusTexts[$booking['status']] ?? 'Không xác định';
                                                ?>
                                                <span class="badge badge-booking bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Filter Modal -->
    <div class="modal fade" id="dateFilterModal" tabindex="-1" aria-labelledby="dateFilterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateFilterModalLabel">Cài đặt thống kê</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label date-filter-header">Tuỳ chọn thời gian</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary nav-link" data-value="this_week">Tuần này</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary nav-link" data-value="last_week">Tuần trước</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary nav-link" data-value="this_month">Tháng này</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary nav-link" data-value="last_month">Tháng trước</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary nav-link active" data-value="this_year">Năm này</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary nav-link" data-value="last_year">Năm trước</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="statsType" name="stats_type" value="this_year">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="date-filter-header">Thời gian bắt đầu</div>
                            <input type="text" class="form-control" id="startDate" name="start_date"
                                placeholder="Ngày bắt đầu..." readonly>
                        </div>
                        <div class="col-md-6">
                            <div class="date-filter-header">Thời gian kết thúc</div>
                            <input type="text" class="form-control" id="endDate" name="end_date"
                                placeholder="Ngày kết thúc..." readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="button" class="btn btn-primary" id="applyDateFilter">Áp dụng</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer">

    </div>


    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            padding: 15px 20px;
            font-weight: 500;
        }

        .stats-card {
            transition: all 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .apexcharts-toolbar {
            z-index: 2 !important;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .star-rating .bi-star-fill {
            color: #ffc107;
        }

        .badge-booking {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .filter-btn {
            position: absolute;
            top: 12px;
            right: 15px;
            z-index: 10;
        }

        .date-filter-header {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }

        /* Add new styles for AJAX features */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Fade effect for content updates */
        .fade-update {
            animation: fadeUpdate 0.5s ease;
        }

        @keyframes fadeUpdate {
            0% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }
    </style>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // Truyền dữ liệu PHP sang JavaScript
        window.SITE_URL = "<?= SITE_URL ?>";
        window.dashboardData = {
            charts: {
                revenue: <?= json_encode($charts['revenue'] ?? []) ?>,
                occupancy: <?= json_encode($charts['occupancy'] ?? []) ?>,
                room_types: <?= json_encode($charts['room_types'] ?? []) ?>,
                ratings: <?= json_encode($charts['ratings'] ?? []) ?>
            }
        };
    </script>

    <script src="<?= SITE_URL ?>/public/js/dashboard.js?v=<?= time() ?>"></script>