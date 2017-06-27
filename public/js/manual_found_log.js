
$( document ).ready(function() {

    $('#example').DataTable( {
        'iDisplayLength': 100,
        "order": [[ 3, "desc" ]],
        serverSide: true,
        processing: true,
        ajax: {
            "url": "/manual/found_log/data",
            "type": "POST"
        },
        columns: [
            { data: 'user.name', width: '10%' },
            { data: 'domain.domain', width: '35%', render: function(d) {
                return '<a value="'+d+'" target="_blank" href="//' + d + '">' + d + '</a>';
            }},
            { data: 'action', width: '35%', render: function(d) {
                var buttonClass = d == 1 ? 'info' : (d == 2 ? 'success' : (d == 3 ? 'danger' : 'warning'));
                var text = d == 1 ? 'email' : (d == 2 ? 'форма' : (d == 3 ? 'не найдено' : 'плохой сайт'));

                return '<button class="btn btn-' +  buttonClass + ' btn-xs main" value="1" type="button">'+ text +'</button>';
            }},
            { data: 'created_at', width: '20%' }
        ],
        drawCallback: function () {

        }
    } );


});