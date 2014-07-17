<?php
namespace JDI\TntAffiliate\Models;

class ActionOptions implements \JsonSerializable
{
  public $userReference;
  public $eventReference;
  public $amount;
  public $data;
  public $pixels = false;

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }
}
 