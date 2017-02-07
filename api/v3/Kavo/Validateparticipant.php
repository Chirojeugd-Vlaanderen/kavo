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
 * Kavo.Validateparticipant API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_kavo_Validateparticipant_spec(&$spec) {
  $spec['event_id'] = [
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
  $spec['contact_id'] = [
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
  $spec['role_id'] = [
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
}

/**
 * Kavo.Validateparticipant API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_kavo_Validateparticipant($params) {
  $worker = new CRM_Kavo_Worker_ParticipantWorker();
  // TODO: handle $params['contact_id'] = ['in' => [/*array*/]].
  $civiParticipant = $worker->create($params['contact_id'], $params['event_id'], $params['role_id']);
  $validationResult = $worker->validateKavo($civiParticipant);
  return civicrm_api3_create_success((array)$validationResult, 'Kavo', 'validateparticipant');
}

