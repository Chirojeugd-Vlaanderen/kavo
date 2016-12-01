# be.chiro.civi.kavo

CiviCRM extension to link CiviCRM to the 'Kadervormingstool' of the Flemish government. Work in progress.

## prerequisites

This extension depends on two other CiviCRM extensions:

* [org.civicoop.configitems](https://github.com/CiviCooP/org.civicoop.configitems). At the moment
of this writing (2016-11-28), you need the version from git.
* [be.chiro.civi.idcache](https://github.com/Chirojeugd-Vlaanderen/idcache)

So install those before you install the kavo extension.

## configuration

Add these two lines to your `civicrm.config.php` file:

    global $civicrm_setting;
    $civicrm_setting['kavo']['kavo_endpoint'] = 'https://cjsm.be/kavotest/api/v1';
    $civicrm_setting['kavo']['kavo_key'] = 'YOURKAVOKEY';
    $civicrm_setting['kavo']['kavo_secret'] = 'YOURKAVOSECRET';

Of course, you use your own key and secret. And change the endpoint if you want to connect to the real KAVO tool
instead of the test.

## create a KAVO-ID

On a contact summary page, you can click 'Actions', 'Generate KAVO-ID'. This creates a
KAVO-account for the contact, and saves the KAVO-ID in the contact's KAVO-ID field.
![Generate KAVO-ID](https://civicrm.org/sites/civicrm.org/files/Screenshot%20from%202016-11-30%2013-03-14.png)

## api examples

I aim to make the KAVO-API accessible via the CiviCRM API. For the moment
only creating new IDs is supported. This is how it works with drush:

    drush cvapi Kavo.createaccount contact_id=204

The PHP code is:

    civicrm_api3('Kavo', 'createaccount', ['contact_id' => 204]);

