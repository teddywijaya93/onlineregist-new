document.addEventListener("DOMContentLoaded", function () {
    const previewMode = document.getElementById("previewMode");
    const drawMode = document.getElementById("drawMode");
    const btnLeft = document.getElementById("btnLeft");
    const btnRight = document.getElementById("btnRight");
    const canvas = document.getElementById("signature-pad");
    const wrapper = document.querySelector(".signature-pad-wrapper");
    const form = document.getElementById("signatureForm");
    const fileInput = document.getElementById("fileInput");
    const imageInput = document.getElementById("imageInput");

    let signaturePad;
    let isDrawMode = false;
    let galleryImage = null;

    function initCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);

        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        signaturePad = new SignaturePad(canvas, {
            penColor: "#ffffff"
        });

        signaturePad.addEventListener("endStroke", () => {
            if (!signaturePad.isEmpty()) {
                btnRight.classList.remove("disabled");
                wrapper.classList.remove("error");
            }
        });
    }

    // RIGHT BUTTON
    btnRight.addEventListener("click", function () {
        // MASUK DRAW MODE
        if (!isDrawMode && !galleryImage) {
            isDrawMode = true;

            previewMode.classList.add("d-none");
            drawMode.classList.remove("d-none");

            initCanvas();

            btnLeft.innerText = "Hapus";
            btnLeft.classList.remove("btn-primary");
            btnLeft.classList.add("btn-danger-outline");
            btnRight.innerText = "Selesai";
            btnRight.classList.add("disabled");

            return;
        }

        // SAVE CANVAS
        if (isDrawMode) {
            if (signaturePad.isEmpty()) {
                wrapper.classList.add("error");
                return;
            }
            submit(convertToBlack(signaturePad));
        }

        // SAVE GALLERY
        if (galleryImage) {
            submit(galleryImage);
        }
    });

    // LEFT BUTTON
    btnLeft.addEventListener("click", function () {
        // OPEN GALLERY
        if (!isDrawMode && !galleryImage) {
            fileInput.click();
            return;
        }

        // CLEAR CANVAS
        if (isDrawMode) {
            signaturePad.clear();
            btnRight.classList.add("disabled");
            wrapper.classList.add("error");
            return;
        }

        // RESET GALLERY
        if (galleryImage) {
            galleryImage = null;

            previewMode.innerHTML = `<img src="/storage/ttd-area.svg" class="w-100">`;
            btnRight.innerText = "Mulai ambil tanda tangan";
            btnLeft.innerText = "Ambil dari galeri";
            btnLeft.classList.remove("btn-danger-outline");
            btnLeft.classList.add("btn-primary");
        }
    });

    // FILE INPUT
    fileInput.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            galleryImage = event.target.result;

            previewMode.innerHTML = `<img src="${galleryImage}" class="w-100 rounded">`;

            btnRight.innerText = "Selesai";
            btnLeft.innerText = "Hapus";
            btnLeft.classList.remove("btn-primary");
            btnLeft.classList.add("btn-danger-outline");
        };

        reader.readAsDataURL(file);
    });

    // SUBMIT
    function submit(base64) {
        imageInput.value = base64; // ✅ penting
        form.submit();
    }
});

function convertToBlack(signaturePad) {
    const originalCanvas = signaturePad.canvas;
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    canvas.width = originalCanvas.width;
    canvas.height = originalCanvas.height;

    // ✅ background putih
    ctx.fillStyle = "#FFFFFF";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // ambil data dari canvas asli
    const srcCtx = originalCanvas.getContext("2d");
    const srcData = srcCtx.getImageData(0, 0, canvas.width, canvas.height);

    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;
    const src = srcData.data;

    for (let i = 0; i < data.length; i += 4) {

        const alpha = src[i + 3]; // ambil dari canvas asli

        // 👉 kalau ada stroke
        if (alpha > 0) {
            data[i] = 0;     // R
            data[i + 1] = 0; // G
            data[i + 2] = 0; // B
            data[i + 3] = 255;
        }
    }

    ctx.putImageData(imageData, 0, 0);

    return canvas.toDataURL("image/png");
}