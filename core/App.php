<?php

class App
{
    private $config;
    private $db;
    private $router;
    
    public function __construct(array $config, Database $db)
    {
        $this->config = $config;
        $this->db = $db;
        $this->router = new Router();
        
        $this->registerRoutes();
    }
    
    private function registerRoutes()
    {
        // Auth routes
        $this->router->post('/api/auth/login', 'AuthController@login');
        $this->router->post('/api/auth/register', 'AuthController@register');
        $this->router->post('/api/auth/logout', 'AuthController@logout');
        
        // Customer routes
        $this->router->get('/api/items', 'ItemController@index');
        $this->router->get('/api/items/{id}', 'ItemController@show');
        $this->router->get('/api/categories', 'CategoryController@index');
        
        // Cart routes (requires authentication)
        $this->router->post('/api/cart/add', 'CartController@addItem', ['middleware' => 'auth:customer']);
        $this->router->post('/api/cart/remove', 'CartController@removeItem', ['middleware' => 'auth:customer']);
        $this->router->get('/api/cart', 'CartController@getCart', ['middleware' => 'auth:customer']);
        
        // Order routes
        $this->router->post('/api/orders', 'OrderController@store', ['middleware' => 'auth:customer']);
        $this->router->get('/api/orders', 'OrderController@index', ['middleware' => 'auth:customer']);
        $this->router->get('/api/orders/{id}', 'OrderController@show', ['middleware' => 'auth:customer']);
        
        // Seller routes
        $this->router->post('/api/seller/items', 'SellerItemController@store', ['middleware' => 'auth:seller']);
        $this->router->put('/api/seller/items/{id}', 'SellerItemController@update', ['middleware' => 'auth:seller']);
        $this->router->delete('/api/seller/items/{id}', 'SellerItemController@destroy', ['middleware' => 'auth:seller']);
        $this->router->get('/api/seller/orders', 'SellerOrderController@index', ['middleware' => 'auth:seller']);
        $this->router->post('/api/seller/reviews/{id}/comments', 'SellerReviewController@addComment', ['middleware' => 'auth:seller']);
        $this->router->get('/api/seller/reports/top-sellers', 'SellerReportController@topSellers', ['middleware' => 'auth:seller']);
        
        // Admin routes
        $this->router->get('/api/admin/businesses/pending', 'AdminBusinessController@pendingApprovals', ['middleware' => 'auth:admin']);
        $this->router->post('/api/admin/businesses/{id}/approve', 'AdminBusinessController@approve', ['middleware' => 'auth:admin']);
        $this->router->get('/api/admin/users', 'AdminUserController@index', ['middleware' => 'auth:admin']);
        $this->router->delete('/api/admin/users/{id}', 'AdminUserController@destroy', ['middleware' => 'auth:admin']);
        $this->router->get('/api/admin/items/reported', 'AdminItemController@reportedItems', ['middleware' => 'auth:admin']);
        $this->router->delete('/api/admin/items/{id}', 'AdminItemController@destroy', ['middleware' => 'auth:admin']);
        $this->router->post('/api/admin/admins', 'AdminController@store', ['middleware' => 'auth:admin']);
    }
    
    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        // Handle CORS for API
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($method === 'OPTIONS') {
            exit(0);
        }
        
        try {
            $response = $this->router->dispatch($method, $uri);
            $this->sendResponse($response);
        } catch (Exception $e) {
            $this->sendErrorResponse($e);
        }
    }
    
    private function sendResponse($response)
    {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    private function sendErrorResponse(Exception $e)
    {
        $statusCode = 500;
        
        if ($e instanceof AuthenticationException) {
            $statusCode = 401;
        } elseif ($e instanceof ValidationException) {
            $statusCode = 422;
        } elseif ($e instanceof NotFoundException) {
            $statusCode = 404;
        } elseif ($e instanceof ForbiddenException) {
            $statusCode = 403;
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage(),
            'code' => $statusCode
        ]);
    }
}
