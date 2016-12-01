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
 * Some contact related logic.
 */
class CRM_Kavo_ContactWorker {
  /**
   * Returns the required fields of a contact to generate a KAVO-ID.
   *
   * See https://cjsm.be/kavotest/docs/#api-User-CreateUser
   *
   * @return array
   */
  public static function kavoRequiredKeys() {
    return ['last_name', 'first_name', 'birth_date', 'email'];
  }

  /**
   * Returns all relevant contact keys for this extension.
   *
   * @return array
   */
  public static function relevantKeys() {
    $result = self::kavoRequiredKeys();
    $result[] = 'contact_type';
    $result[] = CRM_Kavo_Field::KAVO_ID();
    return $result;
  }

  /**
   * Retrieves a contact using the API.
   *
   * @param int $contactId CiviCRM Contact ID.
   * @return array
   */
  public static function get($contactId) {
    // This will throw an exception if no unique contact is found.
    // Which is fine, I think.
    $contact = civicrm_api3('Contact', 'getsingle', [
      'id' => $contactId,
      'return' => self::relevantKeys(),
    ]);
    return $contact;
  }

  /**
   * Checks whether a KAVO-ID can be created for the given $contact.
   *
   * @param array $contact
   * @return CRM_Kavo_ValidationResult
   */
  public static function canCreateAccount(array $contact) {
    $status = 0;
    $message = '';
    if ($contact['contact_type'] != 'Individual') {
      $status |= CRM_Kavo_Error::WRONG_CONTACT_TYPE;
      $message .= "Only individuals can get a KAVO-ID.\n";
    }
    if (!empty($contact[CRM_Kavo_Field::KAVO_ID()])) {
      $status |= CRM_Kavo_Error::KAVO_ID_NOT_EMPTY;
      $message .= "Contact already has a KAVO-ID.\n";
    }
    $extra = CRM_Kavo_Check::getMissingValues(self::kavoRequiredKeys(), $contact);
    if (count($extra) > 0) {
      $status |= CRM_Kavo_Error::REQUIRED_FIELDS_MISSING;
      $message .= "Required fields missing: " . implode(', ', $extra) . "\n";
    }
    return new CRM_Kavo_ValidationResult($status, $message, $extra);
  }
}