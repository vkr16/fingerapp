<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk</title>
    <link rel="manifest" href="<?= base_url('public/assets/manifest.json') ?>">
    <?= $this->include('components/links.php') ?>
</head>

<body class="font-montserrat">
    <div class="container d-flex align-items-center justify-content-center px-4 min-h-100">
        <div class="my-4 col-12 col-md-6 col-lg-4">
            <?php
            if (isset($_SESSION['verification-success'])) {
            ?>
                <div class="alert alert-success text-success py-2 px-3 small">
                    <i class="fa-solid fa-check-circle"></i>&nbsp; &nbsp;
                    <?= $_SESSION['verification-success'] ?>
                </div>
            <?php
            }
            ?>
            <?php
            if (isset($_SESSION['verification-fail'])) {
            ?>
                <div class="alert alert-danger text-danger py-2 px-3 small">
                    <i class="fa-solid fa-xmark-circle"></i>&nbsp; &nbsp;
                    <?= $_SESSION['verification-fail'] ?>
                </div>
            <?php
            }
            ?>

            <p class="display-4 mb-2 fw-bold">Masuk</p>
            <hr style="width: 70%;" class="mb-4">
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
                <a href="<?= base_url('recovery') ?>" class="text-primary fw-light small">Lupa password?</a>
            </div>
            <div class="d-grid mb-4">
                <button type="button" class="btn btn-primary rounded-1" onclick="loginProcess()">Masuk</button>
                <p class="text-center small my-2">atau</p>
                <a href="<?= base_url('register') ?>" class="btn btn-outline-primary rounded-1">Buat Akun</a>
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

        function loginProcess() {
            clearFieldError('inputEmail');
            clearFieldError('inputPassword');
            Notiflix.Loading.dots();
            const email = $('#inputEmail').val();
            const password = $('#inputPassword').val();

            $.post('<?= base_url('login') ?>', {
                    email: email,
                    password: password
                })
                .done((data) => {
                    console.log(data)
                    Notiflix.Loading.remove();
                    data = JSON.parse(data);
                    if (data.hasOwnProperty('email')) {
                        showFieldError('inputEmail', data.email);
                    }
                    if (data.hasOwnProperty('password')) {
                        showFieldError('inputPassword', data.password);
                    }

                    if (data.hasOwnProperty('response')) {
                        switch (data.response) {
                            case 'LOGIN_VALID':
                                window.location.href = "<?= base_url('user/pockets') ?>";
                                break;
                            case 'ACCOUNT_DISABLED':
                                Notiflix.Report.failure('Akun Nonaktif', 'Akun anda dinonaktifkan, silahkan hubungi administrator <b>fm@akuonline.my.id</b> untuk informasi lebih lanjut', 'Oke');
                                break;
                        }
                    }
                })
                .fail(() => {
                    Notiflix.Loading.remove();
                    /* fail */
                })
        }

        function showFieldError(field, message) {
            switch (field) {
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

    <script src="<?= base_url('public/assets/js/index.js') ?>"></script>
</body>

</html>