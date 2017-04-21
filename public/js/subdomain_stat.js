
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
        "order": [[ 4, "desc" ]],
        serverSide: true,
        processing: true,
        ajax: {
            "url": "/subdomain/statistic/data",
            "type": "POST"
        },
        columns: [
            { data: 'url', width: '15%' },
            { data: 'stat', width: '7%' },
            { data: 'domain.domain', width: '35%' , render: function(d) {
                return '<a target="_blank" href="//' + d + '">' + d + '</a>';
            }},
            { data: 'user.name', width: '10%' },
            { data: 'created_at', width: '15%' }
        ],
        drawCallback: function () {
            var api = this.api();
            $( api.table().footer() ).html(
                '<tr><td>Итого</td><td>' + api.column( 1, {page:'current'} ).data().sum() + '</td><<td></td><td></td><td></td></tr>'
            );
        }
    } );
    
});