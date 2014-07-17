<?php
namespace JDI\TntAffiliate\Models;

class RefundOptions implements \JsonSerializable
{
  const FIXED = 'fixed';
  const PERCENT = 'percent';

  public $amount;
  public $type;
  public $reason;

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }
}
