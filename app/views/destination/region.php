<div class="container mt-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="page-title">Khám phá <?php echo $currentRegion; ?></h1>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="dropdown region-selector">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="regionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-globe"></i> Chọn khu vực
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="regionDropdown">
                    <?php foreach ($allRegions as $regionName): ?>
                        <?php if (!empty($regionName)): ?>
                            <li>
                                <a class="dropdown-item <?php echo ($regionName === $currentRegion) ? 'active' : ''; ?>"
                                    href="<?php echo SITE_URL . '/regions/' . urlencode($regionName); ?>">
                                    <?php echo $regionName; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="stats-banner mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <h4><?php echo count($destinations); ?></h4>
                        <p>Địa điểm du lịch</p>
                    </div>
                    <div class="col">
                        <h4><?php
                            $totalHotels = array_sum(array_column($destinations, 'hotel_count'));
                            echo $totalHotels;
                            ?></h4>
                        <p>Khách sạn</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Destinations list -->
    <div class="destinations-list">
        <?php if (empty($destinations)): ?>
            <div class="alert alert-info">Không có địa điểm du lịch nào ở <?php echo $currentRegion; ?></div>
        <?php else: ?>
            <?php foreach ($destinations as $destination): ?>
                <div class="card mb-3 destination-card animate__animated animate__fadeIn">
                    <div class="row g-0 h-100">
                        <div class="col-md-4">
                            <div class="destination-image-container">
                                <img src="<?php echo SITE_URL; ?>/public/images/cities/<?php echo $destination['image'] ?? 'default.jpg'; ?>"
                                    class="img-fluid destination-image"
                                    alt="<?php echo $destination['name']; ?>"
                                    onerror="this.src='<?php echo SITE_URL; ?>/public/images/cities/default.jpg';">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-body h-100 d-flex flex-column">
                                <h5 class="card-title"><?php echo $destination['name']; ?></h5>
                                <p class="card-text location"><i class="bi bi-geo-alt"></i> <?php echo $destination['address']; ?></p>
                                <p class="card-text description flex-grow-1"><?php echo $destination['description']; ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <div class="hotel-count">
                                        <i class="bi bi-building"></i> <?php echo $destination['hotel_count']; ?> khách sạn
                                    </div>
                                    <a href="<?php echo SITE_URL . '/search?city=' . urlencode($destination['name']); ?>" class="btn btn-primary">
                                        Xem khách sạn
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .destination-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        height: 250px;
    }

    .destination-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .destination-image-container {
        height: 100%;
        overflow: hidden;
        border-radius: 0.375rem 0 0 0.375rem;
    }

    .destination-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .destination-card:hover .destination-image {
        transform: scale(1.05);
    }

    .stats-banner {
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .region-selector .dropdown-menu {
        max-height: 300px;
        overflow-y: auto;
    }

    .description {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.4;
        max-height: 4.2em;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .location {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }

    .hotel-count {
        color: #495057;
        font-size: 0.9rem;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .destination-card {
            height: auto;
            min-height: 300px;
        }

        .destination-image-container {
            height: 200px;
            border-radius: 0.375rem 0.375rem 0 0;
        }

        .description {
            -webkit-line-clamp: 2;
            max-height: 2.8em;
        }
    }
</style>