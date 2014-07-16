<?php
namespace JDI\TntAffiliate;

use JDI\TntAffiliate\Api\ApiClient;

abstract class ApiBase
{
  const DEFAULT_API_URL = 'https://api.tntaffiliate.com';
  /**
   * @var ApiClient
   */
  protected $_client;

  protected $_token;
  protected $_tokenType;

  /**
   * @param string $apiUrl API URL to connect to
   */
  public function __construct($apiUrl = self::DEFAULT_API_URL)
  {
    $this->_client = new ApiClient($apiUrl);
  }

  /**
   * Set the token on the client, if it has been cached
   *
   * @param $token
   * @param $tokenType
   *
   * @return $this
   */
  public function setToken($token, $tokenType)
  {
    $this->_token     = $token;
    $this->_tokenType = $tokenType;
    $this->_setAuthHeader();
    return $this;
  }

  /**
   * Retrieve the current token
   *
   * @return string
   */
  public function getToken()
  {
    return $this->_token;
  }

  /**
   * Retrieve the current token type
   *
   * @return string
   */
  public function getTokenType()
  {
    return $this->_tokenType;
  }

  protected function _clientPost($call, array $params = [])
  {
    return $this->_client->post($call, $params);
  }

  /**
   * Set the auth header on the api client
   */
  protected function _setAuthHeader()
  {
    $this->_client->addGlobalHeader(
      'Authorization',
      $this->_tokenType . ' ' . $this->_token
    );
  }

  /**
   * Login to the API Service
   *
   * @param       $clientId
   * @param       $clientSecret
   * @param array $options
   *
   * @return bool
   */
  public function login($clientId, $clientSecret, array $options = null)
  {
    $result = $this->_clientPost(
      '/auth/token',
      array_merge(
        (array)$options,
        [
          'grant_type'    => 'client_credentials',
          'client_id'     => $clientId,
          'client_secret' => $clientSecret,
        ]
      )
    );
    if($result->getStatusCode() == 200)
    {
      $result = (array)$result->getResult();

      $this->setToken($result['access_token'], $result['token_type']);
      return true;
    }
    return false;
  }

  /**
   * Retrieve the API Version number
   *
   * @return string
   */
  public function getVersion()
  {
    return $this->_clientPost('version')->getResult();
  }

  public function whoami()
  {
    return $this->_clientPost('auth/whoami')->getResult();
  }
}
