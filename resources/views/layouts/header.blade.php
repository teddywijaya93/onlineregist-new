<header class="position-relative w-100 p-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ url('/') }}"><img src="{{ asset('storage/profits_onreg.png') }}" height="40"></a>
        @if(session()->has('accountId'))
        <button id="logoutBtn" class="btn btn-danger btn-sm">Logout</button>
        @endif
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.getElementById("logoutBtn");

    if (!logoutBtn) return;

    logoutBtn.addEventListener("click", function () {
        if (!confirm("Apakah Anda yakin akan logout?")) {
            return;
        }

        fetch("{{ route('logout') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async response => {
            if (!response.ok) {
                throw new Error("Server error");
            }
            return response.json();
        })
        .then(res => {
            // alert(res.message || "Logout berhasil");

            if (res.status) {
                window.location.href = "{{ route('login') }}";
            }
        })
        .catch(error => {
            console.error(error);
            alert("Terjadi kesalahan sistem");
        });
    });
});
</script>