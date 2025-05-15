<?php

class AuthController
{
    public function login()
    {
        // Validate request
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        
        if (!$email || !$password) {
            throw new ValidationException('Email and password are required');
        }
        
        global $app;
        
        // Find user by email
        $user = $app->db->findBy('users', 'email', $email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new AuthenticationException('Invalid credentials');
        }
        
        // Generate JWT token
        $token = $this->generateToken($user);
        
        return [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    }
    
    public function register()
    {
        // Validate request
        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $role = $_POST['role'] ?? 'customer';
        
        if (!$name || !$email || !$password) {
            throw new ValidationException('Name, email, and password are required');
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Invalid email format');
        }
        
        // Validate role
        $allowedRoles = ['customer', 'seller'];
        if (!in_array($role, $allowedRoles)) {
            throw new ValidationException('Invalid role. Allowed roles: ' . implode(', ', $allowedRoles));
        }
        
        global $app;
        
        // Check if email already exists
        $existingUser = $app->db->findBy('users', 'email', $email);
        
        if ($existingUser) {
            throw new ValidationException('Email already in use');
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Create user
        $userId = $app->db->insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // If registering as a seller, create a business record
        if ($role === 'seller') {
            $businessName = $_POST['business_name'] ?? $name . "'s Business";
            
            $app->db->insert('businesses', [
                'user_id' => $userId,
                'name' => $businessName,
                'status' => 'pending', // Requires admin approval
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Generate JWT token
        $user = $app->db->find('users', $userId);
        $token = $this->generateToken($user);
        
        return [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    }
    
    public function logout()
    {
        // JWT tokens are stateless, so we don't need to do anything server-side
        // The client should remove the token from storage
        
        return [
            'message' => 'Successfully logged out'
        ];
    }
    
    private function generateToken($user)
    {
        $config = require __DIR__ . '/../config/app.php';
        
        $payload = [
            'sub' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + $config['jwt_expiration']
        ];
        
        // In a real application, use a proper JWT library
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode($payload));
        $signature = base64_encode(hash_hmac('sha256', $header . '.' . $payload, $config['jwt_secret'], true));
        
        return $header . '.' . $payload . '.' . $signature;
    }
}
