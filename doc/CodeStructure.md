# Structure of the source code

## api/

This folder contains our custom actions for the CiviCRM API.

## doc/

Developer documentation can be stored here.

## resources/

The custom fields that are created by the KAVO extension, in json format,
so that everything can be configured with
[org.civicoop.configitems](https://github.com/CiviCooP/org.civicoop.configitems).

## CRM/Kavo/

This directory contains the meat of the extension.

* [KavoTool](../CRM/Kavo/KavoTool.php) implements [KavoInterface](../CRM/Kavo/KavoInterface.php).
  The interface should contains a method for every action of the
  [KAVO-API](https://cjsm.be/kavotest/docs/), that returns a relevant result. The API calls the KavoInterface,
  and wraps the result in a CiviCRM-API-result.
* The [Worker](../CRM/Kavo/Worker) directory contains 'workers' that implement the abstract
  [base worker](../CRM/Kavo/Worker.php). A worker applies to a particular CiviCRM entity (like Contact, Event,
  Participant), and the base worker class provides data-access for CiviCRM, mappings from CiviCRM entities
  to KAVO-entities, and some validation.
* [Error](../CRM/Kavo/Error.php) Error codes of the extension.
* [Field](../CRM/Kavo/Field.php) This class provides some static methods that return the 'api-names' (custom_*XX*) of the
  custom fields the extension creates. Those names are cached using the CiviCRM cache (which is not optimal, but
  it works). The static methods create a direct dependency to the API, which might be a problem if we want to
  do mocking in unit tests at some point in the future.
* [Role](../CRM/Kavo/Role.php) Similar to field, with similar problems. Use this class to get ID's of
  CiviCRM participant roles.
* [ValidationResult](../CRM/Kavo/ValidationResult.php) A class that's used for validation results in the
  [worker clases](../CRM/Kavo/Worker).
* [Check](../CRM/Kavo/Check.php) performs some very basic checks.
* [Form/Controller.php](../CRM/Kavo/Form/Controller.php) When a user clicks on one of our custom actions in some
  menu (e.g creating KAVO-IDs or course-IDs), this form makes sure that the action actually happens. This
  can probably use some refactoring.
  
## CRM/templates/

This contains the smarty template of the form.
