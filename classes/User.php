<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateProfile($data, $files) {
        try {
            $userId = $_SESSION['user_id'];
            
            // Handle profile image upload if present
            if (isset($files['profile_image']) && $files['profile_image']['error'] === 0) {
                $uploadDir = 'uploads/';
                
                // Create uploads directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = $files['profile_image']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
                }

                // Generate unique filename
                $extension = pathinfo($files['profile_image']['name'], PATHINFO_EXTENSION);
                $fileName = 'profile_' . $userId . '_' . time() . '.' . $extension;
                $targetPath = $uploadDir . $fileName;

                // Remove old profile image if exists
                $oldImage = $this->getUserById($userId)['profile_image'];
                if ($oldImage && $oldImage != 'uploads/default-avatar.png' && file_exists($oldImage)) {
                    unlink($oldImage);
                }

                // Upload new image
                if (move_uploaded_file($files['profile_image']['tmp_name'], $targetPath)) {
                    // Update database with new image path
                    $stmt = $this->conn->prepare("UPDATE " . $this->table_name . " SET profile_image = ? WHERE id = ?");
                    $stmt->execute([$targetPath, $userId]);
                }
            }

            // Update other profile information
            $stmt = $this->conn->prepare("UPDATE " . $this->table_name . " 
                                        SET fullname = ?, email = ?, bio = ? 
                                        WHERE id = ?");
            
            return $stmt->execute([
                $data['fullname'],
                $data['email'],
                $data['bio'] ?? '',
                $userId
            ]);
        } catch(Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function emailExists($email) {
        try {
            $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function setResetToken($email, $token, $expiry) {
        try {
            // First, delete any existing reset tokens for this email
            $query = "DELETE FROM password_resets WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);

            // Insert new reset token
            $query = "INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$email, $token, $expiry]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getAllUsers() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function deleteUser($id) {
        try {
            // Don't allow deletion of the current user
            if ($id == $_SESSION['user_id']) {
                return false;
            }

            // Get user's profile image before deletion
            $stmt = $this->conn->prepare("SELECT profile_image FROM " . $this->table_name . " WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Delete the user
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$id]);

            // If deletion was successful and user had a custom profile image, delete it
            if ($result && $user && $user['profile_image'] && $user['profile_image'] != 'uploads/default-avatar.png') {
                if (file_exists($user['profile_image'])) {
                    unlink($user['profile_image']);
                }
            }

            return $result;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function createUser($data) {
        try {
            // Check if username already exists
            $stmt = $this->conn->prepare("SELECT id FROM " . $this->table_name . " WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->rowCount() > 0) {
                throw new Exception("Username already exists");
            }

            // Check if email already exists
            $stmt = $this->conn->prepare("SELECT id FROM " . $this->table_name . " WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->rowCount() > 0) {
                throw new Exception("Email already exists");
            }

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insert new user
            $query = "INSERT INTO " . $this->table_name . " 
                      (fullname, username, email, password, role) 
                      VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                $data['fullname'],
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['role']
            ]);
        } catch(PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function updateUser($data) {
        try {
            $fields = [];
            $values = [];

            // Update basic information
            if (isset($data['fullname'])) {
                $fields[] = "fullname = ?";
                $values[] = $data['fullname'];
            }
            if (isset($data['email'])) {
                $fields[] = "email = ?";
                $values[] = $data['email'];
            }
            if (isset($data['username'])) {
                $fields[] = "username = ?";
                $values[] = $data['username'];
            }
            if (isset($data['role'])) {
                $fields[] = "role = ?";
                $values[] = $data['role'];
            }

            // Update password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $fields[] = "password = ?";
                $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            // Add user ID to values array
            $values[] = $data['id'];

            // Construct query
            $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $fields) . " WHERE id = ?";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($values);
        } catch(PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}
?> 