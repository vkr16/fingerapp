<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=NO">
    <title>Rincian Anggaran</title>
    <?= $this->include('components/links') ?>
</head>

<body>
    <div class="bg-light font-montserrat ">
        <div class="main-container min-h-100">
            <div class="bg-info pb-3 rounded-bottom-4" id="header">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="pt-4 fw-bold text-light fs-3"><a href="<?= base_url('user/budgets') ?>" class="text-light" onclick="Notiflix.Loading.dots()"><i class="fa-solid fa-arrow-left"></i></a>&emsp;Rincian Kantong</p>
                    </div>
                    <div class="card mt-4 rounded-4" id="header-content">
                        <div class="">
                            <div class="mt-3 d-flex justify-content-center align-items-center display-2">
                                <img id="budgetIcon" src="<?= base_url('public/assets/img/emojis/') . $budgetDetail['icon'] ?>" width="60px">
                            </div>
                            <p class="text-center fw-bold mt-3 mb-0 fs-6" id="budgetName"><?= $budgetDetail['name'] ?></p>

                            <p class="text-center fw-bolder mt-2 fs-5" id="budgetBalance">Rp <?= number_format($budgetDetail['budget'], 0, ',', '.') ?></p>

                            <div class="d-flex justify-content-around mt-5">
                                <div class="text-center col-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddBalance">
                                    <div class="mx-auto btn btn-info rounded-circle btn-lg d-flex justify-content-center align-items-center" style="width:51px;height: 51px;"><i class="fa-solid fa-plus"></i></div>
                                    <p class="small fw-light mt-2">Tambah</p>
                                </div>
                                <div class="text-center col-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTransferBalance">
                                    <div class="mx-auto btn btn-info rounded-circle btn-lg d-flex justify-content-center align-items-center" style="width:51px;height: 51px;"><i class="fa-solid fa-arrow-right-arrow-left"></i></div>
                                    <p class="small fw-light mt-2">Pindah</p>
                                </div>
                                <div class="text-center col-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMakeTransaction">
                                    <div class="mx-auto btn btn-info rounded-circle btn-lg d-flex justify-content-center align-items-center" style="width:51px;height: 51px;"><i class="fa-solid fa-arrow-up-from-bracket"></i></div>
                                    <p class="small fw-light mt-2">Kirim / Bayar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container my-3">
                <div class="mb-3 d-flex justify-content-between">
                    <input type="text" class="form-control" id="searchTransaction" placeholder="Cari Transaksi">
                    <div class="d-flex align-items-center d-none" id="btnCancelSearch">
                        &emsp;
                        <button class="btn btn-outline-secondary rounded-3">Kembali</button>
                    </div>
                </div>
            </div>

            <div class="container scrollable-y" id="transactionListContainer">
                <!-- Transaksi akan muncul disini -->
            </div>
        </div>
    </div>

    <!-- offcanvas bottom add balance -->
    <div class="offcanvas offcanvas-bottom rounded-top-4 bg-light font-montserrat" tabindex="-1" style="min-height: 50vh;" id="offcanvasAddBalance" aria-labelledby="offcanvasAddBalanceLabel" data-is-fullscreen="false">
        <div class="offcanvas-header swiper">
            <span class="nav-slide-strip bg-info"></span>
        </div>
        <div class="offcanvas-body">
            <div class="mb-4">
                <p class="fw-bold"><img src="<?= base_url('public/assets/img/top-up.png') ?>" style="width: 30px;"> &nbsp; Tambahkan Uang</p>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text bg-white border-info text-info fs-4"><b>Rp</b></span>
                <input type="text" inputmode="numeric" class="form-control border-info text-info border-start-0 py-3 fw-bold fs-4" placeholder="0" id="inputAddBalance" oninput="formatNumber(this)">
            </div>

            <div class="d-grid mb-3">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputAddBalanceNote" placeholder="Keterangan" oninput="charCount('#inputAddBalanceNote','#inputAddBalanceNoteLabel')" onblur="$('#inputAddBalanceNoteLabel').html('Keterangan')">
                    <label for="inputAddBalanceNote" id="inputAddBalanceNoteLabel">Keterangan</label>
                </div>
            </div>
            <div class="d-grid">
                <button class="btn btn-info mt-3 mb-4" onclick="addBudgetBalance()">Selesai</button>
            </div>
        </div>
    </div>

    <!-- offcanvas bottom transfer balance -->
    <div class="offcanvas offcanvas-bottom rounded-top-4 bg-light font-montserrat" tabindex="-1" style="min-height: 50vh; height: min-content;" id="offcanvasTransferBalance" aria-labelledby="offcanvasTransferBalanceLabel" data-is-fullscreen="false">
        <div class="offcanvas-header swiper">
            <span class="nav-slide-strip bg-info"></span>
        </div>
        <div class="offcanvas-body">
            <div class="mb-4">
                <p class="fw-bold"><img src="<?= base_url('public/assets/img/money-transfer.png') ?>" style="width: 30px;"> &nbsp; Pindahkan</p>
            </div>

            <div class="d-grid mb-3">
                <div class="card mb-2 rounded-3" onclick="$('#selectDestinationBudget').modal('show')">
                    <div class="card-body py-1 d-flex justify-content-between align-items-center">
                        <span>
                            <span style="font-size: 10pt;">
                                Kantong Tujuan
                            </span>
                            <br>
                            <span class="fw-bolder" id="selectedBudgetDestinationName">
                                Pilih Kantong
                            </span>
                        </span>
                        <span class="text-info ms-4 py-2">
                            <img src="<?= base_url('public/assets/img/emojis/money-bag.png') ?>" width="30px" id="selectedBudgetDestinationIcon">
                        </span>
                    </div>
                </div>
            </div>
            <input type="password" id="selectedBudgetDestinationUuid" class="d-none">

            <div class="d-grid mb-3">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white border-info text-info fs-4"><b>Rp</b></span>
                    <input type="text" inputmode="numeric" class="form-control border-info text-info border-start-0 py-3 fw-bold fs-4" placeholder="0" id="inputTransferAmount" oninput="formatNumber(this)">
                </div>
            </div>

            <div class="d-grid mb-3">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputTransferAmountNote" placeholder="Keterangan" oninput="charCount('#inputTransferAmountNote','#inputTransferAmountNoteLabel')" onblur="$('#inputTransferAmountNoteLabel').html('Keterangan')">
                    <label for="inputTransferAmountNote" id="inputTransferAmountNoteLabel">Keterangan</label>
                </div>
            </div>
            <div class="d-grid">
                <button class="btn btn-info mt-3 mb-4" onclick="transferBalance()">Selesai</button>
            </div>
        </div>
    </div>

    <!-- offcanvas bottom transfer balance -->
    <div class="offcanvas offcanvas-bottom rounded-top-4 bg-light font-montserrat" tabindex="-1" style="min-height: 50vh; height: min-content;" id="offcanvasMakeTransaction" aria-labelledby="offcanvasMakeTransactionLabel" data-is-fullscreen="false">
        <div class="offcanvas-header swiper">
            <span class="nav-slide-strip bg-info"></span>
        </div>
        <div class="offcanvas-body">
            <div class="mb-4">
                <p class="fw-bold"><img src="<?= base_url('public/assets/img/hand.png') ?>" style="width: 30px;"> &nbsp; Kirim / Bayar</p>
            </div>


            <div class="d-grid">
                <button class="btn btn-info mt-3 mb-4" onclick="transferBalance()">Selesai</button>
            </div>
        </div>
    </div>
    <!-- modal select destination budget -->
    <div class="modal fade" id="selectDestinationBudget" tabindex="-1" aria-labelledby="selectDestinationBudgetLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content rounded-3" style="max-height: fit-content;">
                <div class="modal-body">
                    <div id="listOtherBudget">
                        <?php
                        foreach ($budgets as $key => $budget) {
                        ?>
                            <div class="card mb-2 rounded-3" onclick="selectBudget('<?= $budget['name'] ?>','<?= $budget['uuid'] ?>','<?= $budget['icon'] ?>')">
                                <div class="card-body py-1 d-flex justify-content-between align-items-center">
                                    <span class="text-info me-4 py-2">
                                        <img src="<?= base_url('public/assets/img/emojis/' . $budget['icon'] . '') ?>" width="30px">
                                    </span>
                                    <span class="text-end ">
                                        <span class="fw-bolder">
                                            <?= $budget['name'] ?>
                                        </span>
                                        <br>
                                        <span class="small">
                                            Rp <?= number_format($budget['budget'], 0, ',', '.') ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="container mb-3 d-grid">
                    <button class="btn btn-info" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <?= $this->include('components/bottom-nav') ?>

    <?= $this->include('components/scripts') ?>
    <script>
        let modalSelector = false;
        $(document).ready(() => {
            $('#nav_budgets').addClass('text-info active');
            getTransactionList();
            var options = {
                searchable: true
            };
        })
        let activeCanvas;
        const activeBudget = '<?= $budgetDetail['uuid'] ?>';

        let touchstartY = 0;
        let touchendY = 0;

        const swipeElements = document.getElementsByClassName('swiper');

        function charCount(input, label) {

            if ($(input).val().length > 31) {
                $(input).val($(input).val().slice(0, 32));
            }

            $(label).html("Keterangan (" + $(input).val().length + "/32)")
        }

        Array.from(swipeElements).forEach(element => {
            element.addEventListener('touchstart', e => {
                touchstartY = e.changedTouches[0].screenY;
            });

            element.addEventListener('touchend', e => {
                touchendY = e.changedTouches[0].screenY;
                checkDirection();
            });
        });

        function formatNumber(input) {
            let myNumeral = numeral(input.value).format('0,0');
            myNumeral = myNumeral == 0 ? '' : myNumeral;
            if (numeral(input.value).value() >= 999999999999999) {
                myNumeral = numeral(numeral(input.value).value().toString().slice(0, -1)).format('0,0')
                console.log(myNumeral);
                Notiflix.Notify.failure('Digit angka melebihi batas (Max 15 digit)')
            }
            input.value = myNumeral.toString();
        }

        function checkDirection() {
            if (touchendY < touchstartY) {
                /* UP */
                switch (activeCanvas) {

                }
            } else if (touchendY > touchstartY) {
                /* DOWN */
                switch (activeCanvas) {
                    case 'a':
                        bootstrap.Offcanvas.getInstance('#offcanvasAddBalance').hide()
                        break;
                    case 'b':
                        bootstrap.Offcanvas.getInstance('#offcanvasTransferBalance').hide();
                        break;
                    case 'c':
                        break;
                        bootstrap.Offcanvas.getInstance('#offcanvasAddBudget').hide()
                    default:
                        break;
                }
            }
        }

        function resizeOffcanvas(offcanvas, height) {

            switch (offcanvas) {
                case 'a':
                    $('#offcanvasAddBudget').animate({
                        height: height + 'vh'
                    }, 200);
                    break;
                case 'b':
                    $('#offcanvasDetailBudget').animate({
                        height: height + 'vh'
                    }, 200);
                    break;
                case 'c':
                    $('#offcanvasAddBalance').animate({
                        height: height + 'vh'
                    }, 200);
                    break;

                default:
                    break;
            }

        }

        $('#offcanvasTransferBalance').on('hidden.bs.offcanvas', event => {
            $('#selectedBudgetDestinationName').html('Pilih Kantong');
            $('#selectedBudgetDestinationUuid').val('');
            $('#selectedBudgetDestinationIcon').attr('src', "<?= base_url('public/assets/img/emojis/money-bag.png') ?>")

            activeCanvas = '';
        })

        $('#offcanvasAddBalance').on('shown.bs.offcanvas', event => {
            activeCanvas = 'a';
        })

        $('#offcanvasTransferBalance').on('shown.bs.offcanvas', event => {
            activeCanvas = 'b';
        })

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

        function showBudgetDetail(id) {
            const offcanvasTop = new bootstrap.Offcanvas('#offcanvasDetailBudget').show();

            activeBudget = id;
        }

        $('#searchTransaction').on('focus', () => {
            $('#header-content').addClass('d-none');
            $('#btnCancelSearch').removeClass('d-none')
        })

        $('#searchTransaction').on('blur', () => {
            $('#header-content').removeClass('d-none');
            $('#btnCancelSearch').addClass('d-none')
        })

        function addBudgetBalance() {
            Notiflix.Loading.dots();
            const balance = numeral($('#inputAddBalance').val()).value();
            const note = $('#inputAddBalanceNote').val();

            if (balance < 1) {
                bootstrap.Offcanvas.getInstance('#offcanvasAddBalance').hide();
            } else {
                $.post('<?= base_url('user/budgets/add-balance') ?>', {
                        budgetUuid: activeBudget,
                        balance: balance,
                        note: note
                    })
                    .done((data) => {
                        Notiflix.Loading.remove();
                        console.log(data);
                        data = JSON.parse(data);
                        if (data.hasOwnProperty('response')) {
                            switch (data.response) {
                                case 'FAILED':
                                    Notiflix.Notify.failure('GAGAL');
                                    break;
                                case 'SUCCESS':
                                    Notiflix.Notify.success('BERHASIL');
                                    $('#budgetBalance').html(data.balance);
                                    bootstrap.Offcanvas.getInstance('#offcanvasAddBalance').hide();
                                    $('#inputAddBalance').val('');
                                    $('#inputAddBalanceNote').val('');
                                    break;
                                default:
                                    break;
                            }
                        }
                    })
                    .fail(() => {
                        Notiflix.Loading.remove();
                        Notiflix.Report.failure('Koneksi Internet Terputus', 'Periksa koneksi internet anda dan coba lagi', 'Oke')
                    })
            }

        }

        function getTransactionList() {
            Notiflix.Block.circle('#transactionListContainer', {
                backgroundColor: 'transparent'
            });
            $.get('<?= base_url('user/budgets/transaction-list/') . $budgetDetail['uuid'] ?>', (data) => {
                Notiflix.Block.remove('#transactionListContainer');
                $('#transactionListContainer').html(data);
            })
        }

        if (window.location.hash) {
            // Anchor fragment exists in the URL
            var anchor = window.location.hash.substring(1); // Remove the '#' symbol

            // Do something with the anchor fragment
            switch (anchor) {
                case 'addBalance':
                    const offcanvasBottom = new bootstrap.Offcanvas('#offcanvasAddBalance').show();

                    var url = window.location.href;
                    var newUrl = url.split('#')[0];
                    history.replaceState(null, '', newUrl);
                    break;
                case 'transferBalance':
                    const offcanvasBottom2 = new bootstrap.Offcanvas('#offcanvasTransferBalance').show();

                    var url = window.location.href;
                    var newUrl = url.split('#')[0];
                    history.replaceState(null, '', newUrl);
                    break;

                default:
                    break;
            }
        }

        function selectBudget(name, uuid, icon) {
            $('#selectDestinationBudget').modal('hide');
            $('#selectedBudgetDestinationName').html(name);
            $('#selectedBudgetDestinationUuid').val(uuid);
            $('#selectedBudgetDestinationIcon').attr('src', "<?= base_url('public/assets/img/emojis/') ?>" + icon)
        }

        const hotfixModal = document.getElementById('selectDestinationBudget')
        hotfixModal.addEventListener('hidden.bs.modal', event => {
            modalSelector = false;
        })
        hotfixModal.addEventListener('show.bs.modal', event => {
            modalSelector = true;
        })

        $('#inputTransferAmount').on('focus', () => {
            if (modalSelector == true) {
                $('#inputTransferAmount').blur();
            }
        })

        function transferBalance() {
            const budgetId = $('#selectedBudgetDestinationUuid').val();
            const amount = numeral($('#inputTransferAmount').val()).value();
            const note = $('#inputTransferAmountNote').val();
            let balance = numeral($('#budgetBalance').html().substring(3)).value();

            if (balance < amount) {
                Notiflix.Report.failure('Saldo Tidak Cukup', 'Saldo anda tidak mencukupi untuk melakukan transaksi ini!', 'Oke');
            } else {
                $.post('<?= base_url('user/budgets/transfer-balance') ?>', {
                        budgetId: budgetId,
                        amount: amount,
                        note: note,
                        sourceBudgetId: activeBudget
                    })
                    .done((data) => {
                        data = JSON.parse(data);
                        if (data.hasOwnProperty('response')) {
                            switch (data.response) {
                                case 'SUCCESS':
                                    Notiflix.Report.success('Transfer Berhasil',
                                        '<body><table><tr><td class="col-5" style="vertical-align: top;">Kantong Tujuan</td><td class="col-1" style="vertical-align: top;">:</td><td class="col-6" style="vertical-align: top;">' + $('#selectedBudgetDestinationName').html() + '</td></tr><tr><td class="col-5" style="vertical-align: top;">Nominal Transfer</td><td class="col-1" style="vertical-align: top;">:</td><td class="col-6" style="vertical-align: top;">Rp ' + $('#inputTransferAmount').val() + '</td></tr><tr><td class="col-5" style="vertical-align: top;">Keterangan</td><td class="col-1" style="vertical-align: top;">:</td><td class="col-6" style="vertical-align: top;">' + $('#inputTransferAmountNote').val() + '</td></tr></table>', 'Oke');
                                    bootstrap.Offcanvas.getInstance('#offcanvasTransferBalance').hide();
                                    getTransactionList();
                                    $('#budgetBalance').html(data.balance);
                                    break;
                                case 'FAILED':
                                    Notiflix.Report.failure('Terjadi Kesalahan', 'Silahkan coba lagi!', 'Oke');
                                    break;
                                case 'BALANCE_LIMIT':
                                    Notiflix.Report.failure('Saldo Tidak Cukup', 'Saldo anda tidak mencukupi untuk melakukan transaksi ini!', 'Oke');
                                    break;

                                default:
                                    break;
                            }
                        }

                    })
                    .fail(() => {
                        Notiflix.Loading.remove();
                        Notiflix.Report.failure('Koneksi Internet Terputus', 'Periksa koneksi internet anda dan coba lagi', 'Oke');
                    })

            }


        }
    </script>
</body>

</html>