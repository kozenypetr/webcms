#!/bin/sh
export PATH=/opt/plesk/php/7.1/bin:$PATH;
php bin/console cache:clear -e=prod
