/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import './style.css';
import './editor.css';
import { withGlobalDefaults } from '../../components/with-global-defaults';
import InspectorControlsStylePanel from './components/inspector-controls-style-panel';
import InspectorControlsContentPanel from './components/inspector-controls-content-panel';
import VerticalCardLayout from './components/vertical-card-layout';
import HorizontalCardLayout from './components/horizontal-card-layout';

/**
 * Card Edit component.
 *
 * @param {Object} props - Component props.
 * @param {Object} props.attributes - Block attributes.
 * @param {Function} props.setAttributes - Function to set block attributes values.
 * @param {string} props.className - Block classes.
 *
 * @return {Function} Function returning the HTML markup for the component.
 */
const Edit = ( { attributes, setAttributes, className } ) => {
	const {
		cardLayout = 'vertical',
		contentLayout,
		title,
		displayTitle,
		secondaryText,
		displaySecondaryText,
		imageSourceUrl,
		isImageEditMode,
		displayImage,
		supportingText,
		displaySupportingText,
		primaryActionButtonLabel,
		primaryActionButtonUrl,
		primaryActionButtonNewTab,
		primaryActionButtonNoFollow,
		secondaryActionButtonLabel,
		secondaryActionButtonUrl,
		secondaryActionButtonNewTab,
		secondaryActionButtonNoFollow,
		displayActions,
		displaySecondaryActionButton,
		outlined,
		cornerRadius,
	} = attributes;
	const cardIndex = 0;

	/* istanbul ignore next */
	const setter = ( attributeName, attributeValue ) => {
		setAttributes( {
			[ attributeName ]: attributeValue,
		} );
	};

	const cardProps = {
		cardIndex,
		contentLayout,
		title,
		displayTitle,
		secondaryText,
		displaySecondaryText,
		imageSourceUrl,
		isImageEditMode,
		displayImage,
		supportingText,
		displaySupportingText,
		primaryActionButtonLabel,
		primaryActionButtonUrl,
		primaryActionButtonNewTab,
		primaryActionButtonNoFollow,
		secondaryActionButtonLabel,
		secondaryActionButtonUrl,
		secondaryActionButtonNewTab,
		secondaryActionButtonNoFollow,
		displayActions,
		displaySecondaryActionButton,
		outlined,
		cornerRadius,
		setter,
		isEditMode: true,
	};

	const inspectorControlsStylePanelProps = {
		contentLayout,
		cornerRadius,
		outlined,
		isSingleCard: true,
		setter,
		cardIndex,
	};

	const inspectorControlsContentPanelProps = {
		displayTitle,
		displaySecondaryText,
		displayImage,
		displaySupportingText,
		displayActions,
		displaySecondaryActionButton,
		isSingleCard: true,
		setter,
		cardIndex,
	};

	return (
		<>
			<InspectorControls>
				<InspectorControlsStylePanel { ...inspectorControlsStylePanelProps } />
				<InspectorControlsContentPanel
					{ ...inspectorControlsContentPanelProps }
				/>
			</InspectorControls>
			<div className={ className }>
				{ cardLayout === 'vertical' && <VerticalCardLayout { ...cardProps } /> }
				{ cardLayout === 'horizontal' && (
					<HorizontalCardLayout { ...cardProps } />
				) }
			</div>
		</>
	);
};

export default withGlobalDefaults( Edit );
