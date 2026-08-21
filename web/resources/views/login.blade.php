@section('css-content')
<style>
    :root {
        --ph-login-image: url('{{ asset('Assets/images/background-login/bglogin-desktop.jpg') }}');
    }
</style>
@endsection

@include('layouts.header')
<body class="login-page">
    <div class="loading" id="loading" aria-hidden="true">Loading&#8230;</div>

    <main class="ph-login">
        <section class="ph-login-visual" aria-label="Tentang PondokHuda">
            <div class="ph-login-brand">
                <span class="brand-mark"><img src="{{ asset('Assets/images/logo/default-logo-white.png') }}" alt=""></span>
                <span>Pondok Huda</span>
            </div>

            <div class="ph-login-copy">
                <span class="ph-login-eyebrow">Manajemen kost terpadu</span>
                <h1>Lebih tenang mengelola kost.</h1>
                <p>
                    Pantau kamar, pembayaran, pengumuman, dan keluhan dalam satu
                    ruang kerja yang rapi. Cepat dibaca, mudah ditindaklanjuti.
                </p>
                <div class="ph-login-benefits" aria-label="Fitur utama">
                    <span class="ph-login-benefit"><i class="material-icons">verified_user</i> Akses aman</span>
                    <span class="ph-login-benefit"><i class="material-icons">receipt_long</i> Pembayaran terpantau</span>
                    <span class="ph-login-benefit"><i class="material-icons">forum</i> Komunikasi jelas</span>
                </div>
            </div>

            <div class="ph-login-meta">&copy; {{ date('Y') }} PondokHuda. Semua data tetap milik Anda.</div>
        </section>

        <section class="ph-login-formpane">
            <div class="ph-login-card">
                <div class="ph-login-mobile-brand">
                    <span class="brand-mark"><img src="{{ asset('Assets/images/logo/default-logo-white.png') }}" alt=""></span>
                    <span>Pondok Huda</span>
                </div>

                <h2>Selamat datang</h2>
                <p class="ph-login-subtitle">Masukkan kode akun dan PIN enam digit untuk melanjutkan.</p>

                @if(Session::has('message'))
                <div class="alert alert-danger ph-login-alert" role="alert">
                    <i class="material-icons" aria-hidden="true">error_outline</i>
                    <div>{{ Session::get('message') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                </div>
                @endif

                <form id="sign_in" action="{{ route('login') }}" method="POST" novalidate>
                    @csrf

                    <div class="ph-field">
                        <label for="kode">Kode akun</label>
                        <div class="ph-input-wrap">
                            <i class="material-icons" aria-hidden="true">person_outline</i>
                            <input
                                type="text"
                                class="form-control"
                                id="kode"
                                name="kode"
                                value="{{ old('kode') }}"
                                placeholder="Contoh: pa0001"
                                autocomplete="username"
                                autocapitalize="none"
                                required
                                autofocus>
                        </div>
                    </div>

                    <div class="ph-field">
                        <label for="pass">PIN</label>
                        <div class="ph-input-wrap">
                            <i class="material-icons" aria-hidden="true">lock_outline</i>
                            <input
                                type="password"
                                class="form-control"
                                id="pass"
                                name="pin"
                                placeholder="Enam digit PIN"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                minlength="6"
                                maxlength="6"
                                autocomplete="current-password"
                                required>
                            <button class="ph-pin-toggle" id="pinToggle" type="button" aria-label="Tampilkan PIN">
                                <i class="material-icons" aria-hidden="true">visibility</i>
                            </button>
                        </div>
                    </div>

                    <button class="btn ph-login-submit waves-effect" id="btnsub" type="submit">
                        Masuk ke dashboard
                    </button>

                    <div class="ph-login-actions">
                        <span>PIN harus terdiri dari 6 digit.</span>
                        <button id="forgotpass" class="btn btn-link" type="button">Lupa PIN?</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    @section('js-content')
    <script type="text/javascript">
        (function () {
            var form = document.getElementById('sign_in');
            var loading = document.getElementById('loading');
            var pin = document.getElementById('pass');
            var toggle = document.getElementById('pinToggle');

            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.reportValidity();
                    return;
                }
                loading.style.zIndex = '9999';
                loading.style.opacity = '.82';
            });

            toggle.addEventListener('click', function () {
                var hidden = pin.type === 'password';
                pin.type = hidden ? 'text' : 'password';
                toggle.setAttribute('aria-label', hidden ? 'Sembunyikan PIN' : 'Tampilkan PIN');
                toggle.querySelector('.material-icons').textContent = hidden ? 'visibility_off' : 'visibility';
                pin.focus();
            });

            document.getElementById('forgotpass').addEventListener('click', function () {
                swal('Lupa PIN?', 'Hubungi pemilik atau admin kost untuk mendapatkan bantuan.', 'info');
            });
        })();
    </script>
    @endsection

@include('layouts.footer')
