
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
        "order": [[ 1, "desc" ]],
        serverSide: true,
        processing: true,
        ajax: {
            "url": "/link/moderation_log/data",
            "type": "POST"
        },
        columns: [
            { data: 'user.name', width: '10%' },
            { data: 'domain.domain', width: '35%', render: function(d) {
                return '<a target="_blank" href="//' + d + '">' + d + '</a>';
            }},
            { data: 'result', width: '35%', render: function(d) {
                return d == 1
                    ? '<button class="padd25 btn btn-success btn-xs" value="1" type="button">Да</button>'
                    : '<button class="padd25 btn btn-danger btn-xs" value="1" type="button">Нет</button>';
            }},
            { data: 'created_at', width: '20%' }
        ],
        drawCallback: function () {

        }
    } );
});