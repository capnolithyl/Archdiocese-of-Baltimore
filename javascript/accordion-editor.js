/**
 * Add block-level section heading fields to the native Accordion.
 * Server-registered attributes are saved in the block comment, not extra HTML.
 */
const { createElement: el, Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;
const { __ } = wp.i18n;

wp.hooks.addFilter('editor.BlockEdit', 'baltimore/accordion-section', (BlockEdit) => {
	return function BaltimoreAccordionEdit(props) {
		if (props.name !== 'core/accordion') return el(BlockEdit, props);
		const { attributes, setAttributes, isSelected } = props;
		const eyebrow = attributes.baltEyebrow || '';
		const heading = attributes.baltHeading || '';
		const header = eyebrow.trim() || heading.trim()
			? el('header', { className: 'balt-accordion-section__header' },
				eyebrow.trim() && el('p', { className: 'balt-accordion-section__eyebrow' }, eyebrow),
				heading.trim() && el('h2', { className: 'balt-accordion-section__heading' }, heading))
			: null;
		return el(Fragment, null,
			isSelected && el(InspectorControls, null,
				el(PanelBody, { title: __('Section Heading', 'baltimore'), initialOpen: true },
					el(TextControl, {
						label: __('Eyebrow', 'baltimore'), value: eyebrow,
						onChange: (value) => setAttributes({ baltEyebrow: value }),
						help: __('Optional small line above the heading.', 'baltimore'),
					}),
					el(TextControl, {
						label: __('Heading', 'baltimore'), value: heading,
						onChange: (value) => setAttributes({ baltHeading: value }),
						help: __('Optional section title, separate from the accordion questions.', 'baltimore'),
					}))),
			el('div', { className: 'balt-accordion-section balt-accordion-section--editor' },
				el('div', { className: 'balt-accordion-section__inner' }, header, el(BlockEdit, props)))
		);
	};
});
