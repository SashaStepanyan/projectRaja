$(document).ready(function () {
    $('#persons').find('tr').each(function (index, val) {

        var at = $(val).find('td').last().attr('colspan');
        if (at != 10) {
            $(val).find('td').last().attr('class', 'column-action');
        }
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });
    $('#persons-columns_hider-menu-content ').find(' input[type=checkbox]').last().attr('name', 'action');

    $('#persons').find('tr >  th').last().attr('class', 'column-action');

    $('#persons-columns_hider-btn').click(function () {
        $('.popover-content ul').find(' input[type=checkbox]').last().attr('name', 'action').attr('checked', 'checked');
        $('.popover-content ul').find(' input[type=checkbox]').last().prop("checked", true);

    });

    $('#entity').find('tr').each(function (index, val) {

        var at = $(val).find('td').last().attr('colspan');
        if (typeof at == typeof undefined) {
            $(val).find('td').last().attr('class', 'column-action');
        }
    });

    $('#entity-columns_hider-menu-content ').find(' input[type=checkbox]').last().attr('name', 'action').attr('checked', 'checked');

    $('#entity').find('tr >  th').last().attr('class', 'column-action');

    $('.ids').click(function () {
        var id;
        var n =$(this).attr('data-name');
        var ch = this.checked;
        id = $(this).attr('value');
        $.ajax({
            url: '/admin/person/set',
            type: 'post',
            data: {id: {id: id, ch: ch, n: n}},
            success: function () {

            }
        })

    });

    $('#all').click(function () {
        var id=[];
        var ch = this.checked;
        var n =$(this).attr('data-name');
        $('.ids').each(function (i, v) {
            id.push($(v).attr('value'))
        });
        $.ajax({
            url: '/admin/person/set',
            type: 'post',
            data: {all: ch, id: {id: id, ch: ch, n: n}},
            success: function () {

            }
        })
    });

    $('.idsent').click(function () {
        var id;
        var n =$(this).attr('data-name');
        var ch = this.checked;
        id = $(this).attr('value');
        $.ajax({
            url: '/admin/person/set',
            type: 'post',
            data: {id: {id: id, ch: ch, n: n}},
            success: function () {

            }
        })

    });

    $('#allent').click(function () {
        var id=[];
        var ch = this.checked;
        var n =$(this).attr('data-name');
        $('.idsent').each(function (i, v) {
            id.push($(v).attr('value'))
        });
        $.ajax({
            url: '/admin/person/set',
            type: 'post',
            data: {all: ch, id: {id: id, ch: ch, n: n}},
            success: function () {

            }
        })
    });
    
});