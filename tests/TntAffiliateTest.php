<?php

class TntAffiliateTest extends PHPUnit_Framework_TestCase
{
  public function testGetVisitorId()
  {
    $cookies = $_COOKIE;

    unset($_COOKIE[\JDI\TntAffiliate\TntAffiliate::VISITOR_COOKIE]);
    $this->assertNull(\JDI\TntAffiliate\TntAffiliate::getVisitorId());

    $vid = 'VID:TEST:VISITOR';

    $_COOKIE[\JDI\TntAffiliate\TntAffiliate::VISITOR_COOKIE] = $vid;
    $this->assertEquals($vid, \JDI\TntAffiliate\TntAffiliate::getVisitorId());

    $_COOKIE = $cookies;
  }
}
