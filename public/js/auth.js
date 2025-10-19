document.body.classList.add("auth-page");

document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".auth-form");
    const authBtn = document.querySelector(".auth-btn, .auth-auth-btn");
    const inputs = document.querySelectorAll(".form-input");

    inputs.forEach((input) => {
        input.addEventListener("focus", function () {
            this.parentElement.style.transform = "scale(1.01)";
        });

        input.addEventListener("blur", function () {
            this.parentElement.style.transform = "scale(1)";
        });
    });

    if (form && authBtn) {
        form.addEventListener("submit", function () {
            authBtn.classList.add("loading");

            setTimeout(() => {
                authBtn.classList.remove("loading");
            }, 3000);
        });
    }

    document.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && e.target.classList.contains("form-input")) {
            const currentIndex = Array.from(inputs).indexOf(e.target);
            const nextInput = inputs[currentIndex + 1];

            if (nextInput) {
                nextInput.focus();
            } else if (form) {
                form.submit();
            }
        }
    });

    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector(
        'input[name="password_confirmation"]'
    );

    if (passwordInput && confirmPasswordInput) {
        confirmPasswordInput.addEventListener("blur", function () {
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.style.borderColor = "#ff6b6b";
                confirmPasswordInput.style.boxShadow =
                    "0 0 0 4px rgba(255, 107, 107, 0.1)";
            } else {
                confirmPasswordInput.style.borderColor = "#51cf66";
                confirmPasswordInput.style.boxShadow =
                    "0 0 0 4px rgba(81, 207, 102, 0.1)";
            }
        });
    }
});
