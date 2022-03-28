#!/bin/bash
cd /var/www/vhosts/timeismoney.be/admin.timeismoney.be/

echo "Update composer"
echo "==============="
/usr/bin/php -d memory_limit=-1 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/composer.phar self-update --ansi --profile
echo "Done"

echo "Update vendor"
echo "============="
/usr/bin/php -d memory_limit=-1 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/composer.phar install --optimize-autoloader --ansi --profile
echo "Done"

echo "Update schema DB"
echo "================"
/usr/bin/php -d memory_limit=-1 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/bin/console doctrine:schema:update --force
echo "Done"

echo "Clear cache"
echo "============"
/usr/bin/php -d memory_limit=-1 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/bin/console cache:clear --env=prod --no-debug
/usr/bin/php -d memory_limit=-1 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/bin/console doctrine:cache:clear-metadata --env=prod
/usr/bin/php -d memory_limit=-1 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/bin/console doctrine:cache:clear-query --env=prod
/usr/bin/php -d memory_limit=-1 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/bin/console doctrine:cache:clear-result --env=prod
echo "Done"

echo "Clear dump & install asset"
echo "==========="
/usr/bin/php -d memory_limit=-1 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/bin/console assetic:dump --env=prod --no-debug
/usr/bin/php -d memory_limit=-1 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/bin/console assets:install --env=prod --no-debug
echo "Done"

echo "Update chmod"
echo "============"
rm /var/www/vhosts/timeismoney.be/admin.timeismoney.be/var/cache/prod/ -R
rm /var/www/vhosts/timeismoney.be/admin.timeismoney.be/var/cache/dev/ -R
chmod -R 777 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/var/cache/
chmod -R 777 /var/www/vhosts/timeismoney.be/admin.timeismoney.be/var/logs/
echo "Done"

echo "Update is done !"