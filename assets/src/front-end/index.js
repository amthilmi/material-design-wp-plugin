/* global addEventListener */
/* istanbul ignore file */

/**
 * External dependencies
 */
import { MDCRipple } from '@material/ripple';
import { MDCTabBar } from '@material/tab-bar';

/**
 * Internal dependencies
 */
import '../block-editor/blocks/hello-world/save.css';

const initButtons = () => {
	const buttons = document.querySelectorAll(
		'.mdc-button, .mdc-card__primary-action'
	);

	for ( const button of buttons ) {
		new MDCRipple( button );
	}
};

const initTabBar = () => {
	const tabBars = document.querySelectorAll( '.mdc-tab-bar-container' );

	tabBars.forEach( tabBarContainer => {
		const tabBar = new MDCTabBar(
			tabBarContainer.querySelector( '.mdc-tab-bar' )
		);

		const contentElements = tabBarContainer.querySelectorAll(
			'.mdc-tab-content'
		);

		tabBar.listen( 'MDCTabBar:activated', event => {
			tabBarContainer
				.querySelector( '.mdc-tab-content--active' )
				.classList.remove( 'mdc-tab-content--active' );

			contentElements[ event.detail.index ].classList.add(
				'mdc-tab-content--active'
			);
		} );
	} );
};

addEventListener( 'DOMContentLoaded', () => {
	initButtons();
	initTabBar();
} );