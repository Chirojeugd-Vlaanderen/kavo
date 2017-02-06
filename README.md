# be.chiro.civi.kavo

CiviCRM extension to link CiviCRM to the 'Kadervormingstool' of the Flemish government. Work in progress.

## warning: dev

This extension is under production. If it fails because some custom fields are not found,
disable the extension, and enable it again:

    drush cvapi Extension.disable key=be.chiro.civi.kavo
    drush cvapi Extension.enable key=be.chiro.civi.kavo

## prerequisites

This extension depends on two other CiviCRM extensions:

* [org.civicoop.configitems](https://github.com/CiviCooP/org.civicoop.configitems). At the moment
of this writing (2016-11-28), you need the version from git.
* [be.chiro.civi.idcache](https://github.com/Chirojeugd-Vlaanderen/idcache)

So install those before you install the kavo extension.

The KAVO-API wants a distinct street name and number in the event location. I pass street_name and street_number
to the API, but I think that standard CiviCRM stores the street address in street_address, and leaves street_name
and street_number empty. So in that case, creating new courses will fail. (See issue #7.)

I will ask the KAVO-people to make the API accept street name and number in one field.

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

## send an event as course to KAVO

To send an event as a course to KAVO, you go to the event info page, and click on the 'spanner' icon. In the menu
that pops up, click 'register as KAVO course'.
![Register an event as KAVO course](https://civicrm.org/sites/civicrm.org/files/Screenshot%20from%202016-12-14%2015-58-04.png)

You can do this from the event dashboard, or from the 'manage events' form as well. It does not work from the
page used to edit the event info and settings, because this does not seem to be customizable. If you want to do this,
you might want to provide a custom template for `templates/CRM/Event/Form/ManageEvent/Tab.tpl`.

## api examples

The KAVO-API will be accessable via the CiviCRM API. 

The API actions expects 'kavo params' as opposed to 'normal'
CiviCRM API actions, that expect 'CiviCRM params'. So e.g. you
need to provide kavo_id's instead of contact_id's. Not sure
if that was the best decision. But for the moment I keep it
as it is.

Some examples with drush:

    drush cvapi Kavo.hello
    drush cvapi Kavo.createaccount contact_id=204
    drush cvapi Kavo.createevent event_id=2
    drush cvapi Kavo.gettraject kavo_id=125143-59

Or in php:

    civicrm_api3('Kavo', 'hello');
    civicrm_api3('Kavo', 'createaccount', ['contact_id' => 204]);
    civicrm_api3('Kavo', 'createevent', ['event_id' => 2]);
    civicrm_api3('Kavo', 'gettraject', ['kavo_id' => '125143-59']);

## source code structure

If you want to contribute, you might want to check out how I try to
bring [structure to the code](doc/CodeStructure.md).