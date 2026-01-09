<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>OCR KTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">

            <h4 class="mb-4">OCR KTP (Stable)</h4>

            <input type="file" id="ktp_image" class="form-control mb-3" accept="image/*">

            <div id="preview" class="mb-3"></div>

            <h5>RAW OCR</h5>
            <pre id="raw" class="bg-dark text-success p-3 rounded"></pre>

            <h5 class="mt-4">HASIL PARSING</h5>
            <pre id="parsed" class="bg-light p-3 border rounded"></pre>

        </div>
    </div>
</div>

<script>
document.getElementById('ktp_image').addEventListener('change', function () {

    let fd = new FormData();
    fd.append('ktp_image', this.files[0]);
    fd.append('_token', '{{ csrf_token() }}');

    document.getElementById('preview').innerHTML =
        `<img src="${URL.createObjectURL(this.files[0])}" class="img-fluid rounded">`;

    fetch('/verifikasi_ktp/ocr', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        document.getElementById('raw').innertext = res.raw_ocr || '';
        document.getElementById('parsed').innerText = JSON.stringify(res.data, null, 2);
    });
});
</script>

</body>
</html>
