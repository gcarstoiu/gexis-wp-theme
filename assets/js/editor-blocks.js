/* Editor side of the gexis/* blocks. Every one is rendered on the server, so
   one generic definition serves them all: a server-side preview, plus
   sidebar controls built from each block.json's attributes (label, enum
   with options, min/max). No build step. */
( function ( wp, blocks ) {
	if ( ! wp || ! blocks ) {
		return;
	}
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var C = wp.components;
	var ServerSideRender = wp.serverSideRender;

	function control( key, def, value, set ) {
		var label = def.label || key;
		var common = { key: key, label: label, __nextHasNoMarginBottom: true };
		if ( def.type === 'boolean' ) {
			return el( C.ToggleControl, Object.assign( common, { checked: !! value, onChange: function ( v ) { set( key, v ); } } ) );
		}
		if ( def.enum ) {
			return el( C.SelectControl, Object.assign( common, {
				value: value,
				options: def.enum.map( function ( v, i ) {
					return { value: v, label: ( def.options && def.options[ i ] ) || v || '—' };
				} ),
				onChange: function ( v ) { set( key, v ); },
			} ) );
		}
		if ( def.type === 'number' ) {
			return el( C.RangeControl, Object.assign( common, {
				value: value, min: def.min || 0, max: def.max || 100,
				onChange: function ( v ) { set( key, v ); },
			} ) );
		}
		return el( C.TextControl, Object.assign( common, { value: value || '', onChange: function ( v ) { set( key, v ); } } ) );
	}

	blocks.forEach( function ( b ) {
		wp.blocks.registerBlockType( b.name, {
			edit: function ( props ) {
				var attrs = b.attributes || {};
				var keys = Object.keys( attrs ).filter( function ( k ) { return attrs[ k ].label; } );
				var set = function ( k, v ) { var o = {}; o[ k ] = v; props.setAttributes( o ); };
				return el( Fragment, null,
					keys.length && el( InspectorControls, null,
						el( C.PanelBody, { title: wp.i18n.__( 'Settings', 'gexis' ) },
							keys.map( function ( k ) { return control( k, attrs[ k ], props.attributes[ k ], set ); } )
						)
					),
					el( 'div', useBlockProps(),
						el( C.Disabled, null,
							el( ServerSideRender, {
								block: b.name,
								attributes: props.attributes,
								urlQueryArgs: props.context && props.context.postId ? { post_id: props.context.postId } : undefined,
							} )
						)
					)
				);
			},
			save: function () { return null; },
		} );
	} );
} )( window.wp, window.gexisBlocks );
