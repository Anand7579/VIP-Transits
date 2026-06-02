/**
 * VIP BG Black section — editor (InnerBlocks + button fields).
 */
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.blocks || ! wp.blockEditor || ! wp.element || ! wp.components ) {
		return;
	}

	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var createElement = wp.element.createElement;

	registerBlockType( 'tenku-child/vip-bg-black-section', {
		apiVersion: 3,
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;
			var buttonText = attributes.buttonText || '';
			var buttonUrl = attributes.buttonUrl || '';

			var blockProps = useBlockProps( {
				className: 'vip-bg-black-section',
			} );

			var buttonPreview = null;
			if ( buttonText && buttonUrl ) {
				buttonPreview = createElement(
					'p',
					{ className: 'vip-bg-black-section__button-wrap' },
					createElement(
						'a',
						{
							className: 'vip-bg-black-section__button',
							href: buttonUrl,
							onClick: function ( event ) {
								event.preventDefault();
							},
						},
						buttonText
					)
				);
			}

			return createElement(
				wp.element.Fragment,
				null,
				createElement(
					InspectorControls,
					null,
					createElement(
						PanelBody,
						{ title: 'Button', initialOpen: true },
						createElement( TextControl, {
							label: 'Button Text',
							value: buttonText,
							onChange: function ( value ) {
								setAttributes( { buttonText: value } );
							},
						} ),
						createElement( TextControl, {
							label: 'Button Link',
							value: buttonUrl,
							onChange: function ( value ) {
								setAttributes( { buttonUrl: value } );
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
							{ className: 'vip-bg-black-section__inner' },
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
											placeholder: 'Add description…',
										},
									],
								],
								templateLock: false,
							} )
						),
						createElement(
							'div',
							{ className: 'vip-bg-black-section__button-fields' },
							createElement(
								'p',
								{ className: 'vip-bg-black-section__fields-title' },
								'Button settings'
							),
							createElement( TextControl, {
								label: 'Button Text',
								value: buttonText,
								onChange: function ( value ) {
									setAttributes( { buttonText: value } );
								},
							} ),
							createElement( TextControl, {
								label: 'Button Link',
								value: buttonUrl,
								onChange: function ( value ) {
									setAttributes( { buttonUrl: value } );
								},
							} ),
							buttonPreview
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
