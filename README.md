# CMS

## Instalalce závislostí

Pro správu závislostí se používá [Composer](https://getcomposer.org/) a [YARN](https://yarnpkg.com/).

Instalace závislostí:

1. composer install
    * Musí se vyplnit *mailer_user*, jinak generace parametrů vyhodí exception.
1. yarn install

## Kompilace assets

* Vývoj
    * *./node_modules/.bin/encore dev*
* Deploy
    * *./node_modules/.bin/encore production*
    
Pro automatickou rekompilaci při změně js/less: *./node_modules/.bin/encore dev --watch*