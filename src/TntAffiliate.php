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

  public static function getClientIp()
  {
    static $ip;
    $ipKeys = [
      'HTTP_CLIENT_IP',
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_FORWARDED',
      'HTTP_X_CLUSTER_CLIENT_IP',
      'HTTP_FORWARDED_FOR',
      'HTTP_FORWARDED',
      'REMOTE_ADDR'
    ];

    if($ip === null)
    {
      foreach($ipKeys as $ipKey)
      {
        $ipString = isset($_SERVER[$ipKey]) ? $_SERVER[$ipKey] : null;

        if($ipString !== null)
        {
          foreach(explode(",", $ipString) as $ip)
          {
            if(filter_var($ip, FILTER_VALIDATE_IP) !== false)
            {
              return $ip;
            }
          }
        }
      }
      $ip = "";
    }

    return $ip;
  }
}
