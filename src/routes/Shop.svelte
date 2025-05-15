<script>
  import { Link } from "svelte-routing";
  import { onMount } from "svelte";
  import Header from "../components/Header.svelte";
  import Footer from "../components/Footer.svelte";
  import { cartStore } from "../stores/cartStore";
  import { userStore } from "../stores/userStore";
  
  let products = [];
  let categories = [];
  let isLoading = true;
  let priceRange = [0, 1000];
  let selectedCategories = [];
  let minRating = 0;
  
  // Filter states
  let showFilters = false;
  
  onMount(async () => {
    try {
      // In a real app, fetch from API
      // For now, using mock data
      categories = [
        { id: 1, name: "Food & Drinks" },
        { id: 2, name: "Electronics" },
        { id: 3, name: "Home & Kitchen" },
        { id: 4, name: "Apparel" },
        { id: 5, name: "Books & Gifts" }
      ];
      
      products = Array(9).fill().map((_, i) => ({
        id: i + 1,
        name: "Complete Hiking Gear",
        price: 99.99,
        rating: 4.5,
        reviews: 120,
        image: "/images/product-placeholder.png",
        category_id: Math.floor(Math.random() * 5) + 1
      }));
    } catch (error) {
      console.error("Error fetching products:", error);
    } finally {
      isLoading = false;
    }
  });
  
  function toggleCategory(categoryId) {
    if (selectedCategories.includes(categoryId)) {
      selectedCategories = selectedCategories.filter(id => id !== categoryId);
    } else {
      selectedCategories = [...selectedCategories, categoryId];
    }
  }
  
  function toggleRating(rating) {
    minRating = rating === minRating ? 0 : rating;
  }
  
  function resetFilters() {
    selectedCategories = [];
    minRating = 0;
    priceRange = [0, 1000];
  }
  
  function toggleFilters() {
    showFilters = !showFilters;
  }
  
  async function addToCart(product) {
    if (!$userStore.isAuthenticated) {
      // Redirect to login
      window.location.href = "/login";
      return;
    }
    
    try {
      // In a real app, call API
      // For now, just update the store
      cartStore.update(cart => {
        const existingItem = cart.items.find(item => item.item_id === product.id);
        
        if (existingItem) {
          return {
            ...cart,
            items: cart.items.map(item => 
              item.item_id === product.id 
                ? { ...item, quantity: item.quantity + 1 } 
                : item
            ),
            total: cart.total + product.price,
            itemCount: cart.itemCount + 1
          };
        } else {
          return {
            ...cart,
            items: [...cart.items, {
              id: Date.now(),
              item_id: product.id,
              name: product.name,
              price: product.price,
              quantity: 1,
              image_url: product.image
            }],
            total: cart.total + product.price,
            itemCount: cart.itemCount + 1
          };
        }
      });
      
      // Show success message
      alert("Product added to cart!");
      
    } catch (error) {
      console.error("Error adding to cart:", error);
      alert("Failed to add product to cart");
    }
  }
  
  // Filter products based on selected filters
  $: filteredProducts = products.filter(product => {
    // Filter by category
    if (selectedCategories.length > 0 && !selectedCategories.includes(product.category_id)) {
      return false;
    }
    
    // Filter by price
    if (product.price < priceRange[0] || product.price > priceRange[1]) {
      return false;
    }
    
    // Filter by rating
    if (product.rating < minRating) {
      return false;
    }
    
    return true;
  });
</script>

<div class="shop-page">
  <Header />
  
  <main>
    <div class="container">
      <div class="shop-header">
        <h1>Browse Categories</h1>
        <button class="filter-toggle" on:click={toggleFilters}>
          {showFilters ? 'Hide Filters' : 'Show Filters'}
        </button>
      </div>
      
      <div class="category-icons">
        <div class="category-icon">
          <Link to="/shop?category=home">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
              <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Home</span>
          </Link>
        </div>
        <div class="category-icon">
          <Link to="/shop?category=food">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
              <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
              <line x1="6" y1="1" x2="6" y2="4"></line>
              <line x1="10" y1="1" x2="10" y2="4"></line>
              <line x1="14" y1="1" x2="14" y2="4"></line>
            </svg>
            <span>Food & Drinks</span>
          </Link>
        </div>
        <div class="category-icon">
          <Link to="/shop?category=gifts">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="6" width="18" height="15" rx="2"></rect>
              <path d="M3 6l9 6 9-6"></path>
              <path d="M21 6v10"></path>
            </svg>
            <span>Gifts</span>
          </Link>
        </div>
        <div class="category-icon">
          <Link to="/shop?category=clothing">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"></path>
            </svg>
            <span>Clothing</span>
          </Link>
        </div>
        <div class="category-icon">
          <Link to="/shop?category=books">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <span>Books</span>
          </Link>
        </div>
        <div class="category-icon">
          <Link to="/shop?category=electronics">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
              <rect x="9" y="9" width="6" height="6"></rect>
              <line x1="9" y1="1" x2="9" y2="4"></line>
              <line x1="15" y1="1" x2="15" y2="4"></line>
              <line x1="9" y1="20" x2="9" y2="23"></line>
              <line x1="15" y1="20" x2="15" y2="23"></line>
              <line x1="20" y1="9" x2="23" y2="9"></line>
              <line x1="20" y1="14" x2="23" y2="14"></line>
              <line x1="1" y1="9" x2="4" y2="9"></line>
              <line x1="1" y1="14" x2="4" y2="14"></line>
            </svg>
            <span>Electronics</span>
          </Link>
        </div>
        <div class="category-icon">
          <Link to="/shop?category=services">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
            </svg>
            <span>Services</span>
          </Link>
        </div>
        <div class="category-icon">
          <Link to="/shop?category=sports">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
              <path d="M2 12h20"></path>
            </svg>
            <span>Sports & Outdoors</span>
          </Link>
        </div>
        <div class="category-icon">
          <Link to="/shop">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            </svg>
            <span>General</span>
          </Link>
        </div>
      </div>
      
      <div class="shop-content">
        <div class="filters" class:active={showFilters}>
          <div class="filter-section">
            <h3>Filters</h3>
            <button class="reset-filters" on:click={resetFilters}>Reset</button>
          </div>
          
          <div class="filter-section">
            <h4>Price Range</h4>
            <div class="price-slider">
              <input type="range" min="0" max="1000" bind:value={priceRange[0]} />
              <input type="range" min="0" max="1000" bind:value={priceRange[1]} />
              <div class="price-range-values">
                <span>${priceRange[0]}</span>
                <span>${priceRange[1]}</span>
              </div>
            </div>
          </div>
          
          <div class="filter-section">
            <h4>Categories</h4>
            <div class="category-checkboxes">
              {#each categories as category}
                <label class="category-checkbox">
                  <input 
                    type="checkbox" 
                    checked={selectedCategories.includes(category.id)} 
                    on:change={() => toggleCategory(category.id)} 
                  />
                  <span>{category.name}</span>
                </label>
              {/each}
            </div>
          </div>
          
          <div class="filter-section">
            <h4>Minimum Rating</h4>
            <div class="rating-options">
              {#each [4, 3, 2, 1] as rating}
                <label class="rating-option">
                  <input 
                    type="radio" 
                    name="rating" 
                    checked={minRating === rating} 
                    on:change={() => toggleRating(rating)} 
                  />
                  <span>{rating} Stars</span>
                </label>
              {/each}
            </div>
          </div>
        </div>
        
        <div class="products">
          <h2>Trending Products</h2>
          
          {#if isLoading}
            <div class="loading">Loading products...</div>
          {:else if filteredProducts.length === 0}
            <div class="no-products">No products match your filters</div>
          {:else}
            <div class="product-grid">
              {#each filteredProducts as product}
                <div class="product-card">
                  <Link to={`/product/${product.id}`} class="product-image">
                    <img src={product.image || "/placeholder.svg"} alt={product.name} />
                  </Link>
                  <div class="product-info">
                    <Link to={`/product/${product.id}`} class="product-name">{product.name}</Link>
                    <div class="product-price">${product.price.toFixed(2)}</div>
                    <div class="product-rating">
                      <div class="stars">
                        {#each Array(5) as _, i}
                          <span class={i < Math.floor(product.rating) ? 'star filled' : 'star'}>★</span>
                        {/each}
                      </div>
                      <span class="review-count">({product.reviews})</span>
                    </div>
                    <button class="add-to-cart" on:click={() => addToCart(product)}>Add to Cart</button>
                  </div>
                </div>
              {/each}
            </div>
          {/if}
        </div>
      </div>
      
      <div class="become-seller">
        <div class="seller-content">
          <h2>Become a QuickBuy Seller</h2>
          <p>Join our marketplace and reach more customers in your local community. We provide the tools you need to grow your business online.</p>
          <Link to="/register/seller" class="apply-button">Apply Now</Link>
        </div>
      </div>
    </div>
  </main>
  
  <Footer />
</div>

<style>
  .shop-page {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
  
  main {
    flex: 1;
  }
  
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
  }
  
  .shop-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
  }
  
  .shop-header h1 {
    font-size: 1.5rem;
    margin: 0;
  }
  
  .filter-toggle {
    display: none;
    padding: 0.5rem 1rem;
    background-color: #f5f5f5;
    border: none;
    border-radius: 4px;
    font-size: 0.9rem;
    cursor: pointer;
  }
  
  .category-icons {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
  }
  
  .category-icon a {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    background-color: #f5f5f5;
    border-radius: 8px;
    transition: background-color 0.2s;
  }
  
  .category-icon a:hover {
    background-color: #e0e0e0;
  }
  
  .category-icon svg {
    margin-bottom: 0.5rem;
  }
  
  .category-icon span {
    font-size: 0.8rem;
    text-align: center;
  }
  
  .shop-content {
    display: flex;
    gap: 2rem;
  }
  
  .filters {
    width: 250px;
    flex-shrink: 0;
  }
  
  .filter-section {
    margin-bottom: 1.5rem;
  }
  
  .filter-section:first-child {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .filter-section h3 {
    font-size: 1.25rem;
    margin: 0 0 1rem;
  }
  
  .filter-section h4 {
    font-size: 1rem;
    margin: 0 0 0.75rem;
  }
  
  .reset-filters {
    background: none;
    border: none;
    color: #666;
    font-size: 0.9rem;
    cursor: pointer;
    text-decoration: underline;
  }
  
  .price-slider {
    margin-bottom: 1rem;
  }
  
  .price-slider input {
    width: 100%;
    margin-bottom: 0.5rem;
  }
  
  .price-range-values {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    color: #666;
  }
  
  .category-checkboxes {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .category-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    cursor: pointer;
  }
  
  .rating-options {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .rating-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    cursor: pointer;
  }
  
  .products {
    flex: 1;
  }
  
  .products h2 {
    font-size: 1.5rem;
    margin: 0 0 1.5rem;
  }
  
  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
  }
  
  .product-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    background-color: white;
  }
  
  .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }
  
  .product-image {
    display: block;
    height: 200px;
    overflow: hidden;
  }
  
  .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .product-info {
    padding: 1rem;
  }
  
  .product-name {
    display: block;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #000;
  }
  
  .product-price {
    font-weight: bold;
    margin-bottom: 0.5rem;
  }
  
  .product-rating {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
  }
  
  .stars {
    display: flex;
    margin-right: 0.5rem;
  }
  
  .star {
    color: #ddd;
  }
  
  .star.filled {
    color: #ffc107;
  }
  
  .review-count {
    font-size: 0.8rem;
    color: #666;
  }
  
  .add-to-cart {
    width: 100%;
    padding: 0.5rem;
    background-color: #000;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .add-to-cart:hover {
    background-color: #333;
  }
  
  .loading, .no-products {
    padding: 2rem;
    text-align: center;
    color: #666;
  }
  
  .become-seller {
    margin-top: 4rem;
    padding: 3rem;
    background-color: #000;
    color: white;
    border-radius: 8px;
  }
  
  .seller-content {
    max-width: 600px;
  }
  
  .seller-content h2 {
    font-size: 1.75rem;
    margin: 0 0 1rem;
  }
  
  .seller-content p {
    margin: 0 0 1.5rem;
    font-size: 1.1rem;
    opacity: 0.8;
  }
  
  .apply-button {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background-color: white;
    color: #000;
    border-radius: 4px;
    font-weight: 500;
    transition: background-color 0.2s;
  }
  
  .apply-button:hover {
    background-color: #f0f0f0;
  }
  
  @media (max-width: 768px) {
    .shop-content {
      flex-direction: column;
    }
    
    .filters {
      width: 100%;
      display: none;
    }
    
    .filters.active {
      display: block;
    }
    
    .filter-toggle {
      display: block;
    }
  }
</style>
