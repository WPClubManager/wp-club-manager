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

	test( 'league table add club dropdown supports multiple selection', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_table` );
		await assertNoPhpErrors( page );

		// The club dropdown should have the multiple attribute.
		const dropdown = page.locator( 'select#id, select[name="table_clubs"]' ).first();
		const isMultiple = await dropdown.getAttribute( 'multiple' );
		expect( isMultiple ).not.toBeNull();
	} );

	test( 'roster add player dropdown supports multiple selection', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_roster` );
		await assertNoPhpErrors( page );

		// The player dropdown should have the multiple attribute.
		const dropdown = page.locator( 'select.player-id, select[name="roster_players"]' ).first();
		const isMultiple = await dropdown.getAttribute( 'multiple' );
		expect( isMultiple ).not.toBeNull();
	} );

} );
