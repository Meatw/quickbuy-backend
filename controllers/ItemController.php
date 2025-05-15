<?php

class ItemController
{
    public function index()
    {
        global $app;
        
        // Get query parameters
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        $sort = $_GET['sort'] ?? 'newest';
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        
        // Build query conditions
        $conditions = [];
        $params = [];
        
        if ($category) {
            $conditions[] = "category_id = ?";
            $params[] = $category;
        }
        
        if ($search) {
            $conditions[] = "(name LIKE ? OR description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        // Only show approved items
        $conditions[] = "status = ?";
        $params[] = 'approved';
        
        // Build SQL query
        $sql = "SELECT i.*, c.name as category_name, b.name as business_name 
                FROM items i
                JOIN categories c ON i.category_id = c.id
                JOIN businesses b ON i.business_id = b.id";
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        // Add sorting
        switch ($sort) {
            case 'price_low':
                $sql .= " ORDER BY price ASC";
                break;
            case 'price_high':
                $sql .= " ORDER BY price DESC";
                break;
            case 'popular':
                $sql .= " ORDER BY sales_count DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY created_at DESC";
                break;
        }
        
        // Add pagination
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
        
        // Execute query
        $items = $app->db->query($sql, $params)->fetchAll();
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) as total FROM items";
        if (!empty($conditions)) {
            $countSql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $totalCount = $app->db->query($countSql, $params)->fetch()['total'];
        $totalPages = ceil($totalCount / $limit);
        
        return [
            'items' => $items,
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
        
        // Get item with related data
        $sql = "SELECT i.*, c.name as category_name, b.name as business_name, b.id as business_id
                FROM items i
                JOIN categories c ON i.category_id = c.id
                JOIN businesses b ON i.business_id = b.id
                WHERE i.id = ? AND i.status = 'approved'";
        
        $item = $app->db->query($sql, [$id])->fetch();
        
        if (!$item) {
            throw new NotFoundException('Item not found');
        }
        
        // Get item reviews
        $reviews = $app->db->findAll('reviews', ['item_id' => $id], 'created_at DESC');
        
        // Get review comments
        foreach ($reviews as &$review) {
            $review['comments'] = $app->db->findAll('review_comments', ['review_id' => $review['id']], 'created_at ASC');
        }
        
        $item['reviews'] = $reviews;
        
        return $item;
    }
}
