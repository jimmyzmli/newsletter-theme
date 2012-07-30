jQuery(function($) {
    var g = window;    
    var hiddens = $("#nav-bar1 .hidden-nav-section");
    var clrs = ['#F30C23', '#FFBC00', 'green', 'red', 'blue','yellow','lightblue','green'];


    /* Start header nav menu code */
    g.i = 0;
    $("#nav-bar1-expand").css('visibility','hidden').append(
	hiddens.detach()
    ).children("[style]").removeAttr("style");
  
    hiddens.each(function() {
	$(this).css('position','relative')
	    .prepend( $("<div>")
		      .css('background-color', clrs[g.i++ % clrs.length])
		      .width(5).height($(this).parent().height())
		      .css('position','absolute').css('top','0').css('left','0') );
    });

    $("#nav-bar1 ul>li:not(#nav-expand-btn) a").each( function() {
	$(this)
	    .append( $("<div>")
			.css('background-color', clrs[g.i++ % clrs.length])
			.width( $(this).parent().width() ).height(3)
			.css('visibility','hidden')
		        .addClass('highlighter') )
	    .bind('mouseover', function(){ $('>.highlighter',this).css('visibility','visible'); } )
	    .bind('mouseout', function(){ $('>.highlighter',this).css('visibility','hidden'); } );
	
    });

    $("#nav-bar1-expand").removeAttr('style').css('display','none');
    
    $("#nav-expand-btn").bind( 'click', function() { $("#nav-bar1-expand").slideToggle(); } );

    /* Scale/Trim fonts */
    $("#side .promo-title").scaleFontToFit();
    $(".promo-desc").trimTextToFit();
});
