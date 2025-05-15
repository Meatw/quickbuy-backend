<?php

class AdminBusinessController
{
    public function pendingApprovals()
    {
        global $app;
        
        // Get all pending businesses
        $sql = "SELECT b.*, u.name as owner_name, u.email as owner_email
                FROM businesses b
                JOIN users u ON b.user_id = u.id
                WHERE b.status = 'pending'
                ORDER BY b.created_at ASC";
        
        $pendingBusinesses = $app->db->query($sql)->fetchAll();
        
        return $pendingBusinesses;
    }
    
    public function approve($id)
    {
        global $app;
        
        // Get business
        $business = $app->db->find('businesses', $id);
        
        if (!$business) {
            throw new NotFoundException('Business not found');
        }
        
        if ($business['status'] !== 'pending') {
            throw new ValidationException('Business is not pending approval');
        }
        
        // Update business status
        $app->db->update('businesses', $id, [
            'status' => 'approved',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Get updated business
        $business = $app->db->find('businesses', $id);
        
        return [
            'message' => 'Business approved successfully',
            'business' => $business
        ];
    }
    
    public function reject($id)
    {
        global $app;
        
        // Get business
        $business = $app->db->find('businesses', $id);
        
        if (!$business) {
            throw new NotFoundException('Business not found');
        }
        
        if ($business['status'] !== 'pending') {
            throw new ValidationException('Business is not pending approval');
        }
        
        // Update business status
        $app->db->update('businesses', $id, [
            'status' => 'rejected',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Get updated business
        $business = $app->db->find('businesses', $id);
        
        return [
            'message' => 'Business rejected successfully',
            'business' => $business
        ];
    }
}
