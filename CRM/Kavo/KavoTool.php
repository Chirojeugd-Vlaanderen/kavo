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
 * Access to the kavo tool, based on the CiviCRM settings.
 */
class CRM_Kavo_KavoTool implements CRM_Kavo_KavoInterface {
  /**
   * Implementation of my_http_build_query that does not ignore empty arrays.
   *
   * The KAVO API requires a sections parameter to create a course, even if
   * this settings parameter is empty. This is probably a bug in the API,
   * for which this function is a workaround.
   *
   * @param array $params containing properties.
   * @return string
   */
  private function my_http_build_query(array $params) {
    $result = http_build_query($params);

    foreach ($params as $key => $value) {
      if (is_array($value) && empty($value)) {
        $result .= "&" . $key . "=";
      }
    }
    return $result;
  }
  /**
   * Calls the KAVO API.
   *
   * @param string $resource
   * @param array $bodyParams
   * @param string $token
   * @param string $verb
   * @return array
   * @throws Exception
   */
  protected function callApi($resource, array $bodyParams, $token = NULL, $verb = 'POST') {
    // FIXME: This looks rather messy.
    $endpoint = CRM_Core_BAO_Setting::getItem('kavo', 'kavo_endpoint');
    if (empty($endpoint)) {
      throw new Exception('KAVO endpoint not configured.', CRM_Kavo_Error::API_NOT_CONFIGURED);
    }
    $endpoint .= "/${resource}";
    $curl = curl_init();
    $opts = [];
    // $opts[CURLOPT_HTTPHEADER][] = 'Content-type: application/json';
    if (isset($token)) {
      $opts[CURLOPT_HTTPHEADER][] = "Authorization: Bearer ${token}";
    }
    switch ($verb) {
      case 'GET':
        $opts[CURLOPT_URL] = $endpoint . '?' . $this->my_http_build_query($bodyParams);
        break;
      case 'POST':
        $opts[CURLOPT_URL] = $endpoint;
        $opts[CURLOPT_POST] = TRUE;
        $opts[CURLOPT_POSTFIELDS] = $this->my_http_build_query($bodyParams);
        break;
      default:
        throw new Exception('This is not implemented (yet).');
        break;
    }
    $opts[CURLOPT_RETURNTRANSFER] = TRUE;
    curl_setopt_array($curl, $opts);
    $result = json_decode(curl_exec($curl));
    // TODO: Should I use a more specific exception type?
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($httpCode >= 400) {
      if ($httpCode == KAVO_HTTP_UNAUTHORIZED) {
        throw new Exception($result->message, CRM_Kavo_Error::UNAUTHORIZED);
      }
      if ($httpCode == KAVO_HTTP_UNPROCESSABLE_ENTITY ) {
         if (isset($result->errors->email)){
            throw new Exception($result->errors->email, CRM_Kavo_Error::EMAIL_TAKEN);
         }
         if (isset($result->errors->{'sections.0.start_date'})){
            throw new Exception($result->errors->{'sections.0.start_date'}[0], CRM_Kavo_Error::START_TO_LATE);
          }
      }
      throw new Exception("HTTP status code $httpCode.", CRM_Kavo_Error::UNKNOWN);
    }
    curl_close($curl);
    return $result;
  }

  /**
   * Returns the API token. Requests a new one if the known one has expired.
   */
  protected function getToken() {
    if (CRM_Core_BAO_Setting::getItem('kavo', 'kavo_token_expiration') < new DateTime()) {
      return $this->authenticate();
    }
    else {
      return CRM_Core_BAO_Setting::getItem('kavo', 'kavo_token');
    }
  }

  /**
   * Requests a new authentication token.
   *
   * @return string The token.
   */
  public function authenticate() {
    $result = $this->callApi('authenticate', [
      'key' => CRM_Core_BAO_Setting::getItem('kavo', 'kavo_key'),
      'secret' => CRM_Core_BAO_Setting::getItem('kavo', 'kavo_secret'),
    ], NULL);
    // Store token and expiration date in variable
    // FIXME: This is rather arbitrary:
    $expiresIn = $result->expires_in * 0.9;
    $expirationDate = new DateTime();
    $expirationDate->add(new DateInterval("PT${expiresIn}S"));
    CRM_Core_BAO_Setting::setItem($expirationDate, 'kavo', 'kavo_token_expiration');
    CRM_Core_BAO_Setting::setItem($result->token, 'kavo', 'kavo_token');
    return $result->token;
  }

  /**
   * Test whether the token is valid.
   *
   * @return string The hello-message of the service if successful.
   */
  public function hello() {
    $result = $this->callApi('hello', [], $this->getToken(), 'GET');
    return $result->message;
  }

  /**
   * Create account and return KAVO-ID for contact.
   *
   * @param array $contact
   * @return string KAVO-ID
   */
  public function createAccount(array $contact) {
    $result = $this->callApi('account', $contact, $this->getToken(), 'POST');
    return $result->data->kavo_id;
  }

  /**
   * Create a KAVO course.
   *
   * When you create a course, you can also add courseSections. This is not
   * mandatory, although when adding a section, all section fields will be
   * required. In the response you will receive the course_id and the id's
   * corresponding to the created sections. You will have to store them in
   * order to update/delete a course/section later on.
   *
   * See https://cjsm.be/kavotest/docs/#api-Courses-CreateCourse
   *
   * @param array $course
   * @return string course ID
   */
  public function createCourse(array $course) {
    $result = $this->callApi('courses', $course, $this->getToken(), 'POST');
    return $result->data->course_id;
  }

  /**
   * View participant traject.
   *
   * @param string $kavoId
   * @return mixed
   */
  public function getTraject($kavoId) {
    $result = $this->callApi("participant/$kavoId/traject", [], $this->getToken(), 'GET');
    return $result->data;
  }

  /**
   * Saves a participant to mijnkadervorming.
   *
   * @param $participant - KAVO-participant (kavo_id and course_id).
   * @return string 'success'.
   */
  public function joinCourse($participant) {
    $result = $this->callApi("participant/join-course", $participant, $this->getToken(), 'POST');
    return $result->message;
  }
}
