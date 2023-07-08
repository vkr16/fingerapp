<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <link rel="shortcut icon" href="<?= base_url('public/assets/img/logo.svg') ?>" type="image/x-icon">
    <?= $this->include('components/links.php') ?>
</head>

<body class="font-montserrat">
    <div class="container d-flex align-items-center justify-content-center px-4 min-h-100">
        <div class="my-4 col-12 col-md-6 col-lg-4">
            <p class="display-4 mb-2 fw-bold">Daftar</p>
            <hr style="width: 70%;" class="mb-4">
            <div class="mb-3">
                <label for="inputName" class="form-label fw-light">Nama</label>
                <input type="text" class="form-control rounded-1" id="inputName" oninput="clearFieldError('inputName')">
                <p class="small mt-1 mb-0 text-danger" id="errorInputName"></p>
            </div>
            <div class="mb-3">
                <label for="inputEmail" class="form-label fw-light">Email</label>
                <input type="email" class="form-control rounded-1" id="inputEmail" oninput="clearFieldError('inputEmail')">
                <p class="small mt-1 mb-0 text-danger" id="errorInputEmail"></p>
            </div>
            <div class="mb-3">
                <label for="inputPassword" class="form-label fw-light">Password</label>
                <input type="password" class="form-control rounded-1" id="inputPassword" oninput="clearFieldError('inputPassword')">
                <p class="small mt-1 mb-0 text-danger" id="errorInputPassword"></p>
            </div>
            <div class="mb-3 d-flex justify-content-between flex-wrap">
                <div class="mb-1">
                    <input type="checkbox" class="form-check-input" id="checkShowPassword" onchange="showPassword()">
                    <label class="form-check-label small" for="checkShowPassword">Tampilkan password</label>
                </div>
            </div>
            <div class="d-grid mb-4">
                <button type="button" class="btn btn-primary rounded-1" onclick="addUser()">Daftar</button>
                <p class="text-center small my-2">atau</p>
                <a href="<?= base_url() ?>" class="btn btn-outline-primary rounded-1">Masuk</a>
            </div>
            <br>
            <p class="text-center small">&copy; 2023 Fikri Miftah Akmaludin</p>
        </div>
    </div>
    <?= $this->include('components/scripts.php') ?>

    <script>
        function showPassword() {
            if ($('#inputPassword').attr('type') == "password") {
                $('#inputPassword').attr('type', "text");
            } else {
                $('#inputPassword').attr('type', "password");
            }
        }

        function addUser() {
            Notiflix.Loading.dots();

            const name = $('#inputName').val();
            const email = $('#inputEmail').val();
            const password = $('#inputPassword').val();

            const loadingTimeout = setTimeout(() => {
                Notiflix.Report.warning('Perhatian', 'Proses ini sudah berlangsung lebih lama dari biasanya. <br>Pastikan koneksi anda dalam kondisi normal, atau refresh halaman dan coba lagi.', 'Oke');
            }, 15000);

            $.post('<?= base_url('register') ?>', {
                    name: name,
                    email: email,
                    password: password
                })
                .done((data) => {
                    clearTimeout(loadingTimeout);
                    Notiflix.Loading.remove();

                    console.log(data);

                    data = JSON.parse(data);
                    if (data.hasOwnProperty('name')) {
                        showFieldError('inputName', data.name);
                    }
                    if (data.hasOwnProperty('email')) {
                        showFieldError('inputEmail', data.email);
                    }
                    if (data.hasOwnProperty('domain')) {
                        Notiflix.Report.failure('Domain Email Tidak Dikenali', data.domain, 'Oke');
                    }
                    if (data.hasOwnProperty('password')) {
                        showFieldError('inputPassword', data.password);
                    }

                    if (data.hasOwnProperty('response')) {
                        switch (data.response) {
                            case 'SUCCESS':
                                Notiflix.Report.success('Email Verifikasi Terkirim', 'Periksa email anda untuk memverifikasi akun anda!', 'Oke', () => {
                                    window.location.href = '<?= base_url() ?>';
                                });
                                break;
                            case 'FAIL':
                                Notiflix.Report.failure('Terjadi Kesalahan', 'Akun anda gagal didaftarkan, silahkan coba lagi!', 'Oke');
                                break;
                            case 'EMAIL_FAIL':
                                Notiflix.Report.failure('Terjadi Kesalahan', 'Gagal mengirimkan email verifikasi!', 'Coba lagi', () => {
                                    addUser();
                                });
                                break;
                        }
                    }
                })
                .fail(() => {
                    clearTimeout(loadingTimeout);
                    Notiflix.Loading.remove();
                    Notiflix.Report.failure(
                        'Terjadi Kesalahan',
                        '<center>Periksa koneksi internet anda dan coba lagi, jika error berulang silahkan hubungi administrator.</center>',
                        'Oke'
                    );
                })
        }

        function showFieldError(field, message) {
            switch (field) {
                case 'inputName':
                    $('#inputName').addClass('is-invalid');
                    $('#errorInputName').html(message);
                    break;
                case 'inputEmail':
                    $('#inputEmail').addClass('is-invalid');
                    $('#errorInputEmail').html(message);
                    break;
                case 'inputPassword':
                    $('#inputPassword').addClass('is-invalid');
                    $('#errorInputPassword').html(message);
                    break;
            }
        }

        function clearFieldError(field) {
            switch (field) {
                case 'inputName':
                    $('#inputName').removeClass('is-invalid');
                    $('#errorInputName').html('');
                    break;
                case 'inputEmail':
                    $('#inputEmail').removeClass('is-invalid');
                    $('#errorInputEmail').html('');
                    break;
                case 'inputPassword':
                    $('#inputPassword').removeClass('is-invalid');
                    $('#errorInputPassword').html('');
                    break;
            }
        }
    </script>
</body>

</html>