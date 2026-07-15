# Theme Directory

The `wporg-themes-2024` frontend theme that powers the [WordPress.org Theme Directory](https://wordpress.org/themes/).

## Development

Local development and testing happen in the **Theme Directory environment** in the [`WordPress/wordpress.org`](https://github.com/WordPress/wordpress.org) repo, under [`environments/theme-directory/`](https://github.com/WordPress/wordpress.org/tree/trunk/environments). It mounts this repo, loads the theme-directory plugin, and seeds a realistic directory with real themes.

Follow the setup and testing instructions in that repo's [`environments/README.md`](https://github.com/WordPress/wordpress.org/blob/trunk/environments/README.md).

### Testing local changes to this theme

By default the environment mounts this repo from GitHub. To run it against your local checkout instead:

1. Build the theme so the environment picks up your assets: `npm run build:theme` (or `npm run start:theme` to rebuild on change).

1. In the wordpress.org repo's `environments/theme-directory/` directory, create a git-ignored `.wp-env.override.json` that remaps the theme mount to your checkout. Use an absolute path — wp-env resolves relative paths against the config file's directory:

	```json
	{
		"mappings": {
			"wp-content/wporg-theme-directory": "/absolute/path/to/wporg-theme-directory"
		}
	}
	```

	`mappings` merges key-by-key with `.wp-env.json`, so only this entry is overridden.

1. Restart the environment (`npm run themes:env start` from `environments/`) so the new mount takes effect.
