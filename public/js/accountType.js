async function saveAccountType() {
    const token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const selected = document.querySelector('input[name="accountType"]:checked');

    if (!selected) {
        Swal.fire({
            icon: "warning",
            title: "Pilih Tipe Akun"
        });

        return;
    }

    const type = selected.value;
    try {
        const res = await fetch("/account-type", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token
            },
            body: JSON.stringify({
                registrationId: window.registrationId,
                accountType: type
            })
        });

        const data = await res.json();
        await Swal.fire({
            icon: data.status ? "success" : "error",
            title: data.message
        });

        if (!data.status) return;

        window.location.href = "/verifikasi-ktp";

    } catch {
        Swal.fire({
            icon: "error",
            title: "Terjadi Kesalahan Sistem"
        });
    }
}