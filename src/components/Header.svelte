<script>
  import { Link, navigate } from "svelte-routing";
  import { userStore } from "../stores/userStore";
  import { cartStore } from "../stores/cartStore";
  import { onMount } from "svelte";
  
  let searchQuery = "";
  
  function handleSearch(e) {
    e.preventDefault();
    navigate(`/shop?search=${encodeURIComponent(searchQuery)}`);
  }
  
  function logout() {
    // Clear user data
    userStore.set({
      isAuthenticated: false,
      user: null,
      token: null
    });
    
    // Clear cart data
    cartStore.set({
      items: [],
      total: 0,
      itemCount: 0
    });
    
    // Remove from local storage
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    
    // Redirect to home
    navigate("/");
  }
</script>

<header class="header">
  <div class="container">
    <div class="logo">
      <Link to="/">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shopping-bag">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <path d="M16 10a4 4 0 0 1-8 0"></path>
        </svg>
        <span>QuickBuy</span>
      </Link>
    </div>
    
    <form class="search-form" on:submit={handleSearch}>
      <input 
        type="text" 
        placeholder="Search your product or business" 
        bind:value={searchQuery}
      />
      <button type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>
    </form>
    
    <div class="nav-actions">
      <Link to="/cart" class="cart-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        {#if $cartStore.itemCount > 0}
          <span class="cart-count">{$cartStore.itemCount}</span>
        {/if}
      </Link>
      
      {#if $userStore.isAuthenticated}
        <div class="user-menu">
          <button class="user-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </button>
          <div class="dropdown-menu">
            {#if $userStore.user.role === 'seller'}
              <Link to="/seller/dashboard">Seller Dashboard</Link>
            {:else if $userStore.user.role === 'admin'}
              <Link to="/admin/dashboard">Admin Dashboard</Link>
            {:else}
              <Link to="/orders">My Orders</Link>
            {/if}
            <Link to="/profile">Profile</Link>
            <button on:click={logout}>Logout</button>
          </div>
        </div>
      {:else}
        <Link to="/login" class="login-button">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </Link>
      {/if}
    </div>
  </div>
</header>

<style>
  .header {
    background-color: #000;
    color: white;
    padding: 1rem 0;
  }
  
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  
  .logo a {
    display: flex;
    align-items: center;
    font-weight: bold;
    font-size: 1.25rem;
    color: white;
  }
  
  .shopping-bag {
    margin-right: 0.5rem;
  }
  
  .search-form {
    flex: 1;
    max-width: 500px;
    margin: 0 1rem;
    position: relative;
  }
  
  .search-form input {
    width: 100%;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    border: none;
    font-size: 0.9rem;
  }
  
  .search-form button {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #666;
  }
  
  .nav-actions {
    display: flex;
    align-items: center;
  }
  
  .cart-icon {
    position: relative;
    margin-right: 1rem;
    color: white;
  }
  
  .cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #ff4d4f;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .login-button, .user-button {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 0;
  }
  
  .user-menu {
    position: relative;
  }
  
  .dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    min-width: 150px;
    z-index: 1000;
    display: none;
  }
  
  .user-menu:hover .dropdown-menu {
    display: block;
  }
  
  .dropdown-menu a, .dropdown-menu button {
    display: block;
    padding: 0.5rem 1rem;
    color: #333;
    text-align: left;
    width: 100%;
    background: none;
    border: none;
    font-size: 0.9rem;
  }
  
  .dropdown-menu a:hover, .dropdown-menu button:hover {
    background-color: #f5f5f5;
  }
</style>
