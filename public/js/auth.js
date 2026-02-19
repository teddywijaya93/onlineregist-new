document.addEventListener("DOMContentLoaded", function () {
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
                    text: data.message || "Login berhasil",
                    confirmButtonColor: "#3085d6"
                });

                window.location.replace(data.redirect);

                return;
            }

            await Swal.fire({
                icon: "error",
                title: "Gagal",
                text: data.message || "Login gagal",
                confirmButtonColor: "#d33"
            });

        } catch (err) {

            await Swal.fire({
                icon: "error",
                title: "Error",
                text: err.message || "Terjadi kesalahan sistem",
                confirmButtonColor: "#d33"
            });
        }
    });
});