const RtlCssPlugin = require( 'rtlcss-webpack-plugin' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

// When using `--experimental-modules`, the config comes as two objects,
// one for plain scripts and one for module scripts.
// Add the RTL config to both.
if ( Array.isArray( defaultConfig ) ) {
	module.exports = defaultConfig.map( ( config ) => ( {
		...config,
		plugins: [
			...config.plugins,
			new RtlCssPlugin( {
				filename: `[name]-rtl.css`,
			} ),
		],
	} ) );
} else {
	module.exports = {
		...defaultConfig,
		plugins: [
			...defaultConfig.plugins,
			new RtlCssPlugin( {
				filename: `[name]-rtl.css`,
			} ),
		],
	};
}
