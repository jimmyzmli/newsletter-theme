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
    var g = window;    
    var hiddens = $("#nav-bar1 .hidden-nav-section");
    var clrs = ['#F30C23', 'black', 'green', 'red', 'blue','purple','lightblue','green'];
    
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

    $("header .nav-bar-horizontal ul>li:not(#nav-expand-btn) a").each( function() {
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
    $(".promo-title").each( function(){ $(this).scaleFontToFit(); } );
    $("img").error(g.imageErrHandler).load(g.imageErrHandler);
});
