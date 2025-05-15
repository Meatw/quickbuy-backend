<?php

class CategoryController
{
    public function index()
    {
        global $app;
        
        // Get all categories
        $categories = $app->db->findAll('categories', [], 'name ASC');
        
        return $categories;
    }
}
