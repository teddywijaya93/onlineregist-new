<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Nama</label>
    <input type="text" name="nama_relasi_" id="nama_relasi_" value="{{ old('nama_relasi_', session('referensi_perseorangan.nama_relasi_')) }}" class="form-control form-global" placeholder="Tulis nama lengkap relasi">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Nomor Ponsel</label>
    <input type="text" name="nomor_ponsel_relasi" id="nomor_ponsel_relasi" value="{{ old('nomor_ponsel_relasi', session('referensi_perseorangan.nomor_ponsel_relasi')) }}" class="form-control form-global" placeholder="Tulis nomor ponsel relasi">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Email</label>
    <input type="email" name="email_relasi" id="email_relasi" value="{{ old('email_relasi', session('referensi_perseorangan.email_relasi')) }}" class="form-control form-global" placeholder="Tulis email relasi">
</div>