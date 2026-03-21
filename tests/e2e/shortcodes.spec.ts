/**
 * Shortcode rendering E2E tests.
 *
 * Verifies that the key WPCM shortcodes render correctly on frontend pages
 * using the fixture pages created in global-setup.ts.
 */

import { test, expect } from '@playwright/test';
import { getTestState, assertNoPhpErrors } from './utils';

test.describe( 'Shortcodes', () => {
	let matchListPageUrl: string;
	let leagueTablePageUrl: string;

	test.beforeAll( () => {
		const state = getTestState();
		matchListPageUrl = state.matchListPageUrl;
		leagueTablePageUrl = state.leagueTablePageUrl;
	} );

	// -----------------------------------------------------------------------
	// [match_list]
	// -----------------------------------------------------------------------

	test( '[match_list] page loads with 200 status', async ( { page } ) => {
		const response = await page.goto( matchListPageUrl );
		expect( response?.status() ).toBe( 200 );
	} );

	test( '[match_list] page has no PHP errors', async ( { page } ) => {
		await page.goto( matchListPageUrl );
		await assertNoPhpErrors( page );
	} );

	test( '[match_list] renders shortcode wrapper element', async ( { page } ) => {
		await page.goto( matchListPageUrl );
		const wrapper = page.locator( '.wpcm-shortcode-wrapper' );
		await expect( wrapper ).toBeVisible();
	} );

	test( '[match_list] does not render raw shortcode tag', async ( { page } ) => {
		await page.goto( matchListPageUrl );
		const content = await page.content();
		expect( content ).not.toContain( '[match_list]' );
	} );

	// -----------------------------------------------------------------------
	// [league_table]
	// -----------------------------------------------------------------------

	test( '[league_table] page loads with 200 status', async ( { page } ) => {
		const response = await page.goto( leagueTablePageUrl );
		expect( response?.status() ).toBe( 200 );
	} );

	test( '[league_table] page has no PHP errors', async ( { page } ) => {
		await page.goto( leagueTablePageUrl );
		await assertNoPhpErrors( page );
	} );

	test( '[league_table] renders shortcode wrapper element', async ( { page } ) => {
		await page.goto( leagueTablePageUrl );
		const wrapper = page.locator( '.wpcm-shortcode-wrapper' );
		await expect( wrapper ).toBeVisible();
	} );

	test( '[league_table] does not render raw shortcode tag', async ( { page } ) => {
		await page.goto( leagueTablePageUrl );
		const content = await page.content();
		expect( content ).not.toContain( '[league_table]' );
	} );

} );
