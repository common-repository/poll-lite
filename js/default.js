jQuery(document).ready(function($){

    $('.merlic_poll_container').each(function(index){
        fix_width($(this));
    });
    
    $('input[name="merlic_poll_vote"]').each(function(index){
        if ($(this).is(':checked')) {
            var thisForm = $(this).closest("form");
            $(thisForm).find('input.merlic_poll_submit').removeAttr('disabled');
        }
    });
    
    $('input[name="merlic_poll_vote"]').click(function(){
        var thisForm = $(this).closest("form");
        $(thisForm).find('input.merlic_poll_submit').removeAttr('disabled');
    });
    
});


function fix_width(container){

    var greatestWidth = 0; // Stores the greatest width
    jQuery(container).children('.merlic_poll').each(function(index){ // Select the elements you're comparing
        var theWidth = jQuery(this).width(); // Grab the current width
        if (theWidth > greatestWidth) { // If theWidth > the greatestWidth so far,
            greatestWidth = theWidth; //    set greatestWidth to theWidth
        }
    });
    jQuery(container).children('.merlic_poll').css('width', greatestWidth + 'px');
    
}

function enable_button(vote){
    var thisForm = $(vote).closest("form");
    $(thisForm).find('input.merlic_poll_submit').removeAttr('disabled');
}
