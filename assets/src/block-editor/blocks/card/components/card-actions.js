/**
 * Internal dependencies
 */
import CardActionsEdit from './card-actions-edit';
import CardActionButton from './card-action-button';

/**
 * Card Actions Component.
 *
 * @param {Object} props - Component props.
 * @param {string} props.primaryActionButtonLabel - Primary action button label.
 * @param {string} props.primaryActionButtonUrl - Primary action button URL.
 * @param {boolean} props.primaryActionButtonNewTab - Whether or not the primary action button url should open in a new tab.
 * @param {boolean} props.primaryActionButtonNoFollow - Whether or not the primary action button url rel property should be noFollow.
 * @param {string} props.secondaryActionButtonLabel - Secondary action button label.
 * @param {string} props.secondaryActionButtonUrl - Secondary action button URL.
 * @param {boolean} props.secondaryActionButtonNewTab - Whether or not the secondary action button url should open in a new tab.
 * @param {boolean} props.secondaryActionButtonNoFollow - Whether or not the secondary action button url rel property should be noFollow.
 * @param {boolean} props.displaySecondaryActionButton - Whether or not to show the secondary action button.
 * @param {number} props.cardIndex - Card index
 * @param {Function} props.setter - Block attribute setter.
 * @param {boolean} props.isEditMode - Whether or not the edit mode is enabled.
 *
 * @return {Function} Function returning the HTML markup for the component.
 */
const CardActions = ( {
	primaryActionButtonLabel,
	primaryActionButtonUrl,
	primaryActionButtonNewTab,
	primaryActionButtonNoFollow,
	secondaryActionButtonLabel,
	secondaryActionButtonUrl,
	secondaryActionButtonNewTab,
	secondaryActionButtonNoFollow,
	displaySecondaryActionButton,
	cardIndex,
	setter,
	isEditMode,
} ) => {
	const cardActionsEditProps = {
		primaryActionButtonLabel,
		primaryActionButtonUrl,
		primaryActionButtonNewTab,
		primaryActionButtonNoFollow,
		secondaryActionButtonLabel,
		secondaryActionButtonUrl,
		secondaryActionButtonNewTab,
		secondaryActionButtonNoFollow,
		displaySecondaryActionButton,
		cardIndex,
		setter,
	};

	return (
		<>
			{ isEditMode ? (
				<CardActionsEdit { ...cardActionsEditProps } />
			) : (
				<div className="mdc-card__actions">
					<div className="mdc-card__action-buttons">
						<CardActionButton
							label={ primaryActionButtonLabel }
							url={ primaryActionButtonUrl }
							newTab={ primaryActionButtonNewTab }
							noFollow={ primaryActionButtonNoFollow }
							isEditMode={ false }
						/>
						{ displaySecondaryActionButton && (
							<CardActionButton
								label={ secondaryActionButtonLabel }
								url={ secondaryActionButtonUrl }
								newTab={ secondaryActionButtonNewTab }
								noFollow={ secondaryActionButtonNoFollow }
								isEditMode={ false }
							/>
						) }
					</div>
				</div>
			) }
		</>
	);
};

export default CardActions;