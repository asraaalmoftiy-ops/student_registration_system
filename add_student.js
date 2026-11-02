document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");

    const validationMessage = document.getElementById("validation-message");

    form.addEventListener("submit", function (event) {

        event.preventDefault();

        validationMessage.textContent = "";
        validationMessage.style.color = "";

        const formData = new FormData(form);

        fetch("validate_student.php", {
            method: "POST",
            body: formData
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error("حدث خطأ أثناء الاتصال بالخادم.");
            }

            return response.json();
        })
        .then(function (result) {

            if (!result.valid) {

                const errorMessages = Object.values(result.errors);

                validationMessage.textContent = errorMessages.join(" ");

                validationMessage.style.color = "red";

            } else {

                form.submit();

            }

        })
        .catch(function (error) {

            validationMessage.textContent =
                "تعذّر التحقق من البيانات. يرجى المحاولة مرة أخرى.";

            validationMessage.style.color = "red";

            console.error(error);
        });

    });

});
