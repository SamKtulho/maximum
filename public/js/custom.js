
$( document ).ready(function() {

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
                {data: 'stat', width: '6%', searchable: false, orderable: false},
                {data: 'domain.domain', width: '27%', render: function(d) {
                    return '<a target="_blank" href="//' + d + '">' + d + '</a>';
                }},
                {data: 'email', width: '27%', searchable: false, orderable: false},
                {data: 'user.name', width: '9%'},
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
            serverSide: true,
            processing: true,
            ajax: {
                "url": "/link/statistic/data",
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
    }

    if ($('.moderator').length > 0) {
        var voteAction = function () {
            $.ajax({
                url: '/moderator/vote',
                type: 'POST',
                data: {'domain_id': $('#domain_id').val(), 'vote': $('#vote').val(), '_token': $('input[name=_token]').val()},
                dataType: 'JSON',
                success: function (data) {
                    if (data.response !== undefined) {
                        var domain = data.response.domain;
                        var count = data.response.count;
                        $('.counter').html(count);
                        if (domain) {
                            $('#domain_id').val(domain.id);
                            $('iframe').attr("src", '//' + domain.domain);
                            $('#link').attr("href", '//' + domain.domain);
                            $('#link').html(domain.domain);
                            $('.registrar').html('(' + domain.registrar + ') - ' +  domain.source);
                        }
                    }
                }
            });
        };

        voteAction();
        
        $('.moderator .btn-lg').click(function(){
            $('#vote').val($(this).val());
            setTimeout(voteAction, 400);
        });
    }

    if ($('.moderator_email').length > 0) {
        var voteAction = function () {
            $.ajax({
                url: '/moderator/vote_email',
                type: 'POST',
                data: {'domain_id': $('#domain_id').val(), 'vote': $('#vote').val(), '_token': $('input[name=_token]').val()},
                dataType: 'JSON',
                success: function (data) {
                    if (data.response !== undefined) {
                        var domain = data.response.domain;
                        var count = data.response.count;
                        $('.counter').html(count);
                        if (domain) {
                            $('#domain_id').val(domain.id);
                            $('iframe').attr("src", '//' + domain.domain);
                            $('#link').attr("href", '//' + domain.domain);
                            $('#link').html(domain.domain);
                            $('.email').html('(' + domain.email + ') - ' + domain.source);
                        } else {
                            $('#link').attr("href", '#');
                            $('#link').html('');
                            $('.email').html('');
                            $('iframe').attr("src", '');
                        }

                    }
                }
            });
        };

        voteAction();

        $('.moderator_email .btn-lg').click(function(){
            $('#vote').val($(this).val());
            setTimeout(voteAction, 400);
        });
    }

    if ($('.moderator_subdomain').length > 0) {
        var voteAction = function () {
            $.ajax({
                url: '/moderator/vote_subdomain',
                type: 'POST',
                data: {'domain_id': $('#domain_id').val(), 'vote': $('#vote').val(), '_token': $('input[name=_token]').val()},
                dataType: 'JSON',
                success: function (data) {
                    if (data.response !== undefined) {
                        var domain = data.response.domain;
                        var count = data.response.count;
                        $('#domain_id').val(domain.id);
                        $('iframe').attr("src", '//' + domain.domain);
                        $('#link').attr("href", '//' + domain.domain);
                        $('#link').html(domain.domain);
                        $('.counter').html(count);
                        $('.registrar').html('(' + domain.source + ')');
                    }
                }
            });
        };

        voteAction();

        $('.moderator_subdomain .btn-lg').click(function(){
            $('#vote').val($(this).val());
            setTimeout(voteAction, 400);
        });
    }
});