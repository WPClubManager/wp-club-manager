/**
 * E2E tests for bulk add functionality.
 *
 * Regression tests for known bugs:
 * - Can't bulk add clubs to league table
 * - Can't bulk add players to roster
 *
 * Verifies that the add dropdowns support multiple selection.
 */

import { test, expect } from '@playwright/test';
import { assertNoPhpErrors } from './utils';

const BASE_URL = process.env.WP_BASE_URL || 'http://localhost:8889';
const ADMIN_USER = process.env.WP_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.WP_ADMIN_PASS || 'password';

test.describe( 'Bulk Add', () => {

	test.beforeEach( async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-login.php` );
		await page.fill( '#user_login', ADMIN_USER );
		await page.fill( '#user_pass', ADMIN_PASS );
		await page.click( '#wp-submit' );
		await page.waitForURL( `**\/wp-admin**` );
	} );

	test( 'league table admin page loads without errors', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_table` );
		await assertNoPhpErrors( page );

		// Check that the page has WPCM content.
		const content = await page.content();
		const hasWpcm = content.includes( 'wpcm' ) || content.includes( 'table_clubs' );
		expect( hasWpcm ).toBeTruthy();
	} );

	test( 'league table club dropdown has multiple attribute in source', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_table` );

		// Check the raw HTML source for a select with multiple.
		const content = await page.content();
		const hasMultipleSelect = content.includes( 'multiple' ) && content.includes( 'table_clubs' );
		expect( hasMultipleSelect ).toBeTruthy();
	} );

	test( 'roster admin page loads without errors', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_roster` );
		await assertNoPhpErrors( page );

		const content = await page.content();
		const hasWpcm = content.includes( 'wpcm' ) || content.includes( 'roster_players' );
		expect( hasWpcm ).toBeTruthy();
	} );

	test( 'roster player dropdown has multiple attribute in source', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_roster` );

		// Check the raw HTML source for a select with multiple.
		const content = await page.content();
		const hasMultipleSelect = content.includes( 'multiple' ) && content.includes( 'roster_players' );
		expect( hasMultipleSelect ).toBeTruthy();
	} );

} );
