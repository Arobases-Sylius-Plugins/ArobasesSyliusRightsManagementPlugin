# Arobases Sylius Rights Management Plugin

This plugin allows you to manage the rights and roles of administrator users


![arobases_banner](https://user-images.githubusercontent.com/39689570/219030936-8649849f-11a4-4533-bd5d-6f79662aca87.jpg)




<h2 align="center">Installation</h2>

### Requirements

| Plugin version | Sylius version |
|----------------|----------------|
| 2.x            | ^2.3           |
| 1.x            | ^1.10          |

Upgrading from 1.x? Read the [UPGRADE.md](UPGRADE.md) file.

### Step 1: Download the plugin

* Install the plugin with `composer require arobases/sylius-rights-management-plugin`

### Step 2: Enable the plugin

* Register the bundle in your `bundles.php`:
```php
# config/bundles.php
<?php

return [
...
  Arobases\SyliusRightsManagementPlugin\ArobasesSyliusRightsManagementPlugin::class => ['all' => true],
...
];
```

### Step 3: Import configuration


Create file `config/packages/arobases_sylius_rights_management_plugin.yaml` with this content

```yaml
imports:
  - { resource: "@ArobasesSyliusRightsManagementPlugin/Resources/config/config.yaml" }
```
### Step 4: Import routing


Create file `config/routes/arobases_sylius_rights_management_plugin.yaml` with this content
```yaml
arobases_sylius_rights_management_plugin_admin:
    resource: "@ArobasesSyliusRightsManagementPlugin/Resources/config/admin_routing.yml"
    prefix: '/%sylius_admin.path_name%'
 ```

### Step 5: Use AdminUserTrait and implement AdminUserInterface


```php
//src/Entity/User/AdminUser.php

<?php

declare(strict_types=1);

namespace App\Entity\User;

use Arobases\SyliusRightsManagementPlugin\Entity\AdminUserInterface;
use Arobases\SyliusRightsManagementPlugin\Entity\AdminUserTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\AdminUser as BaseAdminUser;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_admin_user')]
class AdminUser extends BaseAdminUser implements AdminUserInterface
{
    use AdminUserTrait;
}

```

The role field is automatically added to the administrator form (it replaces the Sylius Plus "Roles" placeholder),
no template override is needed.

### Step 6: Update your database:

```
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
 ```
### Step 7: Clear the cache

```
php bin/console cache:clear
```

### Step 8: Define your default role (for example "administrator") and the username of the admin account which will have this role by default 


```env
#.env

###> arobases/sylius-rights-management ###
DEFAULT_ADMIN_USER=EDITME
DEFAULT_ADMIN_ROLE_CODE=EDITME
DEFAULT_ADMIN_ROLE_NAME=EDITME
###> arobases/sylius-rights-management ###

```
if you don't change this lines, when you will create the rights with the command, you will no longer have access to anything in the back office because you will not have the rights.

### Step 9: Use the command line to create roles, rights group and rights :

```
php bin/console arobases:right:create-admin-right
```

### Screenshots

<h3 align="center">Index</h3>

![role_index](https://user-images.githubusercontent.com/39689570/219010702-9d01edba-bc4b-4772-88f6-ef2ca85db530.png) 

<h3 align="center">Edit/create role</h3>

![role_update](https://user-images.githubusercontent.com/39689570/219010777-4b8d00d1-6553-4836-959f-5491883f15a7.png)

![role_update2](https://user-images.githubusercontent.com/39689570/219010876-bb5e3fe1-df3e-411c-b58a-6451139e6a71.png)

<h3>Error if the user does not have the rights</h3>

![access_denied](https://user-images.githubusercontent.com/39689570/219026995-3a37f643-0e10-4637-a40c-70c49077a770.png)



### Usage

There is a pre-configured list of default rights :
```yaml
#vendor/arobases/sylius-rights-management-plugin/src/Resources/config/right_management.yaml

arobases_sylius_rights_management:
  groups:
    arobases_sylius_rights_management_plugin.group.catalog:  #here it's the name of the group of the right
      rights:
        taxon:
          name: 'arobases_sylius_rights_management_plugin.rights.taxons' #here it's the name of the right
          routes: ['sylius_admin_taxon_*']
        product:
          name: 'arobases_sylius_rights_management_plugin.rights.products'
          routes: ['sylius_admin_product_*', 'sylius_admin_ajax_product_*']
          excludes: ['sylius_admin_product_attribute_*', 'sylius_admin_product_option_*', 'sylius_admin_product_association_type_*', 'sylius_admin_product_review_*']
        stock:
          name: 'arobases_sylius_rights_management_plugin.rights.inventory'
          routes: ['sylius_admin_inventory_*']
        attribute:
          name: 'arobases_sylius_rights_management_plugin.rights.attributes'
          routes: ['sylius_admin_product_attribute_*']
        option:
          name: 'arobases_sylius_rights_management_plugin.rights.options'
          routes: ['sylius_admin_product_option_*']
        association_type:
          name: 'arobases_sylius_rights_management_plugin.rights.association_types'
          routes: ['sylius_admin_product_association_type_*']

    arobases_sylius_rights_management_plugin.group.sales:
      rights:
        order:
          name: 'arobases_sylius_rights_management_plugin.rights.orders'
          routes: ['sylius_admin_order_*', 'sylius_admin_payment_*', 'sylius_admin_shipment_*']
          excludes: ['sylius_admin_payment_method_*']
        payment:
            name: 'arobases_sylius_rights_management_plugin.rights.payments'
            routes: ['sylius_admin_payment_*']
            excludes: ['sylius_admin_payment_method_*']
        shipment:
            name: 'arobases_sylius_rights_management_plugin.rights.shipments'
            routes: ['sylius_admin_shipment_*']
    arobases_sylius_rights_management_plugin.group.customer:
      rights:
        customer:
          name: 'arobases_sylius_rights_management_plugin.rights.customers'
          routes: [ 'sylius_admin_customer_*', 'sylius_admin_impersonate_*', 'sylius_admin_shop_user_*' ]
          excludes: [ 'sylius_admin_customer_group_*' ]
        group:
          name: 'arobases_sylius_rights_management_plugin.rights.customer_groups'
          routes: [ 'sylius_admin_customer_group_*', ]
    arobases_sylius_rights_management_plugin.group.marketing:
      rights:
        cart_promotion:
          name: 'arobases_sylius_rights_management_plugin.rights.cart_promotions'
          routes: ['sylius_admin_promotion_*']
        catalog_promotion:
          name: 'arobases_sylius_rights_management_plugin.rights.catalog_promotions'
          routes: ['sylius_admin_catalog_promotion_*']
        product_review:
            name: 'arobases_sylius_rights_management_plugin.rights.product_reviews'
            routes: ['sylius_admin_product_review_*']
    arobases_sylius_rights_management_plugin.group.configuration:
      rights:
        channel:
          name: 'arobases_sylius_rights_management_plugin.rights.channels'
          routes: ['sylius_admin_channel_*']
        country:
          name: 'arobases_sylius_rights_management_plugin.rights.countries'
          routes: ['sylius_admin_country_*']
        zone:
          name: 'arobases_sylius_rights_management_plugin.rights.zones'
          routes: ['sylius_admin_zone_*']
        currency:
          name: 'arobases_sylius_rights_management_plugin.rights.currencies'
          routes: ['sylius_admin_currency_*']
        exchange_rate:
          name: 'arobases_sylius_rights_management_plugin.rights.exchange_rates'
          routes: ['sylius_admin_exchange_rate_*']
        locale:
          name: 'arobases_sylius_rights_management_plugin.rights.locales'
          routes: ['sylius_admin_locale_*']
        payment_method:
          name: 'arobases_sylius_rights_management_plugin.rights.payment_methods'
          routes: ['sylius_admin_payment_method_*']
        shipping_method:
          name: 'arobases_sylius_rights_management_plugin.rights.shipping_methods'
          routes: ['sylius_admin_shipping_method_*', 'webgriffe_admin_shipping_table_rate_*']
        shipping_category:
          name: 'arobases_sylius_rights_management_plugin.rights.shipping_categories'
          routes: ['sylius_admin_shipping_category_*']
        tax_category:
          name: 'arobases_sylius_rights_management_plugin.rights.tax_categories'
          routes: ['sylius_admin_tax_category_*']
        tax_rate:
          name: 'arobases_sylius_rights_management_plugin.rights.tax_rates'
          routes: ['sylius_admin_tax_rate_*']
        admin_user:
          name: 'arobases_sylius_rights_management_plugin.rights.admin_users'
          routes: [ 'sylius_admin_admin_user_*' ]
        admin_role:
          name: 'arobases_sylius_rights_management_plugin.rights.admin_roles'
          routes: [ 'arobases_sylius_rights_management_plugin_admin_role_*' ]
```
However you can modify or add some rights or rights group in your file config/packages/arobases_sylius_rights_management_plugin.yaml : 
```yaml
#config/packages/arobases_sylius_rights_management_plugin.yaml

imports:
  - { resource: "@ArobasesSyliusRightsManagementPlugin/Resources/config/config.yaml" }

arobases_sylius_rights_management:
  groups:
    arobases_sylius_rights_management_plugin.group.product:
      rights:
    #here you will update "arobases_sylius_rights_management_plugin.group.product"

    app.group.my_new_group:
      rights:
        all:
          name: 'app.rights.my_new_right'
          routes: ['app_my_custom_route_*']
    #here you will create a new group and a new right
 ```





