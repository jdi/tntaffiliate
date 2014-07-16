<?php
namespace JDI\TntAffiliate;

use JDI\TntAffiliate\Api\ApiResult;
use JDI\TntAffiliate\Constants\RefundType;
use JDI\TntAffiliate\Models\Action;
use JDI\TntAffiliate\Models\ActionResponse;
use JDI\TntAffiliate\Models\Pixel;

class TntAffiliateApi extends ApiBase
{
  /**
   * Create a new affiliate
   *
   * @param string $email    Affiliates email address
   * @param string $password Affiliates raw password
   * @param string $name     Affiliates Name
   *
   * @return bool
   */
  public function createAffiliate($email, $password, $name)
  {
    try
    {
      $this->_clientPost(
        'affiliates/create',
        ['name' => $name, 'email' => $email, 'password' => $password]
      );
      return true;
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
    return false;
  }

  /**
   * @param Action $action
   *
   * @return ActionResponse
   */
  public function trigger(Action $action)
  {
    return $this->triggerAction(
      $action->getType(),
      $action->getVisitorId(),
      $action->getOptions()
    );
  }

  /**
   * Trigger an action
   *
   * @param string $action
   * @param string $visitorId Visitor ID or User Reference
   * @param array  $options
   *
   * @return ActionResponse
   */
  public function triggerAction($action, $visitorId, array $options = [])
  {
    return new ActionResponse(new ApiResult());
  }

  /**
   * @param string $actionRef visitor ID or action reference e.g. Order ID
   * @param string $state     ApprovalState::
   *
   * @return bool
   */
  public function approveAction($actionRef, $state)
  {
    return false;
  }

  /**
   * Refund an action
   *
   * @param string $actionId
   * @param string $type
   * @param array  $options
   *
   * @return bool
   */
  public function refund(
    $actionId, $type = RefundType::REFUND, array $options = []
  )
  {
    return false;
  }

  /**
   * Retrieve pending pixels for the visitor specified
   *
   * @param null $visitorId if null, visitor ID will be attempted automatically
   *
   * @return Pixel[]
   */
  public function getPendingPixels($visitorId = null)
  {
    if($visitorId === null)
    {
      $visitorId = TntAffiliate::getVisitorId();
    }

    return [];
  }
}
