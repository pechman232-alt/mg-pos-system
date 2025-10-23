const toastDetails = {
    timer: 5000,
    success: { icon: "fa-circle-check", text: "Success toast." },
    error: { icon: "fa-circle-xmark", text: "Error toast." },
    warning: { icon: "fa-triangle-exclamation", text: "Warning toast." },
    info: { icon: "fa-circle-info", text: "Info toast." },
};

const removeToast = (toast) => {
    toast.classList.add("hide");
    if (toast.timeoutId) clearTimeout(toast.timeoutId);
    setTimeout(() => toast.remove(), 500);
};

window.createToast = (type, message = null) => {
    const notifications = document.querySelector(".my_custom_toast_alert"); // Dynamic DOM selection
    if (!notifications) {
        console.error("Toast container not found!");
        return;
    }
    const { icon, text } = toastDetails[type];
    const toastMessage = message || text;
    const toast = document.createElement("li");
    toast.className = `my_toast ${type}`;
    toast.innerHTML = `<div class="column">
                           <i class="fa-solid ${icon}"></i>
                           <span>${toastMessage}</span>
                        </div>
                        <i class="fa-solid fa-xmark" onclick="removeToast(this.parentElement)"></i>`;
    notifications.appendChild(toast);
    toast.timeoutId = setTimeout(() => removeToast(toast), toastDetails.timer);
};
