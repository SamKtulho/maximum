
$( document ).ready(function() {
    $('.show-button').click(function () {
        $('.random-link .mail-settings').toggleClass('hide');
    });

    $('.btn-main').click(function () {
        $('.main-button').prop('disabled', true);

        var data = $( "form" ).serializeArray();
        $.post( "/random/link/store", data, function( data ) {
            $('.main-button').prop('disabled', false);

            if (data.error !== undefined) {
                $ ('.flash-message').addClass('alert alert-danger');
                $( ".flash-message p" ).html( data.error +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
            } else {
                $ ('.flash-message').removeClass('alert alert-danger');
                $( ".flash-message p" ).html('');
                var clear = !data.response[0];
                $( "#result #fio" ).html( clear ? '' : data.response[0] );
                $( "#result #domain" ).html( clear ? '' : data.response[5] );
                $( "#result #email" ).html( clear ? '' : data.response[1] );
                $( "#result h4" ).html( clear ? '' : data.response[3] );
                $( "#result #body" ).html( clear ? '' : data.response[2] );
                $( "#result #link" ).html( clear ? '' : '<a target="_blank" href="' + data.response[4] + '">' + data.response[4] + '</a>' );
                // $('#counter').html('Осталось ' + data.response[4] + ' необработанных домена с текущими настройками.');
            }
        });
    });

    $('.save-button').click(function () {
        var data = $( "form" ).serializeArray();
        data.push({'name':'saveTemplate', 'value':true});
        $.post( "/random/link/store", data, function( data ) {
            if (data.error !== undefined) {
                $ ('.flash-message').addClass('alert alert-danger');
                $( ".flash-message p" ).html( data.error +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
            } else {
                $("#tooltip").attr('title', data.response).tooltip('show');
                setTimeout(function(){$("#tooltip").tooltip('hide')}, 1000);

            }
        });
    });


    function getTotalLinks() {
        $.get( "/link/count", function( data ) {
            if (data.response !== undefined) {
                $.each(data.response, function(key, value) {
                    $( '#' + key + '_count' ).html( '(' + value + ')' );

                });

            }
        });
    }
    getTotalLinks();
    setInterval(getTotalLinks, 60000);
});