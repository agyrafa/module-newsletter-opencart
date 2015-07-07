<div id="newsletter" class="input-group">
    <input type="text" name="newsletter" id="newsletter-email" placeholder="<?php echo $text_email; ?>" class="form-control input-lg" />
    <div class="text-danger error_email"></div>
  <span class="input-group-btn">
     <button type="button" form="form-modal-whats" class="btn btn-sent" id="submit-email"><?php echo $register; ?></button>
  </span>
</div>

<script>

        $('#submit-email').click(function () {

            var node = $(this);

            var newsletterEmail = $('#newsletter-email').val();

            var data = {
                email : newsletterEmail
            };


            $.ajax({
                data: data,
                type: 'post',
                url: 'index.php?route=common/newsletter/addNewsletter',
                dataType: 'json',
                beforeSend: function() {
                    node.button('loading');
                },
                complete: function() {
                    node.button('reset');
                },
                success: function (result) {

                    node.parent().find('.text-danger').remove();

                    $('.error_email').empty();

                    if (result.success) {
                        alert(result.msg);
                        $('#newsletter').find("input").val("");
                    }
                    if ( result.error ) {

                        if(result.email && result.email != '' ){
                            $('.error_email').empty().html( result.email );
                        }


                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert('Erro ao cadastrar.');

                    //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            })
        })

</script>