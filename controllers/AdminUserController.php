<?php

class AdminUserController
{
    public function index()
    {
        global $app;
        
        // Get query parameters
        $role = $_GET['role'] ?? null;
        $search = $_GET['search'] ?? null;
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        
        // Build query conditions
        $conditions = [];
        $params = [];
        
        if ($role) {
            $conditions[] = "role = ?";
            $params[] = $role;
        }
        
        if ($search) {
            $conditions[] = "(name LIKE ? OR email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        // Build SQL query
        $sql = "SELECT id, name, email, role, created_at FROM users";
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        // Add pagination
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
        
        // Execute query
        $users = $app->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM users";
        if (!empty($conditions)) {
            $countSql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $totalCount = $app->db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalCount / $limit);
        
        return [
            'users' => $users,
            'pagination' => [
                'total' => $totalCount,
                'per_page' => $limit,
                'current_page' => $page,
                'total_pages' => $totalPages
            ]
        ];
    }
    
    public function show($id)
    {
        global $app;
        
        // Get user
        $user = $app->db->find('users', $id);
        
        if (!$user) {
            throw new NotFoundException('User not found');
        }
        
        // Remove password
        unset($user['password']);
        
        // Get additional data based on role
        if ($user['role'] === 'seller') {
            // Get business
            $business = $app->db->findBy('businesses', 'user_id', $id);
            $user['business'] = $business;
            
            // Get items
            if ($business) {
                $items = $app->db->findAll('items', ['business_id' => $business['id']], 'created_at DESC');
                $user['items'] = $items;
            }
        } elseif ($user['role'] === 'customer') {
            // Get orders
            $orders = $app->db->findAll('orders', ['user_id' => $id], 'created_at DESC');
            $user['orders'] = $orders;
        }
        
        return $user;
    }
    
    public function destroy($id)
    {
        global $app;
        
        // Get user
        $user = $app->db->find('users', $id);
        
        if (!$user) {
            throw new NotFoundException('User not found');
        }
        
        // Check if user is an admin
        if ($user['role'] === 'admin') {
            throw new ForbiddenException('Cannot delete admin users');
        }
        
        // Start transaction
        $app->db->beginTransaction();
        
        try {
            // Delete related data
            if ($user['role'] === 'seller') {
                // Get business
                $business = $app->db->findBy('businesses', 'user_id', $id);
                
                if ($business) {
                    // Update items to deleted status
                    $app->db->query("UPDATE items SET status = 'deleted' WHERE business_id = ?", [$business['id']]);
                    
                    // Delete business
                    $app->db->delete('businesses', $business['id']);
                }
            }
            
            // Delete cart items
            $app->db->query("DELETE FROM cart_items WHERE user_id = ?", [$id]);
            
            // Delete user
            $app->db->delete('users', $id);
            
            // Commit transaction
            $app->db->commit();
            
            return [
                'message' => 'User deleted successfully'
            ];
            
        } catch (Exception $e) {
            // Rollback transaction
            $app->db->rollback();
            throw $e;
        }
    }
}
