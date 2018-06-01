#!/usr/bin/env bash

set -ex

xdebug_config=/usr/local/etc/php/conf.d/xdebug.ini
opcache_config=/usr/local/etc/php/conf.d/opcache.ini
if [ "$SYMFONY_ENV" == "dev" ]; then
    declare PHP_IDE_CONFIG="serverName=$TARGET_HOST"
    export PHP_IDE_CONFIG
    sed -i -e "/xdebug.remote_host/d" "$xdebug_config"
    echo "xdebug.remote_host=10.0.2.2" >> "$xdebug_config"
    rm -f $opcache_config
else
    rm -f "$xdebug_config" 2>/dev/null
fi

./bin/wait_for_db
./bin/console cache:warmup

if [ "$SYMFONY_ENV" == "dev" ]; then
    bin/console doctrine:schema:update --force
    bin/console doctrine:fixtures:load  -n
fi

php-fpm --allow-to-run-as-root
