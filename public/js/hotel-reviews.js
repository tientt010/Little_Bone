class HotelReviews {
    constructor(hotelId) {
        this.hotelId = hotelId;
        this.currentPage = 1;
        this.pageSize = 5;
        this.hasMoreReviews = true;
        this.activeFilters = [];
        this.sortBy = "created_at";
        this.sortDirection = "desc";

        this.reviewsContainer = document.getElementById("reviewsContainer");
        this.loadMoreContainer = document.getElementById("loadMoreContainer");
        this.reviewForm = document.getElementById("reviewForm");
        this.reviewFormModal = document.getElementById("reviewFormModal");

        this.isSubmitting = false;
        this.newlyAddedReviewId = null;
    }

    init() {
        if (!this.hotelId) {
            console.error("Hotel ID is missing!");
            return;
        }

        this.initializeEventListeners();
        this.loadReviews(true);
    }

    initializeEventListeners() {
        const loadMoreBtn = document.getElementById("loadMoreBtn");
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener("click", () => this.loadMoreReviews());
        }

        const writeReviewBtn = document.getElementById("writeReviewBtn");
        if (writeReviewBtn) {
            writeReviewBtn.addEventListener("click", () =>
                this.openReviewForm()
            );
        }

        const closeFormBtn = document.getElementById("closeReviewForm");
        if (closeFormBtn) {
            closeFormBtn.addEventListener("click", () =>
                this.closeReviewForm()
            );
        }

        if (this.reviewForm) {
            this.reviewForm.addEventListener("submit", (e) =>
                this.handleReviewSubmit(e)
            );
        }

        const imageInput = document.getElementById("review_images");
        if (imageInput) {
            this.setupImagePreview(imageInput, "imagePreviewContainer");
        }

        const starFilters = document.querySelectorAll(".star-filter");
        if (starFilters && starFilters.length > 0) {
            starFilters.forEach((filter) => {
                filter.addEventListener("click", () =>
                    this.toggleStarFilter(filter)
                );
            });
        }

        const sortSelect = document.getElementById("sortSelect");
        if (sortSelect) {
            sortSelect.addEventListener("change", () =>
                this.handleSortChange()
            );
        }

        const sortDirectionBtn = document.getElementById("sortDirectionBtn");
        if (sortDirectionBtn) {
            sortDirectionBtn.addEventListener("click", () =>
                this.toggleSortDirection()
            );
        }

        const dropzone = document.getElementById("imageDropzone");
        if (dropzone) {
            ["dragenter", "dragover", "dragleave", "drop"].forEach(
                (eventName) => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                }
            );

            ["dragenter", "dragover"].forEach((eventName) => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.add("dragover");
                });
            });

            ["dragleave", "drop"].forEach((eventName) => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove("dragover");
                });
            });

            dropzone.addEventListener("drop", (e) => {
                const fileInput = document.getElementById("review_images");
                if (fileInput) {
                    fileInput.files = e.dataTransfer.files;

                    const event = new Event("change", { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            });

            dropzone.addEventListener("click", () => {
                const fileInput = document.getElementById("review_images");
                if (fileInput) fileInput.click();
            });
        }

        const commentTextarea = document.getElementById("comment");
        const charCount = document.getElementById("charCount");
        if (commentTextarea && charCount) {
            commentTextarea.addEventListener("input", function () {
                const count = this.value.length;
                charCount.textContent = count;

                if (count > 400) {
                    charCount.style.color = "#e27730";
                } else if (count > 300) {
                    charCount.style.color = "#ffc107";
                } else {
                    charCount.style.color = "#6c757d";
                }
            });
        }

        const ratingInputs = document.querySelectorAll(
            '.rating-input input[name="rating"]'
        );
        const ratingText = document.getElementById("ratingText");
        if (ratingInputs.length > 0 && ratingText) {
            const ratingLabels = {
                5: "Tuyệt vời",
                4: "Rất tốt",
                3: "Bình thường",
                2: "Tạm được",
                1: "Không hài lòng",
            };

            ratingInputs.forEach((input) => {
                input.addEventListener("change", function () {
                    ratingText.textContent = ratingLabels[this.value] || "";
                });
            });
        }
    }

    toggleStarFilter(filter) {
        const star = filter.getAttribute("data-star");

        filter.classList.toggle("active");

        if (filter.classList.contains("active")) {
            if (!this.activeFilters.includes(star)) {
                this.activeFilters.push(star);
            }
        } else {
            this.activeFilters = this.activeFilters.filter((s) => s !== star);
        }

        this.currentPage = 1;
        this.loadReviews(true);
    }

    handleSortChange() {
        const sortSelect = document.getElementById("sortSelect");
        if (sortSelect) {
            this.sortBy = sortSelect.value;
            this.currentPage = 1;
            this.loadReviews(true);
        }
    }

    toggleSortDirection() {
        this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";

        const sortDirectionBtn = document.getElementById("sortDirectionBtn");
        if (sortDirectionBtn) {
            const icon = sortDirectionBtn.querySelector("i");
            if (icon) {
                if (this.sortDirection === "asc") {
                    icon.className = "bi bi-sort-up";
                } else {
                    icon.className = "bi bi-sort-down";
                }
            }
        }

        this.currentPage = 1;
        this.loadReviews(true);
    }

    loadMoreReviews() {
        const loadMoreBtn = document.getElementById("loadMoreBtn");
        if (loadMoreBtn) {
            loadMoreBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang tải...';
            loadMoreBtn.disabled = true;

            this.loadReviews(false).finally(() => {
                loadMoreBtn.innerHTML =
                    '<i class="bi bi-plus-circle"></i> Xem thêm đánh giá';
                loadMoreBtn.disabled = false;
            });
        }
    }

    async loadReviews(reset = false) {
        if (!this.reviewsContainer) return;

        if (reset) {
            this.reviewsContainer.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang tải đánh giá...</p>
                </div>
            `;
            this.hasMoreReviews = true;
        }

        if (!this.hasMoreReviews) return;

        let apiUrl = `${SITE_URL}/api/hotels/${this.hotelId}/reviews?page=${this.currentPage}&pageSize=${this.pageSize}`;

        apiUrl += `&sort=${this.sortBy}&direction=${this.sortDirection}`;

        if (this.activeFilters.length > 0) {
            apiUrl += `&stars=${this.activeFilters.join(",")}`;
        }

        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (data.reviews && data.reviews.length > 0) {
                data.reviews.forEach((review, index) => {});
            }

            if (reset) {
                this.reviewsContainer.innerHTML = "";
            }

            if (data.success) {
                if (data.reviews.length === 0) {
                    if (this.currentPage === 1) {
                        this.showEmptyReviewsMessage();
                    }
                    this.hasMoreReviews = false;
                    if (this.loadMoreContainer) {
                        this.loadMoreContainer.style.display = "none";
                    }
                    return;
                }

                data.reviews.forEach((review) => {
                    if (
                        this.newlyAddedReviewId &&
                        review.id == this.newlyAddedReviewId
                    ) {
                        review.isNewlyAdded = true;
                        setTimeout(() => {
                            this.newlyAddedReviewId = null;
                        }, 2000);
                    }
                    const reviewElement = this.createReviewElement(review);
                    this.reviewsContainer.appendChild(reviewElement);
                });

                const newReview =
                    this.reviewsContainer.querySelector(".new-review");
                if (newReview && reset) {
                    setTimeout(() => {
                        newReview.scrollIntoView({
                            behavior: "smooth",
                            block: "center",
                        });
                    }, 300);
                }

                if (this.loadMoreContainer) {
                    this.loadMoreContainer.style.display =
                        this.hasMoreReviews &&
                        data.reviews.length >= this.pageSize
                            ? "block"
                            : "none";
                }

                this.currentPage++;
            } else {
                this.showErrorMessage(
                    data.message || "Không thể tải đánh giá."
                );
            }
        } catch (error) {
            console.error("Error loading reviews:", error);
            this.showErrorMessage(
                `Đã xảy ra lỗi khi tải đánh giá: ${error.message}`
            );
        }
    }

    showEmptyReviewsMessage() {
        if (this.activeFilters.length > 0) {
            this.reviewsContainer.innerHTML = `
                <div class="empty-reviews">
                    <i class="bi bi-filter-circle display-1 text-muted"></i>
                    <p class="mt-3 text-muted">Không có đánh giá nào phù hợp với bộ lọc bạn đã chọn.</p>
                    <button class="btn btn-outline-secondary btn-sm mt-2" id="clearFiltersBtn">
                        <i class="bi bi-x-circle"></i> Xóa bộ lọc
                    </button>
                </div>
            `;

            const clearFiltersBtn = document.getElementById("clearFiltersBtn");
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener("click", () =>
                    this.clearFilters()
                );
            }
        } else {
            this.reviewsContainer.innerHTML = `
                <div class="empty-reviews">
                    <i class="bi bi-chat-square-text display-1 text-muted"></i>
                    <p class="mt-3 text-muted">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</p>
                </div>
            `;
        }
    }

    showErrorMessage(message) {
        this.reviewsContainer.innerHTML += `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> ${message}
                <button class="btn btn-sm btn-outline-danger ms-2" onclick="hotelReviews.loadReviews(true)">
                    <i class="bi bi-arrow-clockwise"></i> Thử lại
                </button>
            </div>
        `;
        if (this.loadMoreContainer) {
            this.loadMoreContainer.style.display = "none";
        }
    }

    clearFilters() {
        const starFilters = document.querySelectorAll(".star-filter");
        starFilters.forEach((filter) => {
            filter.classList.remove("active");
        });

        this.activeFilters = [];

        this.currentPage = 1;
        this.loadReviews(true);
    }

    createReviewElement(review) {
        const isCurrentUserReview =
            window.currentUserId && window.currentUserId == review.user_id;

        const timestamp = new Date(review.created_at);
        const now = new Date();
        const diffMs = now - timestamp;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);

        let timeDisplay;
        if (diffSec < 60) {
            timeDisplay = "vừa xong";
        } else if (diffMin < 60) {
            timeDisplay =
                diffMin === 1 ? "1 phút trước" : `${diffMin} phút trước`;
        } else if (diffHour < 24) {
            timeDisplay =
                diffHour === 1 ? "1 giờ trước" : `${diffHour} giờ trước`;
        } else if (diffDay < 7) {
            const hours = timestamp.getHours().toString().padStart(2, "0");
            const minutes = timestamp.getMinutes().toString().padStart(2, "0");
            timeDisplay =
                diffDay === 1
                    ? `1 ngày trước lúc ${hours}:${minutes}`
                    : `${diffDay} ngày trước lúc ${hours}:${minutes}`;
        } else {
            const day = timestamp.getDate().toString().padStart(2, "0");
            const month = (timestamp.getMonth() + 1)
                .toString()
                .padStart(2, "0");
            const year = timestamp.getFullYear();
            const hours = timestamp.getHours().toString().padStart(2, "0");
            const minutes = timestamp.getMinutes().toString().padStart(2, "0");
            timeDisplay = `${day}/${month}/${year} lúc ${hours}:${minutes}`;
        }

        const reviewElement = document.createElement("div");
        reviewElement.className = `review-item star-${review.rating}${
            review.isNewlyAdded ? " new-review" : ""
        }`;

        reviewElement.innerHTML = `
            <div class="d-flex">
                <img src="${
                    review.avatar
                        ? `${SITE_URL}/public/images/avatars/${review.avatar}`
                        : `${SITE_URL}/public/images/avatars/default.png`
                }"
                    class="reviewer-avatar me-3" alt="${review.full_name}">
                <div class="flex-grow-1">
                    <div class="review-header">
                        <h5 class="review-author mb-0">${review.full_name}</h5>
                        ${
                            isCurrentUserReview
                                ? `
                            <button class="delete-review" title="Xóa đánh giá">
                                <i class="bi bi-trash"></i>
                            </button>
                        `
                                : ""
                        }
                    </div>
                    <div class="review-date text-muted mb-2">
                        <i class="bi bi-clock me-1"></i> ${timeDisplay}
                    </div>
                    <div class="review-rating mb-2">
                        ${Array(5)
                            .fill()
                            .map(
                                (_, i) => `
                            <i class="bi ${
                                i < review.rating ? "bi-star-fill" : "bi-star"
                            }"></i>
                        `
                            )
                            .join("")}
                    </div>
                    <div class="review-text mb-3">${review.comment || ""}</div>
                </div>
            </div>
            ${
                review.images && review.images.length > 0
                    ? `
                <div class="review-images">
                    ${review.images
                        .map(
                            (img) => `
                        <div class="review-image-container">
                            <img src="${SITE_URL}/public/images/reviews/${review.id}/${img}" 
                                class="review-image" alt="Review Image"
                                onclick="hotelReviews.openImageModal(this.src)">
                        </div>
                    `
                        )
                        .join("")}
                </div>
            `
                    : ""
            }
        `;

        if (true) {
            const deleteBtn = reviewElement.querySelector(".delete-review");
            if (deleteBtn) {
                deleteBtn.addEventListener("click", () =>
                    this.confirmDeleteReview(review.id)
                );
            }
        }

        return reviewElement;
    }

    formatReviewTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffMs = now - date;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);

        if (diffSec < 60) {
            return "vừa xong";
        } else if (diffMin < 60) {
            return diffMin === 1 ? "1 phút trước" : `${diffMin} phút trước`;
        } else if (diffHour < 24) {
            return diffHour === 1 ? "1 giờ trước" : `${diffHour} giờ trước`;
        } else if (diffDay < 7) {
            const hours = date.getHours().toString().padStart(2, "0");
            const minutes = date.getMinutes().toString().padStart(2, "0");
            return diffDay === 1
                ? `1 ngày trước lúc ${hours}:${minutes}`
                : `${diffDay} ngày trước lúc ${hours}:${minutes}`;
        } else {
            const day = date.getDate().toString().padStart(2, "0");
            const month = (date.getMonth() + 1).toString().padStart(2, "0");
            const year = date.getFullYear();
            const hours = date.getHours().toString().padStart(2, "0");
            const minutes = date.getMinutes().toString().padStart(2, "0");
            return `${day}/${month}/${year} lúc ${hours}:${minutes}`;
        }
    }

    openReviewForm() {
        this.reviewFormModal = document.getElementById("reviewFormModal");
        if (this.reviewFormModal) {
            this.reviewFormModal.style.display = "block";
            document.body.style.overflow = "hidden";
        } else {
            console.error("Review form modal not found!");
        }
    }

    closeReviewForm() {
        this.reviewFormModal = document.getElementById("reviewFormModal");
        if (this.reviewFormModal) {
            this.reviewFormModal.style.display = "none";
            document.body.style.overflow = "";
        } else {
            console.error("Review form modal not found!");
        }
    }

    safeCloseReviewForm() {
        try {
            this.reviewFormModal = document.getElementById("reviewFormModal");
            if (this.reviewFormModal) {
                this.reviewFormModal.style.display = "none";
                document.body.style.overflow = "";
            }
        } catch (error) {
            console.warn("Error when closing review form:", error);
        }
    }

    async handleReviewSubmit(e) {
        e.preventDefault();

        if (!this.reviewForm || this.isSubmitting) return;

        const rating = this.reviewForm.querySelector(
            'input[name="rating"]:checked'
        );

        const comment = this.reviewForm.querySelector(
            'textarea[name="comment"]'
        );
        const hotelId = this.reviewForm.querySelector('input[name="hotel_id"]');
        const fileInput = this.reviewForm.querySelector('input[type="file"]');

        if (!rating) {
            this.showToast("Lỗi", "Vui lòng chọn số sao đánh giá.", "error");
            return;
        }

        this.isSubmitting = true;

        const formElements = this.reviewForm.elements;
        for (let i = 0; i < formElements.length; i++) {
            formElements[i].disabled = true;
        }

        const formData = new FormData();

        formData.append("hotel_id", hotelId?.value || this.hotelId);
        formData.append("rating", rating.value);
        formData.append("comment", comment?.value || "");

        if (fileInput && fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append("review_images[]", fileInput.files[i]);
            }
        }

        const submitBtn = this.reviewForm.querySelector(
            'button[type="submit"]'
        );
        const originalBtnText = submitBtn
            ? submitBtn.innerHTML
            : "Gửi đánh giá";
        if (submitBtn) {
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang gửi...';
        }

        try {
            const response = await fetch(`${SITE_URL}/api/reviews`, {
                method: "POST",
                body: formData,
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.showToast(
                    "Thành công",
                    "Đánh giá của bạn đã được gửi thành công!",
                    "success"
                );

                this.reviewForm.reset();
                const imagePreviewContainer = document.getElementById(
                    "imagePreviewContainer"
                );
                if (imagePreviewContainer) {
                    imagePreviewContainer.innerHTML = "";
                }

                this.safeCloseReviewForm();

                setTimeout(() => {
                    this.newlyAddedReviewId = data.review_id;
                    this.currentPage = 1;
                    this.loadReviews(true);
                }, 500);
            } else {
                console.error("Server error:", data.message);
                this.showToast(
                    "Lỗi",
                    data.message || "Không thể gửi đánh giá.",
                    "error"
                );
            }
        } catch (error) {
            console.error("Error submitting review:", error);
            this.showToast(
                "Lỗi",
                "Đã xảy ra lỗi khi gửi đánh giá. Vui lòng thử lại sau.",
                "error"
            );
        } finally {
            for (let i = 0; i < formElements.length; i++) {
                formElements[i].disabled = false;
            }

            if (submitBtn) {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            }

            this.isSubmitting = false;
        }
    }

    setupImagePreview(input, containerId) {
        const container = document.getElementById(containerId);
        if (!input || !container) return;

        const newInput = input.cloneNode(true);
        input.parentNode.replaceChild(newInput, input);
        input = newInput;

        input.addEventListener("change", function () {
            container.innerHTML = "";

            if (this.files && this.files.length > 0) {
                const maxFiles = 5;
                if (this.files.length > maxFiles) {
                    alert(
                        `Bạn chỉ có thể tải lên tối đa ${maxFiles} hình ảnh.`
                    );
                    this.value = "";
                    return;
                }

                Array.from(this.files).forEach((file, index) => {
                    if (file.size > 2 * 1024 * 1024) {
                        alert(
                            `File "${file.name}" vượt quá kích thước tối đa 2MB.`
                        );
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const previewItem = document.createElement("div");
                        previewItem.className =
                            "col-md-4 col-4 image-preview-item";
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-image" data-index="${index}">
                                <i class="bi bi-x"></i>
                            </button>
                            <textarea class="form-control image-comment mt-1" placeholder="Mô tả hình ảnh..."></textarea>
                        `;
                        container.appendChild(previewItem);

                        previewItem
                            .querySelector(".remove-image")
                            .addEventListener("click", function () {
                                previewItem.remove();
                            });

                        const imageComment =
                            previewItem.querySelector(".image-comment");
                        imageComment.addEventListener("change", function () {
                            const commentInput =
                                document.createElement("input");
                            commentInput.type = "hidden";
                            commentInput.name = `image_comment_${index}`;
                            commentInput.value = this.value;
                            previewItem.appendChild(commentInput);
                        });
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    }

    openImageModal(src) {
        let modal = document.getElementById("reviewImageModal");
        if (!modal) {
            modal = document.createElement("div");
            modal.className = "modal fade";
            modal.id = "reviewImageModal";
            modal.tabIndex = "-1";
            modal.setAttribute("aria-hidden", "true");

            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Xem ảnh</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <img src="" id="modalImage" class="img-fluid" alt="Review Image">
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
        }

        document.getElementById("modalImage").src = src;

        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }

    showToast(title, message, type = "success") {
        const toastEl = document.createElement("div");
        toastEl.className = `toast align-items-center text-white bg-${
            type === "success" ? "success" : "danger"
        } border-0`;
        toastEl.setAttribute("role", "alert");
        toastEl.setAttribute("aria-live", "assertive");
        toastEl.setAttribute("aria-atomic", "true");
        toastEl.style.position = "fixed";
        toastEl.style.top = "20px";
        toastEl.style.left = "20px";
        toastEl.style.zIndex = "10000";

        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}:</strong> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        document.body.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();

        toastEl.addEventListener("hidden.bs.toast", function () {
            document.body.removeChild(toastEl);
        });
    }

    confirmDeleteReview(reviewId) {
        let modal = document.getElementById("confirmDeleteModal");
        if (!modal) {
            modal = document.createElement("div");
            modal.id = "confirmDeleteModal";
            modal.className = "confirm-delete-modal";
            modal.innerHTML = `
                <div class="confirm-delete-content">
                    <h5>Xác nhận xóa</h5>
                    <p>Bạn có chắc chắn muốn xóa đánh giá này không?</p>
                    <div class="confirm-delete-actions">
                        <button type="button" class="btn btn-secondary btn-sm cancel-delete">Hủy</button>
                        <button type="button" class="btn btn-danger btn-sm confirm-delete">Xóa</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            modal
                .querySelector(".cancel-delete")
                .addEventListener("click", () => {
                    modal.classList.remove("active");
                });
        }

        modal.classList.add("active");

        const confirmBtn = modal.querySelector(".confirm-delete");
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        newConfirmBtn.addEventListener("click", () =>
            this.deleteReview(reviewId)
        );
    }

    async deleteReview(reviewId) {
        try {
            const confirmBtn = document.querySelector(".confirm-delete");
            if (confirmBtn) {
                confirmBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xóa...';
                confirmBtn.disabled = true;
            }

            const response = await fetch(
                `${SITE_URL}/api/reviews/${reviewId}`,
                {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                    },
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                const modal = document.getElementById("confirmDeleteModal");
                if (modal) modal.classList.remove("active");

                this.showToast(
                    "Thành công",
                    "Đánh giá đã được xóa thành công!",
                    "success"
                );

                this.currentPage = 1;
                this.loadReviews(true);
            } else {
                this.showToast(
                    "Lỗi",
                    data.message || "Không thể xóa đánh giá.",
                    "error"
                );
            }
        } catch (error) {
            console.error("Error deleting review:", error);
            this.showToast(
                "Lỗi",
                "Đã xảy ra lỗi khi xóa đánh giá. Vui lòng thử lại sau.",
                "error"
            );
        } finally {
            const confirmBtn = document.querySelector(".confirm-delete");
            if (confirmBtn) {
                confirmBtn.innerHTML = "Xóa";
                confirmBtn.disabled = false;
            }
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const hotelIdElement = document.querySelector("[data-hotel-id]");
    if (hotelIdElement) {
        const hotelId = hotelIdElement.getAttribute("data-hotel-id");

        const hotelIdInput = document.querySelector('input[name="hotel_id"]');
        if (hotelIdInput) {
        } else {
            console.warn(
                "Không tìm thấy input hotel_id trong form - sẽ sử dụng hotel ID từ trang"
            );
        }

        window.hotelReviews = new HotelReviews(hotelId);
        window.hotelReviews.init();
    } else {
        console.error("Không tìm thấy hotel ID từ trang!");
    }
});
