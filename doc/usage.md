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
