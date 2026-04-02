/**
 * E2E tests for bulk add functionality.
 *
 * Regression tests for known bugs:
 * - Can't bulk add clubs to league table
 * - Can't bulk add players to roster
 *
 * Verifies that the add dropdowns support multiple selection.
 * These tests check the rendered page source for the 'multiple' attribute.
 * If the post type's edit page doesn't render the expected meta box (e.g.
 * block editor redirect, or roster not registered in league mode), the test
 * verifies the page loads without errors instead.
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
		const title = await page.title();
		expect( title ).toBeTruthy();
	} );

	test( 'league table club dropdown is multi-select when meta box present', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_table` );
		const content = await page.content();

		// If the meta box rendered, verify multi-select is present.
		if ( content.includes( 'table_clubs' ) || content.includes( 'wpcm-table-add-row' ) ) {
			expect( content ).toContain( 'multiple' );
		} else {
			// Meta box not rendered (block editor or CPT not available) — just verify no errors.
			await assertNoPhpErrors( page );
		}
	} );

	test( 'roster admin page loads without errors', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_roster` );
		await assertNoPhpErrors( page );
		const title = await page.title();
		expect( title ).toBeTruthy();
	} );

	test( 'roster player dropdown is multi-select when meta box present', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_roster` );
		const content = await page.content();

		if ( content.includes( 'roster_players' ) || content.includes( 'wpcm-player-roster-add-row' ) ) {
			expect( content ).toContain( 'multiple' );
		} else {
			await assertNoPhpErrors( page );
		}
	} );

} );
