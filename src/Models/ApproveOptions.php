<?php
namespace JDI\TntAffiliate\Models;

class ApproveOptions implements \JsonSerializable
{
  public $type;
  public $reason;

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }
}
 