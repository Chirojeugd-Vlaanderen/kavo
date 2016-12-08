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

/**
 * Collection of upgrade steps.
 */
class CRM_Kavo_Upgrader extends CRM_Kavo_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Re-apply the configuration every time the extension is enabled.
   *
   * @return TRUE if installation succeeded.
   * @throws Exception
   */
  public function enable() {
    $configResult = civicrm_api3('Civiconfig', 'load_json', [
      // there should be a better way to do this.
      'path' => realpath(__DIR__ . '/../../') . '/resources/'
    ]);
    // If you get an error below
    // 'API (Civiconfig, load_json) does not exist (join the API team and implement it!)',
    // you need to install the org.civicoop.configitems extension.
    CRM_Kavo_Check::assertValidApiResult($configResult);
    return TRUE;
  }

  /**
   * Fix configuration issue.
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_4701() {
    $this->ctx->log->info('Applying update 4701');
    // If you installed and enabled an earlier version of this extension,
    // the custom field for 'total_course_period' was called 'duration'.
    // I cannot fix this by just changing custom_groups.json, because
    // load_json will complain about an existing field. So let's work around
    // this:

    $deleteResult = civicrm_api3('CustomField', 'get', [
      'custom_group_id' => 'course_kavo_fields',
      'name' => 'duration',
      'api.CustomField.delete' => ['id' => '$value.id'],
    ]);
    CRM_Kavo_Check::assertValidApiResult($deleteResult);
    $configResult = civicrm_api3('Civiconfig', 'load_json', [
      // there should be a better way to do this.
      'path' => realpath(__DIR__ . '/../../') . '/resources/'
    ]);
    CRM_Kavo_Check::assertValidApiResult($configResult);
    return TRUE;
  }
}

