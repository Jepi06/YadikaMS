<?php

namespace App\Http\Controllers\Lms\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Lms\JenisPelanggaran;
use App\Models\Lms\PelanggaranSiswa;
use App\Models\Siswa;
use App\Services\Lms\PelanggaranAkses as Akses;
use App\Services\Lms\PelanggaranService;
use App\Support\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Poin siswa (deposit 500): pelanggaran mengurangi, prestasi menambah — dipakai semua guru.
 */
class PelanggaranController extends Controller
{
    private function user()
    {
        $user = Auth::guard('lms')->user();
        abort_unless(Akses::bolehInput($user), 403, 'Fitur ini khusus guru.');

        return $user;
    }

    /** Riwayat pelanggaran (sesuai kelas yang boleh diakses). */
    public function index(Request $request)
    {
        $user = $this->user();

        $kelasList = Akses::kelasBoleh($user);
        $kelasIds  = $kelasList->pluck('id')->all();
        $cakupan   = Akses::cakupanLabel($user);

        $kelasId  = (int) $request->query('kelas_id') ?: null;
        $kategori = $request->query('kategori');
        $q        = trim((string) $request->query('q'));

        if ($kelasId && ! in_array($kelasId, $kelasIds, true)) {
            abort(403);
        }

        $riwayat = PelanggaranSiswa::with(['siswa.kelas', 'pencatat'])
            ->whereHas('siswa', function ($s) use ($kelasIds, $kelasId, $q) {
                $s->whereIn('kelas_id', $kelasId ? [$kelasId] : $kelasIds)
                    ->when($q, fn($s) => $s->where(fn($w) => $w
                        ->where('nama', 'like', "%{$q}%")
                        ->orWhere('nis', 'like', "%{$q}%")));
            })
            ->when($kategori, fn($x) => $x->where('kategori', $kategori))
            ->when($request->query('tipe'), fn($x, $t) => $x->where('tipe', $t))
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $saldoPerSiswa = PelanggaranService::saldoMassal($riwayat->pluck('siswa_id')->unique()->all());

        $riwayat->getCollection()->each(function ($r) use ($user) {
            $r->bisa_hapus = Akses::bolehHapus($user, $r);
        });

        $kategoriList = JenisPelanggaran::select('kategori')->distinct()->orderBy('kategori')->pluck('kategori');

        return view('lms.guru.pelanggaran.index', compact(
            'kelasList', 'kelasId', 'kategori', 'q', 'riwayat', 'saldoPerSiswa', 'kategoriList', 'cakupan'
        ));
    }

    /** Form catat pelanggaran massal. */
    public function create(Request $request)
    {
        $user = $this->user();

        $kelasList = Akses::kelasBoleh($user);
        abort_if($kelasList->isEmpty(), 403, 'Anda tidak mengampu atau mewalikan kelas manapun pada periode ini.');

        $kelasId = (int) $request->query('kelas_id', $kelasList->first()->id);
        if (! $kelasList->contains('id', $kelasId)) {
            $kelasId = $kelasList->first()->id;
        }

        $jenis = JenisPelanggaran::aktif()->orderBy('urutan')->get();
        $pelanggaranPerKategori = $jenis->where('tipe', 'pelanggaran')->groupBy('kategori');
        $prestasiPerKategori    = $jenis->where('tipe', 'prestasi')->groupBy('kategori');
        $tipeAwal = $request->query('tipe') === 'prestasi' ? 'prestasi' : 'pelanggaran';

        $kelasPrestasiIds = Akses::kelasPrestasiIds($user);   // null = semua kelas
        $cakupan = Akses::cakupanLabel($user);

        return view('lms.guru.pelanggaran.create', compact(
            'kelasList', 'kelasId', 'pelanggaranPerKategori', 'prestasiPerKategori', 'tipeAwal', 'kelasPrestasiIds', 'cakupan'
        ));
    }

    /** JSON daftar siswa aktif satu kelas (untuk daftar centang). */
    public function siswaByKelas(Kelas $kelas)
    {
        $user = $this->user();
        abort_unless(Akses::kelasBoleh($user)->contains('id', $kelas->id), 403);

        $siswa = Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->orderBy('nama')->get(['id', 'nis', 'nama']);
        $saldo = PelanggaranService::saldoMassal($siswa->pluck('id')->all());

        return response()->json([
            'siswa' => $siswa->map(fn($s) => [
                'id' => $s->id, 'nis' => $s->nis, 'nama' => $s->nama, 'saldo' => $saldo[$s->id] ?? PelanggaranService::DEPOSIT,
            ])->values(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->user();

        $data = $request->validate([
            'tipe'                 => ['required', 'in:pelanggaran,prestasi'],
            'kelas_id'             => ['required', 'integer', 'exists:kelas,id'],
            'siswa_ids'            => ['required', 'array', 'min:1'],
            'siswa_ids.*'          => ['integer'],
            'jenis_pelanggaran_id' => ['nullable', 'integer', 'exists:jenis_pelanggaran_lms,id'],
            'nama_manual'          => ['nullable', 'string', 'max:150'],
            'poin_manual'          => ['nullable', 'integer', 'min:1', 'max:100'],
            'tanggal'              => ['required', 'date', 'before_or_equal:today'],
            'catatan'              => ['nullable', 'string', 'max:1000'],
        ], [
            'siswa_ids.required' => 'Pilih minimal satu siswa.',
            'siswa_ids.min'      => 'Pilih minimal satu siswa.',
            'poin_manual.max'    => 'Poin manual maksimal 100 per catatan.',
        ]);

        $tipe = $data['tipe'];
        $kelasId = (int) $data['kelas_id'];

        if (! Akses::bolehKelasTipe($user, $kelasId, $tipe)) {
            return back()->withInput()->withErrors([
                'kelas_id' => $tipe === 'prestasi'
                    ? 'Anda hanya dapat memberi prestasi pada kelas yang Anda ampu atau walikan.'
                    : 'Anda hanya dapat mencatat poin untuk kelas yang Anda ampu atau walikan.',
            ]);
        }

        $jenis = ! empty($data['jenis_pelanggaran_id'])
            ? JenisPelanggaran::aktif()->where('tipe', $tipe)->find($data['jenis_pelanggaran_id'])
            : null;

        if (! empty($data['jenis_pelanggaran_id']) && ! $jenis) {
            return back()->withInput()->withErrors(['jenis_pelanggaran_id' => 'Jenis tidak sesuai dengan tipe yang dipilih.']);
        }

        if ($jenis) {
            [$nama, $kategori, $poin] = [$jenis->nama, $jenis->kategori, $jenis->poin];
        } elseif ($tipe === 'prestasi') {
            // prestasi "lainnya": nama & poin diisi manual
            if (blank($data['nama_manual'] ?? null) || empty($data['poin_manual'])) {
                return back()->withInput()->withErrors(['nama_manual' => 'Pilih prestasi, atau isi nama dan poin prestasi manual.']);
            }
            [$nama, $kategori, $poin] = [$data['nama_manual'], 'Prestasi Lainnya', (int) $data['poin_manual']];
        } else {
            // pelanggaran tanpa jenis = catatan saja (0 poin)
            if (blank($data['catatan'] ?? null)) {
                return back()->withInput()->withErrors(['catatan' => 'Pilih jenis pelanggaran, atau isi catatan.']);
            }
            [$nama, $kategori, $poin] = ['Catatan (tanpa poin)', 'Catatan', 0];
        }

        $siswa = Siswa::where('kelas_id', $kelasId)->where('status', 'aktif')
            ->whereIn('id', $data['siswa_ids'])->get(['id', 'nama']);

        if ($siswa->isEmpty()) {
            return back()->withInput()->withErrors(['siswa_ids' => 'Siswa yang dipilih tidak ditemukan di kelas ini.']);
        }

        $sebelum = PelanggaranService::terpakaiMassal($siswa->pluck('id')->all());
        $now = now();
        $peran = Akses::peran($user);

        DB::transaction(function () use ($siswa, $data, $jenis, $tipe, $nama, $kategori, $poin, $kelasId, $user, $peran, $now) {
            PelanggaranSiswa::insert($siswa->map(fn($s) => [
                'siswa_id'             => $s->id,
                'kelas_id'             => $kelasId,
                'jenis_pelanggaran_id' => $jenis?->id,
                'tipe'                 => $tipe,
                'kategori'             => $kategori,
                'nama_pelanggaran'     => $nama,
                'poin'                 => $poin,
                'tanggal'              => $data['tanggal'],
                'catatan'              => $data['catatan'] ?? null,
                'dicatat_oleh'         => $user->id,
                'peran_pencatat'       => $peran,
                'tahun_ajaran'         => TahunAjaran::sekarang(),
                'semester'             => TahunAjaran::semesterSekarang(),
                'created_at'           => $now,
                'updated_at'           => $now,
            ])->all());
        });

        // Siswa yang naik tingkat sanksi (hanya untuk pelanggaran)
        $naik = [];
        if ($tipe === 'pelanggaran' && $poin > 0) {
            foreach ($siswa as $s) {
                $lama = PelanggaranService::tingkat($sebelum[$s->id] ?? 0);
                $baru = PelanggaranService::tingkat(($sebelum[$s->id] ?? 0) + $poin);
                if ($baru['kode'] > $lama['kode']) {
                    $naik[] = "{$s->nama} → {$baru['label']}";
                }
            }
        }

        $pesan = match (true) {
            $tipe === 'prestasi' => "Prestasi \"{$nama}\" (+{$poin} poin) dicatat untuk {$siswa->count()} siswa.",
            $poin > 0            => "Pelanggaran \"{$nama}\" (−{$poin} poin) dicatat untuk {$siswa->count()} siswa.",
            default              => "Catatan disimpan untuk {$siswa->count()} siswa.",
        };

        return redirect()
            ->route('lms.guru.pelanggaran.index', ['kelas_id' => $kelasId])
            ->with('success', $pesan)
            ->with('naik_tingkat', $naik);
    }

    public function destroy(PelanggaranSiswa $pelanggaran)
    {
        $user = $this->user();

        abort_unless(Akses::bolehHapus($user, $pelanggaran), 403, 'Anda hanya dapat menghapus catatan milik sendiri.');

        $pelanggaran->delete();

        return back()->with('success', 'Catatan pelanggaran dihapus.');
    }
}
