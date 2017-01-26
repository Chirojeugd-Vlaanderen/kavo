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
 * Kavo.Createcourse API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_kavo_Createcourse_spec(&$spec) {
  $spec['event_id'] = [
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1
  ];
}

/**
 * Kavo.Createcourse API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_kavo_Createcourse($params) {
  // TODO: inject dependencies? Especially useful for the KavoInterface.
  // TODO: Refactor: This looks way too much like Kavo.createaccount.
  $kavo = new CRM_Kavo_KavoTool();
  $worker = new CRM_Kavo_Worker_CourseWorker();

  $event = $worker->get($params['event_id']);
  $validationResult = $worker->validateKavo($event);
  if ($validationResult->status != CRM_Kavo_Error::OK) {
    throw new API_Exception($validationResult->message, $validationResult->status, [
      'missing' => $validationResult->extra
    ]);
  }

  try {
    $courseId = $kavo->createCourse($worker->mapToKavo($event));
  }
  catch (Exception $e) {
    throw new API_Exception($e->getMessage(), $e->getCode());
  }

  // TODO: support sequential => 1

  $saveResult = civicrm_api3('Event', 'create', [
    'id' => $event['id'],
    CRM_Kavo_Field::COURSE_ID() => $courseId,
  ]);

  // CiviCRM does not return custom fields in a create-result, so we'll add
  // the event ID to the result ourselves.
  $saveResult['values'][$params['event_id']][CRM_Kavo_Field::COURSE_ID()] = $courseId;

  return $saveResult;
}

