/**
 * @summary Plugin Administration.
 *
 * Takes care of changes in the inputs and image selection.
 *
 * @since 0.1.0
 *
 * @var   string  ID       The unique ID of this instance.
 * @var   string  terms    The terms to be animated.
 * @var   string  opts     The plugin options.
 * @var   string  selector The plugin selector name - #id.
 * @var   string  path     The plugin directory.
 *
 * @see js/actus-aninated-tags.js
 * @global actusAnitID, anitTerms, anitOptions, anitCurrent, anitStyle, anitDir, actusAnitSelector;
 *
 * @param array  actusAnitParamsAdmin  Parameters received from PHP call.
 * @return  Shows a spinning gear during the saving proccess.
*/


(function( $ ){
	
	let anitOptions = actusAnitParamsAdmin.opts;
	let defaultStyle = actusAnitParamsAdmin.default_style;
	let anitTaxonomies = actusAnitParamsAdmin.taxonomies;

	
	// EVENTS
    $( 'body' ).on( 'click', '.actus-save', anitSave)
    $( 'body' ).on( 'click', '.actus-anit-color', anitSelectBackColor)
    $( 'body' ).on( 'click', '.actus-anit-add-image', anitAddImage)
    $( 'body' ).on( 'click', '.actus-anit-thumb', anitChangeBack)
    $( 'body' ).on( 'change', '.actus-anit-input', anitInputChange);
    $( 'body' ).on( 'click', '.actus-anit-option-tax .row', anitSetTaxonomies)

	

	
	// -----------------------------
	// SAVE ARRAY
	function anitSave(){
		
        anitOptions[ anitStyle ] = anitCurrent;
		
        $( '.actus-save' ).hide();
        $( '.actus-saving' ).fadeIn( 200 );
        $.post( actusAnitParamsAdmin.ajax_url, {
           _ajax_nonce: actusAnitParamsAdmin.nonce,
            action: 'actus_anit_save',
            options: anitOptions,
        }, function(data) {
            $( '.actus-saving' ).fadeOut( 200 );
        });
		
	}

	
	// -----------------------------
	// Styles List
	function anitStylesList(){
		
		$('.anit-styles-list').empty() 
		anitOptions.forEach((row, idx)=>{
			$('<div class="A-item">')
				.html( row.name )
				.attr('data-idx', idx)
				.appendTo('.anit-styles-list')
				.click( anitChangeStyle )
		})
		
		// add style button
		if ( ! $('.anit-add-style').length )
			$('<div class="anit-add-style">')
				.html('add style')
				.insertAfter('.anit-styles-list')
				.click( addStyle )
	}
	function anitChangeStyle( e ){
		let idx = $(e.target).attr('data-idx')
		$(e.target).siblings().removeClass('active')
		$(e.target).addClass('active')
		
		anitStyle = idx;
		anitCurrent = anitOptions[ idx ];
		setControls();
		
		// set new style
		$( '.actus-anit-cloud' )
			.css( 'height', anitCurrent.height + 'px' );
		
		anitSetBackground( anitCurrent.background )
		
	}
	function addStyle( e ){
		anitOptions.push( $.extend(true, {}, defaultStyle) );
		anitStylesList();
		
		anitStyle = anitOptions.length - 1;
		anitCurrent = anitOptions[ anitStyle ];
		

		$( '.actus-animated-tag' ).remove();
		anitStart();
		
		$('.anit-styles-list').children().last().trigger('click')
		
		$('.actus-save').show();
	}

	
	
	// -----------------------------
	// Set Controls
	function setControls(){
		// name
		$('input[name="ACTUS_ANIT_name"]')
			.val( anitCurrent.name )
		
		// shortcode
		$('.actus-anit-shortcode')
			.html('[actus_animated_tags]')
		if ( anitStyle > 0 )
			$('.actus-anit-shortcode')
				.html('[actus_animated_tags style="'+ anitStyle +'"]')
		
		// height
		$('input[name="ACTUS_ANIT_height"]')
			.val(parseInt( anitCurrent.height ))
		
		// density
		$('input[name="ACTUS_ANIT_density"]')
			.val(parseInt( anitCurrent.density ))
		
		//color
		$('.actus-anit-cloud .actus-animated-tag')
			.css('color', anitCurrent.color)
		$('.actus-anit-option-terms-color > *:not(.label)')
			.remove();
		$('<input>')
			.addClass('actus-anit-input actus-anit-terms-color')
			.attr('name', 'ACTUS_ANIT_color')
			.attr('type', 'text')
			.val( anitCurrent.color )
			.prependTo('.actus-anit-option-terms-color')
		anitColorPicker();
		
		
		// background
		$('.actus-anit-thumb').removeClass('selected')
		$('.actus-anit-thumb[alt="'+ anitCurrent.background +'"]')
			.addClass('selected')
		
		// set color picker
        if ( anitCurrent.background.substr(0, 1) == '#' ) {
			
            anitSetBackground( anitCurrent.background );
			anitBackColorPicker();
			
		// set image on actus-anit-add-image button
		} else if ( anitCurrent.background.split('/').length > 1 ) {
			$('.actus-anit-add-image img').remove();
			$('<img>')
				.attr('src', anitCurrent.background)
				.appendTo('.actus-anit-add-image')
		}
		
		
		
	}
	
	
	
	// INPUT CHANGE
	function anitInputChange( e ){
    /**
     * Handles input changes.
     *
     * Detects an input change, proccesses the value accordingly and saves it.
     *
     * @var string  $name   Name of input.
     * @var string  $value  Value of input.
     *
     * @global string    $anit_terms    The terms to be animated.
     */
		var name  = $.trim( $( e.target ).attr('name') ),
            value = $( e.target ).val();
		
		// checkboxes
		if ( $( e.target ).attr('type') == 'checkbox' ) {
			value = 0;
			if ( $( e.target).is(":checked") ) value = 1;
		}
		
		
		name = name.replace("ACTUS_ANIT_", "");
		
		anitCurrent[ name ] = value;

		
        if ( name == 'height' )  anitSetHeight( value );
        if ( name == 'density' ) anitSetDensity( value );
        if ( name == 'name' )
			$('.anit-styles-list .A-item.active').html( value );
		
		
		anitOptions[ anitStyle ] = anitCurrent;
		$('.actus-save').show();
		
	}
	
	function anitSetHeight( value ){
		anitCurrent.height = parseInt( value );
		$( '.actus-anit-cloud' ).css( 'height', value + 'px' );
		
	}
	function anitSetDensity( value ){
		if ( parseInt( value ) > 50 ) {
			value = 50;
		}
		if ( parseInt( value ) < 1 ) {
			value = 1;
		}
		$( e.target ).val( value );
		opts.density = value;
		anitCurrent.density = parseInt( value );
		$( '.actus-animated-tag' ).remove();
		
		anitStart();
	}
	function anitSetTaxonomies( e ){
		$( e.target ).toggleClass('selected');
		anitCurrent.taxonomies = [];
		$('.actus-anit-option-tax .row.selected').each(function(){
			anitCurrent.taxonomies.push( $(this).text() );
		})
		
		$('.actus-save').show();
	}
	
	

	// COLOR
	function anitColorPicker(){
		anitCurrent.color = anitCurrent.color || '#ffffff';
		$('.actus-anit-terms-color').val( anitCurrent.color )
			.wpColorPicker({
				change: function (event, ui) {
					var element = event.target;
					var color = ui.color.toString();

					anitCurrent.color = color;
					$('.actus-save').show();
					
					$('.actus-anit-cloud .actus-animated-tag')
						.css('color', color)

				},
			})
	}
	function anitSelectBackColor( e ){
		$('.actus-anit-add-image img').remove();
		$('.actus-anit-color .A-color-pick').remove();
		
		anitCurrent.background = '#00c6af';
		anitSetBackground( anitCurrent.background );
		
		anitBackColorPicker();
	
	}
	function anitBackColorPicker(){
		let $color = $('<div class="A-color-pick">')
			.appendTo('.actus-anit-color')
		$('<input class="color-field">')
			.val( anitCurrent.background )
			.appendTo( $color )
		
		$('.color-field').wpColorPicker({
			change: function (event, ui) {
				var element = event.target;
				var color = ui.color.toString();
				
				anitCurrent.background = color;
				anitSetBackground( color );

			},
		})
	}

	

	// ADD IMAGE
	function anitAddImage( e ){
    /**
     * Adds and image from library.
     *
	 */
		
		$('.actus-anit-color .A-color-pick').remove();
		
        // MEDIA LIBRARY SETUP
        actus_anit_uploader = wp.media.frames.file_frame =
			wp.media({
				title: 'Select Image',
				button: {
					text: 'Add Image'
				},
				multiple: false
			});
		
        // SELECT IMAGE
        actus_anit_uploader.on('select', function() {
            var attachment = 
				actus_anit_uploader.state()
					.get('selection').first().toJSON();
            var img_id     = attachment['id'];
            var img_url    = attachment['url']; 

			anitCurrent.background = img_url;
			
			anitSetBackground( img_url );
			
			$('.actus-anit-add-image img').remove();
			$('<img>')
				.attr('src', img_url)
				.attr('id', img_id)
				.appendTo('.actus-anit-add-image')

        });
		
        // MEDIA LIBRARY OPEN
        actus_anit_uploader.on('open', function(){
            var selection = actus_anit_uploader.state().get('selection');
        });
        actus_anit_uploader.open();
        return false;
    };
    // CHANGE BACKGROUND
	function anitChangeBack( e ){
	/**
     * Changes background image.
     *
     * @var string  $n  Name of input.
     * @var string  $v  Filename of the clicked image.
     *
     * @global string    $anit_terms    The terms to be animated.
     */
		$('.actus-save').show();
		
		
		if ( $( this ).hasClass('actus-anit-random') ||
		     $( this ).hasClass('actus-anit-add-image') ||
		     $( this ).hasClass('actus-anit-color') ) return;
		
		$('.actus-anit-add-image img').remove();
		$('.actus-anit-color .A-color-pick').remove();
		
		// get filename
        let fname1 = $.trim( $( this ).attr( 'alt' ) );
		let fname = anitDir + 'img/back/' + fname1;
		
		// set Current and Options
		anitCurrent.background = fname1;
		anitOptions[ anitStyle ] = anitCurrent;
		
		// add selected class
        $( '.actus-anit-thumbs .actus-anit-thumb' )
			.removeClass( 'selected' );
        $( this ).addClass( 'selected' );
   
		// set image
        itm = this;
		anitSetBackground( fname );
    };
	
	// SET BACKGROUND
	function anitSetBackground( fname ){
		     
        if ( fname.substr(0, 1) == '#' ) {
            $( '.actus-anit-preview .actus-anit-cloud' ).css({
				'background-image': 'unset',
				'background-color': fname,
			});
			return;
		}
			
			
        if ( fname.split('/').length < 2 )
			fname = actusAnitParams.plugin_dir + 'img/back/' + fname;
		
        if ( fname != 'random' )
            $( '.actus-anit-preview .actus-anit-cloud' ).css(
                'background-image', 'url( ' + fname + ' )'
            );

		
	}

	
	
	// -----------------------------
	anitStylesList();
	$('.anit-styles-list').children().first().trigger('click')

})(jQuery);

