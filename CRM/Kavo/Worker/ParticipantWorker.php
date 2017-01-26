<?php
/*
  be.chiro.civi.kavo - support for the KAVO-API.
  Copyright (C) 2017  Chirojeugd-Vlaanderen vzw

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
 * Data access to CiviCRM participants, validation and mapping to KAVO participants.
 */
class CRM_Kavo_Worker_ParticipantWorker extends CRM_Kavo_Worker {

  /**
   * Returns the required fields for a new entity in the kavo tool.
   *
   * @return array
   */
  public function getKavoRequiredKeys() {
    return [
      'kavo_id',
      'course_id',
    ];
  }

  /**
   * Returns the relevant fields for CiviCRM to return.
   *
   * @return array
   */
  protected function getCiviRelevantKeys() {
    return [
      'contact_id',
      'event_id',
    ];
  }

  /**
   * Returns the required chained calls to retrieve the entity from CiviCRM.
   *
   * @return array
   */
  protected function getCiviChainedRequests() {
    return [
      'api.Contact.getsingle' => [
        'id' => '$value.contact_id',
        'return' => [CRM_Kavo_Field::KAVO_ID()],
      ],
      'api.Event.getsingle' => [
        'id' => '$value.event_id',
        'return' => [
          CRM_Kavo_Field::COURSE_ID(),
          CRM_Kavo_Field::ACKNOWLEDGEMENT_TYPE(),
          'start_date',
        ],
      ],
    ];
  }

  /**
   * Map the CiviCRM entity to the corresponding KAVO-entity.
   *
   * @param array $civiEntity
   * @return array
   */
  public function mapToKavo(array $civiEntity) {
    return [
      'kavo_id' => $civiEntity['api.Contact.getsingle'][CRM_Kavo_Field::KAVO_ID()],
      'course_id' => $civiEntity['api.Event.getsingle'][CRM_Kavo_Field::COURSE_ID()],
    ];
  }

  /**
   * Checks whether the given participant can be used to create a KAVO participant.
   *
   * @param array $civiEntity a CiviCRM participant.
   * @return \CRM_Kavo_ValidationResult
   */
  public function canCreate(array $civiEntity) {
    $result = parent::canCreate($civiEntity);

    if ($civiEntity['role_id'] != CRM_Kavo_Role::ATTENDEE()) {
      // We only care about attendees atm.
      return $result;
    }

    return $result;
  }

  /**
   * Creates a CiviCRM participant to be used with this worker. Does not save.
   *
   * The problem I want to solve, is that I want to be able to check the the
   * requirements for participants (#10) before the participant is actually
   * created in CiviCRM.
   *
   * So this function provides a particpant-array as if it was retrieved
   * by the get() function. I.e. it contains the keys 'api.Contact.getsingle'
   * and 'api.Event.getsingle'.
   *
   * @param $contactId int
   * @param $eventId int
   * @param $roleId int participant role id.
   * @return array
   */
  public function create($contactId, $eventId, $roleId) {
    // TODO: make this a generic function in the base Worker class.
    // All needed information is in $this->getCiviChainedRequests().
    $result = [
      'contact_id' => $contactId,
      'event_id' => $eventId,
      'role_id' => $roleId,
      'api.Contact.get' => civicrm_api3('Contact', 'get', [
        'id' => $contactId,
        'return' => [CRM_Kavo_Field::KAVO_ID()],
      ]),
      'api.Event.get' => civicrm_api3('Event', 'get', [
        'id' => $eventId,
        'return' => [
          CRM_Kavo_Field::COURSE_ID(),
          CRM_Kavo_Field::ACKNOWLEDGEMENT_TYPE(),
          'start_date',
        ],
      ]),
    ];
    return $result;
  }
}