<?php

class SellerReportController
{
    public function topSellers()
    {
        $user = $_REQUEST['user'];
        
        global $app;
        
        // Get business ID for the seller
        $business = $app->db->findBy('businesses', 'user_id', $user['id']);
        
        if (!$business) {
            throw new NotFoundException('Business not found');
        }
        
        // Get top selling items
        $sql = "SELECT i.id, i.name, i.image_url, i.price,
                COUNT(oi.id) as order_count,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.quantity * oi.price) as total_revenue
                FROM items i
                LEFT JOIN order_items oi ON i.id = oi.item_id
                WHERE i.business_id = ?
                GROUP BY i.id
                ORDER BY total_quantity DESC";
        
        $topSellers = $app->db->query($sql, [$business['id']])->fetchAll();
        
        // Get sales by date (last 30 days)
        $sql = "SELECT DATE(o.created_at) as date,
                COUNT(DISTINCT o.id) as order_count,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.quantity * oi.price) as total_revenue
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE oi.business_id = ? AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(o.created_at)
                ORDER BY date ASC";
        
        $salesByDate = $app->db->query($sql, [$business['id']])->fetchAll();
        
        return [
            'top_sellers' => $topSellers,
            'sales_by_date' => $salesByDate
        ];
    }
}
