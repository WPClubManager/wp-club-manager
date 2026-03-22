/**
 * Single club page E2E tests.
 *
 * Verifies the single-club template renders correctly using the fixture
 * club created in global-setup.ts.
 */

import { test, expect } from '@playwright/test';
import { getTestState, assertNoPhpErrors } from './utils';

test.describe( 'Single Club page', () => {
	let homeClubUrl: string;

	test.beforeAll( () => {
		const state = getTestState();
		homeClubUrl = state.homeClubUrl;
	} );

	test( 'single club page loads with 200 status', async ( { page } ) => {
		const response = await page.goto( homeClubUrl );
		expect( response?.status() ).toBe( 200 );
	} );

	test( 'single club page has no PHP errors', async ( { page } ) => {
		await page.goto( homeClubUrl );
		await assertNoPhpErrors( page );
	} );

	test( 'single club page has body class wpcm_club', async ( { page } ) => {
		await page.goto( homeClubUrl );
		const bodyClass = await page.getAttribute( 'body', 'class' );
		expect( bodyClass ).toContain( 'single-wpcm_club' );
	} );

	test( 'club name is present in page', async ( { page } ) => {
		await page.goto( homeClubUrl );
		const content = await page.content();
		expect( content ).toContain( 'E2E Home FC' );
	} );

	test( 'single club page title is not empty', async ( { page } ) => {
		await page.goto( homeClubUrl );
		const title = await page.title();
		expect( title ).toBeTruthy();
	} );

} );
