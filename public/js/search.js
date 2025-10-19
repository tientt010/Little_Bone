document.addEventListener("DOMContentLoaded", function () {
    const searchCheckInInput = document.getElementById("search_check_in");
    const searchCheckOutInput = document.getElementById("search_check_out");

    if (searchCheckInInput && searchCheckOutInput) {
        const today = new Date().toISOString().split("T")[0];
        searchCheckInInput.min = today;

        function updateSearchCheckOutMin() {
            if (searchCheckInInput.value) {
                // Ngày trả phòng tối thiểu là ngày nhận phòng + 1
                const checkInDate = new Date(searchCheckInInput.value);
                checkInDate.setDate(checkInDate.getDate() + 1);
                searchCheckOutInput.min = checkInDate
                    .toISOString()
                    .split("T")[0];
            } else {
                // Nếu không có ngày nhận phòng, đặt ngày trả phòng tối thiểu là ngày mai
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                searchCheckOutInput.min = tomorrow.toISOString().split("T")[0];
            }
        }
        searchCheckInInput.addEventListener("change", function () {
            updateSearchCheckOutMin();

            if (
                searchCheckOutInput.value &&
                searchCheckOutInput.value <= this.value
            ) {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                searchCheckOutInput.value = nextDay.toISOString().split("T")[0];
            }
        });

        // Validate
        searchCheckOutInput.addEventListener("change", function () {
            if (
                searchCheckInInput.value &&
                this.value <= searchCheckInInput.value
            ) {
                const nextDay = new Date(searchCheckInInput.value);
                nextDay.setDate(nextDay.getDate() + 1);
                this.value = nextDay.toISOString().split("T")[0];
                alert(
                    "Ngày trả phòng phải sau ngày nhận phòng. Đã tự động điều chỉnh."
                );
            }
        });

        updateSearchCheckOutMin();

        if (searchCheckInInput.value && !searchCheckOutInput.value) {
            const nextDay = new Date(searchCheckInInput.value);
            nextDay.setDate(nextDay.getDate() + 1);
            searchCheckOutInput.value = nextDay.toISOString().split("T")[0];
        }
    }

    // Handle sort changes
    const sortBySelect = document.getElementById("sort_by");
    const sortDirSelect = document.getElementById("sort_dir");

    if (sortBySelect && sortDirSelect) {
        sortBySelect.addEventListener("change", updateSorting);
        sortDirSelect.addEventListener("change", updateSorting);
    }

    function updateSorting() {
        // Cập nhật các input ẩn trong form lọc
        document.querySelector('#filterForm input[name="sort_by"]').value =
            sortBySelect.value;
        document.querySelector('#filterForm input[name="sort_dir"]').value =
            sortDirSelect.value;

        // Đặt lại về trang 1 khi thay đổi sắp xếp
        document.querySelector("#page_input").value = 1;

        // Submit the form
        document.getElementById("filterForm").submit();
    }
});
document.addEventListener("DOMContentLoaded", function () {
    // Xử lý khi thay đổi thứ tự sắp xếp
    const sortBySelect = document.getElementById("sort_by");
    const sortDirSelect = document.getElementById("sort_dir");

    if (sortBySelect && sortDirSelect) {
        sortBySelect.addEventListener("change", updateSorting);
        sortDirSelect.addEventListener("change", updateSorting);
    }

    function updateSorting() {
        document.querySelector('#filterForm input[name="sort_by"]').value =
            sortBySelect.value;
        document.querySelector('#filterForm input[name="sort_dir"]').value =
            sortDirSelect.value;
        document.getElementById("filterForm").submit();
    }

    // Xử lý khi click vào nút "Xem thêm ưu đãi"
    const showMoreButtons = document.querySelectorAll(".show-more-promos");
    showMoreButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const hotelId = this.getAttribute("data-hotel-id");
            const hotelName = this.getAttribute("data-hotel-name");

            // Cập nhật tên khách sạn trong modal
            document.getElementById("hotelNameInModal").textContent = hotelName;
            document.getElementById("promotionsContainer").innerHTML = `
                <div class="text-center w-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                </div>
            `;

            // Gọi API để lấy tất cả khuyến mãi
            fetch(SITE_URL + "/api/hotels/" + hotelId + "/promotions")
                .then((response) => response.json())
                .then((data) => {
                    if (data.promotions && data.promotions.length > 0) {
                        let html = "";
                        data.promotions.forEach((promo) => {
                            let discountText = "";
                            if (promo.discount_type === "percentage") {
                                discountText = `Giảm ${promo.discount_value}%`;
                            } else if (promo.discount_type === "fixed") {
                                discountText = `Giảm ${new Intl.NumberFormat(
                                    "vi-VN"
                                ).format(promo.discount_value)} VNĐ`;
                            }

                            html += `
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="promotion-banner">
                                            <img src="${
                                                promo.image
                                                    ? SITE_URL +
                                                      "/public/images/promotions/" +
                                                      promo.image
                                                    : SITE_URL +
                                                      "/public/images/promotions/default.jpg"
                                            }" 
                                                class="card-img-top promotion-image" 
                                                alt="${promo.title}"
                                                onerror="this.src='${SITE_URL}/public/images/promotions/default.jpg';">
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <i class="bi ${
                                                    promo.icon || "bi-tag"
                                                }"></i> 
                                                ${promo.title}
                                            </h5>
                                            <div class="badge bg-success mb-2">${discountText}</div>
                                            <p class="card-text">${
                                                promo.description
                                            }</p>
                                            <div class="small text-muted mt-2">
                                                Mã: <strong>${
                                                    promo.code
                                                }</strong><br>
                                                Hiệu lực: ${formatDate(
                                                    promo.start_date
                                                )} - ${formatDate(
                                promo.end_date
                            )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        document.getElementById(
                            "promotionsContainer"
                        ).innerHTML = html;
                    } else {
                        document.getElementById(
                            "promotionsContainer"
                        ).innerHTML =
                            '<div class="col-12 text-center">Không có khuyến mãi nào.</div>';
                    }
                })
                .catch((error) => {
                    document.getElementById("promotionsContainer").innerHTML =
                        '<div class="col-12 text-center text-danger">Đã xảy ra lỗi khi tải dữ liệu.</div>';
                    console.error("Lỗi khi tải khuyến mãi:", error);
                });
        });
    });

    // Hàm format ngày tháng theo định dạng Việt Nam
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString("vi-VN", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        });
    }
});

// Hàm xử lý thêm/xóa khách sạn khỏi danh sách yêu thích
function toggleFavorite(button, hotelId) {
    fetch(`${SITE_URL}/api/favorites/toggle`, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-Requested-With": "XMLHttpRequest",
        },
        body: `hotel_id=${hotelId}`,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Cập nhật giao diện nút
                const icon = button.querySelector("i");
                if (data.is_favorite) {
                    icon.className = "bi bi-heart-fill";
                    button.classList.add("active");
                } else {
                    icon.className = "bi bi-heart";
                    button.classList.remove("active");
                }
            } else {
                showToast(
                    data.message || "Có lỗi xảy ra. Vui lòng thử lại sau.",
                    "error"
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showToast("Có lỗi xảy ra khi xử lý yêu cầu", "error");
        });
}

// Hàm hiển thị thông báo
function showToast(message, type = "info") {
    if (typeof bootstrap !== "undefined" && bootstrap.Toast) {
        const toastEl = document.createElement("div");
        toastEl.className = `toast align-items-center text-white bg-${
            type === "error" ? "danger" : type
        } border-0`;
        toastEl.setAttribute("role", "alert");
        toastEl.setAttribute("aria-live", "assertive");
        toastEl.setAttribute("aria-atomic", "true");

        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        // Thêm vào container
        const toastContainer = document.querySelector(".toast-container");
        if (!toastContainer) {
            const container = document.createElement("div");
            container.className =
                "toast-container position-fixed top-0 end-0 p-3";
            document.body.appendChild(container);
            container.appendChild(toastEl);
        } else {
            toastContainer.appendChild(toastEl);
        }

        // Hiển thị toast
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();

        // Xóa toast sau khi ẩn
        toastEl.addEventListener("hidden.bs.toast", function () {
            toastEl.remove();
        });
    } else {
        // Fallback nếu không có Bootstrap
        alert(message);
    }
}

// Hàm thay đổi ảnh chính khi click vào ảnh thu nhỏ
function changeMainImage(thumbnailElement, hotelId, imageSrc) {
    // Đổi ảnh chính
    document.getElementById("main-image-" + hotelId).src = imageSrc;

    // Cập nhật trạng thái active
    const hotelContainer = thumbnailElement.closest(".hotel-image-wrapper");
    const thumbnails = hotelContainer.querySelectorAll(".thumbnail");

    thumbnails.forEach((thumb) => {
        thumb.classList.remove("active");
    });

    thumbnailElement.classList.add("active");
}
