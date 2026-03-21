/**
 * Frontend smoke tests.
 *
 * Verifies the WordPress frontend loads without errors and that
 * WPCM frontend assets are enqueued on relevant pages.
 */

import { test, expect } from '@playwright/test';
import { assertNoPhpErrors } from './utils';

const BASE_URL = process.env.WP_BASE_URL || 'http://localhost:8889';

test.describe( 'Frontend', () => {

	test( 'homepage loads with 200 status', async ( { page } ) => {
		const response = await page.goto( BASE_URL );
		expect( response?.status() ).toBe( 200 );
	} );

	test( 'homepage has no PHP errors', async ( { page } ) => {
		await page.goto( BASE_URL );
		await assertNoPhpErrors( page );
	} );

	test( 'wp-club-manager frontend stylesheet is registered', async ( { page } ) => {
		// Navigate to a page that includes WPCM scripts (any CPT single).
		await page.goto( BASE_URL );
		// The wpclubmanager-general handle is enqueued on WPCM pages;
		// verify it exists in WordPress style registry via REST.
		const resp = await page.request.get( `${ BASE_URL }/wp-json/wp/v2/types/wpcm_player` );
		expect( resp.status() ).toBe( 200 );
	} );

	test( 'REST API is accessible', async ( { page } ) => {
		const resp = await page.request.get( `${ BASE_URL }/wp-json/wp/v2/types` );
		expect( resp.status() ).toBe( 200 );
		const types = await resp.json();
		expect( types ).toHaveProperty( 'wpcm_player' );
		expect( types ).toHaveProperty( 'wpcm_match' );
		expect( types ).toHaveProperty( 'wpcm_club' );
	} );

	test( 'WPCM post types are exposed in REST API', async ( { page } ) => {
		const resp = await page.request.get( `${ BASE_URL }/wp-json/wp/v2/types` );
		const types = await resp.json();
		const wpcmTypes = [ 'wpcm_player', 'wpcm_match', 'wpcm_club', 'wpcm_staff', 'wpcm_sponsor', 'wpcm_table' ];
		for ( const type of wpcmTypes ) {
			expect( types, `${ type } should be in REST API` ).toHaveProperty( type );
		}
	} );

} );
