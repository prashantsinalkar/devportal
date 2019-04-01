<?php

/********************************************************* {COPYRIGHT-TOP} ***
 * Licensed Materials - Property of IBM
 * 5725-L30, 5725-Z22
 *
 * (C) Copyright IBM Corporation 2018, 2019
 *
 * All Rights Reserved.
 * US Government Users Restricted Rights - Use, duplication or disclosure
 * restricted by GSA ADP Schedule Contract with IBM Corp.
 ********************************************************** {COPYRIGHT-END} **/

namespace Drupal\ibm_apim\Rest\Payload;

use Drupal\ibm_apim\Rest\Exception\RestResponseParseException;
use Drupal\ibm_apim\Rest\Interfaces\RestResponseInterface;
use Drupal\ibm_apim\Rest\RestResponse;

/**
 * Create a new object to represent the response from an API call.
 */
class RestResponseReader {

  /**
   * Constructor.
   */
  public function __construct() {
  }

  /**
   * Read rest response.
   *
   * @param $response
   * @param null $response_object
   *
   * @return \Drupal\ibm_apim\Rest\RestResponse|null
   */
  public function read($response, $response_object = NULL): ?RestResponseInterface {

    if ($response_object === NULL) {
      $response_object = new RestResponse();
    }

    try {
      $response_object->setCode($this->parseCode($response));
      $response_object->setHeaders($this->parseHeaders($response));
      $response_object->setData($this->parseData($response));
      $code = $response_object->getCode();
      if ($code >= 400 && $code !== 404) {
        $response_object->setErrors($this->parseErrors($response));
      }
    } catch (RestResponseParseException $exception) {
      if (!isset($GLOBALS['__PHPUNIT_BOOTSTRAP']) && \Drupal::hasContainer()) {
        $contact_link = \Drupal::l(t('contact'), \Drupal\Core\Url::fromRoute('contact.site_page'));
        drupal_set_message(t('We appear to be having trouble processing your request. Please try again later or @contact_link the owner of this site if the problem persists.', ['@contact_link' => $contact_link]), 'warning');
      }
      \Drupal::logger('auth_apic')
        ->error('Exception occurred while parsing response from management appliance. Exception was: @exception', ['@exception' => $exception->getMessage()]);
    }

    return $response_object;

  }

  /**
   * Read the status code.
   *
   * @param $response
   *
   * @return null|int
   * @throws \Drupal\ibm_apim\Rest\Exception\RestResponseParseException
   */
  private function parseCode($response): ?int {

    if (empty($response->code)) {
      throw new RestResponseParseException('No status code in response. ' . serialize($response));
    }
    return $response->code;
  }

  /**
   * Read the HTTP headers.
   *
   * @param $response
   *
   * @return null|array
   * @throws \Drupal\ibm_apim\Rest\Exception\RestResponseParseException
   */
  private function parseHeaders($response): ?array {
    if (empty($response->headers)) {
      throw new RestResponseParseException('No HTTP headers in response');
    }
    return $response->headers;
  }

  /**
   * Parse the errors from the management server.
   *
   * @param $response
   *
   * @return mixed
   * @throws \Drupal\ibm_apim\Rest\Exception\RestResponseParseException
   */
  private function parseErrors($response) {

    if (empty($response->data) || (empty($response->data['errors']) && empty($response->data['message']))) {
      throw new RestResponseParseException('No errors present in failed API call.');
    }
    $details = empty($response->data['errors']) ? $response->data['message'] : $response->data['errors'];
    return $details;

  }

  /**
   * Read the HTTP response body as is and store it.
   *
   * @param $response
   *    HTTP response.
   *
   * @return mixed
   */
  protected function parseData($response) {
    return $response->data;
  }

}