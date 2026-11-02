const savedTheme = localStorage.getItem("theme") || "light";
document.documentElement.setAttribute("data-bs-theme", savedTheme);

document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("theme-toggle");
    if (!toggleBtn) return;

    const updateUI = (theme) => {
        toggleBtn.textContent = theme === "dark" ? "الوضع النهاري" : "الوضع الليلي";
        toggleBtn.setAttribute("aria-label", theme === "dark" ? "التحول إلى الوضع النهاري" : "التحول إلى الوضع الليلي");
    };

    updateUI(savedTheme);

    toggleBtn.addEventListener("click", () => {
        const current = document.documentElement.getAttribute("data-bs-theme");
        const next = current === "dark" ? "light" : "dark";

        document.documentElement.setAttribute("data-bs-theme", next);
        localStorage.setItem("theme", next);
        updateUI(next);
    });
});