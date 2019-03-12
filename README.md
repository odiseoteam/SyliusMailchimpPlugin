<h1 align="center">
    <a href="https://odiseo.com.ar/" target="_blank" title="Odiseo">
        <img src="https://github.com/odiseoteam/SyliusMailchimpPlugin/blob/master/logo_odiseo.png" alt="Odiseo" width="300px" />
    </a>
    <br />
    Odiseo Sylius Mailchimp Plugin
    <br />
    <a href="https://packagist.org/packages/odiseoteam/sylius-mailchimp-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/odiseoteam/sylius-mailchimp-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/odiseoteam/sylius-mailchimp-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/odiseoteam/sylius-mailchimp-plugin.svg" />
    </a>
    <a href="http://travis-ci.org/odiseoteam/SyliusMailchimpPlugin" title="Build status" target="_blank">
        <img src="https://img.shields.io/travis/odiseoteam/SyliusMailchimpPlugin/master.svg" />
    </a>
    <a href="https://scrutinizer-ci.com/g/odiseoteam/SyliusMailchimpPlugin/" title="Scrutinizer" target="_blank">
        <img src="https://img.shields.io/scrutinizer/g/odiseoteam/SyliusMailchimpPlugin.svg" />
    </a>
    <a href="https://packagist.org/packages/odiseoteam/sylius-mailchimp-plugin" title="Total Downloads" target="_blank">
        <img src="https://poser.pugx.org/odiseoteam/sylius-mailchimp-plugin/downloads" />
    </a>
</h1>

## Description

This is a Sylius Plugin that connect your store with Mailchimp to use their ecommerce features: 
<a href="https://mailchimp.com/en/resources/mailchimp-e-commerce">https://mailchimp.com/en/resources/mailchimp-e-commerce</a>. 

Some of that features are:

* Product blocks: Customizable product blocks provide you with a quick way to promote your best-selling items or 
showcase seasonal products in your campaign or automation email. Once you connect your store, we’ll automatically pull 
product images, descriptions, and prices for you.

* Product recommendations: Product recommendations feature analyzes the previous purchase behavior of your customers 
and automatically suggests products they’re most likely to buy.

* E-commerce automations: Welcome first-time customers, Reward your best customers, Send a product follow-up message, 
Re-engage lapsed customers.

* Abandoned cart: Allows you to give customers the incentive they need to return to your store, so you can recapture 
sales and generate more revenue.

* Order notifications: Allow you to design, send, and track all of those notifications you need to send customers—like 
order confirmations, invoices, shipping confirmations, refund confirmations, and cancellation confirmations—directly 
through your Mailchimp account.

Now supporting Sylius 1.4 with Symfony 4 + Flex structure.

## Demo

You can see this plugin in action in our Sylius Demo application.

- Frontend: [sylius-demo.odiseo.com.ar](https://sylius-demo.odiseo.com.ar). 
- Administration: [sylius-demo.odiseo.com.ar/admin](https://sylius-demo.odiseo.com.ar/admin) with `odiseo: odiseo` credentials.

## Installation

1. Run `composer require odiseoteam/sylius-mailchimp-plugin`

2. Enable the plugin in bundles.php

```php
<?php

return [
    // ...
    Odiseo\SyliusMailchimpPlugin\OdiseoSyliusMailchimpPlugin::class => ['all' => true],
    // ...
];
```
 
3. Import the plugin configurations
 
```yml
imports:
    - { resource: "@OdiseoSyliusMailchimpPlugin/Resources/config/config.yaml" }
```

4. Add routes:

```yml
odiseo_sylius_mailchimp_plugin:
    resource: "@OdiseoSyliusMailchimpPlugin/Resources/config/routing.yaml"
    prefix: /{_locale}
    requirements:
        _locale: ^[a-z]{2}(?:_[A-Z]{2})?$
```

5. Add the enviroment variables:

```yml
ODISEO_MAILCHIMP_APIKEY=EDITME
ODISEO_MAILCHIMP_DEFAULT_LISTID=EDITME
```

6. Finish the installation updating the database schema and installing assets
   
```
php bin/console doctrine:schema:update --force
php bin/console sylius:theme:assets:install
```

If you want to use different List Id for each Sylius Channel, you need to 
implement the MailchimpListIdAwareInterface on your Channel entity. If you don't do that
the default list id will be used.

## Usage

After installing this plugin the store will be connected to Mailchimp. This mean that Mailchimp will be updated with 
your realtime data using differents event listeners.

#### Get the mailchimp service on your class

```php
/** @var Mailchimp $mailchimp */
$mailchimp = $this->get('odiseo.mailchimp_plugin.mailchimp');
```

#### Available CLI commands to synchronize your store data with Mailchimp.

```bash
$ bin/console odiseo:mailchimp:sync-stores
```
```bash 
$ bin/console odiseo:mailchimp:sync-customers
```
```bash
$ bin/console odiseo:mailchimp:sync-products
```
```bash
$ bin/console odiseo:mailchimp:sync-carts
```
```bash
$ bin/console odiseo:mailchimp:sync-orders
```

## Todo

* Promo codes sync.
* Connected Sites sync.

## Test the plugin

You can follow the instructions to test this plugins in the proper documentation page: [Test the plugin](doc/tests.md).
    
## Credits

This plugin is maintained by <a href="https://odiseo.com.ar">Odiseo</a>. Want us to help you with this plugin or any Sylius project? Contact us on <a href="mailto:team@odiseo.com.ar">team@odiseo.com.ar</a>.
