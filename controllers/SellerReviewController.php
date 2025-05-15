<?php

class SellerReviewController
{
    public function addComment($reviewId)
    {
        $user = $_REQUEST['user'];
        $comment = $_POST['comment'] ?? null;
        
        if (!$comment) {
            throw new ValidationException('Comment is required');
        }
        
        global $app;
        
        // Get business ID for the seller
        $business = $app->db->findBy('businesses', 'user_id', $user['id']);
        
        if (!$business) {
            throw new NotFoundException('Business not found');
        }
        
        // Get review
        $review = $app->db->find('reviews', $reviewId);
        
        if (!$review) {
            throw new NotFoundException('Review not found');
        }
        
        // Get item to check if it belongs to the seller
        $item = $app->db->find('items', $review['item_id']);
        
        if (!$item || $item['business_id'] !== $business['id']) {
            throw new ForbiddenException('You can only comment on reviews for your own items');
        }
        
        // Add comment
        $commentId = $app->db->insert('review_comments', [
            'review_id' => $reviewId,
            'user_id' => $user['id'],
            'comment' => $comment,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Get created comment
        $createdComment = $app->db->find('review_comments', $commentId);
        
        return $createdComment;
    }
}
