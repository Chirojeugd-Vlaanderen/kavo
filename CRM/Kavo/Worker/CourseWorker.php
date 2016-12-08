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
 * KAVO course logic.
 */
class CRM_Kavo_Worker_CourseWorker extends CRM_Kavo_Worker {
  public function __construct() {
    parent::__construct('Event');
  }

  /**
   * Returns the required fields for a new entity in the kavo tool.
   *
   * @return array
   */
  public function getKavoRequiredKeys() {
    return [
      'target',
      'max_participants',
      'price_participant',
      'responsible_last_name',
      'responsible_first_name',
      'responsible_email',
      'responsible_phone',
      'number_of_sections',
      'other_sections_unknown',
      'total_course_period',
      'acknowledgement_type',
    ];
  }

  /**
   * Returns the relevant fields for CiviCRM to return.
   *
   * @return array
   */
  protected function getCiviRelevantKeys() {
    return [
      CRM_Kavo_Field::TARGET(),
      'max_participants',
      // we'll need chaining for responsible information.
      CRM_Kavo_Field::TOTAL_COURSE_PERIOD(),
      CRM_Kavo_Field::ACKNOWLEDGEMENT_TYPE(),
      // Participant price is stored in a dedicated custom field, because
      // we don't know which price field to use.
      CRM_Kavo_Field::PRICE_PARTICIPANT(),
      // If we want to do chained calls, we need to retrieve the fields
      // needed for chaining.
      'price_set_id',
      CRM_Kavo_Field::RESPONSIBLE_CONTACT_ID(),
    ];
  }

  /**
   * Returns the required chained calls to retrieve the entity from CiviCRM.
   *
   * @return array
   */
  protected function getCiviChainedRequests() {
    return [
      // I fetch the price fields and the relevant price field values, but
      // I don't know which price field to use for the participant price.
      // (We call it 'deelnameprijs', but this should be configurable.)
      'api.PriceField.get' => [
        'price_set_id' => '$value.price_set_id',
        // Because this is not configurable (yet?), I will use a custom field
        // for participant price.
        'name' => 'deelnameprijs',
        'api.PriceFieldValue.get' => ['is_default' => 1]
      ],
      'api.Contact.getsingle' => [
        'id' => '$value.' . CRM_Kavo_Field::RESPONSIBLE_CONTACT_ID() . '_id',
        'return' => ['first_name', 'last_name', 'email', 'phone'],
      ],
    ];
  }

  /**
   * Maps a CiviCRM event to a KAVO course.
   *
   * @param array $civiEntity CiviCRM event.
   * @return array KAVO course.
   */
  public function mapToKavo(array $civiEntity) {
    return [
      'target' => $civiEntity[CRM_Kavo_Field::TARGET()],
      'max_participants' => $civiEntity['max_participants'],
      'price_participants' => $civiEntity[CRM_Kavo_Field::PRICE_PARTICIPANT()],
      'responsible_last_name' => $civiEntity['api.Contact.getsingle']['last_name'],
      'responsible_first_name' => $civiEntity['api.Contact.getsingle']['first_name'],
      'responsible_email' => $civiEntity['api.Contact.getsingle']['email'],
      'responsible_phone' => $civiEntity['api.Contact.getsingle']['phone'],
      // We don't do sections.
      'number_of_sections' => 0,
      'other_sections_unknown' => TRUE,
      'total_course_period' => $civiEntity[CRM_Kavo_Field::TOTAL_COURSE_PERIOD()],
      'acknowledgement_type' => $civiEntity[CRM_Kavo_Field::ACKNOWLEDGEMENT_TYPE()],
    ];
  }

  /**
   * Checks whether the given event can be used to create a KAVO course.
   *
   * @param array $civiEntity
   * @return \CRM_Kavo_ValidationResult
   */
  public function canCreate(array $civiEntity) {
    $result = parent::canCreate($civiEntity);
    if (empty($civiEntity[CRM_Kavo_Field::RESPONSIBLE_CONTACT_ID()])) {
      $result->addStatus(CRM_Kavo_Error::RESPONSIBLE_MISSING);
      $result->addMessage("A course needs a responsible contact.\n");
    }
    if (!empty($civiEntity[CRM_Kavo_Field::COURSE_ID()])) {
      $result->addStatus(CRM_Kavo_Error::ALREADY_REGISTERED);
      $result->addMessage("This course already has a KAVO-ID.\n");
    }
    return $result;
  }
}