<?php
namespace JDI\TntAffiliate;

use JDI\TntAffiliate\Models\ActionOptions;
use JDI\TntAffiliate\Models\ActionResponse;
use JDI\TntAffiliate\Models\ApproveOptions;
use JDI\TntAffiliate\Models\Pixel;
use JDI\TntAffiliate\Models\RefundOptions;

class TntAffiliateApi extends ApiBase
{
  /**
   * Create a new affiliate
   *
   * @param string $email         Affiliates email address
   * @param string $password      Affiliates raw password
   * @param string $name          Affiliates Name
   * @param string $affiliateName Affiliate Company Name
   *
   * @return bool
   */
  public function createAffiliate($email, $password, $name, $affiliateName = '')
  {
    try
    {
      return $this->_clientPost(
        'affiliates/create',
        [
          'email'         => $email,
          'password'      => $password,
          'name'          => $name,
          'affiliateName' => $affiliateName ?: $name
        ]
      )->getStatusCode() === 200;
    }
    catch(\Exception $e)
    {
    }
    return false;
  }

  /**
   * Create a lookup reference for a visitor id
   *
   * @param string|int $reference Your reference for a visitor ID e.g. User Id
   * @param string     $visitorId Visitor ID to reference
   *
   * @return bool
   */
  public function visitorReference($reference, $visitorId = null)
  {
    if($visitorId === null)
    {
      $visitorId = TntAffiliate::getVisitorId();
    }

    return $this->_clientPost(
      'tracking/reference',
      [
        'reference' => $reference,
        'visitorId' => $visitorId
      ]
    )->getStatusCode() == 200;
  }

  /**
   * Trigger an action
   *
   * @param string        $action
   * @param string        $visitorId Visitor ID or User Reference
   * @param ActionOptions $options
   *
   * @return ActionResponse
   *
   * @throws \Exception
   */
  public function triggerAction(
    $action, $visitorId, ActionOptions $options = null
  )
  {
    if($visitorId === null)
    {
      $visitorId = TntAffiliate::getVisitorId();
    }

    if(empty($visitorId))
    {
      throw new \Exception(
        "A visitor ID or reference is required to trigger an action"
      );
    }

    return new ActionResponse(
      $this->_clientPost(
        'actions/trigger',
        [
          'type'      => $action,
          'reference' => $visitorId,
          'options'   => json_encode($options)
        ]
      )
    );
  }

  /**
   * Approve or decline a pending action
   *
   * @param string         $actionId visitor ID or action reference e.g. Order ID
   * @param string         $state    ApprovalState::
   * @param ApproveOptions $options
   *
   * @return bool
   */
  public function approveAction(
    $actionId, $state, ApproveOptions $options = null
  )
  {
    return $this->_clientPost(
      'actions/approval',
      [
        'reference' => $actionId,
        'state'     => $state,
        'options'   => json_encode($options)
      ]
    )->getStatusCode() === 200;
  }

  /**
   * Refund an action
   *
   * @param string        $actionId
   * @param RefundOptions $options
   *
   * @return bool
   */
  public function refund($actionId, RefundOptions $options = null)
  {
    try
    {
      $response = $this->_clientPost(
        'actions/refund',
        ['reference' => $actionId, 'options' => json_encode($options)]
      );
      return $response->getStatusCode() === 200 && $response->getResult();
    }
    catch(\Exception $e)
    {
      return false;
    }
  }

  /**
   * Retrieve pending pixels for the visitor specified
   *
   * @param null $visitorId if null, visitor ID will be attempted automatically
   *
   * @return Pixel[]
   *
   * @throws \Exception
   */
  public function getPendingPixels($visitorId = null)
  {
    if($visitorId === null)
    {
      $visitorId = TntAffiliate::getVisitorId();
    }

    if(empty($visitorId))
    {
      throw new \Exception(
        "A visitor ID or reference is required to trigger an action"
      );
    }

    return $this->_clientPost(
      'pixels/pending',
      ['visitorId' => $visitorId]
    )->getResult();
  }

  /**
   * Create a new visitor ID
   *
   * @param string $productId    TNTs Product ID for the visitor
   * @param string $clientIp     IP Address of the client
   * @param string $type         Traffic Type e.g. Direct
   * @param bool   $setCookie    Set the Cookie on the clients device
   * @param string $cookieDomain Domain to set the cookie on, recommended to .yourdomain.tld
   *
   * @return string Visitor ID
   */
  public function createVisitorId(
    $productId, $clientIp = null, $type = 'direct', $setCookie = false,
    $cookieDomain = null
  )
  {
    if($clientIp === null)
    {
      $clientIp = TntAffiliate::getClientIp();
    }

    $visitorId = $this->_clientPost(
      'visitors/create-id',
      [
        'type'          => $type,
        'client_ip'     => $clientIp,
        'product'       => $productId,
        'cookie_domain' => $cookieDomain
      ]
    )->getResult();

    if($setCookie)
    {
      setcookie('TNT:VID', $visitorId, 2592000, '/', $cookieDomain);
    }

    return $visitorId;
  }
}
