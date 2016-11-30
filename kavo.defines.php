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

define('KAVO_ERROR_OK' ,0);
define('KAVO_ERROR_REQUIRED_FIELDS_MISSING', 1);
define('KAVO_ERROR_WRONG_CONTACT_TYPE', 2);
define('KAVO_ERROR_KAVO_ID_NOT_EMPTY', 4);
define('KAVO_ERROR_EMAIL_TAKEN', 8);
define('KAVO_ERROR_UNKNOWN', 128);

// Hmmm... Wouldn't this be defined already somewhere?
define('KAVO_HTTP_UNPROCESSABLE_ENTITY', 422);

// If CRM_IdCache_Cache_CustomField is not found, you need to install
// the be.chiro.civi.idcache extension.
define('KAVO_FIELD_KAVO_ID', CRM_IdCache_Cache_CustomField::getApiField('individual_kavo_fields', 'kavo_id'));
