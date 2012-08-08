/*
	Copyright (c) 2012 Jimmy Li (JzL)

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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
	if( $(box).css('font-size') == '' ) {
	    lim = Math.round( $(box).width()/5 );
	}else {
	    lim = parseInt($(box).css('font-size'));
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

    $.prototype.jslides = function(info, usr_opts) {
	var root, slider = this;
	root = $("<div>").addClass("slides_container");

	$(info).each( function(i,p) {
	    $(slider).empty()
		.append($("<div>").append( $("<a>").addClass("slider-topic") ))
		.append(root);
	    $(root).append(
		$("<div>").append(
		    $("<div>")
			.append(
			    $("<div>").append( $("<a>").text(p.title).attr("href",p.link).addClass("promo-title") )
			)
			.append(
			    $("<a>").attr("href",p.link).append( $("<img>").attr('src', p.img).addClass("promo-img") )
			).addClass("clearfix").addClass("promo-story")
			.append(
			    $("<div>").html(p.desc).addClass("promo-desc")
			)
		)
	    );
	} );

	var font_fixed = false;
	var name_cat = function(k) {
	    /* Set topic text */
	    $(".slider-topic", slider).text( info[k-1].cat ).attr("href",info[k-1].catLink);
	    /* Fix fonts */
	    if( font_fixed === false ) {
		font_fixed = true;
		$(".promo-title",slider).each( function() { $(this).scaleFontToFit(); } );
	    }
	};
	if( typeof(usr_opts) != "object" ) usr_opts = {};	
	var opts = $.extend({
	    generateNextPrev: true,
	    start: 1,
	    animationComplete: name_cat
	}, usr_opts);

	/* Create slideshow */
	name_cat(opts.start);
	$(".slider").slides( opts );
	    
	/* Fix floats */
	$(".pagination",slider).addClass("clearfix");
	$(slider).addClass("clearfix");
    };    
});
