<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Kelas;
use App\Models\Lms\WaliKelasPeriode;
use App\Models\Mapping\PenempatanPkl;
use App\Support\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('pkl')->user();
        $query = PenempatanPkl::with(['siswa.kelas.jurusan', 'tempatPkl', 'guruPembimbing']);

        /**
         * Role many-to-many: satu user bisa punya lebih dari satu role PKL.
         * Tiap role yang dimiliki menambahkan kondisi OR-nya sendiri, dengan
         * scoping yang sama seperti PenempatanPkl::tahapUntukUser().
         */
        $query->where(function ($outer) use ($user) {
            $adaCabangCocok = false;

            if ($user->hasPklRole('wali_kelas')) {
                $adaCabangCocok = true;
                // PERBAIKAN: wali kelas dibaca dari WaliKelasPeriode (periode aktif),
                // bukan hanya kolom kelas.wali_kelas_id yang tidak pernah terisi.
                $kelasDiwalikan = $this->kelasYangDiwalikan($user);
                $outer->orWhere(function ($q) use ($kelasDiwalikan) {
                    $q->where('status', 'diajukan')
                        ->where('status_wali_kelas', 'pending')
                        ->whereHas('siswa', fn($qq) => $qq->whereIn('kelas_id', $kelasDiwalikan));
                });
            }

            if ($user->hasPklRole('guru_bk')) {
                $adaCabangCocok = true;
                $kelasDiampu = $this->kelasYangDiampuGuruBk($user);
                $outer->orWhere(function ($q) use ($kelasDiampu) {
                    $q->where('status', 'diajukan')
                        ->where('status_wali_kelas', 'approved')
                        ->where('status_guru_bk', 'pending')
                        ->whereHas('siswa', fn($qq) => $qq->whereIn('kelas_id', $kelasDiampu));
                });
            }

            if ($user->hasPklRole('kesiswaan')) {
                $adaCabangCocok = true;
                $outer->orWhere(function ($q) {
                    $q->where('status', 'diajukan')
                        ->where('status_guru_bk', 'approved')
                        ->where('status_kesiswaan', 'pending');
                });
            }

            if ($user->hasPklRole('kepala_jurusan')) {
                $adaCabangCocok = true;
                $outer->orWhere(function ($q) use ($user) {
                    $q->where('status', 'diajukan')
                        ->where('status_kesiswaan', 'approved')
                        ->where('status_kepala_jurusan', 'pending')
                        ->whereHas('siswa.kelas.jurusan', fn($qq) => $qq->where('kepala_jurusan_id', $user->id));
                });
            }

            // admin/hubin/siswa: tidak ada cabang yang cocok -> jangan tampilkan apa-apa
            // (admin/hubin kelola lewat halaman Penempatan langsung)
            if (!$adaCabangCocok) {
                $outer->whereRaw('1=0');
            }
        });

        $penempatan = $query->latest()->paginate(15);

        return view('Mapping.approval.index', compact('penempatan', 'user'));
    }

    public function approve(Request $request, PenempatanPkl $penempatan)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = Auth::guard('pkl')->user();

        if (!$penempatan->canApproveBy($user)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menyetujui penempatan ini.');
        }

        $this->processApproval($penempatan, $user, 'approved', $request->catatan);

        return back()->with('success', 'Penempatan berhasil disetujui.');
    }

    public function reject(Request $request, PenempatanPkl $penempatan)
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        $user = Auth::guard('pkl')->user();

        if (!$penempatan->canApproveBy($user)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menolak penempatan ini.');
        }

        $this->processApproval($penempatan, $user, 'rejected', $request->catatan);

        return back()->with('success', 'Penempatan telah ditolak.');
    }

    private function processApproval(PenempatanPkl $penempatan, $user, string $action, ?string $catatan): void
    {
        /**
         * Pakai tahapUntukUser() yang sama persis dipakai canApproveBy(),
         * supaya kolom yang diupdate selalu sesuai tahap yang benar-benar
         * sedang aktif & jadi hak user ini.
         */
        $key = $penempatan->tahapUntukUser($user);

        if (!$key) {
            return;
        }

        $penempatan->update([
            "status_{$key}" => $action,
            "catatan_{$key}" => $catatan,
            "approved_at_{$key}" => now(),
            "approved_by_{$key}" => $user->id,
        ]);

        $penempatan->refresh();

        if ($action === 'rejected') {
            $penempatan->update(['status' => 'rejected']);
        } elseif (
            $penempatan->status_wali_kelas === 'approved' &&
            $penempatan->status_guru_bk === 'approved' &&
            $penempatan->status_kesiswaan === 'approved' &&
            $penempatan->status_kepala_jurusan === 'approved'
        ) {
            $penempatan->update(['status' => 'approved']);
        }
    }

    /**
     * Daftar kelas_id yang diwalikan user ini: dari WaliKelasPeriode (periode
     * aktif) ditambah kolom lama kelas.wali_kelas_id kalau masih terisi.
     * Aturannya sama dengan PenempatanPkl::diampuOlehWaliKelas().
     */
    private function kelasYangDiwalikan($user): array
    {
        $dariPeriode = WaliKelasPeriode::where('user_id', $user->id)
            ->where('tahun_ajaran', TahunAjaran::sekarang())
            ->where('semester', TahunAjaran::semesterSekarang())
            ->pluck('kelas_id')
            ->all();

        $dariKolomLama = Kelas::where('wali_kelas_id', $user->id)->pluck('id')->all();

        return array_values(array_unique(array_merge($dariPeriode, $dariKolomLama)));
    }

    /**
     * Daftar kelas_id yang diampu guru BK ini, diturunkan dari pengampu_mapel
     * (BK dianggap "mengajar" kelas itu lewat mata pelajaran berkode BK).
     */
    private function kelasYangDiampuGuruBk($user): array
    {
        return DB::table('pengampu_mapel')
            ->join('mata_pelajaran', 'mata_pelajaran.id', '=', 'pengampu_mapel.mata_pelajaran_id')
            ->where('pengampu_mapel.guru_id', $user->id)
            ->where('mata_pelajaran.kode', PenempatanPkl::KODE_MAPEL_BK)
            ->pluck('pengampu_mapel.kelas_id')
            ->all();
    }
}