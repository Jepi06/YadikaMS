@extends('lms.layouts.app')

@section('title', 'Presensi Sekarang')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="text-center mb-3">
                <h4 class="fw-bold mb-1">Presensi Sekarang</h4>
                <p class="text-muted mb-0">Arahkan kamera ke QR yang ditampilkan guru di kelas.</p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div id="reader" class="mx-auto"></div>
                    <div id="status" class="text-center small text-muted mt-3">Menyalakan kamera…</div>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('lms.siswa.dashboard') }}" class="text-decoration-none small text-muted">
                    <i class="bi bi-arrow-left"></i> Batal, kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<style>
    #reader { width: 100%; max-width: 400px; }
    #reader video { border-radius: 12px; }
</style>
@endpush

@push('scripts')
<script>
    const statusEl = document.getElementById('status');
    let sudahDiproses = false;

    function onScanSuccess(decodedText) {
        if (sudahDiproses) return;
        sudahDiproses = true;

        statusEl.textContent = 'QR terdeteksi, memproses presensi…';
        statusEl.classList.add('text-success', 'fw-semibold');

        // Hasil decode berisi URL lengkap ke /lms/siswa/presensi/scan/{token}
        // yang sudah dibuat guru — cukup arahkan browser ke sana.
        window.location.href = decodedText;
    }

    function onScanFailure() {
        // Dipanggil terus-menerus saat belum ada QR terdeteksi di frame — normal, diamkan saja.
    }

    const scanner = new Html5Qrcode('reader');
    scanner.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 240, height: 240 } },
        onScanSuccess,
        onScanFailure
    ).then(() => {
        statusEl.textContent = 'Kamera aktif — arahkan ke QR.';
    }).catch((err) => {
        statusEl.textContent = 'Tidak bisa mengakses kamera. Pastikan izin kamera diaktifkan untuk situs ini.';
        statusEl.classList.add('text-danger');
    });
</script>
@endpush
