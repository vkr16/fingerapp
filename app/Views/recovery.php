<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link rel="shortcut icon" href="<?= base_url('public/assets/img/logo.svg') ?>" type="image/x-icon">
    <?= $this->include('components/links.php') ?>
</head>

<body class="font-montserrat">
    <div class="container d-flex align-items-center justify-content-center px-4 min-h-100">
        <div class="my-4 col-12 col-md-6 col-lg-4">
            <p class="display-4 mb-2 fw-bold">Lupa Password</p>
            <hr style="width: 70%;" class="mb-4">
            <div class="mb-5">
                <label for="inputEmail" class="form-label fw-light">Email</label>
                <input type="email" class="form-control rounded-1" id="inputEmail">
            </div>
            <div class="d-grid mb-4">
                <button type="button" class="btn btn-primary rounded-1">Reset Password</button>
                <p class="text-center small my-2">atau</p>
                <a href="<?= base_url() ?>" class="btn btn-outline-primary rounded-1">Masuk</a>
            </div>
            <br>
            <p class="text-center small">&copy; 2023 Fikri Miftah Akmaludin</p>
        </div>
    </div>
    <?= $this->include('components/scripts.php') ?>
</body>

</html>