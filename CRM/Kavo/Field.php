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
 * Custom fields created by the kavo extension.
 *
 * (One right now, but more to follow, like kavo course ID).
 * I would love to do this with something like class constants, but you cannot
 * assign the result of a function call to a const.
 */
class CRM_Kavo_Field {
  /**
   * Returns the api identifier for the custom field containing the KAVO-ID.
   * @return string
   */
  static function KAVO_ID() {
    // You need to install the be.chiro.civi.idcache extension for this to work.
    return CRM_IdCache_Cache_CustomField::getApiField('individual_kavo_fields', 'kavo_id');
  }
}