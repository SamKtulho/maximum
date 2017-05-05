
$( document ).ready(function() {
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
        "order": [[ 3, "desc" ]],
        serverSide: true,
        processing: true,
        ajax: {
            "url": "/email/moderation_log/data",
            "type": "POST"
        },
        columns: [
            { data: 'user.name', width: '10%' },
            { data: 'domain.domain', width: '35%', render: function(d) {
                return '<a target="_blank" href="//' + d + '">' + d + '</a>';
            }},
            { data: 'result', width: '35%', render: function(d) {
                return d == 1
                    ? '<button class="btn btn-success btn-xs main" value="1" type="button">Да</button> <button value="2" title="Изменить на НЕТ" type="button" class="btn btn-default btn-xs change_vote"> <span class="glyphicon glyphicon-retweet" aria-hidden="true"></span></button>'
                    : '<button class="btn btn-danger btn-xs main" value="1" type="button">Нет</button> <button value="1" title="Изменить на ДА" type="button" class="btn btn-default btn-xs change_vote"> <span class="glyphicon glyphicon-retweet" aria-hidden="true"></span></button>';
            }},
            { data: 'created_at', width: '20%' }
        ],
        drawCallback: function () {
            $('.change_vote').click(function (e, a) {
                var button = this;
                var domain = $($($(this).closest('tr').children()[1]).find('a')).attr('value');
                var vote = $(this).val();
                var data = [{'name': 'vote', 'value': vote}, {'name': 'domain', 'value': domain}];

                $(button).prop('disabled', true);
                $.post( "/moderator/change_vote_link", data, function( data ) {

                    $(button).prop('disabled', false);

                    if (data.error !== undefined) {

                    } else {
                        $($(button).closest('td').find('button.main')).addClass(vote == 1 ? 'btn-success' : 'btn-danger').removeClass(vote == 1 ? 'btn-danger' : 'btn-success').html(vote == 1 ? 'Да' : 'Heт');
                        $(button).attr('value', vote == 1 ? 2 : 1);
                    }
                });
            });
        }
    } );

  
});