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
 * Abstract worker class.
 *
 * Derived classes provide logic for specific KAVO entities, without
 * dependencies to the KAVO-API.
 */
abstract class CRM_Kavo_Worker {
  protected $civiEntityType;

  /**
   * CRM_Kavo_Worker constructor.
   *
   * @param string $civiEntityType The CiviCRM entity type that corresponds
   *          to the KAVO entity this class is operating on.
   */
  public function __construct($civiEntityType) {
    $this->civiEntityType = $civiEntityType;
  }

  /**
   * Returns the required fields for a new entity in the kavo tool.
   *
   * @return array
   */
  public abstract function getKavoRequiredKeys();

  /**
   * Returns the relevant fields for CiviCRM to return.
   *
   * @return array
   */
  protected abstract function getCiviRelevantKeys();

  /**
   * Returns the required chained calls to retrieve the entity from CiviCRM.
   *
   * @return array
   */
  protected abstract function getCiviChainedRequests();

  /**
   * Map the CiviCRM entity to the corresponding KAVO-entity.
   *
   * @param array $civiEntity
   * @return array
   */
  public abstract function mapToKavo(array $civiEntity);

  /**
   * Retrieves the CiviCRM entity with given $id.
   *
   * @param int $id
   * @return array
   */
  public function get($id) {
    $request = $this->getCiviChainedRequests();
    $request['return'] = $this->getCiviRelevantKeys();
    $request['id'] = $id;

    // This will throw an exception if no unique entity is found.
    // Which is fine, I presume.
    $result = civicrm_api3($this->civiEntityType, 'getsingle', $request);
    return $result;
  }

  /**
   * Checks whether the given $civiEntity can be created in the KAVO tool.
   *
   * Override as needed.
   *
   * @param array $civiEntity
   * @return \CRM_Kavo_ValidationResult
   */
  public function validateKavo(array $civiEntity) {
    $status = 0;
    $message = '';
    $kavoEntity = $this->mapToKavo($civiEntity);
    $missing = CRM_Kavo_Check::getMissingValues($this->getKavoRequiredKeys(), $kavoEntity);
    if (count($missing) > 0) {
      $status |= CRM_Kavo_Error::REQUIRED_FIELDS_MISSING;
      $message .= "Required fields missing: " . implode(', ', $missing) . "\n";
    }
    return new CRM_Kavo_ValidationResult($status, $message, empty($missing) ? [] : ['missing' => $missing]);
  }

  /**
   * Extract date from CiviCRM API datetime result.
   *
   * @param $civiDate
   * @return string
   */
  protected function extractDate($civiDate) {
    $date = date_parse($civiDate);
    return "${date['year']}-${date['month']}-${date['day']}";
  }

  /**
   * Extract time from CiviCRM API datetime result.
   *
   * @param $civiDate
   * @return string
   */
  protected function extractTime($civiDate) {
    $date = date_parse($civiDate);
    return "${date['hour']}:${date['minute']}";
  }
   /**
   * Extract year from CiviCRM API datetime result.
   *
   * @param $civiDate
   * @return integer|NULL
   */
  protected function extractYear($civiDate) {
    $date = date_parse($civiDate);
    return $date ? intval($date['year']) : NULL;
  }
}