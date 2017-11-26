
# Redirect disabled products
This module  changes the default magento 404 by redirecting users who try to go to a disabled product URL to the first category the product belongs

## Features of this extension:

### Backend
- Customisable message once user is redirected from the disabled product URL to the first category it belongs to.

## Installation

#### Manual Installation

 * Download the extension
 * Unzip the file
 * Create a folder {Magento root}/app/code/Sysforall/DisabledProductsRedirect
 * Copy the content from the unzip folder


#### Using Composer

```
composer require sysforall/disabled-products-redirect
```

#### Enable Extension
 * php bin/magento module:enable Sysforall DisabledProductsRedirect
 * php bin/magento setup:upgrade
 * php bin/magento setup:static-content:deploy

## Settings
To customise the redirection message log into your Magento Admin Panel, go to Stores -> Configuration -> Sysforall -> Disabled Products Redirect

When complete, Save Config.

#### Clear cache if necessary
* php bin/magento cache:clean