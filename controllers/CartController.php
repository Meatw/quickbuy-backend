<?php

class CartController
{
    public function getCart()
    {
        $user = $_REQUEST['user'];
        
        global $app;
        
        // Get cart items
        $sql = "SELECT c.id, c.item_id, c.quantity, i.name, i.price, i.image_url, 
                (c.quantity * i.price) as total_price
                FROM cart_items c
                JOIN items i ON c.item_id = i.id
                WHERE c.user_id = ?";
        
        $cartItems = $app->db->query($sql, [$user['id']])->fetchAll();
        
        // Calculate cart total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['total_price'];
        }
        
        return [
            'items' => $cartItems,
            'total' => $total,
            'item_count' => count($cartItems)
        ];
    }
    
    public function addItem()
    {
        $user = $_REQUEST['user'];
        $itemId = $_POST['item_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if (!$itemId || $quantity <= 0) {
            throw new ValidationException('Item ID and quantity are required');
        }
        
        global $app;
        
        // Check if item exists and is available
        $item = $app->db->find('items', $itemId);
        
        if (!$item || $item['status'] !== 'approved') {
            throw new NotFoundException('Item not found or unavailable');
        }
        
        // Check if item is already in cart
        $existingCartItem = $app->db->findBy('cart_items', 'user_id', $user['id'], 'item_id', $itemId);
        
        if ($existingCartItem) {
            // Update quantity
            $newQuantity = $existingCartItem['quantity'] + $quantity;
            $app->db->update('cart_items', $existingCartItem['id'], [
                'quantity' => $newQuantity,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Add new cart item
            $app->db->insert('cart_items', [
                'user_id' => $user['id'],
                'item_id' => $itemId,
                'quantity' => $quantity,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Return updated cart
        return $this->getCart();
    }
    
    public function removeItem()
    {
        $user = $_REQUEST['user'];
        $itemId = $_POST['item_id'] ?? null;
        
        if (!$itemId) {
            throw new ValidationException('Item ID is required');
        }
        
        global $app;
        
        // Find cart item
        $cartItem = $app->db->findBy('cart_items', 'user_id', $user['id'], 'item_id', $itemId);
        
        if (!$cartItem) {
            throw new NotFoundException('Item not found in cart');
        }
        
        // Remove cart item
        $app->db->delete('cart_items', $cartItem['id']);
        
        // Return updated cart
        return $this->getCart();
    }
}
