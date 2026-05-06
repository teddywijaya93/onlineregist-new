<footer class="text-center text-muted py-4">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: "{{ session('error') }}"
                });
            @endif

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    html: "{{ session('success') }}",
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`
                });
            @endif

        });
    </script>
</footer>