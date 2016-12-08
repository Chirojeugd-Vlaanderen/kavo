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
 * Error codes used by the kavo extension
 */
class CRM_Kavo_Error {
  const OK = 0;
  const REQUIRED_FIELDS_MISSING = 1;
  const WRONG_CONTACT_TYPE = 2;
  const ALREADY_REGISTERED = 4;
  const EMAIL_TAKEN = 8;
  const RESPONSIBLE_MISSING = 16;
  const ADDRESS_MISSING = 32;
  const UNKNOWN = 128;
}