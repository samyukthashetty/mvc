<?php

require_once '../repositories/AuthControllerRepository.php';
require_once '../repositories/TokenRepository.php';
require_once '../validations/validation.php';
require_once '../Database/Database.php';
require_once '../exceptions/UnprocessableException.php';
require_once '../exceptions/errors.php';
require_once '../php-jwt/src/JWT.php';
require_once '../php-jwt/src/Key.php';
require_once '../HelperMethods/ResponseHelper.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthController {

    private $repository;
    private $tokenRepository;
    private $database;
    private $secretKey = 'secretkey';

    public function __construct(AuthControllerRepository $repository, TokenRepository $tokenRepository, Database $database) {
        $this->repository = $repository;
        $this->tokenRepository = $tokenRepository;
        $this->database = $database;
    }

    public function registerUser($userData) {
        try {
            $conn = $this->database->getConnection();

            // Validate user data
            Validation::validateUsername($userData['username']);
            Validation::validateEmail($userData['email']);
            Validation::validateAddress($userData['address']);
            Validation::validatePhoneNumber($userData['phone_number']);

            AuthControllerRepository::validateDuplicateUsername($userData['username'], $conn);

            // Call the repository method to register the user
            $this->repository->registerUser($userData); 
            http_response_code(HttpStatusCodes::HTTP_CREATED);
            return ResponseHelper::success("User registered successfully");
        } catch (UnprocessableException $e) {
            return ($e->getResponse()); 
        } catch (Exception $e) {
            http_response_code(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            return ResponseHelper::error("Error registering user: " . $e->getMessage(), HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function loginUser($userData) {
        try {
            $user = $this->repository->loginUser($userData['email'], $userData['password']);
            if ($user) {
                $payload = [
                    'iss' => 'localhost',
                    'aud' => 'localhost',
                    'iat' => time(),
                    'nbf' => time(),
                    'exp' => time() + (60 * 60), 
                    'data' => [
                        'user_id' => $user['user_id'],
                        'email' => $user['email'],
                        'role' => $user['role_type']
                    ]
                ];
    
                $jwt = JWT::encode($payload, $this->secretKey, 'HS256');
                $this->tokenRepository->saveToken($user['user_id'], $jwt);
    
                return ResponseHelper::success("Login successful. Welcome, " . $user['username'], ["token" => $jwt]);
            } else {
                throw new Exception("Invalid email or password");
            }
        } catch (Exception $e) {
            http_response_code(HttpStatusCodes::HTTP_UNAUTHORIZED);
            return ResponseHelper::error("Error logging in: " . $e->getMessage(), HttpStatusCodes::HTTP_UNAUTHORIZED);
        }
    }
    

    public function validateToken($data) {
        try {
            $token = $data['token'];
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $valid = $this->tokenRepository->validateToken($token);
            if ($valid) {
                return ResponseHelper::success("Token is valid", $decoded);
            } else {
                throw new Exception("Invalid token");
            }
        } catch (Exception $e) {
            http_response_code(HttpStatusCodes::HTTP_UNAUTHORIZED);
            return ResponseHelper::error("Token validation failed: " . $e->getMessage(), HttpStatusCodes::HTTP_UNAUTHORIZED);
        }
    }

    public function logoutUser($data) {
        try {
            $token = $data['token'];
            $this->tokenRepository->deleteToken($token);
            return ResponseHelper::success("Logout successful");
        } catch (Exception $e) {
            http_response_code(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            return ResponseHelper::error("Error logging out: " . $e->getMessage(), HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }                
}
?>
