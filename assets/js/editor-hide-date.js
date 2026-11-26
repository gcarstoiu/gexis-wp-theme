/* The "Hide the date" switch in the post's Summary panel (inc/hide-date.php).
   No build step. */
( function ( wp ) {
	if ( ! wp || ! wp.plugins ) {
		return;
	}
	var el = wp.element.createElement;
	var Info = ( wp.editor && wp.editor.PluginPostStatusInfo ) || ( wp.editPost && wp.editPost.PluginPostStatusInfo );
	if ( ! Info ) {
		return;
	}

	function HideDate() {
		var meta = wp.data.useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		}, [] );
		var editPost = wp.data.useDispatch( 'core/editor' ).editPost;
		return el( Info, null,
			el( wp.components.ToggleControl, {
				label: wp.i18n.__( 'Hide the date', 'gexis' ),
				help: wp.i18n.__( 'On the post and in lists.', 'gexis' ),
				checked: !! meta.gexis_hide_date,
				onChange: function ( v ) { editPost( { meta: { gexis_hide_date: v } } ); },
				__nextHasNoMarginBottom: true,
			} )
		);
	}

	wp.plugins.registerPlugin( 'gexis-hide-date', { render: HideDate } );
} )( window.wp );
