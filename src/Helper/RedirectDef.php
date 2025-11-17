<?php
  namespace App\Helper;

  class RedirectDef {
    public string $url;
    public function __construct(string $url) {
      $this->url = $url;
    }

    public function redirect() {
      $url = $this->url;
      header("Location: $url");
    }
  }  
?>
