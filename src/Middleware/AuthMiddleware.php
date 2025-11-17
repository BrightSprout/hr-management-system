<?php
  namespace App\Middleware; 

  use Firebase\JWT\JWT;
  use Firebase\JWT\Key;
  use App\Model\UserModel;
  use Firebase\JWT\SignatureInvalidException;
  use Firebase\JWT\ExpiredException;
  use Firebase\JWT\BeforeValidException;

  class AuthMiddleware{
    public function handle(callable $next)
    {
      $accessToken = $_COOKIE["access_token"] ?? NULL;
      $refreshToken = $_COOKIE["refresh_token"] ?? NULL;       

      if (!$accessToken && !$refreshToken) {
        throw new \Exception("Access Token and Refresh Token does not exist!");
      }

      // I've decided to not put the other try-catch inside the this catch, it kinda looks good in this way :)
      if ($accessToken) {
        try {
          $decoded = JWT::decode($accessToken, new Key($_ENV["JWT_ACCESS_KEY"], "HS256"));
          return $next(new UserModel(json_decode(json_encode($decoded->sub), true)));
        } catch (SignatureInvalidException | ExpiredException | BeforeValidException) {}
      }

      try {
          $decoded = JWT::decode($refreshToken, new Key($_ENV["JWT_REFRESH_KEY"], "HS256"));
          $encoded = JWT::encode([
            "sub" => $decoded->sub,
            "exp" => time() + 900
          ], $_ENV["JWT_ACCESS_KEY"], "HS256");
          setcookie("access_token", $encoded, time() + 900, "/", "", true, true);
          return $next(new UserModel(json_decode(json_encode($decoded->sub), true)));
        } catch (SignatureInvalidException | ExpiredException | BeforeValidException $e) {
          throw new \Exception("Invalid or expired refresh token" . $_ENV["JWT_REFRESH_KEY"]);
        }
      }
  }
?>
