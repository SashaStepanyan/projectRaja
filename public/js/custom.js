$(document).ready(function ($) {
    var show = function(){
        $(".page-loader").css('display','table');
        setTimeout(hide, 1000);  // 5 seconds
    }

    var hide = function(){
        $(".page-loader").css('display','none');
    }
    $('#createPersonModal').on('hidden.bs.modal', function () {
        if ($(this).find('form').length > 0) {
            $(this).find('form')[0].reset();
        }
        $('.hasError').empty();
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });
    $('#addNewPerson').click(function () {
        show();

        $('#createPersonModal').on('shown.bs.modal', function () {
            $(".selectpicker").selectpicker();

        });

        $.ajax({
            url: "/admin/person/create",
            method: 'GET',
            dataType: "html",
            success: function (data) {
                $('#myModalLabel').text('Add Person Information');
                $('.modal-body').html(data);
                $('#createPersonModal').modal('show');
                $('#create_new_person').submit(function () {
                    $.ajax({
                        url: "/admin/person/store",
                        method: 'POST',
                        dataType: "json",
                        data: $(this).serialize(),
                        success: function (data) {
                            if (data.status == 'success') {
                                show();
                                window.location.href = document.URL;
                            } else {
                                show();
                                var li = '';
                                $.each(data.errors, function (index, value) {
                                    li += '<li>' + value + '</li>'
                                });


                                var htmlMessage = '<div class="alert alert-danger"><ul>' + li + '</ul></div>';
                                $(".hasError").html(htmlMessage);
                            }


                        }
                    });
                    return false;
                });
            }
        });

    });
    $('.peditPerson').click(function () {
        show();
        $('#createPersonModal').on('shown.bs.modal', function () {
            $(".selectpicker").selectpicker();

        });
        var id = $(this).val();
        $.ajax({
            url: "/admin/person/" + id + "/edit",
            method: 'GET',
            dataType: "html",
            success: function (data) {
                $('#myModalLabel').text('Edit Person Information');
                $('.modal-body').html(data);
                $('#createPersonModal').modal('show');
                $('#update_person').submit(function () {
                    $.ajax({
                        url: "/admin/person/update/" + id,
                        method: 'POST',
                        dataType: "json",
                        data: $(this).serialize(),
                        success: function (data) {

                            if (data.status == 'success') {
                                show();
                                window.location.href = document.URL;
                            } else {
                                show();
                                var li = '';
                                $.each(data.errors, function (index, value) {
                                    li += '<li>' + value + '</li>'
                                });


                                var htmlMessage = '<div class="alert alert-danger"><ul>' + li + '</ul></div>';
                                $(".hasError").html(htmlMessage);
                            }


                        }
                    });
                    return false;
                });
            }
        });
        return false;
    });


    $('.pviewPerson').click(function () {
        var id = $(this).val();
        $.ajax({
            url: "/admin/person/view",
            method: 'GET',
            data: {id: id},
            dataType: "html",
            success: function (data) {
                $('#myModalLabel').text('Person information');
                $('.modal-body').html(data);
                $('#createPersonModal').modal('show');
            }
        });
        return false;
    });
    $('.viewEntity').click(function () {
        var id = $(this).val();
       
        $.ajax({
            url: "/admin/entity/view",
            method: 'GET',
            data: {id: id},
            dataType: "html",
            success: function (data) {
                $('#myModalLabel').text('Entity information');
                $('.modal-body').html(data);
                $('#createEntityModal').modal('show');
            }
        });
        return false;
    });

    $('body').on('click', '.pchangeStatus', function () {
        var id = $(this).val();
        $.ajax({
            url: "/admin/person/changeStatus/" + id,
            method: 'POST',
            success: function (data) {
                $('#' + id).text(data.status);
            }
        });
        return false;
    });

    $('body').on('click', '.echangeStatus', function () {
        var id = $(this).val();
        $.ajax({
            url: "/admin/entity/changeStatus/" + id,
            method: 'POST',
            success: function (data) {
                $('#' + id).text(data.status);
            }
        });
        return false;
    });

    $("#reset").click(function () {
        $("#persons").find('input').val("");
        $.ajax({
            url:'/apmin/person/forget',
            method: 'POST',
            data: {session: 'per'},
            success:function () {
                window.location='/admin/person';
            }
        })
    });

    $("#resetent").click(function () {
        $("#entity").find('input').val("");
        $.ajax({
            url:'/apmin/person/forget',
            method: 'POST',
            data: {session: 'ent'},
            success:function () {
                window.location='/admin/entity';
            }
        })
    });
    
    var menu = $('#persons-columns_hider-menu-content').html();
    var $btn = $('#persons-columns_hider-btn');
    $btn.popover({
        content: menu,
        html: true
    });

    // hides the popover when clicking outside
    $(document).on('click', function (e) {
        if (
            !$btn.is(e.target)
            && $btn.has(e.target).length === 0
            && $('#persons-columns_hider-container>.popover').has(e.target).length === 0
        ) {
            $btn.popover('hide');
        }
    });

    $("#persons").find('td.column-id > input.ids').attr('name', 'persons[filters][id-like][]');
    $("#persons").find('thead tr td.column-id:first').html('<lable for="all"><input class="form-control" data-name="per" name="persons[filters][id-like][]" value="all" id="all" type="checkbox"></lable>');
    $("#persons").find('thead td.column-id > input').attr('name', 'persons[filters][id-like][]');

    $("#entity").find('td.column-id > input.ids').attr('name', 'entity[filters][id-like][]');
    $("#entity").find('thead tr td.column-id:first').html('<input class="form-control" data-name="per" name="persons[filters][id-like][]" value="all" id="allent" type="checkbox" ><lable for="allent"></lable>');
    $("#entity").find('thead td.column-id > input').attr('name', 'entity[filters][id-like][]');

    
    $('#all').click(function () {
        if (this.checked) {
            // Iterate each checkbox
            $(':checkbox').each(function () {
                this.checked = true;
            });
        }
        else {
            $(':checkbox').each(function () {
                this.checked = false;
            });
        }
    });

    $('#allent').click(function () {
        if (this.checked) {
            // Iterate each checkbox
            $(':checkbox').each(function () {
                this.checked = true;
            });
        }
        else {
            $(':checkbox').each(function () {
                this.checked = false;
            });
        }
    });


    $('.ids').click(function () {
        var C = 0;
        var count = [];
        $("#persons").find('input.ids').each(function () {
            C = C + 1;
            if (this.checked) {
                count.push(C)
            }
        });
        if (count.length == C && count.length > 0) {
            $('#all').prop("checked", true);
            $('#all').checked = true;
 
        } else {
            $('#all').prop("checked", false);
            $('#all').checked = false;
        }

    });

    $('.idsent').click(function () {
        var C = 0;
        var count = [];
        $("#entity").find('input.idsent').each(function () {
            C = C + 1;
            if (this.checked) {
                count.push(C)
            }
        });
        if (count.length == C && count.length > 0) {
            $('#allent').prop("checked", true);
            $('#allent').checked = true;
        } else {
            $('#allent').prop("checked", false);
            $('#allent').checked = false;
        }

    });


    var per_C = 0;
    var per_count = [];
    $("#persons").find('input.ids').each(function () {
        per_C = per_C + 1;
        if (this.checked) {
            per_count.push(per_C)
        }
    });
    if (per_count.length == per_C && per_count.length > 0) {
        $('#all').prop("checked", true);
        $('#all').checked = true;
    } else {
        $('#all').prop("checked", false);
        $('#all').checked = false;
    }

    var ent_C = 0;
    var ent_count = [];
    $("#entity").find('input.idsent').each(function () {
        ent_C = ent_C + 1;
        if (this.checked) {
            ent_count.push(ent_C)
        }
    });
    if (ent_count.length == ent_C && ent_count.length > 0) {
        $('#allent').prop("checked", true);
        $('#allent').checked = true;
    } else {
        $('#allent').prop("checked", false);
        $('#allent').checked = false;
    }

});

$(window).load(function() {
    $('#persons-columns_hider-btn').removeClass('btn-default');
    $('#persons-columns_hider-btn').addClass('btn-success');
    $('#entity-columns_hider-btn').removeClass('btn-default');
    $('#entity-columns_hider-btn').addClass('btn-success');
});



