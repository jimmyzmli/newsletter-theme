jQuery(function($) {
    var g = window;    
    var hiddens = $("#nav-bar1 .hidden-nav-section");
    var clrs = ['#F30C23', '#FFBC00', 'green', 'red', 'blue','yellow','lightblue','green'];


    $.prototype.hasScrollBar = function() {
	/* @AUTHOR Praveen Prasad From StackOverFlow */
	/* note: clientHeight= height of holder */
	/* scrollHeight= we have content till this height */
	var _elm = $(this)[0];
	var _hasScrollBar = false; 
	if ((_elm.clientHeight < _elm.scrollHeight) || (_elm.clientWidth < _elm.scrollWidth)) {
            _hasScrollBar = true;
	}
	return _hasScrollBar;
    };

    $.prototype.scaleFontToFit = function() {
	/* @TODO Optimize */
	/* Note: This function is fairly expensive, do not use extensively */
	var box = this[0], lim;
	if( box.style.fontSize == '' ) {
	    lim = Math.round( $(box).width()/5 );
	}else {
	    lim = parseInt(box.style.fontSize);
	}
	box.style.fontSize = lim+'px';
	var search = function( a, b, f ) {
	    /* If f == true, it means a is small enough */
	    /* In range [a,b] */
	    var mid = Math.round((a+b)/2);
	    if( mid == a || mid == b ) return;	    
	    if( f!==true )
		box.style.fontSize = mid+'px';
	    else
		box.style.fontSize = b+'px';

	    if( !$(box).hasScrollBar() ) {
		if( f === true ) {
		    console.log( "SEARCHING ", a, b );	    
		    /* a fits, b doesn't */
		    box.style.fontSize = a+'px';
		    return; 
		}
		search( mid, b, true );
	    } else {
		search( a, mid, f );
		search( mid, b, f );
	    }
	};
	while( lim-- >= 1 && $(box).hasScrollBar() )
	    box.style.fontSize = lim+'px';
    };

    $.prototype.trimTextToFit = function() {
	/* @AUTHOR Alex from alexgorbatchev.com */
        return this.each(function()
			 {
                             var el = $(this);
                             if(el.css("overflow") == "hidden")
                             {
                                 var text = el.html();
                                 var multiline = true;
                                 var t = $(this.cloneNode(true))
                                     .hide()
                                     .css('position', 'absolute')
                                     .css('overflow', 'visible')
                                     .width(multiline ? el.width() : 'auto')
                                     .height(multiline ? 'auto' : el.height())
                                 ;

                                 el.after(t);

                                 function height() { return t.height() > el.height(); };
                                 function width() { return t.width() > el.width(); };

                                 var func = multiline ? height : width;

                                 while (text.length > 0 && func())
                                 {
                                     text = text.substr(0, text.length - 1);
                                     t.html(text + "...");
                                 }
				 
                                 el.html(t.html());
                                 t.remove();
                             }
			 });
    };

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
    $("#side .promo-desc").trimTextToFit();
});
