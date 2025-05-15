<?php

class OrderController
{
    public function index()
    {
        $user = $_REQUEST['user'];
        
        global $app;
        
        // Get all orders for the user
        $sql = "SELECT o.*, 
                COUNT(oi.id) as item_count, 
                SUM(oi.quantity) as total_items,
                SUM(oi.price * oi.quantity) as total_amount
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC";
        
        $orders = $app->db->query($sql, [$user['id']])->fetchAll();
        
        return $orders;
    }
    
    public function show($id)
    {
        $user = $_REQUEST['user'];
        
        global $app;
        
        // Get order
        $order = $app->db->find('orders', $id);
        
        if (!$order || $order['user_id'] !== $user['id']) {
            throw new NotFoundException('Order not found');
        }
        
        // Get order items
        $sql = "SELECT oi.*, i.name, i.image_url, b.name as business_name
                FROM order_items oi
                JOIN items i ON oi.item_id = i.id
                JOIN businesses b ON i.business_id = b.id
                WHERE oi.order_id = ?";
        
        $orderItems = $app->db->query($sql, [$id])->fetchAll();
        
        // Get order status history
        $statusHistory = $app->db->findAll('order_status_history', ['order_id' => $id], 'created_at ASC');
        
        return [
            'order' => $order,
            'items' => $orderItems,
            'status_history' => $statusHistory
        ];
    }
    
    public function store()
    {
        $user = $_REQUEST['user'];
        
        global $app;
        
        // Get cart items
        $sql = "SELECT c.item_id, c.quantity, i.price, i.name, i.business_id
                FROM cart_items c
                JOIN items i ON c.item_id = i.id
                WHERE c.user_id = ?";
        
        $cartItems = $app->db->query($sql, [$user['id']])->fetchAll();
        
        if (empty($cartItems)) {
            throw new ValidationException('Cart is empty');
        }
        
        // Calculate total amount
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }
        
        // Start transaction
        $app->db->beginTransaction();
        
        try {
            // Create order
            $orderId = $app->db->insert('orders', [
                'user_id' => $user['id'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Create order items
            foreach ($cartItems as $item) {
                $app->db->insert('order_items', [
                    'order_id' => $orderId,
                    'item_id' => $item['item_id'],
                    'business_id' => $item['business_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            // Add order status history
            $app->db->insert('order_status_history', [
                'order_id' => $orderId,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Clear cart
            $app->db->query("DELETE FROM cart_items WHERE user_id = ?", [$user['id']]);
            
            // Commit transaction
            $app->db->commit();
            
            // Get created order
            $order = $app->db->find('orders', $orderId);
            
            return [
                'message' => 'Order created successfully',
                'order_id' => $orderId,
                'order' => $order
            ];
            
        } catch (Exception $e) {
            // Rollback transaction
            $app->db->rollback();
            throw $e;
        }
    }
}
