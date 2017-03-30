
$( document ).ready(function() {

    $('.show-button').click(function () {
        $('.random-email .mail-settings').toggleClass('hide');
    });

    $('.btn-main').click(function () {
        $('.main-button').prop('disabled', true);

        var data = $( "form" ).serializeArray();
        $.post( "/random/email/store", data, function( data ) {
            $('.main-button').prop('disabled', false);

            if (data.error !== undefined) {
                $ ('.flash-message').addClass('alert alert-danger');
                $( ".flash-message p" ).html( data.error +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
            } else {
                var clear = !data.response[0];
                $ ('.flash-message').removeClass('alert alert-danger');
                $( ".flash-message p" ).html('');
                $( "#result #email" ).html( clear ? '' : data.response[3] );
                $( "#result h4" ).html( clear ? '' : data.response[1] );
                $( "#result #body" ).html( clear ? '' : data.response[0] );
            }
        });
    });

    $('.save-button').click(function () {
        var data = $( "form" ).serializeArray();
        data.push({'name':'saveTemplate', 'value':true});
        $.post( "/random/email/store", data, function( data ) {
            if (data.error !== undefined) {
                $ ('.flash-message').addClass('alert alert-danger');
                $( ".flash-message p" ).html( data.error +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
            } else {
                $("#tooltip").attr('title', data.response).tooltip('show');
                setTimeout(function(){$("#tooltip").tooltip('hide')}, 1000);
            }
        });
    });

    function getTotalEmails() {
        $.get( "/email/count", function( data ) {
            if (data.response !== undefined) {
                $.each(data.response, function(key, value) {
                    $( '#' + key + '_count' ).html( '(' + value + ')' );

                });

            }
        });
    }
    getTotalEmails();
    setInterval(getTotalEmails, 60000);

});