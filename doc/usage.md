## Usage

After installing this plugin the store will be connected to Mailchimp. This mean that Mailchimp will be updated with 
your realtime data using differents event listeners.

The Mailchimp services are enabled by default.
You can enable or disable the events by configuration:

```yml
# config/packages/odiseo_sylius_mailchimp.yaml
odiseo_sylius_mailchimp:
    enabled: false
```

#### Get the mailchimp service on your class

```php
/** @var \DrewM\MailChimp\MailChimp $mailchimp */
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

#### Newsletter

To render this view you can run `mkdir -p templates/bundles/SyliusShopBundle` then `cp vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/Resources/views/_footer.html.twig templates/bundles/SyliusShopBundle/_footer.html.twig` and then add:

```twig
{# ... #}
{% include '@OdiseoSyliusMailchimpPlugin/Shop/_newsletter.html.twig' %}
{# ... #}
```
