class DateValidation {
    constructor(checkInSelector, checkOutSelector, options = {}) {
        this.checkInInput = document.querySelector(checkInSelector);
        this.checkOutInput = document.querySelector(checkOutSelector);
        this.options = {
            autoCorrect: true,
            showAlert: true,
            alertMessage:
                "Ngày trả phòng phải sau ngày nhận phòng. Đã tự động điều chỉnh.",
            ...options,
        };

        if (this.checkInInput && this.checkOutInput) {
            this.init();
        }
    }

    init() {
        this.setMinDates();
        this.attachEventListeners();
        this.initialValidation();
    }

    getTodayString() {
        return new Date().toISOString().split("T")[0];
    }

    getTomorrowString() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        return tomorrow.toISOString().split("T")[0];
    }

    getNextDayString(dateString) {
        const date = new Date(dateString);
        date.setDate(date.getDate() + 1);
        return date.toISOString().split("T")[0];
    }

    setMinDates() {
        this.checkInInput.min = this.getTodayString();
        this.updateCheckOutMin();
    }

    updateCheckOutMin() {
        if (this.checkInInput.value) {
            this.checkOutInput.min = this.getNextDayString(
                this.checkInInput.value
            );
        } else {
            this.checkOutInput.min = this.getTomorrowString();
        }
    }

    attachEventListeners() {
        this.checkInInput.addEventListener("change", () => {
            this.handleCheckInChange();
        });

        this.checkOutInput.addEventListener("change", () => {
            this.handleCheckOutChange();
        });
    }

    handleCheckInChange() {
        this.updateCheckOutMin();

        if (
            this.checkOutInput.value &&
            this.checkOutInput.value <= this.checkInInput.value
        ) {
            if (this.options.autoCorrect) {
                this.checkOutInput.value = this.getNextDayString(
                    this.checkInInput.value
                );
                if (this.options.showAlert) {
                    this.showAlert();
                }
            }
        }
    }

    handleCheckOutChange() {
        if (
            this.checkInInput.value &&
            this.checkOutInput.value <= this.checkInInput.value
        ) {
            if (this.options.autoCorrect) {
                this.checkOutInput.value = this.getNextDayString(
                    this.checkInInput.value
                );
                if (this.options.showAlert) {
                    this.showAlert();
                }
            }
        }
    }

    initialValidation() {
        if (this.checkInInput.value && !this.checkOutInput.value) {
            this.checkOutInput.value = this.getNextDayString(
                this.checkInInput.value
            );
        }
    }

    showAlert() {
        if (typeof this.options.alertCallback === "function") {
            this.options.alertCallback(this.options.alertMessage);
        } else {
            alert(this.options.alertMessage);
        }
    }

    validateBeforeSubmit() {
        const checkInDate = new Date(this.checkInInput.value);
        const checkOutDate = new Date(this.checkOutInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (checkInDate < today) {
            return {
                valid: false,
                message: "Ngày nhận phòng phải từ hôm nay trở đi.",
            };
        }

        if (checkOutDate <= checkInDate) {
            return {
                valid: false,
                message: "Ngày trả phòng phải sau ngày nhận phòng.",
            };
        }

        return { valid: true };
    }
}

document.addEventListener("DOMContentLoaded", function () {
    if (
        document.querySelector("#check_in") &&
        document.querySelector("#check_out")
    ) {
        new DateValidation("#check_in", "#check_out");
    }

    if (
        document.querySelector("#search_check_in") &&
        document.querySelector("#search_check_out")
    ) {
        new DateValidation("#search_check_in", "#search_check_out");
    }

    if (
        document.querySelector('input[name="check_in"]') &&
        document.querySelector('input[name="check_out"]')
    ) {
        new DateValidation('input[name="check_in"]', 'input[name="check_out"]');
    }
});
