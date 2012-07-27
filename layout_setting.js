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

/*
  Data structure for layout:

  Unit : <"em" | "px" | "%">
  
  box : {
    type : <"root" | "tile" | "box">
    w: <n | "x"> #The special character "x" means extensible
    h: <n | "x"> 
    children: [ box, box, box, ... ]
  }

  #Wordpress specific types
  box.type == "tile" : {
    func: <"cat" | "widget">
    id : <n> #An ID to identify the resource involved    
  }
*/

jQuery(function($) {

    var g = {};
    window.global = g;

    g.tile_class = ".tile";
    g.cat_class=".cat";
    
    g.lastDrop = 0;

    g.millitime = function() {
	return new Date().getTime();
    };

    $.prototype.layoutAttr = function(a,b) {
	var n = $(">[name='"+a+"']:first",this)[0];
	if( typeof(b) == "undefined" ) {
	    /* Set some defaults */
	    if( typeof(n) == "undefined" || typeof(n.value) == "undefined" ) {
		if( a == "type" ) return "box";
		else return;
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

    $.prototype.layoutType = function( t ) {
	return $(this).layoutAttr("type") == t;
    };

    $.prototype.isSiblingTo = function( tgt ) {
	var that = this, found = false;
	$(this).siblings().each( function() {
	    if( $(this)[0] === $(tgt)[0] ) found = true;
	    return !found;
	});
	return found;
    };

    $.prototype.resetFloat = function() {
	var w = $(this).width(), h = $(this).height();
	$(this).removeAttr('style').removeClass('hover').css('position','relative');
	/*Save special attribute height */
	if( $(this).layoutType("tile") )  $(this).height( h );
	if( 1 ) $(this).width( w );
	return $(this);
    };

    g.splitTileRows = function( c, cw ) {
	if( typeof(c) == "undefined" ) return;

	c.rows = []; c.rw = []; c.mw = 0; c.i = 0;
	var rh = 50; /* no block should be smaller than one row */

	$('>.box',c).each( function() {
	    var size = $(this).layoutAttr('size');
	    var n = Math.floor( $(this).height()/rh );
	    
	    for(var j=c.i;j<c.i+n;j++) {
		if( typeof(c.rows[j]) == "undefined" ) { c.rows[j] = []; c.rw[j] = cw; }
		c.rows[j].push( this );
	    }

	    if( typeof(size) == "undefined" ) {
		g.splitTileRows( this, c.rw[c.i] );
	    } else {
		if( c.rw[c.i]+(1/+size) >= 1 ) {
		    c.i++;
		} else {
		    c.rw[c.i] += 1/+size;
		}
		if( c.rw[c.i]-cw > c.mw ) c.mw = c.rw[c.i]-cw;
	    }
	});
	//if( !$(c).is(".boxroot") ) console.log( c, c.rows );
    };

    g.resizeTileWidth = function( c, cw ) {

	for( i in c.rows ) {
	    var w = cw;
	    //console.log( c.rows[i] );
	    console.log( "-------------------------------" );
	    for(var j in c.rows[i] ) {
		var pad = $(c.rows[i][j]).outerWidth( true ) - $(c.rows[i][j]).width();
		w -= pad; 
	    }
	    for(var j in c.rows[i] ) {
		console.log( c.rows[i][j] );
		//if( typeof(k) == "undefined" ) continue;
		var k = c.rows[i][j];
		var size = $(k).layoutAttr('size');
		var r = (1/+size);
		if( k.resized != 1 ) {
		    if( typeof(size) == "undefined" ) {
			//console.log( k, w * k.mw );
			$(k).width( w * k.mw );
			console.log("||||||||||||||||||||||||||||||||");
			g.resizeTileWidth( k, w );
			console.log("||||||||||||||||||||||||||||||||");
		    } else {
			$(k).width( w * r );
		    }
		    k.resized = 1;
		}
	    }
	}
	$(">.box",c).each( function() { delete this.resized; } );
    };

    g.adjustTileWidth = function( c ) {
	if( typeof(c) == "undefined" ) return;
	g.splitTileRows( c, 0 );
	//console.log( c.rows );
	g.resizeTileWidth( c, $(c).width() );
    };

    g.moveBox = function(drop, drag) {
	if( g.millitime() - g.lastDrop < 150 ) {
	    /* Probably a duplicate event */
	    return false;
	}else {
	    g.lastDrop = g.millitime();
	}

	/* Check for special draggables */
	var parentName = $(drag).parents("#box-board").attr('id');
	if( parentName == "box-board" ) {
	    /* Copying box from building blocks */
	    drag = $(drag).clone( );
	    $(drag)
		.draggable(g.tileDraggableProperties)
	    	.resizable( $(drag).layoutType("tile") ?
			    g.tileResizableProperties : g.boxResizableProperties )
	    	.droppable(g.tileDroppableProperties);
	}

	if ( ( $(drag).isSiblingTo(drop) && $(".box",drop).size() > 0 ) ||
	     $(drop).layoutType("tile") ) {
	    /* Dropping to sibling */
	    if( $(drop).index() < $(drag).index() ) {
		$(drop).before( $(drag).detach() );
	    }else {
		$(drop).after( $(drag).detach() );
	    }
	}else {
	    /* Dropping to container */
	    if( !$(drop).layoutType("tile") ) {
		$(drop).append( $(drag).detach() );
	    }
	}

	$(drag,drop).resetFloat();
	
	/* Adjust width to fit */
	if( $(drop).is(".boxroot") ) {
	    g.adjustTileWidth( drop );
	} else {
	    g.adjustTileWidth( $(drop).parents(".boxroot")[0] );
	}
    };


    
    /* These are the boxs on the building block board */
    $("#box-board .box").draggable({
	revert: "invalid",
	helper:"clone",
    });

    /* These are re-usable properties for clones of the mother tiles (on #box-board) */
    g.tileDraggableProperties = {
	revert: "invalid"
    };
    
    g.tileDroppableProperties = {
	greedy: true,
	tolerance: "pointer",
	drop: function(e, ui) {
	    g.moveBox( this, ui.draggable );
	    $(this).resetFloat();
	},
	over: function() {
	    $(this).addClass("hover");
	},
	out: function() {
	    $(this).resetFloat();
	}
    };

    g.resizeStartPlaceholder = function() {
	if( typeof(this.placeholder) != "undefined" ) {
	    $(this.placeholder).remove();
	    delete this.placeholder;
	}
	this.placeholder = $("<div>")
	    .width( $(this).width() ).height( $(this).height() )
	    .css('float', $(this).css('float') ).css('position', 'static' )
	    .css('visibility','hidden');
	$(this).before( this.placeholder );
    };

    g.resizeStopPlaceholder = function() {
	$(this).resetFloat();
	$(this.placeholder).remove();
	delete this.placeholder;
    };
    
    g.tileResizableProperties = {
	handle: "n,s,w,e",
	grid: [62,10],
	start: g.resizeStartPlaceholder,
	stop: g.resizeStopPlaceholder
    };


    /* All tiles */
    $("#box-main "+g.tile_class+",#box-hidden "+g.tile_class)
	.draggable(
	    g.tileDraggableProperties
	).droppable(
	    g.tileDroppableProperties
	).resizable(
	    g.tileResizableProperties
	);

    /* TL;DR Non-tiles/containers */
    /* These are the layout and cateogory box containers (including category boxes) */
    /* Excluding tiles */
    $("#box-main .box:not("+g.tile_class+"),#box-hidden .box:not("+g.tile_class+")")
	.draggable({
	    revert: "invalid"
	}).droppable({
	    tolerance: "pointer",
	    greedy: true,
	    drop: function(e, ui) {
		g.moveBox( this, ui.draggable );
		$(this).resetFloat();
	    },
	    over: function() {
		$(this).addClass("hover");
	    },
	    out: function() {
		$(this).resetFloat();
	    }
	});
    /* Resizable Containers */
    g.boxResizableProperties = g.tileResizableProperties;
    
    $(".box:not("+g.tile_class+")")
	.filter(function(){ return !$(this).parent().is("#box-board"); })
	.resizable(
	    g.boxResizableProperties
	);

    /* The layout (main) panel */
    $("#box-main").droppable({
	greedy: true,
	drop: function(e, ui) {
	    g.moveBox( this, ui.draggable );
	    ui.draggable.resetFloat();
	    g.adjustTileWidth( this );
	}
    });


    /* The category panel */
    $("#box-hidden").droppable({
	greedy: true,
	tolerance: "touch",
	drop: function(e, ui) {
	    /* Dropping a category */
	    g.moveBox( this, ui.draggable );
	    g.adjustTileWidth( this );
	}
    });

    /* Remove all that touches trash */
    $("#trash-bin").droppable( {
	accept: "#box-hidden .box:not("+g.cat_class+"),#box-main .box:not("+g.cat_class+")",
	tolerance: "intersect",
	drop: function(e, ui) {
	    ui.draggable.remove();
	}
    });



    g.adjustTileWidth( $("#box-board") );
    g.adjustTileWidth( $("#box-hidden") );
    g.adjustTileWidth( $("#box-main") );

    /* Start parsing functions */

    g.getLayoutObject = function( k, r ) {
	var next_k = $(">.box",k);
	/* Get property of current DOM object */
	r.children = [];
	$(">input[name]",k).each( function() {
	    r[this.name] = this.value;
	});
	/* Get special properties */
	if( $(next_k).size() == 0 ) {
	    /* r.w = $(k).width(); */
	    r.w = "0px"; /* Will use propotional width instead */
	    r.h = $(k).height();
	} else {
	    r.w = "0px"; r.h = "0px"; /* Containers are "without dimensions" */
	}
	/* Go through child boxes */
	$(next_k).each( function() {
	    var p = {};
	    g.getLayoutObject( this, p );
	    r.children.push( p );
	});
	return r;	
    }

    g.getLayoutJSON = function( sect ) {
	var r = {};
	g.getLayoutObject( sect, r );
	r.type = "root";
	return JSON.stringify( r );
    }
    
    $("#form-ctl").bind("submit", function() {
	$(this).children("#layout_field").val( g.getLayoutJSON("#box-main") );
	$(this).children("#hidden_layout_field").val( g.getLayoutJSON("#box-hidden") );
	return true;
    });

});
