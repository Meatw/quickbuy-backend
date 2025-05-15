<?php

require_once __DIR__ . '/../bootstrap/app.php';

// Seed the database with sample data
function seedDatabase($db) {
    // Create sample users
    $customerIds = [];
    $sellerIds = [];
    
    // Create customers
    for ($i = 1; $i <= 5; $i++) {
        $customerId = $db->insert('users', [
            'name' => "Customer {$i}",
            'email' => "customer{$i}@example.com",
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'customer',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $customerIds[] = $customerId;
    }
    
    // Create sellers
    for ($i = 1; $i <= 3; $i++) {
        $sellerId = $db->insert('users', [
            'name' => "Seller {$i}",
            'email' => "seller{$i}@example.com",
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'seller',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $sellerIds[] = $sellerId;
        
        // Create business for seller
        $businessId = $db->insert('businesses', [
            'user_id' => $sellerId,
            'name' => "Business {$i}",
            'description' => "This is a sample business {$i}",
            'status' => 'approved',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Create items for business
        $categories = $db->findAll('categories');
        
        for ($j = 1; $j <= 5; $j++) {
            $categoryId = $categories[array_rand($categories)]['id'];
            $price = rand(10, 1000) / 10;
            
            $db->insert('items', [
                'business_id' => $businessId,
                'category_id' => $categoryId,
                'name' => "Item {$j} from Business {$i}",
                'description' => "This is a sample item {$j} from business {$i}",
                'price' => $price,
                'stock' => rand(10, 100),
                'status' => 'approved',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    // Create sample orders
    foreach ($customerIds as $customerId) {
        // Create 1-3 orders per customer
        $orderCount = rand(1, 3);
        
        for ($i = 1; $i <= $orderCount; $i++) {
            // Get random items
            $items = $db->query("SELECT * FROM items ORDER BY RAND() LIMIT " . rand(1, 3))->fetchAll();
            
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += $item['price'] * rand(1, 3);
            }
            
            // Create order
            $orderId = $db->insert('orders', [
                'user_id' => $customerId,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Create order items
            foreach ($items as $item) {
                $quantity = rand(1, 3);
                
                $db->insert('order_items', [
                    'order_id' => $orderId,
                    'item_id' => $item['id'],
                    'business_id' => $item['business_id'],
                    'quantity' => $quantity,
                    'price' => $item['price'],
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            // Add order status history
            $db->insert('order_status_history', [
                'order_id' => $orderId,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    echo "Database seeded successfully!\n";
}

// Seed the database
seedDatabase($app->db);
