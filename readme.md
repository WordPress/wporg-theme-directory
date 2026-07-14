# Theme Directory

The codebase and development environment for the WordPress.org Theme Directory.

This is as-yet incomplete, a starting point. Following this will get you partially set up, but you need a `ratings` database table to prevent fatal errors.

## Development

### Prerequisites

* Docker
* Node/npm
* Composer

### Setup

1. Set up repo dependencies.

	```bash
	npm run setup:tools
	```

1. Add the theme-directory plugin, as this is not installed by composer.

	```bash
	cd source/wp-content/plugins/
	svn checkout https://meta.svn.wordpress.org/sites/trunk/wordpress.org/public_html/wp-content/plugins/theme-directory
	```

1. Start the local environment.

	```bash
	npm run wp-env start
	```

1. Run the setup script.

	```bash
	npm run setup:wp
	```

1. (optional) There may be times when you want to make changes to the Parent theme and test them with the Main them. To do that:
	1. Clone the Parent repo and follow the setup instructions in its `readme.md` file.
	1. Create a `.wp-env.override.json` file in this repo
	1. Copy the `themes` section from `.wp-env.json` and paste it into the override file. You must copy the entire section for it to work, because it won't be merged with `.wp-env.json`.
	1. Update the path to the Parent theme to the Parent theme folder inside the Parent repository you cloned above.

	```json
	{
		"themes": [
			"./source/wp-content/themes/wporg",
			"./source/wp-content/themes/wporg-themes",
			"./source/wp-content/themes/wporg-themes-2024"
			"../wporg-parent-2021/source/wp-content/themes/wporg-parent-2021"
		]
	}
	```

1. Visit site at [localhost:8888](http://localhost:8888).

1. Log in with username `admin` and password `password`.

### Environment management

These must be run in the project's root folder, _not_ in theme/plugin subfolders.

* Stop the environment.

	```bash
	npm run wp-env stop
	```

* Restart the environment.

	```bash
	npm run wp-env start
	```

* Refresh local WordPress content with a current copy from the staging site.

	```bash
	npm run setup:refresh
	```

* Reset WordPress to a clean install, and reconfigure. This will nuke all local WordPress content!

	```bash
	npm run wp-env clean all
	npm run setup:wp
	```

* SSH into docker container.

	```bash
	npm run wp-env run wordpress bash
	```

* Run wp-cli commands. Keep the wp-cli command in quotes so that the flags are passed correctly.

	```bash
	npm run wp-env run cli "post list --post_status=publish"
	```

* Update composer dependencies and sync any `repo-tools` changes.

	```bash
	npm run update:tools
	```
