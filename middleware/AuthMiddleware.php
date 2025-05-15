<?php

class AuthMiddleware
{
    public function handle($role = null)
    {
        // Check if Authorization header exists
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            throw new AuthenticationException('Authorization header missing');
        }
        
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        try {
            // Verify JWT token
            $payload = $this->verifyToken($token);
            
            // Check if user exists
            $user = $this->getUserFromPayload($payload);
            
            if (!$user) {
                throw new AuthenticationException('User not found');
            }
            
            // Check if user has the required role
            if ($role && $user['role'] !== $role) {
                throw new ForbiddenException('Insufficient permissions');
            }
            
            // Set authenticated user in the request
            $_REQUEST['user'] = $user;
            
        } catch (Exception $e) {
            throw new AuthenticationException('Invalid token: ' . $e->getMessage());
        }
    }
    
    private function verifyToken($token)
    {
        $config = require __DIR__ . '/../config/app.php';
        
        try {
            // Simple JWT verification (in production, use a proper JWT library)
            list($header, $payload, $signature) = explode('.', $token);
            
            $decodedPayload = json_decode(base64_decode($payload), true);
            
            // Check if token is expired
            if (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time()) {
                throw new Exception('Token expired');
            }
            
            return $decodedPayload;
            
        } catch (Exception $e) {
            throw new Exception('Token verification failed: ' . $e->getMessage());
        }
    }
    
    private function getUserFromPayload($payload)
    {
        global $app;
        
        $userId = $payload['sub'] ?? null;
        
        if (!$userId) {
            return null;
        }
        
        return $app->db->find('users', $userId);
    }
}
