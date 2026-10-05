{{-- resources/views/lms/siswa/kelas.blade.php --}}
@extends('lms.layouts.app')

@section('title', 'Mata Pelajaran - LMS Yadika')
@section('breadcrumb', 'Mata Pelajaran')

@section('content')
    @php
        $namaKelas = $siswa->kelas->nama_kelas ?? ($siswa->kelas->nama ?? null);
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Mata Pelajaran</h1>
            <p class="text-sm text-slate-500 mt-1">
                Semua mata pelajaran{{ $namaKelas ? ' untuk kelas ' . $namaKelas : '' }}. Buka materi dan tugas dari tiap mapel.
            </p>
        </div>
        <a href="{{ route('lms.siswa.dashboard') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-600">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    @include('lms.siswa._tabel-mapel', ['mapel' => $mapelDiKelas])
@endsection