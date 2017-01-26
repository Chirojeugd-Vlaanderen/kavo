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
 * KAVO Account logic.
 */
class CRM_Kavo_Worker_AccountWorker extends CRM_Kavo_Worker {
  public function __construct() {
    parent::__construct('Contact');
  }

  /**
   * Checks whether a KAVO-ID can be created for the given $contact.
   *
   * @param array $contact
   * @return CRM_Kavo_ValidationResult
   */
  public function validateKavo(array $contact) {
    $result = parent::validateKavo($contact);
    if ($contact['contact_type'] != 'Individual') {
      $result->addStatus(CRM_Kavo_Error::WRONG_CONTACT_TYPE);
      $result->addMessage("Only individuals can get a KAVO-ID.\n");
    }
    if (!empty($contact[CRM_Kavo_Field::KAVO_ID()])) {
      $result->addStatus(CRM_Kavo_Error::ALREADY_REGISTERED);
      $result->addMessage("Contact already has a KAVO-ID.\n");
    }
    return $result;
  }

  /**
   * Returns the required fields for a new entity in the kavo tool.
   *
   * @return array
   */
  public function getKavoRequiredKeys() {
    return ['last_name', 'first_name', 'birth_date', 'email'];
  }

  /**
   * Returns the relevant fields for CiviCRM to return.
   *
   * @return array
   */
  protected function getCiviRelevantKeys() {
    return [
      'last_name',
      'first_name',
      'birth_date',
      'email',
      'contact_type',
      CRM_Kavo_Field::KAVO_ID(),
    ];
  }

  /**
   * Returns the required chained calls to retrieve the entity from CiviCRM.
   *
   * @return array
   */
  protected function getCiviChainedRequests() {
    return [];
  }

  /**
   * Map the CiviCRM entity to the corresponding KAVO-entity.
   *
   * Override as needed.
   *
   * @param array $civiEntity
   * @return array
   */
  public function mapToKavo(array $civiEntity) {
    // Contact maps 1-to-1 on KAVO account.
    return $civiEntity;
  }
}