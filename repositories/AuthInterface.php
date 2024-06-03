
<?php
interface AuthControllerRepositoryInterface {
  public function registerUser($userData);
  public function loginUser($username, $password);
}
