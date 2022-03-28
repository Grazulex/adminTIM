#!/bin/bash
echo "Update composer"
echo "==============="
php -d memory_limit=-1 composer.phar self-update --ansi
echo "Done"

echo "Update vendor"
echo "============="
php -d memory_limit=-1 composer.phar update --ansi --verbose
echo "Done"

echo "Update schema DB"
echo "================"
php bin/console doctrine:schema:update --force
echo "Done"

echo "Clear cache"
echo "============"
php bin/console assets:install web
php -d memory_limit=-1 bin/console cache:clear --env=prod --no-debug
php -d memory_limit=-1 bin/console cache:clear --env=dev --no-debug
echo "Done"

echo "Clear dump"
echo "==========="
php -d memory_limit=-1 bin/console assetic:dump --env=dev
echo "Done"

echo "Translation"
echo "==========="
php bin/console translation:update fr  --output-format="yml" --force
php bin/console translation:update nl  --output-format="yml" --force
php bin/console translation:update en  --output-format="yml" --force

php bin/console translation:update --output-format="yml" fr --force AppBundle
php bin/console translation:update --output-format="yml" nl --force AppBundle
php bin/console translation:update --output-format="yml" en --force AppBundle
echo "Done"

echo "Update chmod"
echo "============"
chmod -R 777 var/cache/
chmod -R 777 var/logs/
echo "Done"


echo "Update is done !"
