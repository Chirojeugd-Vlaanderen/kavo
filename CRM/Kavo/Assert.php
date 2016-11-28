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
 * Some stupid checks.
 */
class CRM_Kavo_Assert {
  /**
   * Checks whether the array contains a non-empty value for all keys specified.
   *
   * @param array $keys Keys to check
   * @param array $array Some array
   * @return array The original array
   * @throws Exception if not all given keys are non-empty.
   */
  public static function arrayKeysNotEmpty(array $keys, array $array) {
    $problems = [];
    foreach ($keys as $key) {
      if (empty($array[$key])) {
        $problems[] = $key;
      }
    }
    if (count($problems) == 0) {
      return $array;
    }
    throw new Exception('Values missing: ' . explode(', ', $problems));
  }

  public static function validCiviApiResult(array $apiResult) {
    if ($apiResult['is_error'] == 0) {
      return $apiResult;
    }
    throw new Exception($apiResult['error_message']);
  }
}