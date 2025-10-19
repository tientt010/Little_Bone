// Biến toàn cục cho khách sạn
const hotelData = {
    id: window.hotelId,
    name: window.hotelName,
    latitude: window.hotelLatitude,
    longitude: window.hotelLongitude,
    address: window.hotelAddress,
};

// Khởi tạo bản đồ Google Maps
function initMap() {
    if (hotelData.latitude && hotelData.longitude) {
        renderMapWithLocation(hotelData.latitude, hotelData.longitude);
    } else {
        geocodeAddress(hotelData.address);
    }
}

function geocodeAddress(address) {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: address }, function (results, status) {
        if (status === "OK" && results[0]) {
            const lat = results[0].geometry.location.lat();
            const lng = results[0].geometry.location.lng();
            renderMapWithLocation(lat, lng);
        } else {
            console.error("Geocode was not successful:", status);
            document.getElementById("hotelMap").innerHTML =
                '<div class="alert alert-warning m-3">Không tìm thấy vị trí trên bản đồ. Vui lòng thử lại sau.</div>';
            document.getElementById("loadingPlaces").style.display = "none";
            document.getElementById("noPlacesFound").style.display = "block";
        }
    });
}

function renderMapWithLocation(lat, lng) {
    const hotelPosition = {
        lat: parseFloat(lat),
        lng: parseFloat(lng),
    };

    const map = new google.maps.Map(document.getElementById("hotelMap"), {
        center: hotelPosition,
        zoom: 15,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
    });

    const hotelMarker = new google.maps.Marker({
        position: hotelPosition,
        map: map,
        title: hotelData.name,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: "#3d7bb7",
            fillOpacity: 1,
            strokeColor: "#ffffff",
            strokeWeight: 2,
            scale: 10,
        },
        animation: google.maps.Animation.DROP,
    });

    const infowindow = new google.maps.InfoWindow({
        content: `<div class="p-2"><strong>${hotelData.name}</strong><br>${hotelData.address}</div>`,
    });

    hotelMarker.addListener("click", function () {
        infowindow.open(map, hotelMarker);
    });

    infowindow.open(map, hotelMarker);
    searchNearbyPlaces(map, hotelPosition);
}

// Tìm kiếm địa điểm gần khách sạn
function searchNearbyPlaces(map, hotelPosition) {
    const service = new google.maps.places.PlacesService(map);

    document.getElementById("loadingPlaces").style.display = "block";
    document.getElementById("noPlacesFound").style.display = "none";
    document.getElementById("nearbyPlaces").innerHTML = "";

    const request = {
        location: hotelPosition,
        rankBy: google.maps.places.RankBy.DISTANCE,
        keyword: "tourist attraction",
        fields: [
            "name",
            "vicinity",
            "rating",
            "user_ratings_total",
            "geometry",
            "place_id",
            "types",
        ],
    };

    service.nearbySearch(request, (results, status, pagination) => {
        processSearchResults(results, status);
    });
}

function processSearchResults(results, status) {
    const loadingElement = document.getElementById("loadingPlaces");
    const placesListElement = document.getElementById("nearbyPlaces");
    const noPlacesElement = document.getElementById("noPlacesFound");

    loadingElement.style.display = "none";

    if (
        status === google.maps.places.PlacesServiceStatus.OK &&
        results.length > 0
    ) {
        const nearbyPlaces = results.slice(0, 5);
        placesListElement.innerHTML = "";

        nearbyPlaces.forEach((place) => {
            const distance = calculateDistance(
                hotelData.latitude,
                hotelData.longitude,
                place.geometry.location.lat(),
                place.geometry.location.lng()
            );

            const placeElement = createPlaceElement(place, distance);
            placesListElement.appendChild(placeElement);
        });

        placesListElement.style.display = "block";
    } else {
        placesListElement.style.display = "none";
        noPlacesElement.style.display = "block";
    }
}

function createPlaceElement(place, distance) {
    const li = document.createElement("li");
    li.className = "list-group-item border-0 border-bottom py-2";

    const rating = place.rating || 0;
    let starsHtml = "";
    for (let i = 0; i < 5; i++) {
        if (i < Math.floor(rating)) {
            starsHtml += '<i class="bi bi-star-fill text-warning small"></i>';
        } else if (i < Math.ceil(rating) && rating % 1 !== 0) {
            starsHtml += '<i class="bi bi-star-half text-warning small"></i>';
        } else {
            starsHtml += '<i class="bi bi-star text-warning small"></i>';
        }
    }

    li.innerHTML = `
        <div class="d-flex align-items-start">
            <div class="place-icon me-2 fs-5">
                <i class="bi bi-geo-fill text-danger"></i>
            </div>
            <div class="place-info">
                <h6 class="mb-0">${place.name}</h6>
                <p class="mb-1 small text-muted">
                    <i class="bi bi-geo-alt"></i> ${
                        place.vicinity || "Không có địa chỉ"
                    }
                </p>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="rating small">
                        ${starsHtml}
                        <small class="ms-1 text-muted">(${
                            place.user_ratings_total || 0
                        })</small>
                    </div>
                    <div class="distance badge bg-light text-dark small">
                        <i class="bi bi-signpost-2"></i> ${distance} km
                    </div>
                </div>
            </div>
        </div>
    `;

    return li;
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(deg2rad(lat1)) *
            Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) *
            Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c;
    return distance.toFixed(1);
}

function deg2rad(deg) {
    return deg * (Math.PI / 180);
}

// Navigation và scrollspy
function initializeNavigation() {
    const sections = document.querySelectorAll(".section-container, #overview");
    const navItems = document.querySelectorAll("#hotel-navbar .nav-link");
    const navbarOffset = 100;

    function updateActiveNavItem() {
        let currentSection = null;

        sections.forEach((section) => {
            const sectionTop = section.getBoundingClientRect().top;
            const sectionBottom = section.getBoundingClientRect().bottom;

            if (
                (sectionTop <= navbarOffset && sectionBottom > navbarOffset) ||
                (sectionTop > 0 && sectionTop < window.innerHeight / 3)
            ) {
                currentSection = section;
            }
        });

        if (!currentSection && sections.length > 0) {
            let closestDistance = Infinity;
            let closestSection = null;

            sections.forEach((section) => {
                const sectionTop = section.getBoundingClientRect().top;
                const distance = Math.abs(sectionTop - navbarOffset);

                if (distance < closestDistance) {
                    closestDistance = distance;
                    closestSection = section;
                }
            });

            currentSection = closestSection;
        }

        if (currentSection) {
            const currentSectionId = currentSection.getAttribute("id");

            navItems.forEach((navItem) => {
                navItem.classList.remove("active");

                const href = navItem.getAttribute("href").substring(1);
                if (href === currentSectionId) {
                    navItem.classList.add("active");

                    const navbarContainer = document.querySelector(
                        "#hotel-navbar .navbar-collapse"
                    );
                    if (navbarContainer) {
                        const containerRect =
                            navbarContainer.getBoundingClientRect();
                        const itemRect = navItem.getBoundingClientRect();

                        if (
                            itemRect.left < containerRect.left ||
                            itemRect.right > containerRect.right
                        ) {
                            navItem.scrollIntoView({
                                behavior: "smooth",
                                block: "nearest",
                                inline: "center",
                            });
                        }
                    }
                }
            });
        }
    }

    window.addEventListener("scroll", updateActiveNavItem);
    setTimeout(updateActiveNavItem, 100);

    navItems.forEach((navItem) => {
        navItem.addEventListener("click", function (e) {
            e.preventDefault();
            const targetId = this.getAttribute("href").substring(1);
            const targetSection = document.getElementById(targetId);

            navItems.forEach((item) => item.classList.remove("active"));
            this.classList.add("active");

            if (targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop - 70,
                    behavior: "smooth",
                });
            }
        });
    });
}

// Modal xem tất cả phòng
function initializeRoomModal() {
    const viewAllRoomsBtn = document.getElementById("viewAllRoomsBtn");
    const allRoomsModal = new bootstrap.Modal(
        document.getElementById("allRoomsModal")
    );
    const modalRoomSearchForm = document.getElementById("modalRoomSearchForm");
    const modalRoomsList = document.getElementById("modalRoomsList");
    const modalRoomsLoading = document.getElementById("modalRoomsLoading");
    const noRoomsFound = document.getElementById("noRoomsFound");

    viewAllRoomsBtn.addEventListener("click", function () {
        allRoomsModal.show();
        loadRooms();
    });

    modalRoomSearchForm.addEventListener("submit", function (e) {
        e.preventDefault();
        loadRooms();
    });

    function loadRooms() {
        modalRoomsLoading.style.display = "block";
        modalRoomsList.style.display = "none";
        noRoomsFound.style.display = "none";

        const formData = new FormData(modalRoomSearchForm);

        fetch(`${SITE_URL}/hotels/getAvailableRooms`, {
            method: "POST",
            body: formData,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                modalRoomsLoading.style.display = "none";

                if (data.success && data.rooms && data.rooms.length > 0) {
                    renderRooms(data.rooms);
                    modalRoomsList.style.display = "block";
                } else {
                    noRoomsFound.style.display = "block";
                }
            })
            .catch((error) => {
                console.error("Error loading rooms:", error);
                modalRoomsLoading.style.display = "none";
                noRoomsFound.textContent =
                    "Có lỗi xảy ra khi tải danh sách phòng. Vui lòng thử lại sau.";
                noRoomsFound.style.display = "block";
            });
        initRoomCheckboxes();
    }

    function renderRooms(rooms) {
        modalRoomsList.innerHTML = "";

        rooms.forEach((room) => {
            let isSelected = false;

            if (
                window.cartManager &&
                typeof window.cartManager.findRoomInCart === "function"
            ) {
                isSelected =
                    window.cartManager.findRoomInCart(room.id) !== null;
            }

            const roomCard = createRoomCard(room, isSelected);
            modalRoomsList.appendChild(roomCard);
        });

        var tooltipTriggerList = [].slice.call(
            modalRoomsList.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        if (typeof window.initRoomCheckboxes === "function") {
            window.initRoomCheckboxes();
        } else {
            console.error(
                "initRoomCheckboxes is not available as a global function"
            );
        }
    }

    function createRoomCard(room, isSelected = false) {
        const roomElement = document.createElement("div");
        roomElement.className = isSelected ? "room-card selected" : "room-card";
        roomElement.setAttribute("data-room-id", room.id);
        roomElement.setAttribute("data-room-price", room.price);
        roomElement.setAttribute("data-room-name", room.name);
        roomElement.setAttribute(
            "data-room-type",
            room.room_type || "standard"
        );

        let typeBadgeClass = "secondary";
        switch (room.room_type) {
            case "single":
                typeBadgeClass = "info";
                break;
            case "double":
                typeBadgeClass = "primary";
                break;
            case "suite":
                typeBadgeClass = "success";
                break;
            case "family":
                typeBadgeClass = "warning";
                break;
        }

        const roomTypeNames = {
            single: "Phòng đơn",
            double: "Phòng đôi",
            suite: "Suite",
            family: "Phòng gia đình",
        };
        const roomTypeName = roomTypeNames[room.room_type] || room.room_type;

        let amenitiesHtml = "";
        if (room.amenities && room.amenities.length > 0) {
            const maxAmenities = 5;
            const displayedCount = Math.min(
                maxAmenities,
                room.amenities.length
            );

            for (let i = 0; i < displayedCount; i++) {
                const amenity = room.amenities[i];
                amenitiesHtml += `
                    <span class="amenity-icon" data-bs-toggle="tooltip" title="${
                        amenity.name
                    }">
                        <i class="bi ${amenity.icon} ${
                    amenity.is_premium ? "premium" : ""
                }"></i>
                    </span>
                `;
            }

            if (room.amenities.length > maxAmenities) {
                amenitiesHtml += `
                    <span class="amenity-more" data-bs-toggle="tooltip" 
                        title="${
                            room.amenities.length - maxAmenities
                        } tiện nghi khác">
                        <i class="bi bi-plus-circle"></i>
                    </span>
                `;
            }
        }

        roomElement.innerHTML = `
            <div class="row g-0">
                <div class="col-md-4">
                    <div class="room-image-container">
                        <img src="${SITE_URL}/public/images/rooms/${
            room.id
        }.jpg"
                            class="room-image" alt="${room.name}"
                            onerror="this.src='${SITE_URL}/public/images/rooms/default.jpg'">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="room-details">
                        <h4 class="room-name">${room.name}</h4>
                        <div class="room-type-badge mb-2">
                            <span class="badge bg-${typeBadgeClass}">
                                ${roomTypeName}
                            </span>
                            ${
                                room.area
                                    ? `
                            <span class="room-area ms-2">
                                <i class="bi bi-rulers"></i> ${room.area} m²
                            </span>`
                                    : ""
                            }
                            <span class="room-capacity ms-2">
                                <i class="bi bi-people-fill"></i> Tối đa ${
                                    room.capacity
                                } người
                            </span>
                        </div>
                        <div class="room-amenities mb-3">
                            ${amenitiesHtml}
                        </div>
                        <div class="room-description">
                            ${
                                room.description
                                    ? room.description.length > 120
                                        ? room.description.substring(0, 120) +
                                          "..."
                                        : room.description
                                    : ""
                            }
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="room-price-container">
                        <div class="room-price">
                            <span class="price-value">${Number(
                                room.price
                            ).toLocaleString("vi-VN")} VNĐ</span>
                        </div>
                        <div class="room-selection">
                            <div class="form-check">
                                <input class="form-check-input room-checkbox" type="checkbox"
                                    id="modal-room-${room.id}" data-room-id="${
            room.id
        }" ${isSelected ? "checked" : ""}>
                                <label class="form-check-label select-room-label" for="modal-room-${
                                    room.id
                                }">
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

    // Đồng bộ form modal với form chính
    document
        .getElementById("checkInDate")
        .addEventListener("change", function () {
            document.getElementById("modalCheckInDate").value = this.value;
            document
                .getElementById("modalCheckInDate")
                .dispatchEvent(new Event("change"));
        });

    document
        .getElementById("checkOutDate")
        .addEventListener("change", function () {
            document.getElementById("modalCheckOutDate").value = this.value;
        });

    document
        .getElementById("guestCount")
        .addEventListener("change", function () {
            document.getElementById("modalGuestCount").value = this.value;
        });

    document.getElementById("roomType").addEventListener("change", function () {
        document.getElementById("modalRoomType").value = this.value;
    });

    // Validation ngày tháng
    const modalCheckInDate = document.getElementById("modalCheckInDate");
    const modalCheckOutDate = document.getElementById("modalCheckOutDate");

    modalCheckInDate.addEventListener("change", function () {
        const checkInDate = new Date(this.value);
        checkInDate.setDate(checkInDate.getDate() + 1);

        const minCheckOutDate = checkInDate.toISOString().split("T")[0];
        modalCheckOutDate.min = minCheckOutDate;

        if (modalCheckOutDate.value < minCheckOutDate) {
            modalCheckOutDate.value = minCheckOutDate;
        }
    });
}

// Khởi tạo tất cả chức năng
document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    initializeNavigation();

    initializeRoomModal();
    window.addEventListener("load", function () {
        const cartLink = document.getElementById("cart-link");
        if (cartLink) {
            cartLink.setAttribute("data-bs-toggle", "tooltip");
            cartLink.setAttribute("data-bs-placement", "left");
            cartLink.setAttribute("title", "Xem giỏ hàng của bạn");

            var tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });
});
