<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Lms\MataPelajaran;
use App\Models\Lms\PengampuMapel;
use App\Models\Lms\WaliKelasPeriode;
use App\Models\Mapping\GuruPembimbing;
use App\Models\Role;
use App\Models\User;
use App\Support\SinkronRolePkl;
use App\Support\TahunAjaran;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GuruController extends Controller
{
    /** Sesuaikan dengan data tabel modules/roles Anda. */
    private const KODE_MODUL_PKL = 'pkl';
    private const KODE_ROLE_KAJUR = 'kepala_jurusan';
    private const KODE_ROLE_KESISWAAN = 'kesiswaan';
    /** Daftar semua guru LMS + jumlah penugasan mengajar periode aktif. */
    public function index(Request $request)
    {
        $q = $request->query('q');
        $tahunAjaran = TahunAjaran::sekarang();
        $semester = TahunAjaran::semesterSekarang();

        $guru = User::whereHas('roles', fn($r) => $r->where('kode', 'guru')
            ->whereHas('module', fn($m) => $m->where('kode', 'lms')))
            ->when($q, fn($query) => $query->where(fn($sub) => $sub
                ->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")))
            ->withCount(['pengampuMapel as mengajar_aktif_count' => fn($qq) => $qq
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)])
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $waliKelasMap = WaliKelasPeriode::where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->with('kelas')
            ->get()
            ->keyBy('user_id');

        return view('admin.guru.index', compact('guru', 'q', 'waliKelasMap', 'tahunAjaran', 'semester'));
    }

    /** Form tambah 1 guru baru (di luar Import Excel — buat kasus bikin 1 akun aja). */
    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
        ]);

        $email = $data['email'] ?: $this->generateEmailUnik($data['name']);

        $guru = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => false,
        ]);

        $roleGuruLmsId = Role::whereHas('module', fn($m) => $m->where('kode', 'lms'))
            ->where('kode', 'guru')
            ->value('id');

        if ($roleGuruLmsId) {
            $guru->roles()->attach($roleGuruLmsId, ['assigned_at' => now()]);
        }

        $this->syncGuruPembimbing($guru);

        return redirect()->route('admin.guru.kelola', $guru)
            ->with('status', "Guru {$guru->name} dibuat. Email login: {$guru->email}, password default: password. Lanjutkan atur penugasan mengajar di bawah.");
    }

    /** Form edit nama/email 1 guru. */
    public function editAccount(User $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function updateAccount(Request $request, User $guru)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $guru->id],
        ]);

        $guru->update($data);

        $this->syncGuruPembimbing($guru);

        return redirect()->route('admin.guru.index')->with('status', 'Data guru diperbarui.');
    }

    /** Hapus akun guru. Ditolak kalau masih punya penugasan mengajar aktif (FK restrict). */
    public function destroy(User $guru)
    {
        try {
            $guru->delete();
        } catch (QueryException $e) {
            return back()->withErrors([
                'guru' => "Gagal menghapus {$guru->name} — masih punya penugasan mengajar. Hapus dulu semua penugasannya lewat halaman \"Kelola\".",
            ]);
        }

        return redirect()->route('admin.guru.index')->with('status', "Guru {$guru->name} dihapus.");
    }

    /** Reset password guru ke default: "password". */
    public function resetPassword(User $guru)
    {
        $guru->update([
            'password' => Hash::make('password'),
        ]);

        return back()->with('status', "Password {$guru->name} berhasil direset ke default: \"password\".");
    }

    /** Halaman kelola 1 guru: penugasan mengajar + wali kelas + kepala jurusan. */
    public function kelola(User $guru)
    {
        $tahunAjaran = TahunAjaran::sekarang();
        $semester = TahunAjaran::semesterSekarang();

        $pengampuMapel = PengampuMapel::with('mataPelajaran', 'kelas')
            ->where('guru_id', $guru->id)
            ->orderByDesc('tahun_ajaran')
            ->orderByDesc('semester')
            ->get();

        $mataPelajaranList = MataPelajaran::orderBy('nama')->get();
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();

        $waliKelasSaatIni = WaliKelasPeriode::where('user_id', $guru->id)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->first();

        $jurusanList = Jurusan::with('kepalaJurusan')->orderBy('nama')->get();
        // Guru ini sudah jadi kesiswaan? + daftar kesiswaan lain (untuk info)
        $adalahKesiswaan = $guru->hasPklRole(self::KODE_ROLE_KESISWAAN);

        $daftarKesiswaan = User::whereHas('roles', fn($r) => $r->where('kode', self::KODE_ROLE_KESISWAAN)
            ->whereHas('module', fn($m) => $m->where('kode', self::KODE_MODUL_PKL)))
            ->orderBy('name')
            ->get(['id', 'name']);
        return view('admin.guru.kelola', compact(
            'guru',
            'pengampuMapel',
            'mataPelajaranList',
            'kelasList',
            'waliKelasSaatIni',
            'tahunAjaran',
            'semester',
            'jurusanList',
            'adalahKesiswaan',   // ← BARU
            'daftarKesiswaan'    // ← BARU
        ));
    }

    /** Tambah 1 penugasan mengajar (guru+mapel+kelas+periode). */
    public function storeMengajar(Request $request, User $guru)
    {
        $data = $request->validate([
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
            'semester' => ['required', 'in:Ganjil,Genap'],
        ]);

        $sudahAda = PengampuMapel::where($data)->where('guru_id', $guru->id)->exists();

        if ($sudahAda) {
            return back()->withErrors(['mata_pelajaran_id' => 'Penugasan ini sudah ada sebelumnya.']);
        }

        PengampuMapel::create($data + ['guru_id' => $guru->id]);

        // Kalau mapelnya BK → otomatis dapat role PKL Guru BK
        SinkronRolePkl::untuk($guru);

        return back()->with('status', 'Penugasan mengajar ditambahkan.');
    }

    public function destroyMengajar(PengampuMapel $pengampuMapel)
    {
        $guruId = $pengampuMapel->guru_id;
        $pengampuMapel->delete();

        // Cabut role Guru BK kalau sudah tidak mengajar BK
        SinkronRolePkl::untuk($guruId, true);

        return redirect()->route('admin.guru.kelola', $guruId)->with('status', 'Penugasan mengajar dihapus.');
    }

    /** Atur wali kelas guru ini, buat 1 periode tertentu. */
    public function storeWaliKelas(Request $request, User $guru)
    {
        $data = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
            'semester' => ['required', 'in:Ganjil,Genap'],
        ]);

        // Wali kelas lama untuk kelas ini (akan tergeser)
        $waliLama = WaliKelasPeriode::where('kelas_id', $data['kelas_id'])
            ->where('tahun_ajaran', $data['tahun_ajaran'])
            ->where('semester', $data['semester'])
            ->value('user_id');

        WaliKelasPeriode::updateOrCreate(
            ['kelas_id' => $data['kelas_id'], 'tahun_ajaran' => $data['tahun_ajaran'], 'semester' => $data['semester']],
            ['user_id' => $guru->id]
        );

        SinkronRolePkl::untuk($guru);

        if ($waliLama && (int) $waliLama !== (int) $guru->id) {
            SinkronRolePkl::untuk($waliLama, true);
        }

        return back()->with('status', 'Wali kelas berhasil diatur.');
    }

    public function destroyWaliKelas(WaliKelasPeriode $waliKelasPeriode)
    {
        $guruId = $waliKelasPeriode->user_id;
        $waliKelasPeriode->delete();

        SinkronRolePkl::untuk($guruId, true);

        return redirect()->route('admin.guru.kelola', $guruId)->with('status', 'Penugasan wali kelas dicabut.');
    }

    /** Atur jurusan yang dipimpin guru ini (checkbox). */
    /** Atur jurusan yang dipimpin guru ini (checkbox). Satu jurusan = satu kepala. */
    public function updateKepalaJurusan(Request $request, User $guru)
    {
        $data = $request->validate([
            'jurusan_ids' => ['nullable', 'array'],
            'jurusan_ids.*' => ['exists:jurusan,id'],
        ]);

        $dipilih = collect($data['jurusan_ids'] ?? [])->map(fn($id) => (int) $id)->all();

        // Tolak kalau ada jurusan yang sudah dipimpin guru lain
        $dipegangLain = Jurusan::with('kepalaJurusan')
            ->whereIn('id', $dipilih)
            ->whereNotNull('kepala_jurusan_id')
            ->where('kepala_jurusan_id', '!=', $guru->id)
            ->get();

        if ($dipegangLain->isNotEmpty()) {
            $daftar = $dipegangLain->map(function ($j) {
                $nama = $j->kepalaJurusan->name ?? '-';
                return "{$j->nama} (dipimpin {$nama})";
            })->implode(', ');

            return back()->withErrors([
                'jurusan_ids' => "Jurusan sudah punya kepala: {$daftar}. Lepas dulu dari guru tersebut sebelum menunjuk guru lain.",
            ]);
        }

        // Lepas jurusan yang sebelumnya dipimpin guru ini tapi sekarang tidak dicentang
        Jurusan::where('kepala_jurusan_id', $guru->id)
            ->whereNotIn('id', $dipilih)
            ->update(['kepala_jurusan_id' => null]);

        // Jadikan guru ini kepala jurusan yang dicentang
        Jurusan::whereIn('id', $dipilih)->update(['kepala_jurusan_id' => $guru->id]);

        // Sinkronkan role PKL 'kepala_jurusan'
        $roleId = Role::whereHas('module', fn($m) => $m->where('kode', self::KODE_MODUL_PKL))
            ->where('kode', self::KODE_ROLE_KAJUR)
            ->value('id');

        if ($roleId) {
            if (Jurusan::where('kepala_jurusan_id', $guru->id)->exists()) {
                if (! $guru->roles()->where('role_id', $roleId)->exists()) {
                    $guru->roles()->attach($roleId, ['assigned_at' => now()]);
                }
            } else {
                $guru->roles()->detach($roleId);
            }
        }

        return back()->with('status', 'Jurusan yang dipimpin berhasil diperbarui.');
    }
    /** Jadikan / cabut guru ini sebagai guru kesiswaan (boleh banyak orang). */
    public function updateKesiswaan(Request $request, User $guru)
    {
        $aktif = $request->boolean('kesiswaan');

        $roleId = Role::whereHas('module', fn($m) => $m->where('kode', self::KODE_MODUL_PKL))
            ->where('kode', self::KODE_ROLE_KESISWAAN)
            ->value('id');

        if (! $roleId) {
            return back()->withErrors([
                'kesiswaan' => "Role PKL '" . self::KODE_ROLE_KESISWAAN . "' tidak ditemukan di tabel roles.",
            ]);
        }

        $sudahPunya = $guru->roles()->where('role_id', $roleId)->exists();

        if ($aktif && ! $sudahPunya) {
            $guru->roles()->attach($roleId, ['assigned_at' => now()]);
        } elseif (! $aktif && $sudahPunya) {
            $guru->roles()->detach($roleId);
        }

        return back()->with('status', $aktif
            ? "{$guru->name} sekarang menjadi guru kesiswaan."
            : "Status guru kesiswaan {$guru->name} dicabut.");
    }
    public function importForm()
    {
        return view('admin.guru.import');
    }

    /** Download template Excel kosong (dengan contoh baris). */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Guru');

        $header = ['Nama Guru', 'Email Guru (opsional)', 'Mata Pelajaran', 'Kelas Diajar', 'Wali Kelas Dari (opsional)'];
        foreach ($header as $i => $label) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . '1', $label);
        }
        $sheet->getStyle('A1:E1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0D47A1');

        $sheet->fromArray([
            ['Budi Santoso, S.Kom', 'budi.santoso@smk.sch.id', 'Pemrograman Web', 'XI RPL 1', ''],
            ['Budi Santoso, S.Kom', 'budi.santoso@smk.sch.id', 'Pemrograman Web', 'XII RPL 1', 'XII RPL 1'],
        ], null, 'A2');

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'Template-Import-Guru.xlsx');
    }

    /** Proses file Excel yang diupload. */
    public function importProcess(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $tahunAjaran = TahunAjaran::sekarang();
        $semester = TahunAjaran::semesterSekarang();

        $roleGuruLmsId = Role::whereHas('module', fn($m) => $m->where('kode', 'lms'))
            ->where('kode', 'guru')
            ->value('id');

        $hasil = [];
        $errors = [];
        $userCache = [];

        foreach ($rows as $i => $row) {
            if ($i === 1) {
                continue; // baris header
            }

            $namaGuru = trim((string) ($row['A'] ?? ''));
            if ($namaGuru === '') {
                continue;
            }

            $emailGuru = trim((string) ($row['B'] ?? ''));
            $namaMapel = trim((string) ($row['C'] ?? ''));
            $namaKelasAjar = trim((string) ($row['D'] ?? ''));
            $namaKelasWali = trim((string) ($row['E'] ?? ''));

            $cacheKey = $emailGuru ?: $namaGuru;

            if (! isset($userCache[$cacheKey])) {
                $guru = $emailGuru
                    ? User::where('email', $emailGuru)->first()
                    : User::where('name', $namaGuru)->first();

                if (! $guru) {
                    $email = $emailGuru ?: $this->generateEmailUnik($namaGuru);
                    $guru = User::create([
                        'name' => $namaGuru,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'is_active' => true,
                        'is_super_admin' => false,
                    ]);
                }

                if ($roleGuruLmsId && ! $guru->roles()->where('role_id', $roleGuruLmsId)->exists()) {
                    $guru->roles()->attach($roleGuruLmsId, ['assigned_at' => now()]);
                }

                $this->syncGuruPembimbing($guru);

                $userCache[$cacheKey] = $guru;
            }

            $guru = $userCache[$cacheKey];

            // ── Penugasan mengajar ──────────────────────────────
            if ($namaMapel && $namaKelasAjar) {
                $mapel = MataPelajaran::whereRaw('LOWER(nama) = ?', [strtolower($namaMapel)])->first();
                $kelas = Kelas::whereRaw('LOWER(nama_kelas) = ?', [strtolower($namaKelasAjar)])->first();

                if (! $mapel) {
                    $errors[] = "Baris {$i}: mata pelajaran '{$namaMapel}' tidak ditemukan.";
                } elseif (! $kelas) {
                    $errors[] = "Baris {$i}: kelas '{$namaKelasAjar}' tidak ditemukan.";
                } else {
                    PengampuMapel::firstOrCreate([
                        'guru_id' => $guru->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'kelas_id' => $kelas->id,
                        'tahun_ajaran' => $tahunAjaran,
                        'semester' => $semester,
                    ]);

                    SinkronRolePkl::untuk($guru); // otomatis role Guru BK kalau mapel BK

                    $hasil[] = "{$guru->name} mengajar {$mapel->nama} di {$kelas->nama_kelas}";
                }
            }

            // ── Wali kelas ──────────────────────────────────────
            if ($namaKelasWali) {
                $kelasWali = Kelas::whereRaw('LOWER(nama_kelas) = ?', [strtolower($namaKelasWali)])->first();

                if (! $kelasWali) {
                    $errors[] = "Baris {$i}: kelas wali '{$namaKelasWali}' tidak ditemukan.";
                } else {
                    WaliKelasPeriode::updateOrCreate(
                        ['kelas_id' => $kelasWali->id, 'tahun_ajaran' => $tahunAjaran, 'semester' => $semester],
                        ['user_id' => $guru->id]
                    );

                    SinkronRolePkl::untuk($guru); // otomatis role Wali Kelas

                    $hasil[] = "{$guru->name} jadi wali kelas {$kelasWali->nama_kelas}";
                }
            }
        }

        return view('admin.guru.import-hasil', compact('hasil', 'errors'));
    }

    /**
     * Buat/tautkan baris guru_pembimbing untuk 1 user guru.
     * Urutan cari: (1) sudah tertaut ke user ini, (2) belum tertaut
     * tapi emailnya sama. Kalau tidak ada, buat baru.
     */
    private function syncGuruPembimbing(User $guru): void
    {
        $row = GuruPembimbing::where('user_id', $guru->id)->first()
            ?? GuruPembimbing::whereNull('user_id')->where('email', $guru->email)->first();

        if ($row) {
            $row->update([
                'user_id' => $guru->id,
                'nama' => $guru->name,
                'email' => $guru->email,
            ]);
        } else {
            GuruPembimbing::create([
                'user_id' => $guru->id,
                'nama' => $guru->name,
                'email' => $guru->email,
            ]);
        }
    }

    /** "Budi Santoso, S.Kom" -> "budi.santoso@smk.sch.id" (gelar dibuang, unik). */
    private function generateEmailUnik(string $nama): string
    {
        $namaBersih = trim(explode(',', $nama)[0]);
        $slug = Str::slug($namaBersih, '.');

        $email = $slug . '@smk.sch.id';
        $counter = 2;

        while (User::where('email', $email)->exists()) {
            $email = $slug . $counter . '@smk.sch.id';
            $counter++;
        }

        return $email;
    }
}
