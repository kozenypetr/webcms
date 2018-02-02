#!/bin/sh
php bin/console doctrine:generate:entities CmsBundle
php bin/console doctrine:generate:entities AdminBundle
php bin/console doctrine:schema:update --force
