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
   * CRM_Kavo_Worker_ParticipantWorker constructor.
   */
  public function __construct() {
    parent::__construct('Participant');
  }

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
   * Checks whether participant validation is required.
   *
   * @param $op - operation: 'create', 'view', 'edit', 'delete'
   * @param array $params - the params as passed to hook_civicrm_pre.
   * @return boolean
   */
  public function needsValidation($op, array $params) {
    if ($op != 'create' && $op != 'edit') {
      return FALSE;
    }

    // Don't require validation on update, because participants might have been
    // created before the extension was enabled (see #27). Except when the
    // status equals 'attended' (see #30).
    // FIXME: if status_id equals 'attended', a check is only required when status_id changes.
    return ($op == 'create' && !empty(Civi::settings()->get('kavo_enforce')))
      || ($params['status_id'] == CRM_Kavo_Status::ATTENDED());
  }

  /**
   * Returns contact_id, event_id and participant_role_id for $participantParams.
   *
   * If the ID's are contained within $participantParams, return those ID's.
   * If not, get them from the existing participant (if possible).
   *
   * @param array $participantParams - params as sent to hook_civicrm_pre
   * @return array with keys contact_id, event_id, participant_role_id.
   */
  public function getIds(array $participantParams) {
    if (!CRM_Kavo_Helper::civiNullOrEmpty(CRM_Utils_Array::value('contact_id', $participantParams))
      && !CRM_Kavo_Helper::civiNullOrEmpty(CRM_Utils_Array::value('event_id', $participantParams))
      && !CRM_Kavo_Helper::civiNullOrEmpty(CRM_Utils_Array::value('participant_role_id', $participantParams))
      || empty(CRM_Utils_Array::value('id', $participantParams))) {
      // If all ID's are already present, use the ID's from $participantParams.
      // Also if no participant id is given, so that we can't retrieve existing
      // values, return the ID's from $participantParams.
      return array_intersect_key($participantParams, array_flip(['contact_id', 'event_id', 'participant_role_id']));
    }
    $existing = $this->get($participantParams['id']);
    $result = [];

    $result['contact_id'] = empty($params['contact_id']) ? $existing['contact_id']: $params['contact_id'];
    $result['event_id'] = empty($params['event_id']) ? $existing['event_id']: $params['event_id'];
    $result['participant_role_id'] = empty($params['participant_role_id']) ? $existing['participant_role_id']: $params['participant_role_id'];

    return $result;
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
      'participant_role_id',
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
        'return' => [
          'display_name',
          'birth_date',
          CRM_Kavo_Field::KAVO_ID(),
        ],
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
      'kavo_id' => $this->getContact($civiEntity)[CRM_Kavo_Field::KAVO_ID()],
      'course_id' => $this->getCourse($civiEntity)[CRM_Kavo_Field::COURSE_ID()],
    ];
  }

  /**
   * Returns the event of the CiviCRM participant API result.
   *
   * @param array $participant - participant as returned by $this->get().
   * @return array
   */
  public function getCourse(array $participant) {
    return $participant['api.Event.getsingle'];
  }

  /**
   * Returns the contact of the CiviCRM participant API result.
   *
   * @param array $participant - participant as returned by $this->get().
   * @return array
   */
  public function getContact(array $participant) {
    return $participant['api.Contact.getsingle'];
  }

  /**
   * Checks whether the given CiviCRM-participant can be used to create a KAVO
   * participant.
   *
   * @param array $civiEntity a CiviCRM participant.
   * @return \CRM_Kavo_ValidationResult
   * @throws \Exception
   */
  public function validateKavo(array $civiEntity) {
    // TODO: split function
    $contact = $this->getContact($civiEntity);
    $event = $this->getCourse($civiEntity);
    $result = new CRM_Kavo_ValidationResult(0, 'OK', []);

    if ($civiEntity['participant_role_id'] != CRM_Kavo_Role::ATTENDEE()) {
      // We only care about attendees atm.
      return $result;
    }
    if (!$this->isKavoCourse($event)) {
      // We don't care if the event is not a KAVO course.
      return $result;
    }

    $result = parent::validateKavo($civiEntity);

    if (empty($contact[CRM_Kavo_Field::KAVO_ID()])) {
      $result->addStatus(CRM_Kavo_Error::REQUIRED_FIELDS_MISSING);
      $result->addMessage('You need a KAVO-ID to subscribe to this course.');
      $result->extra['missing'][] = 'kavo_id';
    }

    // Age check.

    // We need to verify the age that the participant will have in the calendar
    // year of the course, see the specs, #10.
    $ageRestriction = [
      'animator' => 16,
      'hoofdanimator' => 18,
      'instructeur' => 19,
    ];
    $yearOfBirth = $this->extractYear($contact['birth_date']);
    $yearOfEvent = $this->extractYear($event['start_date']);
    if (empty($yearOfBirth)) {
      $result->addStatus(CRM_Kavo_Error::REQUIRED_FIELDS_MISSING);
      $result->addMessage('Birth date is required to subscribe.');
      $result->extra['missing'][] = 'birth_date';
    }
    if (empty($yearOfEvent)) {
      $result->addStatus(CRM_Kavo_Error::REQUIRED_FIELDS_MISSING);
      $result->addMessage('Event date missing.');
      $result->extra['missing'][] = 'start_date';
    }

    if (!empty($yearOfBirth) && !empty($yearOfEvent)) {
      $age = $yearOfEvent - $yearOfBirth;
      $minAge = $ageRestriction[$event[CRM_Kavo_Field::ACKNOWLEDGEMENT_TYPE()]];
      if ($age < $minAge) {
        $result->addStatus(CRM_Kavo_Error::PARTICIPANT_TOO_YOUNG);
        $result->addMessage("Participant too young; born after " . ($yearOfEvent - $minAge) . ".\n");
        $result->extra['ageLimit'] = $minAge;
      }
    }

    if (!empty($result->extra['missing'])) {
      $result->extra['missing'] = array_unique($result->extra['missing']);
    }

    if (!empty($result->status)) {
      // If we have errors here, we return. Because the checks below can
      // fail if e.g. the contact doesn't have a KAVO-id.
      return $result;
    }

    // For the moment we use the same criteria for hoofdanimator and
    // instructeur, which might not be 100% correct. But it is unclear
    // for everybody anyway ;-)
    switch ($event[CRM_Kavo_Field::ACKNOWLEDGEMENT_TYPE()]) {
      case "hoofdanimator":
      case "instructeur":
        // if you are a certified animator, everything's fine.
        if (!$this->isCertified($civiEntity, 'animator')) {
          // Otherwise we assume by default that you have the required
          // experience.
          $experience = 1;
          // But you can override this by implementing hook_kavo_experience.
          CRM_Utils_Hook::singleton()->invoke(3, $experience,
            $contact['id'], DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $event['start_date']),
            CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject,
            CRM_Utils_Hook::$_nullObject, 'kavo_experience');
          if (!$experience) {
            $result->addStatus(CRM_Kavo_Error::PARTICIPANT_LACKS_EXPERIENCE);
            $result->addMessage("A year of experience as leader is required.\n");
          }
        }
        break;
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
      'participant_role_id' => $roleId,
      'api.Contact.getsingle' => civicrm_api3('Contact', 'getsingle', [
        'id' => $contactId,
        'return' => ['birth_date', CRM_Kavo_Field::KAVO_ID()],
      ]),
      'api.Event.getsingle' => civicrm_api3('Event', 'getsingle', [
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

  /**
   * Checks in the KAVO-tool whether the participant $civiParticipant is animator.
   * @param array $civiParticipant
   * @param string $acknowledgement animator, hoofdanimator, instructeur
   * @return bool
   */
  public function isCertified(array $civiParticipant, $acknowledgement) {
    $result = civicrm_api3('Kavo', 'gettraject', [
      // TODO: Find a better way to get kavo_id of participant.
      'kavo_id' => $this->getContact($civiParticipant)[CRM_Kavo_Field::KAVO_ID()],
    ]);
    return $result['values'][$acknowledgement]['certification'];
  }

  /**
   * Returns TRUE if the given $civiEvent is a KAVO course.
   *
   * @param array $civiEvent
   * @return bool
   */
  public function isKavoCourse(array $civiEvent) {
    return !empty($civiEvent[CRM_Kavo_Field::COURSE_ID()]);
  }
}