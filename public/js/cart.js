class CartManager {
    constructor() {
        this.cart = {
            hotels: {},
            totalCount: 0,
            hotelPromotions: {},
        };

        this.loadCart();

        if (
            document.readyState === "complete" ||
            document.readyState === "interactive"
        ) {
            this.updateCartDisplay();
        } else {
            document.addEventListener("DOMContentLoaded", () =>
                this.updateCartDisplay()
            );
        }
    }

    initUI() {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", () => this.setupUI());
        } else {
            this.setupUI();
        }
    }

    setupUI() {
        this.createCartIcon();
        this.updateCartDisplay();
    }

    createCartIcon() {
        const navbar = document.querySelector(".navbar-nav");
        if (!navbar) return;

        if (!document.getElementById("cart-icon-container")) {
            const cartContainer = document.createElement("div");
            cartContainer.id = "cart-icon-container";
            cartContainer.className = "ms-2 d-flex align-items-center";

            const cartLink = document.createElement("a");
            cartLink.href = SITE_URL + "/booking/cart";
            cartLink.id = "cart-link";
            cartLink.className = "position-relative";
            cartLink.innerHTML = `
                <i class="bi bi-cart3 fs-4" id="cart-icon"></i>
                <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger${
                    this.cart.totalCount > 0 ? "" : " d-none"
                }">
                    ${this.cart.totalCount}
                </span>
            `;

            cartContainer.appendChild(cartLink);
            navbar.appendChild(cartContainer);
        }
    }

    animateCartIcon() {
        const cartIcon = document.getElementById("cart-icon");
        if (cartIcon) {
            cartIcon.classList.add("cart-updated");
            setTimeout(() => {
                cartIcon.classList.remove("cart-updated");
            }, 1000);
        }
    }

    updateCartDisplay() {
        const cartCount = document.getElementById("cart-count");
        if (cartCount) {
            if (this.cart.totalCount > 0) {
                cartCount.textContent = this.cart.totalCount;
                cartCount.classList.remove("d-none");
            } else {
                cartCount.classList.add("d-none");
            }
        }

        const cartBadge = document.getElementById("cart-badge");
        if (cartBadge) {
            cartBadge.textContent = this.cart.totalCount;

            if (this.cart.totalCount > 0) {
                cartBadge.classList.remove("empty");

                const cartIcon = document.getElementById("cart-icon");
                if (cartIcon) {
                    cartIcon.classList.add("has-items");
                }

                const cartIconContainer = document.querySelector(
                    ".cart-icon-container"
                );
                if (cartIconContainer) {
                    cartIconContainer.classList.add("active");
                }
            } else {
                cartBadge.classList.add("empty");

                const cartIcon = document.getElementById("cart-icon");
                if (cartIcon) {
                    cartIcon.classList.remove("has-items");
                }

                const cartIconContainer = document.querySelector(
                    ".cart-icon-container"
                );
                if (cartIconContainer) {
                    cartIconContainer.classList.remove("active");
                }
            }
        }
    }

    loadCart() {
        const savedCart = localStorage.getItem("hotelCart");
        if (savedCart) {
            try {
                this.cart = JSON.parse(savedCart);

                if (!this.cart.hotelPromotions) {
                    this.cart.hotelPromotions = {};
                }

                if (this.cart.items && !this.cart.hotels) {
                    this.cart.hotels = this.cart.items;
                    delete this.cart.items;
                }
            } catch (e) {
                console.error("Lỗi khi tải giỏ hàng từ localStorage:", e);
                this.cart = {
                    hotels: {},
                    totalCount: 0,
                    hotelPromotions: {},
                };
            }
        } else {
            this.cart = {
                hotels: {},
                totalCount: 0,
                hotelPromotions: {},
            };
        }
    }

    migrateCartFromOldFormat(oldCart) {
        const newCart = {
            totalCount: 0,
            hotels: {},
        };

        if (oldCart.items && typeof oldCart.items === "object") {
            Object.values(oldCart.items).forEach((item) => {
                const hotelId = item.hotelId.toString();
                const roomId = item.id.toString();

                if (!newCart.hotels[hotelId]) {
                    newCart.hotels[hotelId] = {
                        id: hotelId,
                        name: item.hotelName || "Khách sạn không xác định",
                        address: item.hotelAddress || "",
                        promotions: item.hotelPromotions || [],
                        rooms: {},
                    };
                }

                newCart.hotels[hotelId].rooms[roomId] = {
                    id: roomId,
                    name: item.name,
                    type: item.roomType || "standard",
                    price: item.price,
                    checkIn: item.checkInDate,
                    checkOut: item.checkOutDate,
                    quantity: item.quantity || 1,
                    nights:
                        item.nights ||
                        this.calculateNights(
                            item.checkInDate,
                            item.checkOutDate
                        ),
                };

                newCart.totalCount += item.quantity || 1;
            });
        }

        this.cart = newCart;
        this.saveCart();
    }

    saveCart() {
        localStorage.setItem("hotelCart", JSON.stringify(this.cart));
    }

    resetCart() {
        this.cart = {
            totalCount: 0,
            hotels: {},
        };
        this.saveCart();
        this.updateCartDisplay();
    }

    clearCart() {
        this.resetCart();
    }

    addToCart(item) {
        const { hotelId, roomId, quantity = 1 } = item;

        if (!this.cart.hotels[hotelId]) {
            this.cart.hotels[hotelId] = {
                id: hotelId,
                name: item.hotelName || "Khách sạn không xác định",
                address: item.hotelAddress || "",
                promotions: [],
                rooms: {},
            };

            this.fetchPromotionsForHotel(hotelId);
        }

        if (!this.cart.hotels[hotelId].rooms[roomId]) {
            this.cart.hotels[hotelId].rooms[roomId] = {
                id: roomId,
                name: item.roomName || "Phòng",
                price: parseFloat(item.roomPrice) || 0,
                type: item.roomType || "standard",
                quantity: 0,
                checkInDate: item.checkInDate || "",
                checkOutDate: item.checkOutDate || "",
            };
        }

        this.cart.hotels[hotelId].rooms[roomId].quantity += quantity;
        this.cart.totalCount += quantity;

        this.saveCart();
        this.updateCartDisplay();
    }

    fetchPromotionsForHotel(hotelId) {
        if (!hotelId) return;

        const timestamp = new Date().getTime();
        const url = `${SITE_URL}/api/hotels/${hotelId}/promotions?_=${timestamp}`;

        fetch(url)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`API error: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                if (data.promotions && data.promotions.length > 0) {
                    if (this.cart.hotels[hotelId]) {
                        this.cart.hotels[hotelId].promotions = data.promotions;

                        if (!this.cart.hotelPromotions) {
                            this.cart.hotelPromotions = {};
                        }
                        this.cart.hotelPromotions[hotelId] = data.promotions;

                        this.saveCart();
                    }
                } else {
                    if (this.cart.hotels[hotelId]) {
                        this.cart.hotels[hotelId].promotions = [];
                    }

                    if (!this.cart.hotelPromotions) {
                        this.cart.hotelPromotions = {};
                    }
                    this.cart.hotelPromotions[hotelId] = [];

                    this.saveCart();
                }
            })
            .catch((error) => {
                console.error("Lỗi khi tải khuyến mãi:", error);
            });
    }

    updateRoomDates(hotelId, roomId, checkIn, checkOut) {
        if (
            !this.cart.hotels[hotelId] ||
            !this.cart.hotels[hotelId].rooms[roomId]
        ) {
            console.warn(
                `Phòng ID ${roomId} hoặc khách sạn ID ${hotelId} không tồn tại trong giỏ hàng`
            );
            return false;
        }

        this.cart.hotels[hotelId].rooms[roomId].checkInDate = checkIn;
        this.cart.hotels[hotelId].rooms[roomId].checkOutDate = checkOut;

        const nights = this.calculateNights(checkIn, checkOut);
        this.cart.hotels[hotelId].rooms[roomId].nights = nights;

        this.saveCart();
        this.animateCartIcon();

        return true;
    }

    updateQuantity(hotelId, roomId, quantity) {
        if (
            !this.cart.hotels[hotelId] ||
            !this.cart.hotels[hotelId].rooms[roomId]
        ) {
            console.warn(
                `Phòng ID ${roomId} hoặc khách sạn ID ${hotelId} không tồn tại trong giỏ hàng`
            );
            return false;
        }

        quantity = parseInt(quantity);
        if (isNaN(quantity) || quantity < 1) quantity = 1;
        if (quantity > 10) quantity = 10;

        this.cart.hotels[hotelId].rooms[roomId].quantity = quantity;

        this.updateCartTotal();
        this.saveCart();

        return true;
    }

    calculateNights(checkIn, checkOut) {
        const checkInDate = new Date(checkIn);
        const checkOutDate = new Date(checkOut);
        const timeDiff = checkOutDate.getTime() - checkInDate.getTime();
        const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
        return nights > 0 ? nights : 1;
    }

    removeFromCart(hotelId, roomId) {
        if (
            this.cart.hotels[hotelId] &&
            this.cart.hotels[hotelId].rooms[roomId]
        ) {
            const quantity = this.cart.hotels[hotelId].rooms[roomId].quantity;

            this.cart.totalCount -= quantity;

            delete this.cart.hotels[hotelId].rooms[roomId];

            if (Object.keys(this.cart.hotels[hotelId].rooms).length === 0) {
                delete this.cart.hotels[hotelId];

                if (
                    this.cart.hotelPromotions &&
                    this.cart.hotelPromotions[hotelId]
                ) {
                    delete this.cart.hotelPromotions[hotelId];
                }
            }

            this.saveCart();
            this.updateCartDisplay();
        }
    }

    updateCartTotal() {
        let totalCount = 0;

        Object.values(this.cart.hotels).forEach((hotel) => {
            Object.values(hotel.rooms).forEach((room) => {
                totalCount += room.quantity;
            });
        });

        this.cart.totalCount = totalCount;
        this.updateCartDisplay();
    }

    getCart() {
        return this.cart;
    }

    showToast(title, message, type = "success") {
        if (typeof bootstrap === "undefined") {
            return;
        }

        const toastEl = document.createElement("div");
        toastEl.className = `toast align-items-center text-white bg-${
            type === "success" ? "success" : "danger"
        } border-0`;
        toastEl.setAttribute("role", "alert");
        toastEl.setAttribute("aria-live", "assertive");
        toastEl.setAttribute("aria-atomic", "true");

        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}:</strong> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        let toastContainer = document.querySelector(".toast-container");
        if (!toastContainer) {
            toastContainer = document.createElement("div");
            toastContainer.className =
                "toast-container position-fixed top-0 end-0 p-3";
            document.body.appendChild(toastContainer);
        }

        toastContainer.appendChild(toastEl);

        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();

        toastEl.addEventListener("hidden.bs.toast", function () {
            toastEl.remove();
            if (toastContainer.children.length === 0) {
                toastContainer.remove();
            }
        });
    }

    findRoomInCart(roomId) {
        for (const hotelId in this.cart.hotels) {
            if (this.cart.hotels[hotelId].rooms[roomId]) {
                return {
                    hotelId: hotelId,
                    hotel: this.cart.hotels[hotelId],
                    room: this.cart.hotels[hotelId].rooms[roomId],
                };
            }
        }
        return null;
    }

    getHotelPromotions(hotelId) {
        if (
            this.cart.hotels &&
            this.cart.hotels[hotelId] &&
            this.cart.hotels[hotelId].promotions
        ) {
            return this.cart.hotels[hotelId].promotions;
        }

        if (this.cart.hotelPromotions && this.cart.hotelPromotions[hotelId]) {
            return this.cart.hotelPromotions[hotelId];
        }

        return [];
    }

    getHotelTotal(hotelId) {
        let total = 0;

        if (this.cart.hotels[hotelId]) {
            const hotel = this.cart.hotels[hotelId];

            for (const roomId in hotel.rooms) {
                const room = hotel.rooms[roomId];
                total += room.price * room.quantity;
            }
        }

        return total;
    }

    processCart() {
        this.saveCart();
        this.updateCartTotal();
        this.updateCartDisplay();

        return true;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    if (typeof CartManager === "function" && !window.cartManager) {
        window.cartManager = new CartManager();
    }
});
