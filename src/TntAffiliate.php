<?php
namespace JDI\TntAffiliate;

class TntAffiliate
{
  const VISITOR_COOKIE = 'TNT:VID';

  /**
   * Retrieve the visitor ID from the php $_COOKIE superglobal
   *
   * @return string|null
   */
  public static function getVisitorId()
  {
    return isset($_COOKIE[self::VISITOR_COOKIE]) ?
      $_COOKIE[self::VISITOR_COOKIE] : null;
  }
}
