#!/bin/bash

root=$( dirname $( wp config path ) )

wp theme activate wporg-themes-2022

wp rewrite structure '/%postname%/'
wp rewrite flush --hard

wp option update blogname "Theme Directory"
wp option update blogdescription "Free WordPress Themes"

wp import "${root}/env/data/themes-pages.xml" --authors=create
wp import "${root}/env/data/themes-repopackages.xml" --authors=create
wp import "${root}/env/data/themes-theme_shops.xml" --authors=create

#wp db import "${root}/env/tables.sql"
