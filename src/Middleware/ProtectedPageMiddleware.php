<?php
  namespace App\Middleware; 
  use App\Middleware\AuthMiddleware;

  class ProtectedPageMiddleware {
    public function handle(callable $next)
    {
      try {
        (new AuthMiddleware())->handle($next);   
      } catch (\Throwable $e) {
        header("Location: login", true, 302);
        exit;
      }  
    }
  }
?>
