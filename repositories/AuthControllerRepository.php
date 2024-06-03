<?php

require_once 'AuthInterface.php';
require_once '../Database/Database.php';
require_once '../HelperMethods/Helper.php';

class AuthControllerRepository implements AuthControllerRepositoryInterface {
    private $database;
    private $conn;

    public function __construct(Database $database) {
        $this->database = $database;
        $this->conn = $this->database->getConnection(); 
    }

    public function registerUser($userData) {
        try {
            // Generate a random password
            $password = UserHelper::generateRandomPassword();
           

            // Proceed with registration
            $sql = "INSERT INTO users (username, address, email, phone_number, password, role_type) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $userData['username'], 
                $userData['address'], 
                $userData['email'], 
                $userData['phone_number'], 
                $password,
                $userData['role_type']
            ]);

            // Send email to the registered user
            UserHelper::sendRegistrationEmail($userData['email'], $password);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error registering user: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Validation error: " . $e->getMessage());
        }
    }

    public function loginUser($email, $password) {
        try {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
    
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user && $user['password'] === $password) {
                return $user;
            } else {
                throw new Exception("Invalid email or password");
            }
        } catch (PDOException $e) {
            throw new Exception("Error logging in user: " . $e->getMessage());
        }
    }
    
    public static function validateDuplicateUsername($username, $conn) {
        $checkUsernameStmt = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE username = ?");
        $checkUsernameStmt->execute([$username]);
        $result = $checkUsernameStmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            throw new Exception("Username already exists. Please choose a different username.");
        }
    }
   
   
}

   

?>
