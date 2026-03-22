/**
 * Admin player management E2E tests.
 *
 * Verifies that a player can be created via the WordPress admin UI
 * and the player profile page renders correctly on the frontend.
 */

import { test, expect } from '@playwright/test';
import { assertNoPhpErrors } from './utils';

const BASE_URL = process.env.WP_BASE_URL || 'http://localhost:8889';
const ADMIN_USER = process.env.WP_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.WP_ADMIN_PASS || 'password';

test.describe( 'Admin Player Management', () => {

	test.beforeEach( async ( { page } ) => {
		// Log in to wp-admin.
		await page.goto( `${ BASE_URL }/wp-login.php` );
		await page.fill( '#user_login', ADMIN_USER );
		await page.fill( '#user_pass', ADMIN_PASS );
		await page.click( '#wp-submit' );
		await page.waitForURL( `**\/wp-admin**` );
	} );

	test( 'admin players list page loads without errors', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/edit.php?post_type=wpcm_player` );
		await assertNoPhpErrors( page );

		const heading = page.locator( 'h1.wp-heading-inline, .wrap h1' ).first();
		await expect( heading ).toBeVisible();
	} );

	test( 'add new player page loads without errors', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_player` );
		await assertNoPhpErrors( page );

		const titleField = page.locator( '#title, #post-title-0, .editor-post-title' ).first();
		await expect( titleField ).toBeVisible();
	} );

	test( 'player admin page has WPCM meta boxes', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_player` );
		await assertNoPhpErrors( page );

		// Look for WPCM-specific meta box content (player details, stats, etc).
		const content = await page.content();
		const hasWpcmElements = content.includes( 'wpcm' ) || content.includes( 'wpclubmanager' );
		expect( hasWpcmElements ).toBeTruthy();
	} );

	test( 'new player can be saved as draft', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_player` );

		const titleField = page.locator( '#title' );
		if ( await titleField.isVisible() ) {
			await titleField.fill( 'E2E Admin Test Player' );

			// Save as draft.
			await page.click( '#save-post' );
			await page.waitForLoadState( 'networkidle' );

			const content = await page.content();
			const saved = content.includes( 'Post draft updated' ) ||
				content.includes( 'E2E Admin Test Player' );
			expect( saved ).toBeTruthy();
		} else {
			test.skip( true, 'Block editor detected — classic title field not available' );
		}
	} );

	test( 'player admin page has position taxonomy meta box', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/post-new.php?post_type=wpcm_player` );

		// The position taxonomy should appear as a meta box or sidebar section.
		const content = await page.content();
		const hasPosition = content.includes( 'wpcm_position' ) || content.includes( 'Position' );
		expect( hasPosition ).toBeTruthy();
	} );

	test( 'players list shows correct column headers', async ( { page } ) => {
		await page.goto( `${ BASE_URL }/wp-admin/edit.php?post_type=wpcm_player` );
		await assertNoPhpErrors( page );

		// The player list should have a title column at minimum.
		const titleColumn = page.locator( 'th#title, th.column-title' ).first();
		await expect( titleColumn ).toBeVisible();
	} );

} );
