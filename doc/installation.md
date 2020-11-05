## Installation

1. Run `composer require odiseoteam/sylius-mailchimp-plugin`

2. Enable the plugin in bundles.php

```php
<?php
// config/bundles.php

return [
    // ...
    Odiseo\SyliusMailchimpPlugin\OdiseoSyliusMailchimpPlugin::class => ['all' => true],
];
```

3. Import the plugin configurations

```yml
# config/packages/_sylius.yaml
imports:
    ...

    - { resource: "@OdiseoSyliusMailchimpPlugin/Resources/config/config.yaml" }
```

4. Add the shop routes

```yml
# config/routes.yaml
odiseo_sylius_mailchimp_plugin_shop:
    resource: "@OdiseoSyliusMailchimpPlugin/Resources/config/routing/shop.yaml"
    prefix: /{_locale}/mailchimp
    requirements:
        _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$
```

5. Add the enviroment variables

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
