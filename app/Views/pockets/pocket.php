<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=NO">
    <title>Kantong</title>
    <?= $this->include('components/links') ?>
</head>

<body>
    <div class="bg-light font-montserrat ">
        <div class="main-container min-h-100">
            <div class="bg-primary pb-3 rounded-bottom-4" id="header">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="pt-4 fw-bold text-light fs-3">Kantong</p>
                        <p class="pt-4 small text-light">v0.1-dev</p>
                    </div>
                    <div class="card mt-4 rounded-3">
                        <div class="card-body d-flex justify-content-between">
                            <span class="fw-bold">Total Aset</span>
                            <span class="text-primary"><span class="fw-bolder">Rp <?= number_format($totalAset, 0, ',', '.') ?></span></span>
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
                    <button class="btn btn-outline-primary rounded-3" role="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddPocket">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <span class="me-4">
                                <i class="fa-solid fa-plus fa-fw fa-2x"></i>
                            </span>
                            <span class="fw-bold">
                                Kantong Baru
                            </span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- offcanvas bottom -->
    <div class="offcanvas offcanvas-bottom rounded-top-4 bg-light font-montserrat" tabindex="-1" style="height: 60vh;" id="offcanvasAddPocket" aria-labelledby="offcanvasAddPocketLabel" data-is-fullscreen="false">
        <div class="offcanvas-header swiper">
            <span class="nav-slide-strip bg-primary"></span>
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
                <input type="text" id="inputPocketName" class="form-control rounded-3" placeholder="Nama Kantong" oninput="clearFieldError('inputPocketName')">
                <label for="inputPocketName" class="form-label">Nama Kantong</label>
                <small class="text-danger" id="errorInputPocketName"></small>
            </div>

            <div class="d-grid mb-3">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="radioBtnType" value="0" id="radioTunai" autocomplete="off" checked>
                    <label class="btn btn-outline-primary rounded-start-3" for="radioTunai">Tunai</label>

                    <input type="radio" class="btn-check" name="radioBtnType" value="1" id="radioNontunai" autocomplete="off">
                    <label class="btn btn-outline-primary rounded-end-3" for="radioNontunai">Non-Tunai</label>
                </div>
            </div>

            <div class="d-grid">
                <button class="btn btn-primary mt-3 rounded-3" onclick="submitAddPocket()">Selesai</button>
            </div>

        </div>
    </div>

    <!-- offcanvas top -->
    <div class="offcanvas offcanvas-top rounded-bottom-4 bg-light font-montserrat" tabindex="-1" style="height: 60vh;" id="offcanvasDetailPocket" aria-labelledby="offcanvasDetailPocketLabel" data-is-fullscreen="false">
        <div class="offcanvas-body">

            <div class="mt-5 d-flex justify-content-center align-items-center display-2">
                <img id="pocketIcon" src="<?= base_url('public/assets/img/emojis/money-bag.png') ?>" width="60px">
            </div>
            <p class="text-center fw-bold mt-3 mb-0 fs-6" id="pocketName">Kantong Tunai</p>
            <div class="text-center"><span class="badge text-bg-primary rounded-pill fw-light" id="pocketType"> Tunai </span></div>
            <p class="text-center fw-bolder mt-2 fs-5" id="pocketBalance">Rp 50.000</p>

            <div class="d-flex justify-content-around mt-5">
                <div class="text-center" onclick="shortcutAddBalance()">
                    <div class="mx-auto btn btn-primary rounded-circle btn-lg d-flex justify-content-center align-items-center" style="width:51px;height: 51px;"><i class="fa-solid fa-plus"></i></div>
                    <p class="small fw-light mt-2">Tambah Uang</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto btn btn-primary rounded-circle btn-lg d-flex justify-content-center align-items-center" style="width:51px;height: 51px;"><i class="fa-solid fa-arrow-right-arrow-left"></i></div>
                    <p class="small fw-light mt-2">Pindahkan</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto btn btn-primary rounded-circle btn-lg d-flex justify-content-center align-items-center" style="width:51px;height: 51px;"><i class="fa-solid fa-arrow-up-from-bracket"></i></div>
                    <p class="small fw-light mt-2">Kirim / Bayar</p>
                </div>
            </div>

        </div>
        <div class="swiper text-center pb-2">
            Tarik untuk melihat detail <br>
            <i class="fa-solid fa-angles-down"></i>
        </div>
    </div>

    <?= $this->include('components/bottom-nav') ?>

    <?= $this->include('components/scripts') ?>
    <script>
        $(document).ready(() => {
            getPocketList();
            $('#nav_pockets').addClass('text-primary active');
        })
        let activeCanvas;
        let activePocket;

        const collapseEmoji = new bootstrap.Collapse('#collapseEmoji', {
            toggle: false
        })

        let touchstartY = 0;
        let touchendY = 0;

        const swipeElements = document.getElementsByClassName('swiper');

        Array.from(swipeElements).forEach(element => {
            element.addEventListener('touchstart', e => {
                touchstartY = e.changedTouches[0].screenY;
            });

            element.addEventListener('touchend', e => {
                touchendY = e.changedTouches[0].screenY;
                checkDirection();
            });
        });

        function checkDirection() {
            if (touchendY < touchstartY) {
                /* UP */
                if (activeCanvas == 'a') {
                    resizeOffcanvas('a', 90);
                }
                if (activeCanvas == 'b') {
                    bootstrap.Offcanvas.getInstance('#offcanvasDetailPocket').hide()
                }
            } else if (touchendY > touchstartY) {
                /* DOWN */
                if (activeCanvas == 'b') {
                    resizeOffcanvas('b', 90);
                    Notiflix.Loading.dots();
                    window.location.href = "<?= base_url('user/pockets/detail/') ?>" + activePocket;
                }
                if (activeCanvas == 'a') {
                    bootstrap.Offcanvas.getInstance('#offcanvasAddPocket').hide()
                }
            }
        }

        function resizeOffcanvas(offcanvas, height) {
            if (offcanvas == 'a') {
                $('#offcanvasAddPocket').animate({
                    height: height + 'vh'
                }, 200);
            } else {
                $('#offcanvasDetailPocket').animate({
                    height: height + 'vh'
                }, 200);
            }
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

        $('#offcanvasAddPocket').on('hidden.bs.offcanvas', event => {
            $('#inputPocketName').val('');
            $('#selectedEmoji').html('💰');
            $('#radioTunai').prop('checked', true).change();
            resizeOffcanvas('a', 60)
            activeCanvas = '';
        })

        $('#offcanvasDetailPocket').on('hidden.bs.offcanvas', event => {
            resizeOffcanvas('b', 60)
            activeCanvas = '';
            activePocket = '';
        })

        $('#offcanvasAddPocket').on('shown.bs.offcanvas', event => {
            activeCanvas = 'a';
        })

        $('#offcanvasDetailPocket').on('shown.bs.offcanvas', event => {
            activeCanvas = 'b';
        })

        function submitAddPocket() {
            Notiflix.Loading.dots();
            const icon = $("#selectedEmoji").data('emoji-code');
            const name = $('#inputPocketName').val();
            const type = $("input[name='radioBtnType']:checked").val();
            console.log(icon, name, type)

            $.post('<?= base_url('user/pockets/add') ?>', {
                    icon: icon,
                    name: name,
                    type: type
                })
                .done((data) => {
                    Notiflix.Loading.remove();
                    data = JSON.parse(data)
                    if (data.hasOwnProperty('name')) {
                        showFieldError('inputPocketName', data.name);
                    }
                    if (data.hasOwnProperty('response')) {
                        switch (data.response) {
                            case 'SUCCESS':
                                bootstrap.Offcanvas.getInstance($('#offcanvasAddPocket')).hide();
                                Notiflix.Notify.success('Kantong berhasil dibuat!');
                                getPocketList();
                                break;
                            case 'FAILED':
                                Notiflix.Notify.failure('Gagal membuat kantong!');
                                break;
                            case 'LIMIT':
                                Notiflix.Notify.warning('Setiap pengguna hanya boleh membuat maksimal 10 kantong!');
                                break;
                        }
                    }
                })
        }

        function showFieldError(field, message) {
            switch (field) {
                case 'inputPocketName':
                    $('#inputPocketName').addClass('is-invalid');
                    $('#errorInputPocketName').html(message);
                    break;
            }
        }

        function clearFieldError(field) {
            switch (field) {
                case 'inputPocketName':
                    $('#inputPocketName').removeClass('is-invalid');
                    $('#errorInputPocketName').html('');
                    break;
            }
        }

        function getPocketList() {
            Notiflix.Block.dots('#content');
            $.get('<?= base_url('user/pockets/list') ?>', (data) => {
                $('#content-wrapper').html(data);
                Notiflix.Block.remove('#content');
            })
        }

        function showPocketDetail(uuid, icon, name, balance, type) {
            const offcanvasTop = new bootstrap.Offcanvas('#offcanvasDetailPocket').show();

            activePocket = uuid;
            $('#pocketIcon').attr('src', '<?= base_url('public/assets/img/emojis/') ?>' + icon);
            $('#pocketName').html(name);
            $('#pocketType').html(type);
            $('#pocketBalance').html(balance);
        }

        function shortcutAddBalance() {
            Notiflix.Loading.dots();
            window.location.href = "<?= base_url('user/pockets/detail/') ?>" + activePocket + "#addBalance";
        }
    </script>
</body>

</html>