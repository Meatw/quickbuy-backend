<?php

class SellerItemController
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
        
        // Check if business is approved
        if ($business['status'] !== 'approved') {
            throw new ForbiddenException('Your business is not yet approved');
        }
        
        // Get all items for the business
        $items = $app->db->findAll('items', ['business_id' => $business['id']], 'created_at DESC');
        
        return $items;
    }
    
    public function store()
    {
        $user = $_REQUEST['user'];
        
        global $app;
        
        // Get business ID for the seller
        $business = $app->db->findBy('businesses', 'user_id', $user['id']);
        
        if (!$business) {
            throw new NotFoundException('Business not found');
        }
        
        // Check if business is approved
        if ($business['status'] !== 'approved') {
            throw new ForbiddenException('Your business is not yet approved');
        }
        
        // Validate request
        $name = $_POST['name'] ?? null;
        $description = $_POST['description'] ?? null;
        $price = (float)($_POST['price'] ?? 0);
        $categoryId = $_POST['category_id'] ?? null;
        $stock = (int)($_POST['stock'] ?? 0);
        
        if (!$name || !$description || $price <= 0 || !$categoryId || $stock < 0) {
            throw new ValidationException('Name, description, price, category, and stock are required');
        }
        
        // Check if category exists
        $category = $app->db->find('categories', $categoryId);
        
        if (!$category) {
            throw new NotFoundException('Category not found');
        }
        
        // Handle image upload (simplified)
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageUrl = $this->uploadImage($_FILES['image']);
        }
        
        // Create item
        $itemId = $app->db->insert('items', [
            'business_id' => $business['id'],
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
            'image_url' => $imageUrl,
            'status' => 'approved', // Auto-approve for now
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Get created item
        $item = $app->db->find('items', $itemId);
        
        return $item;
    }
    
    public function update($id)
    {
        $user = $_REQUEST['user'];
        
        global $app;
        
        // Get business ID for the seller
        $business = $app->db->findBy('businesses', 'user_id', $user['id']);
        
        if (!$business) {
            throw new NotFoundException('Business not found');
        }
        
        // Get item
        $item = $app->db->find('items', $id);
        
        if (!$item || $item['business_id'] !== $business['id']) {
            throw new NotFoundException('Item not found');
        }
        
        // Validate request
        $name = $_POST['name'] ?? $item['name'];
        $description = $_POST['description'] ?? $item['description'];
        $price = (float)($_POST['price'] ?? $item['price']);
        $categoryId = $_POST['category_id'] ?? $item['category_id'];
        $stock = (int)($_POST['stock'] ?? $item['stock']);
        
        if ($price <= 0 || $stock < 0) {
            throw new ValidationException('Price must be greater than 0 and stock must be non-negative');
        }
        
        // Check if category exists
        if ($categoryId !== $item['category_id']) {
            $category = $app->db->find('categories', $categoryId);
            
            if (!$category) {
                throw new NotFoundException('Category not found');
            }
        }
        
        // Handle image upload (simplified)
        $imageUrl = $item['image_url'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageUrl = $this->uploadImage($_FILES['image']);
        }
        
        // Update item
        $app->db->update('items', $id, [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category_id' => $categoryId,
            'stock' => $stock,
            'image_url' => $imageUrl,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Get updated item
        $item = $app->db->find('items', $id);
        
        return $item;
    }
    
    public function destroy($id)
    {
        $user = $_REQUEST['user'];
        
        global $app;
        
        // Get business ID for the seller
        $business = $app->db->findBy('businesses', 'user_id', $user['id']);
        
        if (!$business) {
            throw new NotFoundException('Business not found');
        }
        
        // Get item
        $item = $app->db->find('items', $id);
        
        if (!$item || $item['business_id'] !== $business['id']) {
            throw new NotFoundException('Item not found');
        }
        
        // Check if item can be deleted (not in any orders)
        $orderItems = $app->db->findAll('order_items', ['item_id' => $id]);
        
        if (!empty($orderItems)) {
            // Soft delete by changing status
            $app->db->update('items', $id, [
                'status' => 'deleted',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Hard delete
            $app->db->delete('items', $id);
        }
        
        return [
            'message' => 'Item deleted successfully'
        ];
    }
    
    private function uploadImage($file)
    {
        // In a real application, implement proper file upload handling
        // This is a simplified version
        
        $uploadDir = __DIR__ . '/../public/uploads/';
        $filename = uniqid() . '_' . basename($file['name']);
        $uploadPath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload image');
        }
        
        return '/uploads/' . $filename;
    }
}
