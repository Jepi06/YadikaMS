{{--
    Popup wajib ganti password.
    Variabel yang dikirim dari layout:
      - $user          : user yang sedang login
      - $profilUrl     : URL halaman profil (tempat ganti password)
      - $sedangDiProfil: true kalau halaman yang dibuka sekarang adalah halaman profil
--}}
@if (!empty($user) && $user->must_change_password)
    @php $sedangDiProfil = $sedangDiProfil ?? false; @endphp

    {{-- Banner permanen (selalu tampil di bagian atas halaman) --}}
    <div class="fixed top-0 inset-x-0 z-[60] bg-amber-500 text-white text-xs sm:text-sm font-semibold px-4 py-2 flex items-center justify-center gap-2 shadow-md">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>Anda masih memakai password default. Segera ganti password Anda.</span>
        @unless ($sedangDiProfil)
            <a href="{{ $profilUrl }}" class="underline underline-offset-2 hover:text-amber-100">Ganti sekarang</a>
        @endunless
    </div>
    <div class="h-9"></div>{{-- spacer supaya banner tidak menutupi konten --}}

    {{-- Modal popup --}}
    <div id="modalWajibGantiPassword"
        class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl border border-slate-200 p-6 text-center">
            <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                <i class="bi bi-shield-lock-fill text-2xl"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-800">Wajib Ganti Password</h2>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                Akun Anda masih menggunakan <strong>password default</strong>.
                Demi keamanan, silakan ganti password sebelum melanjutkan memakai sistem.
            </p>

            @if ($sedangDiProfil)
                <button type="button" onclick="tutupModalWajibPassword()"
                    class="mt-5 w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-2.5 transition-colors">
                    Mengerti, ganti password sekarang
                </button>
            @else
                {{-- Di halaman lain: modal TIDAK bisa ditutup, harus ke halaman profil --}}
                <a href="{{ $profilUrl }}"
                    class="mt-5 block w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-2.5 transition-colors">
                    Ganti Password Sekarang
                </a>
            @endif
        </div>
    </div>

    <script>
        function tutupModalWajibPassword() {
            document.getElementById('modalWajibGantiPassword')?.classList.add('hidden');
            // fokus ke input password lama kalau ada di halaman profil
            document.querySelector('input[name="current_password"]')?.focus();
        }
    </script>
@endif