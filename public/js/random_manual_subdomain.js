
$( document ).ready(function() {
    $('.show-button').click(function () {
        $('.random-manual-subdomain .mail-settings').toggleClass('hide');
    });

    $('.btn-main').click(function () {
        $('.main-button').prop('disabled', true);
        getRandomManualData({'name': 'domainOnly', 'value': true});
    });

    function getRandomManualData(addata) {
        var data = $( "form" ).serializeArray();
        if (addata) {
            data.push(addata);
        }
        $.post( "/random/manualSubdomain/store", data, function( data ) {
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
                var clear = !data.response.fio;
                $( "#result #fio" ).html( clear ? '' : data.response.fio );
                $( "#result #domain" ).html( clear ? '' : data.response.domain );
                $( "#result #email" ).html( clear ? '' : data.response.email );
                $( "#result h4" ).html( clear ? '' : data.response.title );
                $( "#result #body" ).html( clear ? '' : data.response.text );
            }
        });
    }

    $('.save-button').click(function () {
        var data = $( "form" ).serializeArray();
        data.push({'name':'saveTemplate', 'value':true});
        $.post( "/random/manualSubdomain/store", data, function( data ) {
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
        $.get( "/manualSubdomain/count", function( data ) {
            if (data.response !== undefined) {
                $( '#count' ).html( '(' + data.response + ')' );
            }
        });
    }
    getTotalLinks();
    setInterval(getTotalLinks, 60000);
});