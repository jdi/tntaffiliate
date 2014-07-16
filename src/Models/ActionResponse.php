<?php
namespace JDI\TntAffiliate\Models;

use JDI\TntAffiliate\Api\ApiResult;

class ActionResponse
{
  protected $_actionId;
  protected $_pixels;

  public function __construct(ApiResult $result)
  {
    $res             = $result->getResult();
    $this->_actionId = isset($res->actionId) ? $res->actionId : null;
    $this->_pixels   = isset($res->pixels) ? $res->pixels : null;
  }

  public function getActionId()
  {
    return $this->_actionId;
  }

  public function getPixels()
  {
    $pixels = [];
    if(!empty($this->_pixels) && is_array($this->_pixels))
    {
      foreach($this->_pixels as $pixel)
      {
        $pixels[] = new Pixel($pixel->method, $pixel->url, $pixel->content);
      }
    }
    return $pixels;
  }
}
