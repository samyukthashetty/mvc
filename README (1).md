
# Authorisation and Authentication System

This project implements a  authentication system in PHP, utilizing Object-Oriented Programming (OOP) principles, Model-View-Controller (MVC) architecture, route handling, validations, error and exception handling, as well as token-based authentication.

#Features Implemented

1.Object-Oriented Programming (OOP)

The project is structured using OOP principles to encapsulate related functionality within classes and objects. This promotes code reusability, maintainability, and scalability.
```sh
<?php
class User {
    public $username;
    public $password;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
}
}
?>
```
2.Model-View-Controller (MVC) Architecture

The MVC architecture separates the application into three interconnected components: Models (for data handling), Views (for user interface), and Controllers (for handling user inputs and interactions) and Repository (to handle databasequeries). This separation of concerns enhances modularity and facilitates easier code maintenance.
```sh
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
  ```  

3.Route Handling

The application defines routes for different functionalities (login,registration,authenticationvalidation) using a routing mechanism. Each route is associated with a specific controller method, enabling efficient request handling and mapping the endpoint.
```sh
$routes = [
    'POST' => [
        '/fdapp/routes/routes.php/api/register' => 'registerUser',
        '/fdapp/routes/routes.php/api/login' => 'loginUser',
        '/fdapp/routes/routes.php/api/logout' => 'logoutUser',
    ],
    'GET' => [
        '/fdapp/routes/routes.php/api/validate-token' => 'validateToken',
    ],
];
 ``` 
4.Validations

Input validations are implemented to ensure the security of user-provided data. Validation rules are enforced to validate usernames, email addresses, phone numbers, duplications and other relevant fields.
```sh
class Validation {
    public static function validateUsername($username) {
        
        if (!preg_match('/^[a-zA-Z\s]+$/', $username)) {
            throw new UnprocessableException("Username must contain only alphabetic characters.", HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }
        return ["success" => true, "message" => "Valid username"];
    }

 ```

5.Error and Exception Handling

Error and exception handling mechanisms are in place to handle unexpected situations and provide meaningful error messages to users. Custom exceptions are used to differentiate between various error scenarios.
```sh
<?php
class UnprocessableException extends Exception {
    protected $statusCode;

    public function __construct($message, $statusCode = HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getResponse() {
        http_response_code($this->statusCode);
        return [
            "success" => false,
            "status_code" => $this->statusCode, 
            "message" => $this->getMessage() 
        ];
    }
}

```
6.Authentication and Authorization

Token-based authentication and authorization mechanisms are implemented using JSON Web Tokens (JWT).Upon successful authentication, JWTs are generated and used for subsequent requests to authenticate and authorize users.
```sh
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
            
```

#To-do

1.Token Expiration(User,email,role-based)

2.Migration

3.ORM



