Texthub
=======

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yhoiseth/texthub/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yhoiseth/texthub/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/yhoiseth/texthub/badges/build.png?b=master)](https://scrutinizer-ci.com/g/yhoiseth/texthub/build-status/master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/92570026-27cd-4775-a73e-09e1f1e81e50/mini.png)](https://insight.sensiolabs.com/projects/92570026-27cd-4775-a73e-09e1f1e81e50)

Git writing app.

## Development environment setup

1. Clone the repository
2. Install PHP dependencies: `composer install`
3. Install JavaScript dependencies: `yarn install --dev`
4. Build and watch frontend assets: `yarn run encore dev --watch`
2. Create database: `bin/console doctrine:database:create`
2. Migrate database: `bin/console doctrine:migrations:migrate`
3. Prevent deprecation warnings from messing up your output: `export SYMFONY_DEPRECATIONS_HELPER=weak`
4. Start development web server: `bin/console server:start`
3. Make sure that existing tests pass: `codecept run`
