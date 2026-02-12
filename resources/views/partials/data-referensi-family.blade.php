 <div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Nama Refrensi Perorangan</label>
    <input type="text" name="nama" id="nama" value="" class="form-control form-global" placeholder="Tulis nama lengkap referensi">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Nomor Sesuai e-KTP</label>
    <input type="text" name="nik" id="nik" value="" class="form-control form-global" placeholder="NIK (Nomor Induk Kependudukan)">
</div> 
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Jenis Kelamin</label>
    <select name="jenis_kelamin" id="genderSelect" class="form-control">
        <option value="">Pilih Jenis Kelamin</option>
    </select>
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Tempat Lahir</label>
    <input type="text" name="tempat_lahir" id="tempat_lahir" value="" class="form-control form-global" placeholder="Tulis tempat lahir">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Tanggal Lahir</label>
    <input type="text" name="tanggal_lahir" id="tanggal_lahir" value=""  class="form-control form-global" placeholder="Tulis tanggal lahir">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Status Perkawinan</label>
    <select name="status_perkawinan" id="maritalSelect" class="form-control" data-selected="{{ $data['status_perkawinan'] ?? '' }}">
        <option value="">Pilih Status Perkawinan</option>
    </select>
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Alamat Refrensi Perorangan sesuai e-KTP</label>
    <input type="text" name="alamat" id="alamat" value="" class="form-control form-global" placeholder="Tulis alamat referensi">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Kota</label>
    <input type="text" name="kota" id="kota" value="" class="form-control form-global" placeholder="Kota">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Kelurahan</label>
    <input type="text" name="kelurahan" id="kelurahan" value="" class="form-control form-global" placeholder="Kelurahan">
</div>
<div class="form-group mb-4">
    <label class="form-label text-white text-form-global mb-2">Kecamatan</label>
    <input type="text" name="kecamatan" id="kecamatan" value="" class="form-control form-global" placeholder="Kecamatan">
</div>