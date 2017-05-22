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

// NOTE: These codes should be the same as in
// templates/CRM/Kavo/Form/Controller.tpl.
/**
 * Error codes used by the kavo extension.
 */
class CRM_Kavo_Error {
  const OK = 0;
  const REQUIRED_FIELDS_MISSING = 1;
  const WRONG_CONTACT_TYPE = 2;
  const ALREADY_REGISTERED = 4;
  const EMAIL_TAKEN = 8;
  const RESPONSIBLE_MISSING = 16;
  const ADDRESS_MISSING = 32;
  const PARTICIPANT_TOO_YOUNG = 64;
  const PARTICIPANT_LACKS_EXPERIENCE = 128;
  const UNAUTHORIZED = 16384;
  const API_NOT_CONFIGURED = 32768;
  const UNKNOWN = 65536;

  // Untranslated error messages:
  public static $messages = [
    self::OK => 'Ok',
    self::REQUIRED_FIELDS_MISSING => 'Required fields missing',
    self::WRONG_CONTACT_TYPE => 'Wrong contact type',
    self::ALREADY_REGISTERED => 'Already registered',
    self::EMAIL_TAKEN => 'Email taken',
    self::RESPONSIBLE_MISSING => 'Responsible missing',
    self::ADDRESS_MISSING => 'Address missing',
    self::PARTICIPANT_TOO_YOUNG => 'Participant too young',
    self::PARTICIPANT_LACKS_EXPERIENCE => 'Participant lacks experience',
    self::UNAUTHORIZED => 'KAVO-API: unauthorized',
    self::API_NOT_CONFIGURED => 'API not configured',
    self::UNKNOWN => 'Unknown error',
    ];
}
