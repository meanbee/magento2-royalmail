#!/bin/bash

MAGENTO_ROOT="/magento"
PHP="/usr/local/bin/php"
COMPOSER="$PHP /usr/local/bin/composer"

MODULE_NAME="Meanbee_RoyalMail"
COMPOSER_NAME="meanbee/module-royalmail"

MAGENTO_TOOL="magento-command"

cd $MAGENTO_ROOT

# This is required because Magento doesn't support the path type in composer
# this is a hack.

$COMPOSER config repositories.meanbee_royalmail path /src/src

$COMPOSER require "$COMPOSER_NAME" "*@dev"

# Required due to us using the "path" type for the repository
$COMPOSER require "composer/composer" "1.0.0-alpha11 as 1.0.0-alpha10"

$MAGENTO_TOOL module:enable $MODULE_NAME

$MAGENTO_TOOL setup:upgrade

$MAGENTO_TOOL setup:static-content:deploy

$MAGENTO_TOOL cache:flush

$MAGENTO_TOOL deploy:mode:set developer