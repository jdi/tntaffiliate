<?php
namespace JDI\TntAffiliate\Models;

class RefundOptions implements \JsonSerializable
{
  const RECLAIM_FIXED = 'fixed';
  const RECLAIM_PERCENT = 'percent';
  const RECLAIM_COMMISSION = 'commission';
  const RECLAIM_RESERVE = 'reserve';
  const RECLAIM_BOTH = 'both';
  const RECLAIM_NONE = 'none';

  public $type;
  public $reason;

  public $amount; // Refund amount to customer
  public $fullRefund; // bool, is whole action refunded
  public $reclaim; // reserve,commission,none,percent,fixed
  public $reclaimAmount; // if % or fixed, reclaim this amount

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }
}
