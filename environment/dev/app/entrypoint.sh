#!/bin/bash

# -e is for "automatic error detection", tell shell to abort any time an error occurred
set -e

# Create storage directory and set permissions
mkdir -p /application/storage/app/public/profile-images
chown -R www-data:www-data /application/storage
chmod -R 775 /application/storage

# Create storage link
php artisan storage:link

# bash is not responding to the sigterm and container always have 10 second timeout (when stop/restart)
# exec is related with
# https://docs.docker.com/compose/faq/#why-do-my-services-take-10-seconds-to-recreate-or-stop
# https://github.com/moby/moby/issues/3766
# https://unix.stackexchange.com/a/196053

exec supervisord --configuration /etc/supervisor/custom-supervisord.conf
