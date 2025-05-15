<?php

class AdminController
{
    public function store()
    {
        // Validate request
        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $permissions = $_POST['permissions'] ?? [];
        
        if (!$name || !$email || !$password) {
            throw new ValidationException('Name, email, and password are required');
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Invalid email format');
        }
        
        global $app;
        
        // Check if email already exists
        $existingUser = $app->db->findBy('users', 'email', $email);
        
        if ($existingUser) {
            throw new ValidationException('Email already in use');
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Create admin user
        $userId = $app->db->insert('users', [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => 'admin',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Set admin permissions
        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                $app->db->insert('admin_permissions', [
                    'user_id' => $userId,
                    'permission' => $permission,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        // Get created admin
        $admin = $app->db->find('users', $userId);
        unset($admin['password']);
        
        return $admin;
    }
}
