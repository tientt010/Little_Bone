var selectedRooms = new Map();
var updateBookingSummary;

window.initRoomCheckboxes = function () {
    document.querySelectorAll(".room-checkbox").forEach(function (checkbox) {
        const newCheckbox = checkbox.cloneNode(true);
        checkbox.parentNode.replaceChild(newCheckbox, checkbox);

        newCheckbox.addEventListener("change", function () {
            const roomId = this.dataset.roomId;
            const roomCard = this.closest(".room-card");
            const hotelElement = document.querySelector(".hotel-title");
            const hotelId = hotelElement?.dataset?.hotelId;

            if (!roomCard) {
                console.error("Không tìm thấy room-card parent của checkbox");
                return;
            }

            const roomName = roomCard.dataset.roomName;
            const roomPrice = parseFloat(roomCard.dataset.roomPrice);
            const roomType = roomCard.dataset.roomType || "standard";

            const checkInDate = document.getElementById("checkInDate")?.value;
            const checkOutDate = document.getElementById("checkOutDate")?.value;

            if (!checkInDate || !checkOutDate) {
                alert(
                    "Vui lòng chọn ngày nhận phòng và trả phòng trước khi thêm vào giỏ hàng!"
                );
                this.checked = false;
                return;
            }

            if (this.checked) {
                roomCard.classList.add("selected");
                selectedRooms.set(roomId, {
                    id: roomId,
                    name: roomName,
                    price: roomPrice,
                    quantity: 1,
                });

                if (window.cartManager) {
                    window.cartManager.addToCart({
                        roomId: roomId,
                        roomName: roomName,
                        roomPrice: roomPrice,
                        roomType: roomType,
                        hotelId: hotelId,
                        hotelName: hotelElement?.textContent?.trim() || "",
                        hotelAddress:
                            document
                                .querySelector(".hotel-address span")
                                ?.textContent?.trim() || "",
                        checkInDate: checkInDate,
                        checkOutDate: checkOutDate,
                        quantity: 1,
                    });
                    window.cartManager.animateCartIcon();
                } else {
                }
            } else {
                roomCard.classList.remove("selected");
                selectedRooms.delete(roomId);

                if (window.cartManager) {
                    const roomInfo = window.cartManager.findRoomInCart(roomId);
                    if (roomInfo) {
                        window.cartManager.removeFromCart(
                            roomInfo.hotelId,
                            roomId
                        );
                    } else {
                        console.warn(
                            "Không tìm thấy phòng trong giỏ hàng:",
                            roomId
                        );
                    }
                } else {
                }
            }
        });
    });
};

document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    let totalPrice = 0;
    let finalPrice = 0;

    let selectedRoomsContainer = document.getElementById(
        "selectedRoomsContainer"
    );
    let selectedRoomsList = document.querySelector(".selected-rooms-list");
    let noRoomsMessage = document.querySelector(".no-rooms-message");
    let priceSummary = document.querySelector(".price-summary");
    let totalRoomPriceEl = document.getElementById("totalRoomPrice");

    let finalPriceEl = document.getElementById("finalPrice");
    let bookNowBtn = document.getElementById("bookNowBtn");

    let checkInDateInput = document.getElementById("checkInDate");
    let checkOutDateInput = document.getElementById("checkOutDate");

    let stayNights = 1;
    if (
        checkInDateInput &&
        checkOutDateInput &&
        checkInDateInput.value &&
        checkOutDateInput.value
    ) {
        const checkInDate = new Date(checkInDateInput.value);
        const checkOutDate = new Date(checkOutDateInput.value);
        stayNights = Math.round(
            (checkOutDate - checkInDate) / (1000 * 60 * 60 * 24)
        );
        if (stayNights < 1) stayNights = 1;
    }

    function init() {
        setTimeout(() => {
            if (!document.querySelector(".room-checkbox")) {
                console.warn("Không tìm thấy phần tử có class 'room-checkbox'");
            }

            window.initRoomCheckboxes();
            initTooltips();
            initRoomFilterForm();
        }, 100);
    }

    init();

    document.querySelectorAll(".increase-quantity").forEach((button) => {
        button.addEventListener("click", function () {
            const roomCard = this.closest(".room-card");
            const roomId = roomCard.dataset.roomId;
            const inputEl =
                this.closest(".input-group").querySelector(".quantity-input");
            const currentValue = parseInt(inputEl.value);

            if (currentValue < parseInt(inputEl.max)) {
                inputEl.value = currentValue + 1;

                if (selectedRooms.has(roomId)) {
                    const room = selectedRooms.get(roomId);
                    room.quantity = currentValue + 1;
                    selectedRooms.set(roomId, room);
                }
            }
        });
    });

    document.querySelectorAll(".decrease-quantity").forEach((button) => {
        button.addEventListener("click", function () {
            const roomCard = this.closest(".room-card");
            const roomId = roomCard.dataset.roomId;
            const inputEl =
                this.closest(".input-group").querySelector(".quantity-input");
            const currentValue = parseInt(inputEl.value);

            if (currentValue > parseInt(inputEl.min)) {
                inputEl.value = currentValue - 1;

                if (selectedRooms.has(roomId)) {
                    const room = selectedRooms.get(roomId);
                    room.quantity = currentValue - 1;
                    selectedRooms.set(roomId, room);
                }
            }
        });
    });

    function updateBookNowUrl() {
        if (
            selectedRooms.size > 0 &&
            bookNowBtn &&
            checkInDateInput &&
            checkOutDateInput
        ) {
            const params = new URLSearchParams();
            params.append("check_in", checkInDateInput.value);
            params.append("check_out", checkOutDateInput.value);

            selectedRooms.forEach((room, roomId) => {
                params.append(`rooms[${roomId}]`, room.quantity);
            });

            bookNowBtn.href = `${SITE_URL}/booking/confirm?${params.toString()}`;
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat("vi-VN", {
            style: "currency",
            currency: "VND",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(amount);
    }

    if (checkInDateInput && checkOutDateInput) {
        checkInDateInput.addEventListener("change", validateDates);
        checkOutDateInput.addEventListener("change", validateDates);
    }

    function validateDates() {
        if (checkInDateInput.value && checkOutDateInput.value) {
            const checkIn = new Date(checkInDateInput.value);
            const checkOut = new Date(checkOutDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (checkIn < today) {
                alert("Ngày nhận phòng không thể trong quá khứ");
                checkInDateInput.value = formatDateForInput(today);
                return;
            }

            if (checkOut <= checkIn) {
                const nextDay = new Date(checkIn);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutDateInput.value = formatDateForInput(nextDay);
            }
        }
    }

    function formatDateForInput(date) {
        return date.toISOString().split("T")[0];
    }

    document
        .querySelector(".scroll-to-rooms")
        ?.addEventListener("click", function (e) {
            e.preventDefault();
            document.getElementById("rooms").scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        });

    function calculateTaxes(totalPrice) {
        const taxRate = 0.1;
        const taxAmount = totalPrice * taxRate;
        document.getElementById("taxesFees").textContent =
            formatCurrency(taxAmount);
        return taxAmount;
    }
});
