jQuery(function($) {
    /* Note: Added this event to tax-meta-class/js/tax-meta-class.js*/
    $(".mupload_img_holder").bind( 'imageupdate', function() {
	$("img",this).width(70).height(70);
    }).triggerHandler('imageupdate');
});
