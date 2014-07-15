<?php
namespace JDI\TntAffiliate\Models;

class Pixel
{
  const METHOD_IFRAME = 'iframe';
  const METHOD_IMAGE = 'img';
  const METHOD_JS = 'js';
  const METHOD_CURL = 'curl';

  protected $_method; // iframe,img,js,curl
  protected $_url;
  protected $_content; //Full Code to bypass generation

  public function __construct($method, $url, $content = null)
  {
    $this->_method  = $method;
    $this->_url     = $url;
    $this->_content = $content;
  }

  public function getMethod()
  {
    return $this->_method;
  }

  public function getUrl()
  {
    return $this->_url;
  }

  public function getContent()
  {
    return $this->_content;
  }

  public function render()
  {
    if(!empty($this->_content))
    {
      return $this->_content;
    }

    switch($this->_method)
    {
      case self::METHOD_IFRAME:
        return $this->_renderIframe();
      case self::METHOD_IMAGE:
        return $this->_renderImage();
      case self::METHOD_JS:
        return $this->_renderJavascript();
    }

    return '';
  }

  protected function _renderIframe()
  {
    $data = '<iframe src="' . $this->_url . '" ';
    $data .= 'width="1" height="1" frameborder="0"></iframe>';
    return $data;
  }

  protected function _renderImage()
  {
    $data = '<img src="' . $this->_url . '" ';
    $data .= 'width="1" height="1" border="0"/>';
    return $data;
  }

  protected function _renderJavascript()
  {
    $data = '<script type="text/javascript" src="' . $this->_url . '">';
    $data .= '</script>';
    return $data;
  }

  public function __toString()
  {
    return $this->render();
  }
}
