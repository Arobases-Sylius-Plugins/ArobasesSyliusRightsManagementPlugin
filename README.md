<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>



# Arobases SyliusRightsManagementPlugin

This plugin allows you to manage the rights and roles of administrator users

<h2 align="center">Installation</h2>
### Step 1: Download the plugin

* Install the bundle via composer `require arobases/sylius-rights-management-plugin`

### Step 2: Enable the plugin

* Register the bundle in your `bundles.php`:
```bash
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

```
imports:
  - { resource: "@ArobasesSyliusRightsManagementPlugin/Resources/config/resources.yaml" }
```
### Step 4: Import routing


Create file `config/routes/arobases_sylius_rights_management_plugin.yaml` with this content
```
arobases_sylius_rights_management_plugin_shop:
    resource: "@ArobasesSyliusRightsManagementPlugin/Resources/config/shop_routing.yml"
    prefix: /{_locale}
    requirements:
        _locale: ^[a-z]{2}(?:_[A-Z]{2})?$

arobases_sylius_rights_management_plugin_admin:
    resource: "@ArobasesSyliusRightsManagementPlugin/Resources/config/admin_routing.yml"
    prefix: /admin
 ```

### Step 5: Use AdminUserTrait and use AdminUserInterface


```php
//src/Entity/AdminUser.php

<?php

declare(strict_types=1);

namespace App\Entity\User;

use Arobases\SyliusRightsManagementPlugin\Entity\AdminUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Arobases\SyliusRightsManagementPlugin\Entity\AdminUserTrait;
use Sylius\Component\Core\Model\AdminUser as BaseAdminUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_admin_user")
 */
class AdminUser extends BaseAdminUser implements AdminUserInterface
{
    use AdminUserTrait;
}

```

### Step 6: Modify template SyliusAdminBundle/AdminUser/_form.html.twig
```html.twig
 {# templates/bundles/SyliusAdminBundle/AdminUser/_form.html.twig %}
    ...
    <div class="ui segment">
       <h4 class="ui dividing header">{{ 'arobases.sylius.rights_management.choice_role'|trans }}</h4>
       {{ form_row(form.role) }}
    </div>
    ...
```
### Step 7: Update your database:

```
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
 ```
### Step 8: Install assets

```
php bin/console asset:install
php bin/console cache:clear
```

### Step 9: Define your default role (for example "administrator") and the username of the admin account that will have this role by default 


```env
#.env

###> arobases/sylius-rights-management ###
DEFAULT_ADMIN_USER=EDITME
DEFAULT_ADMIN_ROLE_CODE=EDITME
DEFAULT_ADMIN_ROLE_NAME=EDITME
###> arobases/sylius-rights-management ###

```
if you don't do that, when you create the rights with the command, you will no longer have access to anything in the back office since you will not have the rights.
### Screenshots


### Usage

There is a pre-configured list of default rights :
```yaml
#src/Resources/config/right_management.yaml

arobases_sylius_rights_management:
  groups:
    arobases_sylius_rights_management_plugin.group.taxon:  #here it's the name of the group of the right
      rights:
        all:
          name: 'arobases_sylius_rights_management_plugin.rights.taxon' #here it's the name of the right
          routes: ['sylius_admin_taxon_*', 'sylius_admin_ajax_generate_taxon_slug']

    arobases_sylius_rights_management_plugin.group.product:
      rights:
        product:
          name: 'arobases_sylius_rights_management_plugin.rights.product'
          routes: ['sylius_admin_product_*', 'sylius_admin_get_product_attributes_*', 'sylius_admin_get_attribute_types', 'sylius_admin_ajax_generate_product_slug', 'sylius_admin_ajax_taxon_*', 'sylius_admin_ajax_product_*' ]
        stock:
          name: 'arobases_sylius_rights_management_plugin.rights.inventory'
          routes: ['sylius_admin_inventory_*']

    arobases_sylius_rights_management_plugin.group.order:
      rights:
        all:
          name: 'arobases_sylius_rights_management_plugin.rights.order'
          routes: ['sylius_admin_order_*', 'sylius_admin_payment_*', 'sylius_admin_shipment_*','sylius_invoicing_plugin_admin_invoice_*', 'sylius_refund_*', 'sylius_admin_ajax_render_province_*']
          excludes: ['sylius_admin_payment_method_*']
    arobases_sylius_rights_management_plugin.group.promotion:
      rights:
        all:
          name: 'arobases_sylius_rights_management_plugin.rights.promotion'
          routes: ['sylius_admin_promotion_*']
    arobases_sylius_rights_management_plugin.group.user:
      rights:
        user:
          name: 'arobases_sylius_rights_management_plugin.rights.customer'
          routes: ['sylius_admin_customer_*', 'sylius_admin_impersonate_*']
          excludes: ['sylius_admin_customer_group_*']
        group:
          name: 'sylius.ui.customer_groups'
          routes: ['sylius_admin_customer_group_*',]

    arobases_sylius_rights_management_plugin.group.configuration:
      rights:
        channel:
          name: 'arobases_sylius_rights_management_plugin.rights.channel'
          routes: ['sylius_admin_channel_*']
        country:
          name: 'arobases_sylius_rights_management_plugin.rights.country'
          routes: ['sylius_admin_country_*']
        zone:
          name: 'arobases_sylius_rights_management_plugin.rights.zone'
          routes: ['sylius_admin_zone_*']
        currency:
          name: 'arobases_sylius_rights_management_plugin.rights.currency'
          routes: ['sylius_admin_currency_*']
        exchange_rate:
          name: 'arobases_sylius_rights_management_plugin.rights.exchange_rate'
          routes: ['sylius_admin_exchange_rate_*']
        locale:
          name: 'arobases_sylius_rights_management_plugin.rights.locale'
          routes: ['sylius_admin_locale_*']
        payment_method:
          name: 'arobases_sylius_rights_management_plugin.rights.payment_method'
          routes: ['sylius_admin_payment_method_*', 'sylius_admin_get_payment_gateways']
        shipping_method:
          name: 'arobases_sylius_rights_management_plugin.rights.shipping_method'
          routes: ['sylius_admin_shipping_method_*', 'webgriffe_admin_shipping_table_rate_*']
        shipping_category:
          name: 'arobases_sylius_rights_management_plugin.rights.shipping_category'
          routes: ['sylius_admin_shipping_category_*']
        tax_category:
          name: 'arobases_sylius_rights_management_plugin.rights.tax_category'
          routes: ['sylius_admin_tax_category_*']
        tax_rate:
          name: 'arobases_sylius_rights_management_plugin.rights.tax_rate'
          routes: ['sylius_admin_tax_rate_*']
    arobases_sylius_rights_management_plugin.group.administration:
      rights:
        admin_user:
          name: 'arobases_sylius_rights_management_plugin.rights.admin_user'
          routes: ['sylius_admin_admin_user_*']
        admin_role:
          name: 'arobases_sylius_rights_management_plugin.rights.admin_role'
          routes: ['arobases_sylius_rights_management_plugin_admin_role_*']
 ```
However you can modify or add some right or right group in your file config/packages/arobases_sylius_rights_management_plugin.yaml : 
```yaml
#config/packages/arobases_sylius_rights_management_plugin.yaml

imports:
  - { resource: "@ArobasesSyliusRightsManagementPlugin/Resources/config/resources.yaml" }

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





