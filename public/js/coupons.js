
$( document ).ready(function() {

    getCoupon();

    function getCoupon() {
        clearData();
        $.get( "/heyyy/getCoupon", function( data ) {

            var couponType = data.promo_code !== '' ? 'Online Code' : 'Online Sale';
            $('.name').html(data.name);
            $('.coupon-id').val(data.id);
            $('.description').html(data.description);
            $('.image img').attr('src', data.image);
            $('.active_to').html(data.active_to);
            $('.coupon_type').html(couponType);
            $('.promo_code').html(data.promo_code);
            $('.url a').attr('href', data.url);
            $('.url a').html(data.url);
            $('.offer_name').html(data.offer_name);

        });
    }

    function clearData() {
        $('.name').html('');
        $('.coupon-id').val('');
        $('.description').html('');
        $('.image img').attr('src', '');
        $('.active_to').html('');
        $('.coupon_type').html('');
        $('.promo_code').html('');
        $('.url a').attr('href', '');
        $('.url a').html('');
        $('.offer_name').html('');
    }

    $('.done-button').click(function () {
        $('.done-button').prop('disabled', true);

        var data = $( "form" ).serializeArray();

        $.post( "/heyyy/feed", data, function( data ) {
            if (data.error !== undefined) {

            } else {
                getCoupon();
                $('.done-button').prop('disabled', false);
            }
        });
    });
});