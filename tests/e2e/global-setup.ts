/**
 * Global E2E test setup.
 *
 * Creates fixture content via the WordPress REST API using a logged-in
 * browser session (cookies) so no Application Passwords are needed.
 * Stores created IDs in a shared state file for individual test suites.
 */

import { chromium } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';

const BASE_URL = process.env.WP_BASE_URL || 'http://localhost:8889';
const ADMIN_USER = process.env.WP_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.WP_ADMIN_PASS || 'password';
const STATE_FILE = path.join( __dirname, '.test-state.json' );

export default async function globalSetup() {
	const browser = await chromium.launch();
	const context = await browser.newContext();
	const page = await context.newPage();

	// Log in to get an authenticated session.
	await page.goto( `${ BASE_URL }/wp-login.php` );
	await page.fill( '#user_login', ADMIN_USER );
	await page.fill( '#user_pass', ADMIN_PASS );
	await page.click( '#wp-submit' );
	await page.waitForURL( `**\/wp-admin**` );

	// Use page.request (shares session cookies) for REST API calls.
	async function restPost( endpoint: string, body: object ) {
		// Get a nonce for the REST API.
		const nonce = await page.evaluate( async ( url ) => {
			const resp = await fetch( url, { credentials: 'include' } );
			return resp.headers.get( 'X-WP-Nonce' ) || '';
		}, `${ BASE_URL }/wp-json/wp/v2/types` );

		const resp = await page.request.post( `${ BASE_URL }/wp-json/wp/v2/${ endpoint }`, {
			data: body,
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': nonce,
			},
		} );

		if ( ! resp.ok() ) {
			throw new Error( `REST POST to ${ endpoint } failed: ${ resp.status() } ${ await resp.text() }` );
		}
		return resp.json();
	}

	// -----------------------------------------------------------------------
	// Create fixture content
	// -----------------------------------------------------------------------

	const homeClub = await restPost( 'wpcm_club', { title: 'E2E Home FC', status: 'publish' } );
	const awayClub = await restPost( 'wpcm_club', { title: 'E2E Away United', status: 'publish' } );
	const player   = await restPost( 'wpcm_player', { title: 'E2E Test Player', status: 'publish' } );
	const match    = await restPost( 'wpcm_match', { title: 'E2E Home FC vs E2E Away United', status: 'publish' } );

	const matchListPage = await restPost( 'pages', {
		title: 'E2E Match List',
		content: '[match_list]',
		status: 'publish',
		slug: 'e2e-match-list',
	} );

	const leagueTablePage = await restPost( 'pages', {
		title: 'E2E League Table',
		content: '[league_table]',
		status: 'publish',
		slug: 'e2e-league-table',
	} );

	await browser.close();

	// -----------------------------------------------------------------------
	// Persist IDs for test suites
	// -----------------------------------------------------------------------
	const state = {
		homeClubId: homeClub.id,
		homeClubUrl: homeClub.link,
		awayClubId: awayClub.id,
		playerId: player.id,
		playerUrl: player.link,
		matchId: match.id,
		matchUrl: match.link,
		matchListPageUrl: matchListPage.link,
		leagueTablePageUrl: leagueTablePage.link,
	};

	fs.writeFileSync( STATE_FILE, JSON.stringify( state, null, 2 ) );
}
