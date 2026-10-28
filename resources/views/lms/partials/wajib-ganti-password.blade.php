@php
    $wgpUser = $user ?? \App\Models\User::resolveAnyGuardUser();
    $wgpProfilUrl = $profilUrl ?? '#';
    $wgpDiProfil = (bool) ($sedangDiProfil ?? false);

    $wgpWajib = false;
    $wgpAlasan = '-';

    if ($wgpUser) {
        if ((int) $wgpUser->must_change_password === 1) {
            $wgpWajib = true;
            $wgpAlasan = 'flag';
        } elseif (config('app.default_password')) {
            // hasil cek di-cache di session supaya tidak berat tiap refresh
            $kunci = 'wgp_default_' . md5($wgpUser->password);
            if (!session()->has($kunci)) {
                session([$kunci => \Illuminate\Support\Facades\Hash::check(
                    config('app.default_password'),
                    $wgpUser->password
                )]);
            }
            if (session($kunci)) {
                $wgpWajib = true;
                $wgpAlasan = 'password-default';
            }
        }
    }
@endphp

<!-- wgp-debug: user={{ $wgpUser?->email ?? 'NULL' }} | flag={{ var_export($wgpUser?->must_change_password, true) }} | tampil={{ $wgpWajib ? 'YA' : 'TIDAK' }} | alasan={{ $wgpAlasan }} -->

@if ($wgpWajib)
    <style>
        #wgp-banner {
            position: fixed; left: 0; right: 0; bottom: 0; z-index: 2147483000;
            background: #f59e0b; color: #1f2937; font: 600 14px/1.4 sans-serif;
            padding: 10px 16px; text-align: center; box-shadow: 0 -2px 10px rgba(0,0,0,.2);
        }
        #wgp-banner a { color: #1f2937; text-decoration: underline; margin-left: 8px; }

        #wgp-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            z-index: 2147483001; display: flex; align-items: center; justify-content: center;
            padding: 16px; background: rgba(15, 23, 42, .75);
        }
        #wgp-box {
            width: 100%; max-width: 420px; background: #fff; border-radius: 16px;
            padding: 28px 24px; text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,.35);
            font-family: sans-serif; box-sizing: border-box;
        }
        #wgp-box .wgp-icon {
            width: 56px; height: 56px; margin: 0 auto 14px; border-radius: 50%;
            background: #fef3c7; font-size: 26px; line-height: 56px;
        }
        #wgp-box h2 { margin: 0; font-size: 18px; font-weight: 700; color: #1e293b; }
        #wgp-box p { margin: 10px 0 0; font-size: 14px; line-height: 1.6; color: #475569; }
        #wgp-box .wgp-btn {
            display: block; width: 100%; box-sizing: border-box; margin-top: 20px;
            padding: 11px 16px; border: 0; border-radius: 12px; background: #2563eb;
            color: #fff; font: 600 14px sans-serif; text-decoration: none; cursor: pointer;
        }
        #wgp-box .wgp-btn:hover { background: #1d4ed8; }
    </style>

    <div id="wgp-banner">
        &#9888; Anda masih memakai password default. Segera ganti password Anda.
        @unless ($wgpDiProfil)
            <a href="{{ $wgpProfilUrl }}">Ganti sekarang</a>
        @endunless
    </div>

    <div id="wgp-overlay">
        <div id="wgp-box">
            <div class="wgp-icon">&#128274;</div>
            <h2>Wajib Ganti Password</h2>
            <p>
                Akun Anda masih menggunakan <strong>password default</strong>.
                Demi keamanan, silakan ganti password sebelum melanjutkan memakai sistem.
            </p>

            @if ($wgpDiProfil)
                <button type="button" class="wgp-btn" id="wgp-tutup">Mengerti, ganti password sekarang</button>
            @else
                <a href="{{ $wgpProfilUrl }}" class="wgp-btn">Ke Halaman Profil &amp; Ganti Password</a>
            @endif
        </div>
    </div>

    <script>
        (function () {
            var banner = document.getElementById('wgp-banner');
            var overlay = document.getElementById('wgp-overlay');

            // pindahkan ke akhir <body> supaya tidak terkena flex/overflow layout
            document.body.appendChild(banner);
            document.body.appendChild(overlay);
            document.body.style.paddingBottom = '44px';

            var tutup = document.getElementById('wgp-tutup');
            if (tutup) {
                tutup.addEventListener('click', function () {
                    overlay.style.display = 'none';
                    var input = document.querySelector('input[name="current_password"]');
                    if (input) input.focus();
                });
            }
        })();
    </script>
@endif