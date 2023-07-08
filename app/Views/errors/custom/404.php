<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404</title>
    <link rel="shortcut icon" href="<?= base_url('public/assets/img/logo.svg') ?>" type="image/x-icon">
    <?= $this->include('components/links.php') ?>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center min-h-100 font-montserrat">
        <div class="text-center">
            <p class="display-3">404</p>
            <p class="fw-light fs-3">Halaman Tidak Ditemukan</p>
            <hr width="50%" class="mx-auto">
            <a href="<?= base_url('') ?>" class="btn btn-outline-primary mt-3">Kembali</a>
        </div>
    </div>

</body>

</html>