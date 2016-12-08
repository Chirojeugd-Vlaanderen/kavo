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
 * Returns the API-names of some custom KAVO Fields.
 *
 * This will only work if the be.chiro.civi.idcache extension is installed
 * and enabled.
 */
class CRM_Kavo_Field {
  /**
   * @return string
   */
  static function KAVO_ID() {
    // You need to install the be.chiro.civi.idcache extension for this to work.
    return CRM_IdCache_Cache_CustomField::getApiField('individual_kavo_fields', 'kavo_id');
  }

  /**
   * @return string
   */
  static function TARGET() {
    return CRM_IdCache_Cache_CustomField::getApiField('course_kavo_fields', 'target');
  }

  /**
   * @return string
   */
  static function TOTAL_COURSE_PERIOD() {
    return CRM_IdCache_Cache_CustomField::getApiField('course_kavo_fields', 'total_course_period');
  }

  /**
   * @return string
   */
  static function ACKNOWLEDGEMENT_TYPE() {
    return CRM_IdCache_Cache_CustomField::getApiField('course_kavo_fields', 'acknowledgement_type');
  }

  /**
   * @return string
   */
  static function RESPONSIBLE_CONTACT_ID() {
    return CRM_IdCache_Cache_CustomField::getApiField('course_kavo_fields', 'responsible_contact_id');
  }

  /**
   * @return string
   */
  static function PRICE_PARTICIPANT() {
    return CRM_IdCache_Cache_CustomField::getApiField('course_kavo_fields', 'price_participant');
  }

  /**
   * @return string
   */
  static function COURSE_ID() {
    return CRM_IdCache_Cache_CustomField::getApiField('course_kavo_fields', 'course_id');
  }
}