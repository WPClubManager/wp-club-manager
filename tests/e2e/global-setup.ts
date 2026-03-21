/**
 * Global E2E test setup.
 *
 * Creates the fixture content used across all E2E test suites via the
 * WordPress REST API. Stores created IDs in a shared state file so
 * individual tests can reference them without re-creating content.
 */

import { chromium, request } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';

const BASE_URL = process.env.WP_BASE_URL || 'http://localhost:8889';
const ADMIN_USER = process.env.WP_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.WP_ADMIN_PASS || 'password';
const STATE_FILE = path.join( __dirname, '.test-state.json' );

async function restPost( context: any, endpoint: string, body: object ) {
	const resp = await context.post( `${ BASE_URL }/wp-json/wp/v2/${ endpoint }`, {
		data: body,
		headers: {
			'Content-Type': 'application/json',
		},
		failOnStatusCode: true,
	} );
	return resp.json();
}

export default async function globalSetup() {
	const context = await request.newContext( {
		httpCredentials: {
			username: ADMIN_USER,
			password: ADMIN_PASS,
		},
	} );

	// -----------------------------------------------------------------------
	// Activate plugin + set league mode via WP-CLI (already active via wp-env)
	// Set wpcm_mode option via REST options endpoint isn't available, so we
	// use the admin login cookie approach through the browser.
	// -----------------------------------------------------------------------
	const browser = await chromium.launch();
	const page = await browser.newPage();

	await page.goto( `${ BASE_URL }/wp-login.php` );
	await page.fill( '#user_login', ADMIN_USER );
	await page.fill( '#user_pass', ADMIN_PASS );
	await page.click( '#wp-submit' );
	await page.waitForURL( `**\/wp-admin**` );
	await browser.close();

	// -----------------------------------------------------------------------
	// Create fixture content via REST API
	// -----------------------------------------------------------------------

	// Home club
	const homeClub = await restPost( context, 'wpcm_club', {
		title: 'E2E Home FC',
		status: 'publish',
	} );

	// Away club
	const awayClub = await restPost( context, 'wpcm_club', {
		title: 'E2E Away United',
		status: 'publish',
	} );

	// Player
	const player = await restPost( context, 'wpcm_player', {
		title: 'E2E Test Player',
		status: 'publish',
	} );

	// Match
	const match = await restPost( context, 'wpcm_match', {
		title: `${ homeClub.title.rendered } vs ${ awayClub.title.rendered }`,
		status: 'publish',
	} );

	// Shortcode page — match_list
	const matchListPage = await restPost( context, 'pages', {
		title: 'E2E Match List',
		content: '[match_list]',
		status: 'publish',
		slug: 'e2e-match-list',
	} );

	// Shortcode page — league_table
	const leagueTablePage = await restPost( context, 'pages', {
		title: 'E2E League Table',
		content: '[league_table]',
		status: 'publish',
		slug: 'e2e-league-table',
	} );

	// -----------------------------------------------------------------------
	// Persist IDs for use in tests
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

	await context.dispose();
}
