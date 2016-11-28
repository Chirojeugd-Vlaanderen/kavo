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
 * Interface for the KAVO-API.
 */
interface CRM_Kavo_KavoInterface {
  /**
   * Authenticate using key / secret to obtain the JWT Token.
   *
   * See https://cjsm.be/kavotest/docs/#api-Authenticate-Authenticate
   *
   * @return array
   */
  public function authenticate();

  /**
   * This is an example endpoint to test the token retrieved from the API.
   *
   * See https://cjsm.be/kavotest/docs/#api-Authenticate-Hello
   *
   * @return string
   */
  public function hello();

  /**
   * After account creation, an email is sent to the user for account confirmation (only when using API in production).
   *
   * It is important to save the kavo_id for this user. This kavo_id will be needed for all further communications.
   * See https://cjsm.be/kavotest/docs/#api-User-CreateUser
   *
   * @param array $contact
   * @return string KAVO-ID
   */
  public function createAccount(array $contact);
}