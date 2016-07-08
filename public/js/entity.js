$(document).ready(function () {
    var show = function(){
        $(".page-loader").css('display','table');
        setTimeout(hide, 1000);  // 5 seconds
    }

    var hide = function(){
        $(".page-loader").css('display','none');
    }
    function addAddressBlock() {
        var counter=1;
        $('#add_second_address').on('click',function () {


            $("#address1").clone().attr('id', 'address'+ ++counter).insertAfter('[id^=address]:last').find("input[type='text']").val("");

            $("div[id*='address']").each(function(i, ell) {

                if(ell.id!='address1'){
                    $('#'+ell.id+' span:first-child').css('display','block');

                }
            });
            $('#address'+counter).addClass("added");

            $('.close_address').on('click',function () {
                $(this).closest('.every_address').remove();

            });


        });
    }





    $('#createEntityModal').on('hidden.bs.modal', function(){
       var reset= $(this).find('form')[0];
        if(reset){
            reset.reset();
        }
        $('.hasError').empty();
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });
    $('#addNewEntity').click(  function(e) {
        show();
        $('#createEntityModal').on('shown.bs.modal', function () {
            $(".selectpicker").selectpicker();

        });

        $.ajax({
            url: "/admin/entity/create",
            method: 'GET',
            dataType:"html",
            success: function(data) {
                $('#myModalLabel').text('Add Entity Information');

                $('.modal-body').html(data);
                $('#createEntityModal').modal('show');

                $('#create_new_entity').submit(function () {
                    $.ajax({
                        url: "/admin/entity/store",
                        method: 'POST',
                        dataType:"json",
                        data:  $(this).serialize(),

                        success: function(data) {
                            if(data.status=='success'){
                                show();
                                window.location.href = document.URL;
                            }else{
                                show();
                                var li='';
                                $.each(data.errors,function(index, value){
                                    li+='<li>'+value+'</li>'
                                });


                                var htmlMessage ='<div class="alert alert-danger"><ul>'+li+'</ul></div>';
                                $(".hasError").html(htmlMessage);
                            }



                        }
                    });
                    return false;
                });
                addAddressBlock();

            }
        });
        return false;

    });


    //Edit Entity
    $('.editEntity').click(function () {
        show();
        $('#createEntityModal').on('shown.bs.modal', function () {
           $(".selectpicker").selectpicker();

        });

        var id=$(this).val();
        $.ajax({
            url: "/admin/entity/"+id+"/edit",
            method: 'GET',
            dataType:"html",
            success: function(data) {
                $('#myModalLabel').text('Edit Entity Information');
                $('.modal-body').html(data);
                $('#createEntityModal').modal('show');



                $('#update_entity').submit(function () {
                    $.ajax({
                        url: "/admin/entity/update/"+id,
                        method: 'POST',
                        dataType:"json",
                        data:  $(this).serialize(),

                        success: function(data) {

                            if(data.status=='success'){
                                show();
                                window.location.href = document.URL;
                            }else{
                                show();
                                var li='';
                                $.each(data.errors,function(index, value){
                                    li+='<li>'+value+'</li>'
                                });


                                var htmlMessage ='<div class="alert alert-danger"><ul>'+li+'</ul></div>';
                                $(".hasError").html(htmlMessage);
                            }



                        }
                    });
                    return false;
                });
                var counter=1;
                $("div[id*='address']").each(function(i, ell) {
                    if(ell.id!='address1'){
                        $('#'+ell.id+' span:first-child').css('display','block');

                    }
                });
                $('#add_second_address').on('click',function (e) {
                    e.preventDefault();
                    $(".selectpicker").selectpicker();



                    var cloned=$("#address1").clone().attr('id', 'address'+ ++counter).insertAfter('[id^=address]:last');
                    cloned.find("input[type='hidden']").val('0');
                    cloned.find('input[type="text"]').val('');
                    $('.dropdown-toggle').focus(function (e) {
                        e.preventDefault();
                    });
                    $("div[id*='address']").each(function(i, ell) {

                        if(ell.id!='address1'){
                            $('#'+ell.id+' span:first-child').css('display','block');

                        }
                    });
                    $('#address'+counter).addClass("added");

                    $('.close_address').on('click',function () {
                        $(this).closest('.every_address').remove();

                    });


                });


                $('.close_address').on('click',function () {
                    var confirmed = confirm("Want to delete this address?");
                    if(confirmed){
                        $(this).closest('.every_address').remove();
                        if(this.id){
                            var url = "/admin/entity/destroy_entity";
                            var token=$("input[name='_token']").val();


                            $.ajax({
                                method: 'DELETE',
                                url: url + '/' + this.id,
                                data: {'_token': token},
                                success: function(data) {

                                }
                            });
                        }
                    }

                });



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
                $('#createPersonModal').modal('show');
            }
        });
        return false;
    });



});
