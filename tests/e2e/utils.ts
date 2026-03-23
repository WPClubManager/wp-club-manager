/**
 * Shared utilities for E2E tests.
 */

import * as fs from 'fs';
import * as path from 'path';

const STATE_FILE = path.join( __dirname, '.test-state.json' );

export function getTestState(): Record<string, any> {
	if ( ! fs.existsSync( STATE_FILE ) ) {
		throw new Error( 'Test state file not found. Did global setup run?' );
	}
	return JSON.parse( fs.readFileSync( STATE_FILE, 'utf8' ) );
}

/**
 * Assert no PHP fatal/notice output is present in the page content.
 * wp-env has WP_DEBUG enabled, so errors appear inline.
 */
export async function assertNoPhpErrors( page: any ) {
	const content = await page.content();
	const phpErrorPatterns = [
		/Fatal error:/i,
		/Parse error:/i,
		/Warning: /i,
		/Notice: /i,
		/Deprecated: /i,
		/Call to undefined function/i,
	];
	for ( const pattern of phpErrorPatterns ) {
		if ( pattern.test( content ) ) {
			// Extract a snippet around the match for context.
			const match = content.match( pattern );
			throw new Error( `PHP error detected on page: ${ match?.[0] }` );
		}
	}
}
