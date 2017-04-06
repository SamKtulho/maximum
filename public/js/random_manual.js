
$( document ).ready(function() {
    $('.show-button').click(function () {
        $('.random-manual .mail-settings').toggleClass('hide');
    });

    $('.btn-main').click(function () {
        $('.main-button').prop('disabled', true);
        getRandomManualData({'name': 'domainOnly', 'value': true});
    });

    $('.btn-not-found').click(function () {
        $('#preresult .action-buttons').addClass('hide');
        var data = $( "form" ).serializeArray();
        data.push({'name': 'id', 'value': $('#preresult #domain_id').val()});
        $.post( "/manual/notFound", data, function( data ) {
            $('.main-button').prop('disabled', false);

            if (data.error !== undefined) {
                $ ('.flash-message').addClass('alert alert-danger');
                $( ".flash-message p" ).html( data.error +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
            } else {
                $ ('.flash-message').addClass('alert alert-success');
                $( ".flash-message p" ).html( data.response +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
            }
        });
    });

    $('.btn-text-gen').click(function () {
        $('#preresult .action-buttons').addClass('hide');
        var data = $( "form" ).serializeArray();
        data.push({'name': 'id', 'value': $('#preresult #domain_id').val()});
        $.post( "/random/manualStore", data, function( data ) {
            $('.main-button').prop('disabled', false);

            if (data.error !== undefined) {
                $ ('.flash-message').addClass('alert alert-danger');
                $( ".flash-message p" ).html( data.error +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
            } else {
                $ ('.flash-message').removeClass('alert alert-danger');
                $( ".flash-message p" ).html('');
                var clear = !data.response.fio;
                $( "#result #fio" ).html( clear ? '' : data.response.fio );
                $( "#result #domain" ).html( clear ? '' : data.response.domain );
                $( "#result #email" ).html( clear ? '' : data.response.email );
                $( "#result #link" ).html( clear ? '' : 'http://wapmaximum.ru' );
                $( "#result h4" ).html( clear ? '' : data.response.title );
                $( "#result #body" ).html( clear ? '' : data.response.text );
            }
        });
    });

    function getRandomManualData(addata) {
        var data = $( "form" ).serializeArray();
        if (addata) {
            data.push(addata);
        }
        $.post( "/random/manualDomain", data, function( data ) {
            $('.main-button').prop('disabled', false);

            if (data.error !== undefined) {
                $ ('.flash-message').addClass('alert alert-danger');
                $( ".flash-message p" ).html( data.error +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
            } else {
                $('.flash-message').removeClass('alert alert-danger');
                $(".flash-message p" ).html('');

                if (data.response.domain != undefined) {
                    $('#preresult .action-buttons').removeClass('hide');
                    $('#preresult #domain_id').val(data.response.id);
                    $("#preresult #domain_link" ).html( '<a target="_blank" href="//' + data.response.domain + '">' + data.response.domain + '</a>' );
                } else {
                    $("#preresult #domain_link" ).html('');

                }

                $( "#result #fio" ).html('');
                $( "#result #domain" ).html('');
                $( "#result #email" ).html('');
                $( "#result #link" ).html('');
                $( "#result h4" ).html('');
                $( "#result #body" ).html('');
            }
        });
    }

    $('.save-button').click(function () {
        var data = $( "form" ).serializeArray();
        data.push({'name':'saveTemplate', 'value':true});
        $.post( "/random/manualDomain", data, function( data ) {
            if (data.error !== undefined) {
                $ ('.flash-message').addClass('alert alert-danger');
                $( ".flash-message p" ).html( data.error +  '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>');
            } else {
                $("#tooltip").attr('title', data.response).tooltip('show');
                setTimeout(function(){$("#tooltip").tooltip('hide')}, 1000);

            }
        });
    });

    $('.back-button').click(function () {
        var data = $( "form" ).serializeArray();
        data.push({'name':'domain', 'value':$('#domain').html()});
        $.post( "/domain/back", data, function( data ) {
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
        $.get( "/manual/count", function( data ) {
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