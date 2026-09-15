$(document).ready(function() {
    $(document).on('change','.toggleSwitch', function() {
        // if ($(this).is(':checked')) {
        //     // Perform actions for "on" state
        //     $(this).val(1);
        // } else {
        //     // Perform actions for "off" state
        //     $(this).val(0);
        // }

        // console.log('hello');
    });


    $(".show_hide_password i").on('click', function(event) {
        event.preventDefault();
          var $thisEle = $(this);
    
          var $inputElement = $(this).parent('.input-group-addon').siblings('input');
          // console.log($inputElement);
            if($inputElement.attr("type") == "text"){
              $inputElement.attr('type', 'password');
              $thisEle.addClass( "fa-eye-slash").removeClass( "fa-eye" );
            }else if($inputElement.attr("type") == "password"){
                $inputElement.attr('type', 'text');
                $thisEle.removeClass( "fa-eye-slash").addClass( "fa-eye" );
            }
    });

    // Modal js
  
     
});

