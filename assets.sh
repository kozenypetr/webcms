#!/bin/sh
php bin/console cache:clear --env=prod
php bin/console assetic:dump --env=prod
