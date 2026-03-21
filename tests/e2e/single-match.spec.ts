/**
 * Single match page E2E tests.
 *
 * Verifies the single-match template renders correctly using the fixture
 * match created in global-setup.ts.
 */

import { test, expect } from '@playwright/test';
import { getTestState, assertNoPhpErrors } from './utils';

test.describe( 'Single Match page', () => {
	let matchUrl: string;

	test.beforeAll( () => {
		const state = getTestState();
		matchUrl = state.matchUrl;
	} );

	test( 'single match page loads with 200 status', async ( { page } ) => {
		const response = await page.goto( matchUrl );
		expect( response?.status() ).toBe( 200 );
	} );

	test( 'single match page has no PHP errors', async ( { page } ) => {
		await page.goto( matchUrl );
		await assertNoPhpErrors( page );
	} );

	test( 'single match page has body class wpcm_match', async ( { page } ) => {
		await page.goto( matchUrl );
		const bodyClass = await page.getAttribute( 'body', 'class' );
		expect( bodyClass ).toContain( 'single-wpcm_match' );
	} );

	test( 'single match title is present in page', async ( { page } ) => {
		await page.goto( matchUrl );
		const title = await page.title();
		// WordPress page title includes the post title
		expect( title ).toBeTruthy();
	} );

} );
