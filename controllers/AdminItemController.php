<?php

class AdminItemController
{
    public function reportedItems()
    {
        global $app;
        
        // Get all reported items
        $sql = "SELECT i.*, c.name as category_name, b.name as business_name, u.name as reported_by,
                r.reason, r.created_at as reported_at
                FROM items i
                JOIN item_reports r ON i.id = r.item_id
                JOIN categories c ON i.category_id = c.id
                JOIN businesses b ON i.business_id = b.id
                JOIN users u ON r.user_id = u.id
                ORDER BY r.created_at DESC";
        
        $reportedItems = $app->db->query($sql)->fetchAll();
        
        return $reportedItems;
    }
    
    public function destroy($id)
    {
        global $app;
        
        // Get item
        $item = $app->db->find('items', $id);
        
        if (!$item) {
            throw new NotFoundException('Item not found');
        }
        
        // Update item status to deleted
        $app->db->update('items', $id, [
            'status' => 'deleted',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        return [
            'message' => 'Item deleted successfully'
        ];
    }
}
