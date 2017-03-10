
$( document ).ready(function() {

    if ($('.random-email .btn-main').length > 0) {
        $('.btn-main').click(function () {
            $('.main-button').prop('disabled', true);

            var data = $( "form" ).serializeArray();
            $.post( "/random/email/store", data, function( data ) {
                $('.main-button').prop('disabled', false);

                if (data.error !== undefined) {
                    $ ('.flash-message').addClass('alert alert-danger');
                    $( ".flash-message p" ).html( data.error +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
                } else {
                    $ ('.flash-message').removeClass('alert alert-danger');
                    $( ".flash-message p" ).html('');
                    $( "#result #email" ).html( data.response[3] );
                    $( "#result h4" ).html( data.response[1] );
                    $( "#result #body" ).html( data.response[0] );
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
    }

    if ($('.random-link .btn-main').length > 0) {
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
                    $( "#result #fio" ).html( data.response[0] );
                    $( "#result #email" ).html( data.response[1] );
                    $( "#result h4" ).html( data.response[3] );
                    $( "#result #body" ).html( data.response[2] );
                    $( "#result #link" ).html( '<a target="_blank" href="' + data.response[4] + '">' + data.response[4] + '</a>' );
                    // $('#counter').html('Осталось ' + data.response[4] + ' необработанных домена с текущими настройками.');
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
    }

    if ($('.email-statistic').length > 0) {
        $.fn.dataTable.Api.register( 'sum()', function ( ) {
            return this.flatten().reduce( function ( a, b ) {
                if ( typeof a === 'string' ) {
                    a = a.replace(/[^\d.-]/g, '') * 1;
                }
                if ( typeof b === 'string' ) {
                    b = b.replace(/[^\d.-]/g, '') * 1;
                }

                return a + b;
            }, 0 );
        } );
        $('#example').DataTable( {
            'iDisplayLength': 100,
            "order": [[ 5, "desc" ]],
            drawCallback: function () {
                var api = this.api();
                $( api.table().footer() ).html(
                    '<tr><td>Итого</td><td>' + api.column( 1, {page:'current'} ).data().sum() + '</td><<td></td><td></td><td></td><td></td></tr>'
                );
            }
        } );
    }

    if ($('.link-statistic').length > 0) {
        $.fn.dataTable.Api.register( 'sum()', function ( ) {
            return this.flatten().reduce( function ( a, b ) {
                if ( typeof a === 'string' ) {
                    a = a.replace(/[^\d.-]/g, '') * 1;
                }
                if ( typeof b === 'string' ) {
                    b = b.replace(/[^\d.-]/g, '') * 1;
                }

                return a + b;
            }, 0 );
        } );
        $('#example').DataTable( {
            'iDisplayLength': 100,
            "order": [[ 5, "desc" ]],
            drawCallback: function () {
                var api = this.api();
                $( api.table().footer() ).html(
                    '<tr><td>Итого</td><td>' + api.column( 1, {page:'current'} ).data().sum() + '</td><<td></td><td></td><td></td></tr>'
                );
            }
        } );
    }
});