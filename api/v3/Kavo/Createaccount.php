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
 * Kavo.Createaccount API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_kavo_Createaccount_spec(&$spec) {
  $spec['contact_id'] = [
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1
  ];
}

/**
 * Kavo.Createaccount API.
 *
 * Creates a KAVO account for the CiviCRM contact with id $params['contact_id'],
 * and saves the new KAVO-ID to the appropriate custom field.
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_kavo_Createaccount($params) {
  // TODO: inject dependencies? This would be very useful for the KavoInterface.
  // TODO: Refactor: This looks way too much like Kavo.createcourse
  $kavo = new CRM_Kavo_KavoTool();
  $worker = new CRM_Kavo_Worker_AccountWorker();

  $contact = $worker->get($params['contact_id']);
  $validationResult = $worker->canCreate($contact);
  if ($validationResult->status != CRM_Kavo_Error::OK) {
    throw new API_Exception($validationResult->message, $validationResult->status, [
      'missing' => $validationResult->extra
    ]);
  }
  // TODO: maybe create a add hook to get old_certificate and old_certificate_number.

  try {
    $kavoId = $kavo->createAccount($contact);
  }
  catch (Exception $e) {
    throw new API_Exception($e->getMessage(), $e->getCode());
  }

  // TODO: support sequential => 1
  $saveResult = civicrm_api3('Contact', 'create', [
    'id' => $contact['id'],
    CRM_Kavo_Field::KAVO_ID() => $kavoId,
  ]);

  // You would think that the result of this civicrm API call would include the
  // kavo id. Not. So let's hack it in.
  $saveResult['values'][$params['contact_id']][CRM_Kavo_Field::KAVO_ID()] = $kavoId;

  return $saveResult;
}

