
$( document ).ready(function() {

    if ($('.random-email .btn-main').length > 0) {
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


        $('#example').DataTable({
            'iDisplayLength': 100,
            "order": [[ 5, "desc" ]],
            serverSide: true,
            processing: true,
            ajax: {
                "url": "/email/statistic/data",
                "type": "POST"
            },
            columns: [
                {data: 'url', width: '15%'},
                {data: 'stat', width: '6%'},
                {data: 'domain', width: '27%'},
                {data: 'email', width: '27%'},
                {data: 'user', width: '9%'},
                {data: 'created_at', width: '13%'}
            ],
            drawCallback: function () {
                var api = this.api();
                $( api.table().footer() ).html(
                    '<tr><td>Итого</td><td>' + api.column( 1, {page:'current'} ).data().sum() + '</td><<td></td><td></td><td></td><td></td></tr>'
                );
            }
        });
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
            "order": [[ 4, "desc" ]],
            columns: [
                { width: '15%' },
                { width: '7%' },
                { width: '35%' },
                { width: '10%' },
                { width: '15%' }
            ],
            drawCallback: function () {
                var api = this.api();
                $( api.table().footer() ).html(
                    '<tr><td>Итого</td><td>' + api.column( 1, {page:'current'} ).data().sum() + '</td><<td></td><td></td><td></td></tr>'
                );
            }
        } );
    }
});