document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    if (!form) return;

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();

        const loginUrl = form.dataset.loginUrl;
        const otpUrl   = form.dataset.otpUrl;
        const ktpUrl   = form.dataset.ktpUrl;

        try {
            const response = await fetch(loginUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ username, password })
            });

            const data = await response.json();
            if (!response.ok) throw data;

            if (data.status === true) {
               switch (data.registrationStep) {
                    case "otp":
                        window.location.replace(otpUrl);
                        break;

                    case "uploadKTP":
                        window.location.replace(ktpUrl);
                        break;

                    default:
                        window.location.replace("/dashboard");
                }
                return;
            }

            alert(data.message || "Username / Password Salah!");

        } catch (err) {
            alert(err.message || "Terjadi kesalahan sistem");
        }
    });
});