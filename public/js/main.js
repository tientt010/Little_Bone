document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    var popoverTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="popover"]')
    );
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    function fixCarouselTransition() {
        var carousels = document.querySelectorAll(".carousel");
        carousels.forEach(function (carousel) {
            var items = carousel.querySelectorAll(".carousel-item");
            items.forEach(function (item, index) {
                if (item.classList.contains("active")) {
                    item.style.zIndex = 2;
                } else {
                    item.style.zIndex = 1;
                }
            });
        });
    }

    fixCarouselTransition();

    function setupImageErrorHandling() {
        var images = document.querySelectorAll("img");
        images.forEach(function (img) {
            img.addEventListener("error", function () {
                if (!this.getAttribute("data-error-handled")) {
                    this.setAttribute("data-error-handled", "true");

                    const path = this.src.toLowerCase();
                    let defaultImage =
                        window.SITE_URL + "/public/images/default.jpg";

                    if (path.includes("/hotels/")) {
                        defaultImage =
                            window.SITE_URL +
                            "/public/images/hotels/default.jpg";
                    } else if (path.includes("/cities/")) {
                        defaultImage =
                            window.SITE_URL +
                            "/public/images/cities/default.jpg";
                    } else if (path.includes("/promotions/")) {
                        defaultImage =
                            window.SITE_URL +
                            "/public/images/promotions/default.jpg";
                    } else if (path.includes("/rooms/")) {
                        defaultImage =
                            window.SITE_URL +
                            "/public/images/rooms/default.jpg";
                    } else if (path.includes("/avatars/")) {
                        defaultImage =
                            window.SITE_URL +
                            "/public/images/avatars/default.jpg";
                    }

                    this.src = defaultImage;
                }
            });
        });
    }

    setupImageErrorHandling();

    adjustSliderHeight();

    window.addEventListener("resize", adjustSliderHeight);

    enhanceCarouselEffects();

    window.addEventListener("load", function () {
        setTimeout(adjustSliderHeight, 100);
    });

    initDestinationSuggestions();

    adjustPromotionCarousel();

    window.addEventListener("resize", adjustPromotionCarousel);

    const travelDropdown = document.querySelector(
        ".travel-destinations-dropdown"
    );
    if (travelDropdown) {
    }
});

function adjustSliderHeight() {
    const sliders = document.querySelectorAll(".destination-slider");
    const searchContainers = document.querySelectorAll(
        ".card-style-search-container"
    );

    if (sliders.length > 0 && searchContainers.length > 0) {
        const containerHeight = searchContainers[0].offsetHeight;

        sliders.forEach((slider) => {
            slider.style.height = containerHeight + "px";
            slider.style.minHeight = containerHeight + "px";
        });
    } else {
        const windowWidth = window.innerWidth;

        sliders.forEach((slider) => {
            let heightValue = "380px";

            if (windowWidth <= 576) {
                heightValue = "250px";
            } else if (windowWidth <= 767) {
                heightValue = "300px";
            } else if (windowWidth <= 991) {
                heightValue = "350px";
            }

            slider.style.height = heightValue;
        });
    }

    const carouselItems = document.querySelectorAll(
        ".destination-slider .carousel-item"
    );
    carouselItems.forEach((item) => {
        item.style.height = "100%";
    });
}

function enhanceCarouselEffects() {
    const carousels = document.querySelectorAll(".carousel");

    carousels.forEach((carousel) => {
        carousel.addEventListener("slide.bs.carousel", function (e) {
            const activeItem = this.querySelector(".carousel-item.active");
            const nextItem = e.relatedTarget;

            if (activeItem) {
                const activeCaption =
                    activeItem.querySelector(".carousel-caption");
                if (activeCaption) {
                    activeCaption.style.opacity = "0";
                    activeCaption.style.transform = "translateY(20px)";
                }
            }

            if (nextItem) {
                nextItem.style.opacity = "0";

                setTimeout(() => {
                    nextItem.style.opacity = "1";

                    const nextCaption =
                        nextItem.querySelector(".carousel-caption");
                    if (nextCaption) {
                        setTimeout(() => {
                            nextCaption.style.opacity = "1";
                            nextCaption.style.transform = "translateY(0)";
                        }, 300);
                    }
                }, 50);
            }
        });
    });
}

function initDestinationSuggestions() {
    const destinationInput = document.getElementById("destinationInput");
    const suggestionsContainer = document.getElementById(
        "destinationSuggestions"
    );

    if (!destinationInput || !suggestionsContainer) return;

    let destinations = [];
    let isLoading = false;
    let debounceTimer;
    let selectedIndex = -1;
    let allDestinations = [];
    let currentFilter = "";

    function showLoading() {
        isLoading = true;
        suggestionsContainer.innerHTML =
            '<div class="suggestion-loader"><i class="bi bi-arrow-repeat"></i> Đang tải...</div>';
        suggestionsContainer.classList.add("active");
    }

    function fetchDestinations(query = "") {
        isLoading = true;

        const apiUrl = `${window.SITE_URL}/api/destinations`;

        fetch(apiUrl)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                allDestinations = data.destinations || [];
                destinations = allDestinations;
                isLoading = false;

                renderSuggestions(allDestinations);

                if (currentFilter) {
                    scrollToFirstMatch(currentFilter);
                }
            })
            .catch((error) => {
                console.error("Error fetching destinations:", error);
                isLoading = false;
                suggestionsContainer.innerHTML =
                    '<div class="suggestion-item">Không thể tải dữ liệu</div>';
            });
    }

    function removeVietnameseAccents(str) {
        if (!str) return "";
        str = str.toLowerCase();
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
        str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
        str = str.replace(/đ/g, "d");
        return str;
    }

    function scrollToFirstMatch(letter) {
        if (!allDestinations.length) return;

        const normalizedLetter = removeVietnameseAccents(letter.toLowerCase());

        const index = allDestinations.findIndex((dest) => {
            const normalizedName = removeVietnameseAccents(
                dest.name.toLowerCase()
            );
            return normalizedName.startsWith(normalizedLetter);
        });

        if (index !== -1) {
            selectedIndex = index;
            currentFilter = letter;

            const items =
                suggestionsContainer.querySelectorAll(".suggestion-item");
            updateSelectedItem(items);
        } else {
            selectedIndex = -1;
            currentFilter = letter;
        }
    }

    destinationInput.addEventListener("click", function (e) {
        e.stopPropagation();
        if (allDestinations.length === 0) {
            showLoading();
            fetchDestinations();
        } else {
            renderSuggestions(allDestinations);

            if (currentFilter) {
                scrollToFirstMatch(currentFilter);
            }

            suggestionsContainer.classList.add("active");
        }
    });

    destinationInput.addEventListener("keydown", function (e) {
        if (!suggestionsContainer.classList.contains("active")) {
            if (
                e.key === "ArrowDown" ||
                e.key === "ArrowUp" ||
                e.key === "Enter"
            ) {
                if (allDestinations.length === 0) {
                    showLoading();
                    fetchDestinations();
                } else {
                    suggestionsContainer.classList.add("active");
                    renderSuggestions(allDestinations);

                    if (currentFilter) {
                        scrollToFirstMatch(currentFilter);
                    }
                }
                e.preventDefault();
            }
            return;
        }

        const items = suggestionsContainer.querySelectorAll(".suggestion-item");

        switch (e.key) {
            case "ArrowDown":
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                updateSelectedItem(items);
                break;

            case "ArrowUp":
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateSelectedItem(items);
                break;

            case "Enter":
                e.preventDefault();
                if (selectedIndex >= 0 && selectedIndex < items.length) {
                    selectDestination(destinations[selectedIndex]);
                }
                break;

            case "Escape":
                suggestionsContainer.classList.remove("active");
                break;

            default:
                if (e.key.length === 1 && /[a-zA-Z0-9]/.test(e.key)) {
                    e.preventDefault();
                    currentFilter = e.key;
                    scrollToFirstMatch(e.key);
                } else if (e.key === "Backspace" || e.key === "Delete") {
                    e.preventDefault();
                    currentFilter = "";
                    selectedIndex = -1;
                    updateSelectedItem(items);
                }
                break;
        }
    });

    function updateSelectedItem(items) {
        items.forEach((item) => item.classList.remove("selected-suggestion"));

        if (selectedIndex >= 0 && selectedIndex < items.length) {
            items[selectedIndex].classList.add("selected-suggestion");

            items[selectedIndex].scrollIntoView({
                behavior: "auto",
                block: "center",
            });
        }
    }

    function selectDestination(destination) {
        if (destination) {
            destinationInput.value = destination.name;
            suggestionsContainer.classList.remove("active");
            currentFilter = "";
        }
    }

    document.addEventListener("click", function (e) {
        if (
            e.target !== destinationInput &&
            !suggestionsContainer.contains(e.target)
        ) {
            suggestionsContainer.classList.remove("active");
        }
    });

    function renderSuggestions(destinations) {
        suggestionsContainer.innerHTML = "";

        if (destinations.length === 0) {
            const noResults = document.createElement("div");
            noResults.className = "suggestion-item";
            noResults.innerHTML = "Không tìm thấy địa điểm";
            suggestionsContainer.appendChild(noResults);
            return;
        }

        destinations.forEach((dest, index) => {
            const item = document.createElement("div");
            item.className = "suggestion-item";
            if (index === selectedIndex) {
                item.classList.add("selected-suggestion");
            }

            item.innerHTML = `
                <i class="bi bi-geo-alt"></i>
                <span class="suggestion-name">${dest.name}</span>
            `;

            item.addEventListener("click", function () {
                selectDestination(dest);
            });

            suggestionsContainer.appendChild(item);
        });

        suggestionsContainer.classList.add("active");
    }
}

function adjustPromotionCarousel() {
    const carousel = document.getElementById("promotionCarousel");
    if (!carousel) return;

    const items = carousel.querySelectorAll(".carousel-item");
    const windowWidth = window.innerWidth;

    items.forEach((item) => {
        const row = item.querySelector(".row");
        if (row) {
            if (windowWidth < 768) {
                const cols = row.querySelectorAll(".col-md-4");
                cols.forEach((col) => {
                    col.classList.add("col-12");
                });
            } else if (windowWidth < 992) {
                const cols = row.querySelectorAll(".col-md-4");
                cols.forEach((col) => {
                    col.classList.add("col-6");
                });
            }
        }
    });
}
