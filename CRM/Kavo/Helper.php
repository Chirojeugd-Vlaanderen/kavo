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
 * Class with easy static helper functions.
 */
class CRM_Kavo_Helper {
  /**
   * Extract the bits from an integer in an array.
   *
   * The bits are extracted with their absolute value. So 1, 2, 4, ...
   *
   * @param int $i
   * @return array
   */
  public static function getBits($i) {
    $result = [];
    $current = 1;
    while ($i) {
      if ($i & 1) {
        $result[] = $current;
      }
      $i >>= 1;
      $current <<= 1;
    }
    return $result;
  }

  /**
   * Returns TRUE if $item is empty, or the literal string 'null'.
   *
   * (because CiviCRM seems to use the string 'null' for null values sometimes.
   * https://issues.civicrm.org/jira/browse/CRM-11819)
   *
   * @param $item
   * @return bool
   */
  public static function civiNullOrEmpty($item) {
    $result = (empty($item) || $item == 'null');
    return $result;
  }
}