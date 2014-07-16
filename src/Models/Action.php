<?php
namespace JDI\TntAffiliate\Models;

class Action
{
  protected $_type;
  protected $_visitorId;
  protected $_options;

  public function __construct($type, $visitorId)
  {
    $this->_type      = $type;
    $this->_visitorId = $visitorId;
  }

  public function getType()
  {
    return $this->_type;
  }

  public function getVisitorId()
  {
    return $this->_visitorId;
  }

  public function getOptions()
  {
    return (array)$this->_options;
  }

  public function setOption($key, $value)
  {
    $this->_options[$key] = $value;
    return $this;
  }
}
