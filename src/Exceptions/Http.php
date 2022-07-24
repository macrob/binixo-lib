<?php 
namespace BinixoLib\Exceptions;


class Http extends \Exception {
  public $errorMsg;

  public function __construct($error) {
    $this->errorMsg = $error;
  }
}