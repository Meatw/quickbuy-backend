<script>
  import { Link } from "svelte-routing";
  import { onMount } from "svelte";
  import Header from "../components/Header.svelte";
  import Footer from "../components/Footer.svelte";
  
  let featuredBusinesses = [];
  let isLoading = true;
  
  onMount(async () => {
    try {
      // In a real app, fetch from API
      // For now, using mock data
      featuredBusinesses = [
        {
          id: 1,
          name: "Green Grocery Market",
          location: "Downtown",
          category: "Grocery",
          rating: 4.8,
          image: "/images/product-placeholder.png",
          description: "Fresh local produce and organic goods from local farmer"
        },
        {
          id: 2,
          name: "Tech Haven",
          location: "Downtown",
          category: "Electronics",
          rating: 4.8,
          image: "/images/product-placeholder.png",
          description: "Fresh tech products and organic goods from local stores"
        },
        {
          id: 3,
          name: "Artisan Bakery",
          location: "Downtown",
          category: "Grocery",
          rating: 4.8,
          image: "/images/product-placeholder.png",
          description: "Fresh local produce and organic goods from local farmer"
        }
      ];
    } catch (error) {
      console.error("Error fetching businesses:", error);
    } finally {
      isLoading = false;
    }
  });
</script>

<div class="home-page">
  <Header />
  
  <main>
    <section class="hero">
      <div class="container">
        <h1>Shop Local, Support Community</h1>
        <p>Discover unique products from local businesses in your community.<br>Quality good, delivery fast.</p>
        
        <div class="hero-actions">
          <Link to="/shop" class="btn btn-primary">Browse Products</Link>
          <Link to="/businesses" class="btn btn-outline">Explore Businesses</Link>
        </div>
      </div>
    </section>
    
    <section class="featured-businesses">
      <div class="container">
        <div class="section-header">
          <h2>Featured Local Businesses</h2>
          <p>Discover and support the best businesses in your community</p>
        </div>
        
        <div class="business-grid">
          {#if isLoading}
            <div class="loading">Loading businesses...</div>
          {:else}
            {#each featuredBusinesses as business}
              <div class="business-card">
                <div class="business-image">
                  <img src={business.image || "/placeholder.svg"} alt={business.name} />
                </div>
                <div class="business-info">
                  <div class="business-header">
                    <h3>{business.name}</h3>
                    <div class="rating">
                      <span class="stars">★</span>
                      <span>{business.rating}</span>
                    </div>
                  </div>
                  <div class="business-meta">
                    <span class="location">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                      </svg>
                      {business.location}
                    </span>
                    <span class="category">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 7h-9"></path>
                        <path d="M14 17H5"></path>
                        <circle cx="17" cy="17" r="3"></circle>
                        <circle cx="7" cy="7" r="3"></circle>
                      </svg>
                      {business.category}
                    </span>
                  </div>
                  <p class="business-description">{business.description}</p>
                  <Link to={`/business/${business.id}`} class="view-business">View Business</Link>
                </div>
              </div>
            {/each}
          {/if}
        </div>
        
        <div class="pagination">
          <button class="prev">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 8 8 12 12 16"></polyline>
              <line x1="16" y1="12" x2="8" y2="12"></line>
            </svg>
          </button>
          <button class="next">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 16 16 12 12 8"></polyline>
              <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
          </button>
        </div>
      </div>
    </section>
  </main>
  
  <Footer />
</div>

<style>
  .home-page {
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
    padding: 0 1rem;
  }
  
  .hero {
    background-color: #fff;
    padding: 4rem 0;
    text-align: center;
  }
  
  .hero h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: #000;
  }
  
  .hero p {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 2rem;
  }
  
  .hero-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
  }
  
  .btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    font-weight: 500;
    text-align: center;
    transition: all 0.2s;
  }
  
  .btn-primary {
    background-color: #000;
    color: #fff;
  }
  
  .btn-outline {
    background-color: transparent;
    border: 1px solid #000;
    color: #000;
  }
  
  .btn-primary:hover {
    background-color: #333;
  }
  
  .btn-outline:hover {
    background-color: #f5f5f5;
  }
  
  .featured-businesses {
    padding: 4rem 0;
    background-color: #fff;
  }
  
  .section-header {
    text-align: center;
    margin-bottom: 2.5rem;
  }
  
  .section-header h2 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #000;
  }
  
  .section-header p {
    color: #666;
  }
  
  .business-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
  }
  
  .business-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .business-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }
  
  .business-image {
    height: 200px;
    overflow: hidden;
  }
  
  .business-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .business-info {
    padding: 1.5rem;
  }
  
  .business-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
  }
  
  .business-header h3 {
    margin: 0;
    font-size: 1.25rem;
  }
  
  .rating {
    display: flex;
    align-items: center;
    font-weight: 500;
  }
  
  .stars {
    color: #ffc107;
    margin-right: 0.25rem;
  }
  
  .business-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    color: #666;
  }
  
  .location, .category {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .business-description {
    margin-bottom: 1rem;
    color: #333;
    font-size: 0.95rem;
  }
  
  .view-business {
    display: inline-block;
    padding: 0.5rem 1rem;
    background-color: #f5f5f5;
    border-radius: 4px;
    font-size: 0.9rem;
    transition: background-color 0.2s;
  }
  
  .view-business:hover {
    background-color: #e0e0e0;
  }
  
  .pagination {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
  }
  
  .pagination button {
    background: none;
    border: none;
    cursor: pointer;
    color: #000;
    transition: color 0.2s;
  }
  
  .pagination button:hover {
    color: #666;
  }
  
  .loading {
    grid-column: 1 / -1;
    text-align: center;
    padding: 2rem;
    color: #666;
  }
  
  @media (max-width: 768px) {
    .hero h1 {
      font-size: 2rem;
    }
    
    .hero p {
      font-size: 1rem;
    }
    
    .hero-actions {
      flex-direction: column;
      gap: 0.75rem;
    }
    
    .business-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
