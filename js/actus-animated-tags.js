/**
 * @summary Create and Animate everything.
 *
 * Executed everytime a shortcode or widget is called.
 * Assigns the background, sets the height and creates all the random values needed
 * for the animation.
 *
 * @since 0.1.0
 *
 * @global  string  anitID:true       The unique ID of this instance.
 * @global  string  anitTerms:true    The terms to be animated.
 * @global  string  anitCurrent:true  The plugin options.
 * @global  string  anitDir:true      The plugin directory.
 * @global  string  anitSelector:true The plugin selector name - #id.
 *
 * @param array  actusAnitParams  Parameters received from PHP call.
*/

	var $ = jQuery.noConflict();
    var anitID,
		anitTerms,
		anitOptions,
		anitCurrent,
		anitStyle,
		anitDir,
		anitSelector;

    // Set main variables from parameters received from PHP.
    anitOptions = actusAnitParams.opts;
    anitCurrent = actusAnitParams.current;
    anitID    	= actusAnitParams.id;
    anitTerms 	= actusAnitParams.terms;
    anitStyle 	= actusAnitParams.style;
    anitDir   	= actusAnitParams.plugin_dir;
    site_url	= actusAnitParams.site_url;
    anitSelector = '#' + anitID;
    if ( typeof( actusVisibleTags ) === 'undefined' ) {
        actusVisibleTags = [];
    }
    actusVisibleTags[ anitID ] = 0;
    

    anitCurrent.height        = parseInt( anitCurrent.height );
    anitCurrent.min_font_size = parseInt( anitCurrent.min_font_size );
    anitCurrent.max_font_size = anitCurrent.height - ( anitCurrent.height / 5 );





    if ( typeof( anitArgs  ) === 'undefined' ) {
        var anitArgs = {};
    }
    anitArgs[ anitID ] = {};

    /**
     * @summary Sets the background and the height of the plugin.
     *
     * @since 0.1.0
     *
     * @global type $varname Short description.
     * @fires target:event
     * @listens target:event
     *
     * @var   string  terms        The terms to be animated.
     * @var   string  options      The plugin options.
     * @var   string  backFilename The background filename (or random).
     * @var   string  backURL      The background URL.
     * @var   array   args         All arguments needed for the animation.
     * @var   int     visibleTags  The number of currently visible tags.
    */
    function anitStart( iid ) {

        anitArgs[ iid ] = {
            'id'         : anitID,
            'selector'   : anitSelector,
            'opts'       : anitCurrent,
            'terms'      : anitTerms,
            'plugin_dir' : anitDir,
            'visibleTags': 0
        };
		

		anitSetBackground();
		
        
        anitRandomValues( iid, function() {
            anitAnimate( iid );
        });
        
    }

    // Execute Animation.
    anitStart( anitID );

    

    /**
     * @summary Sets the background and the height of the plugin.
     *
    */
	function anitSetBackground(){
        var backFilename, backURL;
		
		
        if ( anitCurrent.background.substr(0, 1) == '#' ) {
			$( anitSelector + ' .actus-anit-cloud')
				.height( anitCurrent.height )
				.css( "background-color", anitCurrent.background );
			return;
		}
		
		
		backFilename = anitCurrent.background;
        if ( backFilename == 'random' ) {
            backNumber = Math.floor((Math.random() * 14 ) + 1 );
            if ( backNumber < 10 ) {
                backNumber = '0' + backNumber;
            }
            backFilename = backNumber + '.jpg'; 
        }
        backURL =
			'url( ' + anitDir + 'img/back/' + backFilename + ' )';
        if ( backFilename.substr(0,6) == 'https:' )
			backURL = 'url( ' + backFilename + ' )';
		
		
        $( anitSelector + ' .actus-anit-cloud')
			.height( anitCurrent.height )
			.css( "background-image", backURL );
		
	}



    /**
     * @summary Sets the random values needed for the animation.
     *
     * @since 0.1.0
     *
     * @var   string  randomTerm  The term to be animated.
     * @var   string  randomFontSize.
     * @var   string  randomOpacity.
     * @var   string  randomVpos.
     * @var   string  randomSpeed.
     * @var   string  randomDirection.
     * @var   string  minTime  The minimum number of ms for animation.
     * @var   string  maxTime  The maximum number of ms for animation.
     *
     * @param string  id  The unique ID of the current instance.
     * @param string  selector  The plugin selector name - #id.
     * @param string  callback  The callback function.
    */
    function anitRandomValues( iid, callback ) {
        callback = callback || function(){};
        var minTime, maxTime;
        minTime  =  7 * 1000;
        maxTime  = 30 * 1000;
        opts     = anitArgs[ iid ].opts;
        terms    = anitArgs[ iid ].terms;

        anitArgs[ iid ].randomTerm =
            terms [ Math.floor( ( Math.random() * terms.length ) - 0) ];
        
        anitArgs[ iid ].randomFontSize =
            Math.floor( Math.random() * ( opts.max_font_size - opts.min_font_size ) +
            opts.min_font_size );
        
        anitArgs[ iid ].randomOpacity =
            ( Math.floor( ( Math.random() * 70 ) + 10 ) ) / 100;
        
        anitArgs[ iid ].randomVpos =
            Math.floor( ( Math.random() *
            ( opts.height - ( anitArgs[ iid ].randomFontSize / 3.75 ) ) ) -
            ( anitArgs[ iid ].randomFontSize / 5 ) );
        
        anitArgs[ iid ].randomSpeed =
            Math.floor( Math.random() * ( maxTime - minTime + 1 ) + minTime );
        
        anitArgs[ iid ].randomDirection = Math.floor( ( Math.random() * 2 ) + 1 );
        
        callback();
    }

    
    
    /**
     * @summary Sets the background and the height of the plugin.
     *
     * @since 0.1.0
     *
     * @var   int     randomFreq  Time until next timeout.
     * @var   int     visibleTags  The number of currently visible tags.
     *
     * @param string  id  The unique ID of the current instance.
     * @param string  selector  The plugin selector name - #id.
    */
    function anitAnimate( iid ) {
        var randomFreq;
        
        anitRandomValues( iid, function(){
            if ( anitArgs[ iid ].visibleTags < parseInt( opts.density ) ) {
                anitAnimateTag( iid );
            }
        });
        

		randomFreq = Math.floor((Math.random() * 3000) + 350);
		if ( opts.density > 8 ) {
			randomFreq = Math.floor((Math.random() * 2500) + 250);
		}
		if ( opts.density > 20 ) {
			randomFreq = Math.floor((Math.random() * 500) + 50);
		}
		window.setTimeout(function() { 
			anitAnimate( iid )
		}, randomFreq );
     
    }





    /**
     * @summary Sets the random values needed for the animation.
     *
     * @since 0.1.0
     *
     * @var   string  randomTerm  The term to be animated.
     * @var   string  randomFontSize.
     *
     * @param string  id  The unique ID of the current instance.
     * @param string  selector  The plugin selector name - #id.
     * @param string  callback  The callback function.
    */
    function anitAnimateTag( iid ) {
        var termID, termH, cur, curW, cloudW, startX, endX;
		let frame =
			$( anitArgs[ iid ].selector + ' .actus-anit-cloud' );
		cloudW = frame.width();

        if ( typeof( anitArgs[ iid ].randomTerm ) !== 'undefined' ) {
        if ( typeof( anitArgs[ iid ].randomTerm.name ) !== 'undefined' ) {
			
			// increase visible tags count
            anitArgs[ iid ].visibleTags =
				anitArgs[ iid ].visibleTags + 1;
            
			// create element
            termID = 'T-' + $.now();
            termH  = '<a href="' + site_url + '/tag/' + anitArgs[ iid ].randomTerm.name + '" id="' + termID + '" class="actus-animated-tag">' + actusUpper( anitArgs[ iid ].randomTerm.name ) + '</a>';
			
			// append element
			cur = $( termH ).appendTo( frame )


			// set Font size
            $( '#' + termID ).css({
				'color': anitCurrent.color,
				'font-size': anitArgs[ iid ].randomFontSize + 'px',
				//'transform': 'unset'
			});
			
			// set start and end position
            curW   = cur.width();
            startX = 0 - curW;
            endX   = cloudW;
            if ( anitArgs[ iid ].randomDirection == 2 ) {
                startX = cloudW;
                endX   = 0 - curW - 20;
            }
			
			// set Starting position
            $( '#' + termID ).css({ 
                'transform': 'translateX(' + startX + 'px)',
                'top'      : anitArgs[ iid ].randomVpos,
                'opacity'  : anitArgs[ iid ].randomOpacity,
				'-webkit-transition' : 'unset',
				'transition' : 'unset',
            });


			// animate to End position
            window.setTimeout( function( tagID ) {
                $( '#' + tagID )
					.css({
						'-webkit-transition': 'transform ' + anitArgs[ iid ].randomSpeed + 'ms linear',
						'transition'        : 'transform ' + anitArgs[ iid ].randomSpeed + 'ms linear',
						'transform'         : 'translateX(' + endX + 'px)'
					})
					.delay( anitArgs[ iid ].randomSpeed )
					.queue(function() {
						// Animation End
					
						$( this ).remove();
						anitArgs[ iid ].visibleTags = anitArgs[ iid ].visibleTags - 1;
						if ( anitArgs[ iid ].visibleTags < 0 ) {
							anitArgs[ iid ].visibleTags = 0;
						}
					});
            }, 100, termID );

        }
        }
    }








    /**
     * @summary Converts a string to uppercase removing accents on Greek alphabet.
     *
     * @since 0.1.0
     *
     * @param  string  string  The string to be converted.
     *
     * @return string  string  The converted string.
    */
    function actusUpper( string ) {
    // **************************************************************
        string = string.replace(/[ΰ]/g,"ϋ");
        string = string.replace(/[ΐ]/g,"ϊ");
        string = string.toUpperCase();
        string = string.replace(/[Ά]/g,"Α");
        string = string.replace(/[Έ]/g,"Ε");
        string = string.replace(/[Ή]/g,"Η");
        string = string.replace(/[Ύ]/g,"Υ");
        string = string.replace(/[Ώ]/g,"Ω");
        string = string.replace(/[Ί]/g,"Ι");
        string = string.replace(/[Ό]/g,"Ο");

        return string;
    }   


