jQuery(function($) {
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
	return $(this);
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


    $.prototype.layoutAttr = function(a,b) {
	var n = $(">[name='"+a+"']:first",this)[0];
	if( typeof(b) == "undefined" ) {
	    /* Set some defaults */
	    if( typeof(n) == "undefined" || typeof(n.value) == "undefined" ) {
		return;
	    }
	    return n.value;
	} else {
	    if( typeof(n) == "undefined" ) {
		var s = $("<input/>").attr("type","hidden");
		$(this).prepend( s.attr("name",a).attr("value",b) );
	    } else {
		n.value = b;
	    }
	    return $(this);
	}
    };

    $.prototype.jslides = function(info, args) {
	var root, slider = this;
	root = $("<div>").addClass("slides_container");

	$(info).each( function(i,p) {
	    $(slider).empty()
		.append($("<div>").addClass("slider-topic"))
		.append(root);
	    $(root).append(
		$("<div>").append(
		    $("<div>")
			.append(
			    $("<div>").text(p.title).addClass("promo-title")
			)
			.append(
			    $("<img>").attr('src', p.img).addClass("promo-img")
			).addClass("clearfix").addClass("promo-story")
			.append(
			    $("<div>").text(p.desc).addClass("promo-desc")
			)			
		)
	    );
	} );
	var start = typeof(args.start) == "undefined" ? 1 : args.start;
	var name_cat = function(k) {
	    $(".slider-topic", slider).text( info[k-1].cat );
	};
	
	name_cat(start);
	$(".slider").slides({
	    generateNextPrev: true,
	    start: start,
	    animationComplete: name_cat
	});
	
	/* Fix floats */
	$(".pagination",slider).addClass("clearfix");
	$(slider).addClass("clearfix");
    };    
});
