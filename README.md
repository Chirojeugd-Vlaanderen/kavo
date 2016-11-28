# be.chiro.civi.kavo

CiviCRM extension to link CiviCRM to the 'Kadervormingstool' of the Flemish government.

## configuration

Add these two lines to your `civicrm.config.php` file:

    global $civicrm_setting;
    $civicrm_setting['kavo']['kavo_endpoint'] = 'https://cjsm.be/kavotest/api/v1';
    $civicrm_setting['kavo']['kavo_key'] = 'YOURKAVOKEY';
    $civicrm_setting['kavo']['kavo_secret'] = 'YOURKAVOSECRET';

Of course, you use your own key and secret. And change the endpoint if you want to connect to the real KAVO tool
instead of the test.
