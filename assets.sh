#!/bin/sh
php bin/console cache:clear -e=prod
php bin/console assetic:dump --env=prod
