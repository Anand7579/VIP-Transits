/**
 * VIP Border section — editor (InnerBlocks).
 */
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.blocks || ! wp.blockEditor || ! wp.element ) {
		return;
	}

	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var ToggleControl = wp.components.ToggleControl;
	var createElement = wp.element.createElement;

	registerBlockType( 'tenku-child/vip-border-section', {
		apiVersion: 3,
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var surface = !! attributes.surface;

			var blockProps = useBlockProps( {
				className:
					'vip-border-section' + ( surface ? ' vip-border-section--surface' : '' ),
			} );

			return createElement(
				wp.element.Fragment,
				null,
				createElement(
					InspectorControls,
					null,
					createElement(
						PanelBody,
						{ title: 'Section style', initialOpen: true },
						createElement( ToggleControl, {
							label: 'Grey background',
							checked: surface,
							onChange: function ( value ) {
								setAttributes( { surface: value } );
							},
						} )
					)
				),
				createElement(
					'section',
					blockProps,
					createElement(
						'div',
						{ className: 'vip-content-container' },
						createElement(
							'div',
							{ className: 'vip-border-section__frame' },
							createElement(
								'div',
								{ className: 'vip-border-section__content' },
								createElement( InnerBlocks, {
								template: [
									[
										'core/heading',
										{
											level: 2,
											placeholder: 'Section heading',
										},
									],
									[
										'core/paragraph',
										{
											placeholder: 'Add content…',
										},
									],
								],
								templateLock: false,
								} )
							)
						)
					)
				)
			);
		},
		save: function () {
			return createElement( InnerBlocks.Content );
		},
	} );
} )( window.wp );
