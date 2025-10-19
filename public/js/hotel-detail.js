if (typeof SITE_URL === "undefined") {
    const SITE_URL = window.location.origin;
}

document.addEventListener("DOMContentLoaded", function () {
    window.changeMainImage = function (src) {
        document.getElementById("mainImage").src = src;
    };

    window.changeModalMainImage = function (src) {
        document.querySelector(".modal-main-image").src = src;
    };

    window.showAllAmenities = function () {
        const amenitiesModal = new bootstrap.Modal(
            document.getElementById("amenitiesModal")
        );
        amenitiesModal.show();
    };

    const checkInDate = document.getElementById("checkInDate");
    const checkOutDate = document.getElementById("checkOutDate");

    if (checkInDate && checkOutDate) {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}-${month}-${day}`;
        };

        checkInDate.setAttribute("min", formatDate(today));

        checkInDate.addEventListener("change", function () {
            const selectedDate = new Date(this.value);
            const nextDay = new Date(selectedDate);
            nextDay.setDate(nextDay.getDate() + 1);

            checkOutDate.setAttribute("min", formatDate(nextDay));

            if (new Date(checkOutDate.value) <= selectedDate) {
                checkOutDate.value = formatDate(nextDay);
            }
        });
    }

    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    if (
        tooltipTriggerList.length > 0 &&
        typeof bootstrap !== "undefined" &&
        bootstrap.Tooltip
    ) {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    const savedCart = localStorage.getItem("hotelCart");

    if (typeof CartManager === "function") {
        if (!window.cartManager) {
            window.cartManager = new CartManager();
        } else {
            window.cartManager.loadCart();
            window.cartManager.updateCartDisplay();
        }
    } else {
        console.error(
            "CartManager class not found! Check if cart.js is loaded correctly."
        );
    }

    function checkSelectedRoomsInCart() {
        if (
            window.cartManager &&
            window.cartManager.cart &&
            window.cartManager.cart.hotels
        ) {
            const hotelIdElement = document.querySelector("[data-hotel-id]");
            if (!hotelIdElement) return;

            const hotelId = hotelIdElement.getAttribute("data-hotel-id");
            const cart = window.cartManager.cart;

            if (cart.hotels[hotelId]) {
                const hotelCart = cart.hotels[hotelId];

                for (const roomId in hotelCart.rooms) {
                    const roomCheckbox = document.querySelector(
                        `.room-checkbox[data-room-id="${roomId}"]`
                    );
                    if (roomCheckbox) {
                        roomCheckbox.checked = true;

                        const roomCard = roomCheckbox.closest(".room-card");
                        if (roomCard) {
                            roomCard.classList.add("selected");
                        }
                    }
                }
            }
        }
    }

    setTimeout(checkSelectedRoomsInCart, 500);

    window.addEventListener("beforeunload", function () {
        if (window.cartManager) {
            window.cartManager.saveCart();
        }
    });
});
