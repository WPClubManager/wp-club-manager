/**
 * Global E2E teardown — deletes fixture content created in global-setup.ts.
 */

import { request } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';

const BASE_URL = process.env.WP_BASE_URL || 'http://localhost:8889';
const ADMIN_USER = process.env.WP_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.WP_ADMIN_PASS || 'password';
const STATE_FILE = path.join( __dirname, '.test-state.json' );

export default async function globalTeardown() {
	if ( ! fs.existsSync( STATE_FILE ) ) {
		return;
	}

	const state = JSON.parse( fs.readFileSync( STATE_FILE, 'utf8' ) );
	const context = await request.newContext( {
		httpCredentials: {
			username: ADMIN_USER,
			password: ADMIN_PASS,
		},
	} );

	const endpoints: Array<[ string, number ]> = [
		[ 'wpcm_club', state.homeClubId ],
		[ 'wpcm_club', state.awayClubId ],
		[ 'wpcm_player', state.playerId ],
		[ 'wpcm_match', state.matchId ],
	];

	for ( const [ endpoint, id ] of endpoints ) {
		if ( id ) {
			await context.delete(
				`${ BASE_URL }/wp-json/wp/v2/${ endpoint }/${ id }?force=true`
			).catch( () => {} );
		}
	}

	await context.dispose();
	fs.unlinkSync( STATE_FILE );
}
