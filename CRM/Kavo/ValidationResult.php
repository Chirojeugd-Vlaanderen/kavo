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

class CRM_Kavo_ValidationResult {
  /**
   * CRM_Kavo_ValidationResult constructor.
   * @param int $status
   * @param string $message
   * @param array $extra
   */
  public function __construct($status, $message, array $extra) {
    $this->status = $status;
    $this->message = $message;
    $this->extra = $extra;
  }

  /**
   * @var int Error code. 0 if everything is ok.
   */
  public $status;
  /**
   * @var string Human readable error message.
   */
  public $message;
  /**
   * @var (optional) additional info.
   */
  public $extra;

  /**
   * Add extra status bits.
   *
   * @param int $status bits to add to the result status.
   */
  public function addStatus($status) {
    $this->status |= $status;
  }

  /**
   * Appends a string to the message.
   *
   * @param string $message Text to append to the message.
   */
  public function addMessage($message) {
    $this->message .= $message;
  }
}