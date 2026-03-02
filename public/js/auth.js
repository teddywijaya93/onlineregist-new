document.addEventListener("DOMContentLoaded", function () {
    // Login Submit
    const form = document.getElementById("loginForm");
    if (!form) return;

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();
        const loginUrl = form.dataset.loginUrl;
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
                await Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: data.message || "Login Berhasil",
                    confirmButtonColor: "#3085d6"
                });
                window.location.replace(data.redirect);
                return;
            }

            await Swal.fire({
                icon: "error",
                title: "Gagal",
                text: data.message || "Username / Password Salah",
                confirmButtonColor: "#d33"
            });
        } catch (err) {
            await Swal.fire({
                icon: "error",
                title: "Error",
                text: err.message || "Server tidak dapat dihubungi",
                confirmButtonColor: "#d33"
            });
        }
    });

    // Password Toogle
    document.querySelectorAll(".password-toggle").forEach(wrapper => {
        const input = wrapper.querySelector("input");
        if (!input) return;

        const button = document.createElement("button");
        button.type = "button";

        const eyeOpen = `
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        `;

        const eyeClosed = `
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 2l20 20"/>
                <path d="M10.58 10.58a2 2 0 002.83 2.83"/>
                <path d="M9.88 5.08A10.94 10.94 0 0112 4c7 0 11 8 11 8a21.86 21.86 0 01-4.42 5.08"/>
                <path d="M6.61 6.61A21.86 21.86 0 001 12s4 8 11 8c2.06 0 3.93-.5 5.39-1.39"/>
            </svg>
        `;

        button.innerHTML = eyeOpen;
        wrapper.appendChild(button);

        button.addEventListener("click", function () {
            const isHidden = input.type === "password";
            input.type = isHidden ? "text" : "password";
            button.innerHTML = isHidden ? eyeClosed : eyeOpen;
        });
    });
});