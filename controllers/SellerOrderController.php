<?php

class SellerOrderController
{
    public function index()
    {
        $user = $_REQUEST['user'];
        
        global $app;
        
        // Get business ID for the seller
        $business = $app->db->findBy('businesses', 'user_id', $user['id']);
        
        if (!$business) {
            throw new NotFoundException('Business not found');
        }
        
        // Get all order items for the business
        $sql = "SELECT oi.*, o.created_at as order_date, i.name as item_name, i.image_url,
                u.name as customer_name
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN items i ON oi.item_id = i.id
                JOIN users u ON o.user_id = u.id
                WHERE oi.business_id = ?
                ORDER BY o.created_at DESC";
        
        $orderItems = $app->db->query($sql, [$business['id']])->fetchAll();
        
        // Group by order
        $orders = [];
        foreach ($orderItems as $item) {
            $orderId = $item['order_id'];
            
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'order_id' => $orderId,
                    'order_date' => $item['order_date'],
                    'customer_name' => $item['customer_name'],
                    'items' => [],
                    'total_amount' => 0
                ];
            }
            
            $orders[$orderId]['items'][] = [
                'id' => $item['id'],
                'item_id' => $item['item_id'],
                'item_name' => $item['item_name'],
                'image_url' => $item['image_url'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'status' => $item['status']
            ];
            
            $orders[$orderId]['total_amount'] += $item['price'] * $item['quantity'];
        }
        
        return array_values($orders);
    }
    
    public function updateStatus($id)
    {
        $user = $_REQUEST['user'];
        $status = $_POST['status'] ?? null;
        
        if (!$status) {
            throw new ValidationException('Status is required');
        }
        
        $allowedStatuses = ['processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            throw new ValidationException('Invalid status. Allowed statuses: ' . implode(', ', $allowedStatuses));
        }
        
        global $app;
        
        // Get business ID for the seller
        $business = $app->db->findBy('businesses', 'user_id', $user['id']);
        
        if (!$business) {
            throw new NotFoundException('Business not found');
        }
        
        // Get order item
        $orderItem = $app->db->find('order_items', $id);
        
        if (!$orderItem || $orderItem['business_id'] !== $business['id']) {
            throw new NotFoundException('Order item not found');
        }
        
        // Update order item status
        $app->db->update('order_items', $id, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Check if all order items have the same status
        $orderItems = $app->db->findAll('order_items', ['order_id' => $orderItem['order_id']]);
        
        $allSameStatus = true;
        foreach ($orderItems as $item) {
            if ($item['status'] !== $status) {
                $allSameStatus = false;
                break;
            }
        }
        
        // If all items have the same status, update the order status
        if ($allSameStatus) {
            $app->db->update('orders', $orderItem['order_id'], [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Add order status history
            $app->db->insert('order_status_history', [
                'order_id' => $orderItem['order_id'],
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return [
            'message' => 'Order status updated successfully'
        ];
    }
}
