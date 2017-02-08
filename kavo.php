<?php
/*
  be.chiro.civi.kavo - support for the KAVO-API.
  Copyright (C) 2016  Chirojeugd-Vlaanderen vzw

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'kavo.civix.php';
require_once 'kavo.defines.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function kavo_civicrm_config(&$config) {
  _kavo_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function kavo_civicrm_xmlMenu(&$files) {
  _kavo_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function kavo_civicrm_install() {
  _kavo_civix_civicrm_install();
}

/**
* Implements hook_civicrm_postInstall().
*
* @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
*/
function kavo_civicrm_postInstall() {
  _kavo_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function kavo_civicrm_uninstall() {
  _kavo_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function kavo_civicrm_enable() {
  _kavo_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function kavo_civicrm_disable() {
  _kavo_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function kavo_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _kavo_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function kavo_civicrm_managed(&$entities) {
  _kavo_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function kavo_civicrm_caseTypes(&$caseTypes) {
  _kavo_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function kavo_civicrm_angularModules(&$angularModules) {
_kavo_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function kavo_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _kavo_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_summaryActions.
 *
 * @param $actions
 * @param $contactID
 */
function kavo_civicrm_summaryActions(&$actions, $contactID) {
  $actions['generate_kavo_id'] = [
    'title' => 'Generate KAVO-ID',
    'ref' => 'generate_kavo_id',
    'key' => 'kavo_id',
    'weight' => 0,
    'href' => CRM_Utils_System::url("civicrm/kavo/controller", "action=new_id"),
  ];
}

/**
 * Implements hook_civicrm_tabset.
 *
 * @param $tabsetName
 * @param $tabs
 * @param $context
 */
function kavo_civicrm_tabset($tabsetName, &$tabs, $context) {
  if ($tabsetName == 'civicrm/event/manage') {
    $tabs['kavo'] = [
      'title' => 'Register as KAVO course',
      'url' => 'civicrm/kavo/controller',
      'field' => 'id',
    ];
  }
}

/**
 * Implements hook_civicrm_permission.
 *
 * @param array $permissions
 */
function kavo_civicrm_permission(&$permissions) {
  $permissions['access KAVO'] = [
    ts('KAVO: Access KAVO-API'),
    ts('Retrieve data from the KAVO-API'),
  ];
  $permissions['update KAVO'] = [
    ts('KAVO: Register KAVO-information'),
    ts('Send data to the KAVO-API'),
  ];
}

function kavo_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $permissions['kavo']['authenticate'] = ['access KAVO'];
  $permissions['kavo']['hello'] = ['access KAVO'];
  // I'm not sure whether the 'access all custom data' permission
  // is really necessary.
  $permissions['kavo']['createaccount'] = [
    'access KAVO',
    'update KAVO',
    'view all contacts',
    'edit all contacts',
    'access all custom data',
  ];
  $permissions['kavo']['createcourse'] = [
    'access KAVO',
    'update KAVO',
    'view all contacts',
    'view event info',
    'edit all events',
    'access all custom data',
  ];
  $permissions['kavo']['gettraject'] = [
    'access KAVO',
    'view all contacts',
    'access all custom data'
  ];
  $permissions['kavo']['validateparticipant'] = [
    'access KAVO',
    'view all contacts',
    'view event info',
    'access all custom data',
  ];
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function kavo_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function kavo_civicrm_navigationMenu(&$menu) {
  _kavo_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'be.chiro.civi.kavo')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _kavo_civix_navigationMenu($menu);
} // */
