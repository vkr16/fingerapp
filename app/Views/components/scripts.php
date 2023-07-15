<script src="<?= base_url('public/assets/vendor/bootstrap-5.3.0/dist/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('public/assets/vendor/jquery-3.7.0/jquery.min.js') ?>"></script>
<script src="<?= base_url('public/assets/vendor/notiflix-3.2.6/notiflix-aio-3.2.6.min.js') ?>"></script>
<script src="<?= base_url('public/assets/vendor/select2-4.0.13/dist/js/select2.min.js') ?>"></script>
<script src="<?= base_url('public/assets/vendor/numeral-js/numeral.min.js') ?>"></script>

<script>
    Notiflix.Loading.init({
        backgroundColor: 'rgba(255,255,255,0.6)',
        svgColor: '#045498',
        clickToClose: false
    });
    Notiflix.Report.init({
        plainText: false,
        borderRadius: '0.25rem',
        messageMaxLength: '2000'
    });
    Notiflix.Confirm.init({
        plainText: false,
        borderRadius: '0.25rem'
    });
    Notiflix.Notify.init({
        width: '90%',
        position: 'center-top',
        cssAnimationStyle: 'from-top',
        showOnlyTheLastOne: true,
        plainText: false,
        clickToClose: true
    });

    numeral.register('locale', 'id', {
        delimiters: {
            thousands: '.',
            decimal: ','
        },
        abbreviations: {
            thousand: 'rb',
            million: 'jt',
            billion: 'M',
            trillion: 'T'
        },
        ordinal: function(number) {
            return number === 1 ? '' : '';
        },
        currency: {
            symbol: 'Rp '
        }
    });
    numeral.locale('id');
</script>