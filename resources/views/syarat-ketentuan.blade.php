@extends('layouts.app')
@section('title','Syarat & Ketentuan')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="auth-wrapper">
    <div class="container text-start">
        <div class="mb-5">
            <h3 class="head-lanjut text-white text-center mb-2">Syarat dan Ketentuan</h3>
            <p class="desc-lanjut mb-0"></p>
        </div>
        <div class="sk-text text-white">
            Rekening Dana Nasabah (RDN) adalah rekening atas nama nasabah yang digunakan khusus untuk keperluan transaksi investasi melalui Profits. RDN dikelola oleh bank mitra yang bekerja sama dengan Profits dan tunduk pada peraturan perundang-undangan yang berlaku.
            <br/><br/>
            Pembukaan RDN dilakukan berdasarkan data dan dokumen yang diberikan oleh nasabah. Nasabah bertanggung jawab atas kebenaran, kelengkapan, dan keabsahan seluruh data yang disampaikan dalam proses pembukaan dan penggunaan RDN.
            <br/><br/>
            Dana yang tersimpan di RDN hanya dapat digunakan untuk keperluan transaksi investasi, penyelesaian transaksi, dan penarikan dana sesuai dengan ketentuan yang berlaku. Bank mitra tidak bertanggung jawab atas keputusan investasi yang dilakukan oleh nasabah.
            <br/><br/>
            Bank mitra berhak melakukan proses verifikasi, peninjauan, atau pembaruan data nasabah dari waktu ke waktu guna memenuhi ketentuan keamanan dan kepatuhan. Dalam kondisi tertentu, bank mitra dapat menunda atau menolak transaksi apabila ditemukan ketidaksesuaian data atau indikasi aktivitas yang tidak sesuai ketentuan.
            Ketentuan terkait biaya administrasi, layanan perbankan, serta fitur lain yang berkaitan dengan RDN mengikuti kebijakan bank mitra yang berlaku.
            <br/><br/>
            Rekening Dana Nasabah (RDN) adalah rekening atas nama nasabah yang digunakan khusus untuk keperluan transaksi investasi melalui Profits. RDN dikelola oleh bank mitra yang bekerja sama dengan Profits dan tunduk pada peraturan perundang-undangan yang berlaku.
            <br/><br/>
            Pembukaan RDN dilakukan berdasarkan data dan dokumen yang diberikan oleh nasabah. Nasabah bertanggung jawab atas kebenaran, kelengkapan, dan keabsahan seluruh data yang disampaikan dalam proses pembukaan dan penggunaan RDN.
            <br/><br/>
            Dana yang tersimpan di RDN hanya dapat digunakan untuk keperluan transaksi investasi, penyelesaian transaksi, dan penarikan dana sesuai dengan ketentuan yang berlaku. Bank mitra tidak bertanggung jawab atas keputusan investasi yang dilakukan oleh nasabah.
            <br/><br/>
            Bank mitra berhak melakukan proses verifikasi, peninjauan, atau pembaruan data nasabah dari waktu ke waktu guna memenuhi ketentuan keamanan dan kepatuhan. Dalam kondisi tertentu, bank mitra dapat menunda atau menolak transaksi apabila ditemukan ketidaksesuaian data atau indikasi aktivitas yang tidak sesuai ketentuan.
            Ketentuan terkait biaya administrasi, layanan perbankan, serta fitur lain yang berkaitan dengan RDN mengikuti kebijakan bank mitra yang berlaku.
            <br/><br/>
            Rekening Dana Nasabah (RDN) adalah rekening atas nama nasabah yang digunakan khusus untuk keperluan transaksi investasi melalui Profits. RDN dikelola oleh bank mitra yang bekerja sama dengan Profits dan tunduk pada peraturan perundang-undangan yang berlaku.
            <br/><br/>
            Pembukaan RDN dilakukan berdasarkan data dan dokumen yang diberikan oleh nasabah. Nasabah bertanggung jawab atas kebenaran, kelengkapan, dan keabsahan seluruh data yang disampaikan dalam proses pembukaan dan penggunaan RDN.
            <br/><br/>
            Dana yang tersimpan di RDN hanya dapat digunakan untuk keperluan transaksi investasi, penyelesaian transaksi, dan penarikan dana sesuai dengan ketentuan yang berlaku. Bank mitra tidak bertanggung jawab atas keputusan investasi yang dilakukan oleh nasabah.
            <br/><br/>
            Bank mitra berhak melakukan proses verifikasi, peninjauan, atau pembaruan data nasabah dari waktu ke waktu guna memenuhi ketentuan keamanan dan kepatuhan. Dalam kondisi tertentu, bank mitra dapat menunda atau menolak transaksi apabila ditemukan ketidaksesuaian data atau indikasi aktivitas yang tidak sesuai ketentuan.
            Ketentuan terkait biaya administrasi, layanan perbankan, serta fitur lain yang berkaitan dengan RDN mengikuti kebijakan bank mitra yang berlaku.
        </div>
        <p class="bdr-sk"></p>
        <h4 class="sk-desc text-white mb-3">Pemahaman Produk dan Layanan</h4>
        <div class="sk-text text-white mb-4">Nasabah dengan ini telah memahami bahwa instrumen investasi ditawarkan oleh pegawai PT Phintraco Sekuritas yang sah dan kompeten. Nasabah juga telah mendapatkan dan memahami informasi yang memadai terkait kewajaran imbal hasil, potensi risiko, biaya, dan aspek lainnya dari instrumen investasi yang ditawarkan.</div>

        <h4 class="sk-desc text-white mb-3">Pernyataan Kebenaran Identitas Pemilik Manfaat maupun sumber dana</h4>
        <div class="sk-text text-white mb-5">Nasabah dengan ini menyatakan bahwa identitas dan sumber dana dari Pemilik Manfaat telah sesuai dengan yang disampaikan.</div>

        <form method="POST" action="{{ route('syarat.ketentuan.agree') }}">
            @csrf
            <div class="form-group mb-3 text-white d-flex align-items-start gap-2">
                <input type="checkbox" class="agreement-check" style="margin-top:7px">
                <span class="agreement-text">Saya memahami bahwa nasabah dilarang memberikan kuasa transaksi kepada pegawai PT Phintraco Sekuritas, sehingga dengan ini Saya menjamin bahwa Saya tidak akan memberi kuasa transaksi kepada pegawai PT Phintraco Sekuritas atas rekening efek Saya.</span>
            </div>
            <div class="form-group mb-3 text-white d-flex align-items-start gap-2">
                <input type="checkbox" class="agreement-check" style="margin-top:7px">
                <span class="agreement-text">Saya memberikan persetujuan kepada PT Phintraco Sekuritas untuk memproses data pribadi saya sesuai dengan ruang lingkup tujuan pemrosesan data.</span>
            </div>
            <div class="form-group mb-4 text-white d-flex align-items-start gap-2">
                <input type="checkbox" class="agreement-check" style="margin-top:7px">
                <span class="agreement-text">Saya menyatakan telah membaca, memahami dan menyetujui syarat dan ketentuan dari PT Phintraco Sekuritas dan Bank RDN.</span>
            </div>
            <button type="submit" id="btnNext" class="btn btn-primary btn-regist w-100" disabled>Setuju</button>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checks = document.querySelectorAll('.agreement-check');
    const btn = document.getElementById('btnNext');

    function validate() {
        const allChecked = [...checks].every(c => c.checked);
        btn.disabled = !allChecked;
    }

    checks.forEach(c => c.addEventListener('change', validate));
});
</script>

@endsection