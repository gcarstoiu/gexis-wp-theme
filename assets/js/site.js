/* gexis — the only front-end script, and it is optional: without it the
   page follows the OS colour mode and everything else still works.

   - The colour-mode switch: flips <html data-theme> and remembers the choice
     in localStorage['gexis-theme']. The inline head script applies it before
     first paint on the next page.
   - Toasts raised from the page (data-gexis-toast, e.g. "PDF downloaded"),
     shown in the footer's toast host for 2.4s. */
( function () {
	var root = document.documentElement;

	document.addEventListener( 'click', function ( e ) {
		var toggle = e.target.closest( '[data-gexis-theme-toggle]' );
		if ( toggle ) {
			var next = root.getAttribute( 'data-theme' ) === 'light' ? 'dark' : 'light';
			root.setAttribute( 'data-theme', next );
			try {
				localStorage.setItem( 'gexis-theme', next );
			} catch ( err ) {}
			return;
		}

		var raiser = e.target.closest( '[data-gexis-toast]' );
		if ( raiser ) {
			toast( raiser.getAttribute( 'data-gexis-toast' ) );
		}
	} );

	var timer;
	function toast( message ) {
		var host = document.querySelector( '.gx-toast' );
		if ( ! host || ! message ) {
			return;
		}
		clearTimeout( timer );
		host.textContent = message;
		host.hidden = false;
		/* restart the CSS animation */
		host.classList.remove( 'is-live' );
		void host.offsetWidth;
		host.classList.add( 'is-live' );
		timer = setTimeout( function () {
			host.hidden = true;
			host.classList.remove( 'is-live' );
		}, 2400 );
	}
} )();
