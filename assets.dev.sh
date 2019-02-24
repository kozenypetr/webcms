#!/bin/sh
php bin/console cache:clear -e=dev
php bin/console assetic:dump --env=dev
