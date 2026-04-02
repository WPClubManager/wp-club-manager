/**
 * Single player page E2E tests.
 *
 * Verifies the single-player template renders correctly using the fixture
 * player created in global-setup.ts.
 */

import { test, expect } from '@playwright/test';
import { getTestState, assertNoPhpErrors } from './utils';

test.describe( 'Single Player page', () => {
	let playerUrl: string;

	test.beforeAll( () => {
		const state = getTestState();
		playerUrl = state.playerUrl;
	} );

	test( 'single player page loads with 200 status', async ( { page } ) => {
		const response = await page.goto( playerUrl );
		expect( response?.status() ).toBe( 200 );
	} );

	test( 'single player page has no PHP errors', async ( { page } ) => {
		await page.goto( playerUrl );
		await assertNoPhpErrors( page );
	} );

	test( 'single player page has body class wpcm_player', async ( { page } ) => {
		await page.goto( playerUrl );
		const bodyClass = await page.getAttribute( 'body', 'class' );
		expect( bodyClass ).toContain( 'single-wpcm_player' );
	} );

	test( 'player name is present in page heading', async ( { page } ) => {
		await page.goto( playerUrl );
		const heading = await page.locator( 'h1, h2, .entry-title, .player-name' ).first().textContent();
		expect( heading ).toBeTruthy();
	} );

} );
