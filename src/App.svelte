<script>
  import { Router, Route } from "svelte-routing";
  import { onMount } from "svelte";
  import { userStore } from "./stores/userStore";
  import { cartStore } from "./stores/cartStore";
  import { get } from 'svelte/store';
  
  // Components
  import Login from "./routes/Login.svelte";
  import RegisterCustomer from "./routes/RegisterCustomer.svelte";
  import RegisterSeller from "./routes/RegisterSeller.svelte";
  import Home from "./routes/Home.svelte";
  import Shop from "./routes/Shop.svelte";
  import ProductDetail from "./routes/ProductDetail.svelte";
  import Cart from "./routes/Cart.svelte";
  import Checkout from "./routes/Checkout.svelte";
  import SellerDashboard from "./routes/SellerDashboard.svelte";
  import AdminDashboard from "./routes/AdminDashboard.svelte";
  import NotFound from "./routes/NotFound.svelte";
  
  export let url = "";
  
  onMount(() => {
    // Check for stored token and user data
    const token = localStorage.getItem("token");
    const userData = localStorage.getItem("user");
    
    if (token && userData) {
      try {
        const user = JSON.parse(userData);
        userStore.set({ 
          isAuthenticated: true, 
          user,
          token
        });
        
        // Load cart data if user is a customer
        if (user.role === "customer") {
          fetchCart();
        }
      } catch (error) {
        console.error("Error parsing user data:", error);
        localStorage.removeItem("token");
        localStorage.removeItem("user");
      }
    }
  });
  
  async function fetchCart() {
    try {
      const response = await fetch("http://localhost:8000/api/cart", {
        headers: {
          "Authorization": `Bearer ${get(userStore).token}`
        }
      });
      
      if (response.ok) {
        const cartData = await response.json();
        cartStore.update(cart => ({
          ...cart,
          items: cartData.items || [],
          total: cartData.total || 0,
          itemCount: cartData.item_count || 0
        }));
      }
    } catch (error) {
      console.error("Error fetching cart:", error);
    }
  }
</script>

<Router {url}>
  <div class="app">
    <Route path="/" component={Home} />
    <Route path="/login" component={Login} />
    <Route path="/register/customer" component={RegisterCustomer} />
    <Route path="/register/seller" component={RegisterSeller} />
    <Route path="/shop" component={Shop} />
    <Route path="/product/:id" component={ProductDetail} />
    <Route path="/cart" component={Cart} />
    <Route path="/checkout" component={Checkout} />
    <Route path="/seller/dashboard/*" component={SellerDashboard} />
    <Route path="/admin/dashboard/*" component={AdminDashboard} />
    <Route path="*" component={NotFound} />
  </div>
</Router>

<style>
  :global(body) {
    margin: 0;
    padding: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    background-color: #f5f5f5;
  }
  
  :global(*) {
    box-sizing: border-box;
  }
  
  :global(a) {
    text-decoration: none;
    color: inherit;
  }
  
  :global(button) {
    cursor: pointer;
  }
  
  .app {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
</style>
