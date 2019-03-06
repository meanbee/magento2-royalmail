## Synopsis

**NON-FUNCTIONAL & NO LONGER SUPPORTED**

An extension to add Royal Mail shipping rates to Magento 2.


[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/meanbee/magento2-royalmail/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/meanbee/magento2-royalmail/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/meanbee/magento2-royalmail/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/meanbee/magento2-royalmail/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/meanbee/magento2-royalmail/badges/build.png?b=master)](https://scrutinizer-ci.com/g/meanbee/magento2-royalmail/build-status/master)

## Installation

This module is intended to be installed using composer.  After including this component and enabling it, you can verify it is installed by going the backend at:

STORES -> Configuration -> ADVANCED/Advanced ->  Disable Modules Output

Once there check that the module name shows up in the list to confirm that it was installed correctly.

## Development Environment Setup

```
cp composer.env.dist composer.env
cp current.env.dist current.env

docker-compose run cli /usr/local/bin/magento-installer
docker-compose run cli /tools/setup.sh
docker-compose up -d
```

The site should now be available from http://magento2-royalmail.docker

We are experimenting with grump in this repository to sniff commits for code format violations.

You can run these manually by:

`src/vendor/bin/grumphp run`

## Tests

Unit tests could be found in the [Test/Unit](Test/Unit) directory.

Tests can be run through the docker environment

`docker-composer run cli /tools/run_unit_tests.sh`

Alternatively, run natively by:

```
cd src/
phpunit --coverage-html "coverage/" -c dev/tests/unit/phpunit.xml
```

## Notes

If running `composer update` natively in the src/ directory you can run it with the
`--no-scripts options` to avoid errors.

## Contributors

[Tom Robertshaw](http://www.twitter.com/bobbyshaw)

## License

[Open Source License](LICENSE.txt)
