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
 * Returns participant role IDs.
 */
class CRM_Kavo_Role {
  static function ATTENDEE() {
    // FIX ME: make this configurable.
    // We are not sure if every CiviCRM instance has a participant role with
    // name 'Attendee'.
    return CRM_IdCache_Cache_OptionValue::getValue('participant_role', 'Attendee');
  }
}