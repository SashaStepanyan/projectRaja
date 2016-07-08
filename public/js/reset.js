$( document ).ready( function( $ ) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        }
    });
    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }



    $( '#form-add-setting' ).on( 'submit', function() {
        var email=$('#email').val();
        if(email.length){
            var token=$('input[name="_token"]').val();
            $.ajax({
                url: "/password/emailsend",
                method: 'POST',
                data:  { email:email,token:token},

                success: function(data) {
                    var htmlMessage ='<div class="alert alert-'+data.status+'">'+data.message+'</div>';
                    $(".return_message").html(htmlMessage);
                    $('#email').val('')

                }
            });
        }




        return false;
    } );

} );
