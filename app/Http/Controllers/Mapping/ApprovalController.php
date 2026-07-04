<?php

namespace App\Http\Controllers\Mapping;

use App\Models\Mapping\PenempatanPkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::guard('pkl')->user();
        $query = PenempatanPkl::with(['siswa.kelas.jurusan', 'tempatPkl', 'guruPembimbing']);

        // Filter berdasarkan role_pkl: tampilkan yang relevan untuk diapprove
        // PERBAIKAN: sebelumnya $user->role (kolom ini tidak ada di tabel users),
        // sehingga match() selalu jatuh ke default dan admin/siapapun tidak
        // pernah melihat antrian approval-nya.
        match ($user->role_pkl) {
            'wali_kelas'     => $query->where('status', 'diajukan')->where('status_wali_kelas', 'pending'),
            'guru_bk'        => $query->where('status_wali_kelas', 'approved')->where('status_guru_bk', 'pending'),
            'kesiswaan'      => $query->where('status_guru_bk', 'approved')->where('status_kesiswaan', 'pending'),
            'kepala_jurusan' => $query->where('status_kesiswaan', 'approved')->where('status_kepala_jurusan', 'pending'),
            default          => $query->whereRaw('1=0'), // admin lihat semua lewat halaman penempatan
        };

        $penempatan = $query->latest()->paginate(15);

        return view('Mapping.approval.index', compact('penempatan', 'user'));
    }

    public function approve(Request $request, PenempatanPkl $penempatan)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = Auth::guard('pkl')->user();

        if (! $penempatan->canApproveBy($user)) {
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

        if (! $penempatan->canApproveBy($user)) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menolak penempatan ini.');
        }

        $this->processApproval($penempatan, $user, 'rejected', $request->catatan);

        return back()->with('success', 'Penempatan telah ditolak.');
    }

    private function processApproval(PenempatanPkl $penempatan, $user, string $action, ?string $catatan): void
    {
        $roleMap = [
            'wali_kelas'     => 'wali_kelas',
            'guru_bk'        => 'guru_bk',
            'kesiswaan'      => 'kesiswaan',
            'kepala_jurusan' => 'kepala_jurusan',
        ];

        // PERBAIKAN: sebelumnya $roleMap[$user->role] -> selalu error/null
        // karena kolom yang benar adalah role_pkl.
        $key = $roleMap[$user->role_pkl];

        $penempatan->update([
            "status_{$key}"      => $action,
            "catatan_{$key}"     => $catatan,
            "approved_at_{$key}" => now(),
            "approved_by_{$key}" => $user->id,
        ]);

        // Cek apakah semua 4 approver sudah approve → status = approved
        $penempatan->refresh();
        if ($action === 'rejected') {
            $penempatan->update(['status' => 'rejected']);
        } elseif (
            $penempatan->status_wali_kelas     === 'approved' &&
            $penempatan->status_guru_bk        === 'approved' &&
            $penempatan->status_kesiswaan      === 'approved' &&
            $penempatan->status_kepala_jurusan === 'approved'
        ) {
            $penempatan->update(['status' => 'approved']);
        }
    }
}
