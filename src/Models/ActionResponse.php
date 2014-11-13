<?php
namespace JDI\TntAffiliate\Models;

use JDI\TntAffiliate\Api\ApiResult;

class ActionResponse
{
  protected $_actionId;
  protected $_pixels;
  protected $_affiliate;
  protected $_campaign;
  protected $_sid1;
  protected $_sid2;
  protected $_sid3;

  public function __construct(ApiResult $result)
  {
    $res              = $result->getResult();
    $this->_actionId  = isset($res->actionId) ? $res->actionId : null;
    $this->_pixels    = isset($res->pixels) ? $res->pixels : null;
    $this->_sid1      = isset($res->sid1) ? $res->sid1 : null;
    $this->_sid2      = isset($res->sid2) ? $res->sid2 : null;
    $this->_sid3      = isset($res->sid3) ? $res->sid3 : null;
    $this->_affiliate = isset($res->affiliate) ? $res->affiliate : null;
    $this->_campaign  = isset($res->campaign) ? $res->campaign : null;
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

  public function getAffiliate()
  {
    return $this->_affiliate;
  }

  public function getCampaign()
  {
    return $this->_campaign;
  }

  public function getSid1()
  {
    return $this->_sid1;
  }

  public function getSid2()
  {
    return $this->_sid2;
  }

  public function getSid3()
  {
    return $this->_sid3;
  }
}
