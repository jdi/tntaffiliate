<?php
namespace JDI\TntAffiliate;

use JDI\TntAffiliate\Api\ApiClient;

abstract class ApiBase
{
  /**
   * @var ApiClient
   */
  protected $_client;

  protected $_domain;
  protected $_token;
  protected $_tokenType;

  /**
   * @param string $domain Your domain (domain.tld)
   * @param null   $token
   * @param null   $tokenType
   */
  public function __construct($domain, $token = null, $tokenType = null)
  {
    $this->_domain    = $domain;
    $this->_token     = $token;
    $this->_tokenType = $tokenType;
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

  protected function _clientPost($call, array $params)
  {
    if($this->_domain && !isset($params['domain']))
    {
      $params['domain'] = $this->_domain;
    }
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
   */
  public function login($clientId, $clientSecret, array $options = null)
  {
    $result = $this->_clientPost(
      '/auth/token',
      array_merge(
        (array)$options,
        [
          'grant_type'    => 'Client_credentials',
          'client_id'     => $clientId,
          'client_secret' => $clientSecret,
        ]
      )
    );

    $result = (array)$result->getResult();
    $this->setToken($result['access_token'], $result['token_type']);
    $this->_setAuthHeader();
  }
}
