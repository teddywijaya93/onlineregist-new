 <div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Nama Refrensi Perorangan</label>
    <input type="text" name="nama_relasi" id="nama_relasi" value="{{ old('nama_relasi', session('referensi_perseorangan.nama_relasi')) }}" class="form-control form-global" placeholder="Tulis nama lengkap referensi">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Nomor Sesuai e-KTP</label>
    <input type="text" name="nik_relasi" id="nik_relasi" value="{{ old('nik_relasi', session('referensi_perseorangan.nik_relasi')) }}" class="form-control form-global" placeholder="NIK (Nomor Induk Kependudukan)">
</div> 
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Jenis Kelamin</label>
    <select name="jenis_kelamin_relasi" id="genderSelect" class="form-control" data-selected="{{ old('jenis_kelamin_relasi', session('referensi_perseorangan.jenis_kelamin_relasi')) }}">
        <option value="">Pilih Jenis Kelamin</option>
    </select>
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Tempat Lahir</label>
    <input type="text" name="tempat_lahir_relasi" id="tempat_lahir_relasi" value="{{ old('tempat_lahir_relasi', session('referensi_perseorangan.tempat_lahir_relasi')) }}" class="form-control form-global" placeholder="Tulis tempat lahir">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Tanggal Lahir</label>
    <input type="text" name="tanggal_lahir_relasi" id="tanggal_lahir_relasi" value="{{ old('tanggal_lahir_relasi', session('referensi_perseorangan.tanggal_lahir_relasi')) }}" class="form-control form-global" placeholder="Tulis tanggal lahir">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Status Perkawinan</label>
    <select name="status_perkawinan_relasi" id="maritalSelect" class="form-control" data-selected="{{ old('status_perkawinan_relasi', session('referensi_perseorangan.status_perkawinan_relasi')) }}">
        <option value="">Pilih Status Perkawinan</option>
    </select>
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Alamat Refrensi Perorangan sesuai e-KTP</label>
    <input type="text" name="alamat_relasi" id="alamat_relasi" value="{{ old('alamat_relasi', session('referensi_perseorangan.alamat_relasi')) }}" class="form-control form-global" placeholder="Tulis alamat referensi">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Kota</label>
    <input type="text" name="kota_relasi" id="kota_relasi" value="{{ old('kota_relasi', session('referensi_perseorangan.kota_relasi')) }}" class="form-control form-global" placeholder="Kota">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Kelurahan</label>
    <input type="text" name="kelurahan_relasi" id="kelurahan_relasi" value="{{ old('kelurahan_relasi', session('referensi_perseorangan.kelurahan_relasi')) }}" class="form-control form-global" placeholder="Kelurahan">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Kecamatan</label>
    <input type="text" name="kecamatan_relasi" id="kecamatan_relasi" value="{{ old('kecamatan_relasi', session('referensi_perseorangan.kecamatan_relasi')) }}" class="form-control form-global" placeholder="Kecamatan">
</div>