$(function() {

    var global = {};

    global.lastDrop = 0;

    global.millitime = function() {
	return new Date().getTime();
    };

    $.prototype.resetFloat = function() {
	var w = $(this).css('width'), h = $(this).css('height');
	$(this).removeAttr('style').css('position','relative');
	/*Save special attribute height */
	if( $(this).is(".tile") ) {
	    $(this).height(h);
	}
	return $(this);
    };

    global.moveTile = function(drop, drag) {
	if( global.millitime() - global.lastDrop < 150 ) {
	    /* Probably a duplicate event */
	    return false;
	}else {
	    global.lastDrop = global.millitime();
	}

	if ($(drag).hasClass("tile-cat")) {
	    /* Moving an entire category */
	    if( $(drop).hasClass("tile-cat") ) {
		/* Dropped category on another category */
		if( $(drop).index() < $(drag).index() ) {
		    $(drop).before( $(drag).detach() );
		}else {
		    $(drop).after( $(drag).detach() );
		}
		$(drag,drop).resetFloat();
	    }else if( $(drop).hasClass("tile-section") ) {
		/* Dropping to entire section */
		$(drop).append( $(drag).detach() );
		$(drag,drop).resetFloat();
	    }
	}else if( $(drag).is(".tile") ) {
	    /* Moving a tile */
	    var parentName = $(drag).parents("#tile-main,#tile-board").attr('id');
	    if( parentName == "tile-board" ) {
		/* Copying tile from building blocks */
		drag = $(drag).clone();
		$(drag)
		    .draggable(global.tileDraggableProperties)
		    .droppable(global.tileDroppableProperties)
		    .resizable(global.tileResizableProperties);
	    }
	    if( $(drop).hasClass("tile-cat") ) {
		/* Dropping tile on category */
		$(drop).append( $(drag).detach().resetFloat() );
	    }else if( $(drop).is(".tile") ) {
		/* Dropping tile on tile */
		if( $(drop).index() < $(drag).index() ) {
		    $(drop).before( $(drag).detach() );
		}else {
		    $(drop).after( $(drag).detach() );
		}
		$(drag,drop).resetFloat();
	    }else if( $(drop).hasClass("tile-section") ) {
		/* Dropping tile on entire section */
	    }
	}
    };
    
    /* These are the tiles on the building block board */
    $("#tile-board .tile").draggable({
	revert: "invalid",
	helper:"clone",
    });

    /* These are the tiles on the layout panel */
    global.tileDraggableProperties = {
	revert: "invalid"
    };
    
    global.tileDroppableProperties = {
	greedy: true,
	tolerance: "pointer",
	accept: function(drag) {
	    return !drag.is(".tile-cat")
	},
	drop: function(e, ui) {
	    global.moveTile( this, ui.draggable );
	    $(this).resetFloat();
	},
	over: function() {
	    $(this).css('border','1px dashed grey');
	},
	out: function() {
	    $(this).resetFloat();
	}
    };

    global.tileResizableProperties = {
	handles: 'n,s',
	start: function() {
	    if( typeof(this.placeholder) != "undefined" ) {
		$(this.placeholder).remove();
		delete this.placeholder;
	    }
	    
	    this.placeholder = $("<div>")
		.width( $(this).width() ).height( $(this).height() )
		.css('float', $(this).css('float') ).css('position', 'static' )
	    	.css('visibility','hidden');
	    $(this).before( this.placeholder );
	},
	stop: function() {
	    $(this).resetFloat();
	    $(this.placeholder).remove();
	    delete this.placeholder;
	}
    };

    $("#tile-main .tile,#cat-list .tile").draggable(
	global.tileDraggableProperties
    ).droppable(
	global.tileDroppableProperties
    ).resizable(
	global.tileResizableProperties
    );

    /* These are the layout cateogries and */
    /* The category list categories */

    $("#tile-main .tile-cat,#cat-list .tile-cat").draggable({
	revert: "invalid"
    });

    $("#tile-main .tile-cat,#cat-list .tile-cat").droppable({
	tolerance: "pointer",
	drop: function(e, ui) {
	    global.moveTile( this, ui.draggable );
	    $(this).resetFloat();
	},
	over: function() {
	    $(this).css('border','1px dashed grey');
	},
	out: function() {
	    $(this).resetFloat();
	}
    });

    /* The layout panel */
    $("#tile-main").droppable({
	greedy: true,
	accept: ".tile-cat",
	drop: function(e, ui) {
	    global.moveTile( this, ui.draggable );
	    ui.draggable.resetFloat();
	}
    });

    /* The category panel */
    $("#cat-list").droppable({
	greedy: true,
	accept: ".tile-cat",
	tolerance: "touch",
	drop: function(e, ui) {
	    if( ui.draggable.hasClass("tile-cat") ) {
		/* Dropping a category */
		global.moveTile( this, ui.draggable );
	    }
	}
    });

    /* Remove all those that goes out of bounds */
    $("#trash-bin").droppable( {
	accept: "#cat-list .tile,#tile-main .tile",
	tolerance: "intersect",
	drop: function(e, ui) {
	    ui.draggable.remove();
	}
    });

    global.getLayoutJSON = function( sect ) {
	var r = [], done = [];
	$(sect).children(".tile-cat").each( function() {
	    var catID = $(this).children(".cat_ID").val();
	    console.log( done );
	    if( $.inArray(catID,done) >= 0 ) {
		return;
	    }else {
		done.push( catID );
	    }
	    var cat = {
		id: catID,
		tiles: []
	    };
	    $(this).children(".tile").each(function() {
		cat.tiles.push( {
		    size: $(this).children(".tile-size").attr('value'),
		    height: (+$(this).css("height")) * 2
		});
	    });
	    r.push( cat );
	});
	return JSON.stringify(r);
    }
    
    $("#form-ctl").bind("submit", function() {
	$(this).children("#layout_field").val( global.getLayoutJSON("#tile-main") );
	$(this).children("#hidden_layout_field").val( global.getLayoutJSON("#cat-list") );
	return true;
    });

});
