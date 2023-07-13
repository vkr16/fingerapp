<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=NO">
    <title>Anggaran</title>
    <?= $this->include('components/links') ?>
</head>

<body>
    <div class="bg-light font-montserrat ">
        <div class="main-container min-h-100">
            <div class="bg-info pb-3 rounded-bottom-4" id="header">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="pt-4 fw-bold text-light fs-3">Anggaran</p>
                        <p class="pt-4 small text-light">v0.1-dev</p>
                    </div>
                    <div class="card mt-4 rounded-3">
                        <div class="card-body d-flex justify-content-between">
                            <span class="fw-bold">Total Anggaran</span>
                            <span class="text-info fw-bolder">Rp 50.000.000</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <hr>
            </div>

            <div class="container scrollable-y" id="content">
                <span id="content-wrapper"></span>
                <div class="d-grid">
                    <button class="btn btn-outline-info rounded-3" role="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddBudget">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <span class="me-4">
                                <i class="fa-solid fa-plus fa-fw fa-2x"></i>
                            </span>
                            <span class="fw-bold">
                                Anggaran Baru
                            </span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- offcanvas -->
    <div class="offcanvas offcanvas-bottom rounded-top-4 bg-light font-montserrat" tabindex="-1" style="height: 60vh;" id="offcanvasAddBudget" aria-labelledby="offcanvasAddBudgetLabel" data-is-fullscreen="false">
        <div class="offcanvas-header" id="swiper">
            <span class="nav-slide-strip"></span>
        </div>
        <div class="offcanvas-body">
            <div class="card mx-auto mb-3" style="height: 100px; width: 100px;" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEmoji" aria-expanded="false">
                <div class="card-body d-flex justify-content-center align-items-center display-2">
                    <img id="selectedEmoji" src="<?= base_url('public/assets/img/emojis/money-bag.png') ?>" width="60px" data-emoji-code="money-bag.png">
                </div>
            </div>

            <div class="collapse mb-3" id="collapseEmoji">
                <div id="emojiSelector" class="d-flex justify-content-center fs-2 flex-wrap">
                    <button class="btn btn-outline-primary mx-1 my-1" onclick="setEmoji('1') "><img src="<?= base_url('public/assets/img/emojis/money-mouth-face.png') ?>" width="30px"></button>
                    <button class="btn btn-outline-primary mx-1 my-1" onclick="setEmoji('2') "><img src="<?= base_url('public/assets/img/emojis/money-with-wings.png') ?>" width="30px"></button>
                    <button class="btn btn-outline-primary mx-1 my-1" onclick="setEmoji('3') "><img src="<?= base_url('public/assets/img/emojis/money-bag.png') ?>" width="30px"></button>
                    <button class="btn btn-outline-primary mx-1 my-1" onclick="setEmoji('4') "><img src="<?= base_url('public/assets/img/emojis/fire.png') ?>" width="30px"></button>
                    <button class="btn btn-outline-primary mx-1 my-1" onclick="setEmoji('5') "><img src="<?= base_url('public/assets/img/emojis/card.png') ?>" width="30px"></button>
                    <button class="btn btn-outline-primary mx-1 my-1" onclick="setEmoji('6') "><img src="<?= base_url('public/assets/img/emojis/bank.png') ?>" width="30px"></button>
                    <button class="btn btn-outline-primary mx-1 my-1" onclick="setEmoji('7') "><img src="<?= base_url('public/assets/img/emojis/red-heart.png') ?>" width="30px"></button>
                </div>
            </div>

            <div class="form-floating mb-3">
                <input type="text" id="inputBudgetName" class="form-control rounded-3" placeholder="Nama Anggaran" oninput="clearFieldError('inputBudgetName')">
                <label for="inputBudgetName" class="form-label">Nama Anggaran</label>
                <small class="text-danger" id="errorInputBudgetName"></small>
            </div>

            <div class="d-grid">
                <button class="btn btn-info mt-3 rounded-3" onclick="submitAddBudget()">Selesai</button>
            </div>

        </div>
    </div>

    <?= $this->include('components/bottom-nav') ?>

    <?= $this->include('components/scripts') ?>
    <script>
        $(document).ready(() => {
            getBudgetList();
            $('#nav_budgets').addClass('text-info active');
        })

        const collapseEmoji = new bootstrap.Collapse('#collapseEmoji', {
            toggle: false
        })

        let touchstartY = 0;
        let touchendY = 0;

        const swipeElement = document.getElementById('swiper');

        swipeElement.addEventListener('touchstart', e => {
            touchstartY = e.changedTouches[0].screenY;
        });

        swipeElement.addEventListener('touchend', e => {
            touchendY = e.changedTouches[0].screenY;
            checkDirection();
        });

        function checkDirection() {
            if (touchendY < touchstartY) {
                resizeOffcanvas(90);
                $('#offcanvasAddBudget').data('is-fullscreen', 'true');
            };
            if (touchendY > touchstartY) {
                $('#offcanvasAddBudget').data('is-fullscreen', 'false');
                bootstrap.Offcanvas.getInstance($('#offcanvasAddBudget')).hide();
                resizeOffcanvas(60);
            };
        }

        function resizeOffcanvas(height) {
            $('#offcanvasAddBudget').animate({
                height: height + 'vh'
            }, 200);
        }

        function setEmoji(rawmoji) {
            collapseEmoji.hide();
            let emoji = '';

            switch (rawmoji) {
                case '1':
                    emoji = 'money-mouth-face.png';
                    break;
                case '2':
                    emoji = 'money-with-wings.png';
                    break;
                case '3':
                    emoji = 'money-bag.png';
                    break;
                case '4':
                    emoji = 'fire.png';
                    break;
                case '5':
                    emoji = 'card.png';
                    break;
                case '6':
                    emoji = 'bank.png';
                    break;
                case '7':
                    emoji = 'red-heart.png';
                    break;
            }

            $('#selectedEmoji').attr('src', '<?= base_url('public/assets/img/emojis/') ?>' + emoji).data('emoji-code', emoji);
        }

        $('#offcanvasAddBudget').on('hidden.bs.offcanvas', event => {
            $('#inputBudgetName').val('');
            $('#selectedEmoji').html('💰');
            // $('#radioTunai').prop('checked', true).change();
        })

        function submitAddBudget() {
            Notiflix.Loading.dots();
            const icon = $("#selectedEmoji").data('emoji-code');
            const name = $('#inputBudgetName').val();

            $.post('<?= base_url('user/budgets/add') ?>', {
                    icon: icon,
                    name: name
                })
                .done((data) => {
                    Notiflix.Loading.remove();
                    data = JSON.parse(data)
                    if (data.hasOwnProperty('name')) {
                        showFieldError('inputBudgetName', data.name);
                    }
                    if (data.hasOwnProperty('response')) {
                        switch (data.response) {
                            case 'SUCCESS':
                                getBudgetList();
                                bootstrap.Offcanvas.getInstance($('#offcanvasAddBudget')).hide();
                                Notiflix.Notify.success('Anggaran berhasil dibuat!');
                                break;
                            case 'FAILED':
                                Notiflix.Notify.failure('Gagal membuat anggaran!');
                                break;
                        }
                    }
                })
                .fail(() => {
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
                case 'inputBudgetName':
                    $('#inputBudgetName').addClass('is-invalid');
                    $('#errorInputBudgetName').html(message);
                    break;
            }
        }

        function clearFieldError(field) {
            switch (field) {
                case 'inputBudgetName':
                    $('#inputBudgetName').removeClass('is-invalid');
                    $('#errorInputBudgetName').html('');
                    break;
            }
        }

        function getBudgetList() {
            Notiflix.Block.dots('#content');
            $.get('<?= base_url('user/budgets/list') ?>', (data) => {
                $('#content-wrapper').html(data);
                Notiflix.Block.remove('#content');
            })
        }
    </script>
</body>

</html>