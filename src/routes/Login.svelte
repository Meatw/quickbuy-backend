<script>
  import { Link, navigate } from "svelte-routing";
  import { userStore } from "../stores/userStore";
  import { cartStore } from "../stores/cartStore";
  
  let email = "";
  let password = "";
  let isLoading = false;
  let error = null;
  
  async function handleLogin(e) {
    e.preventDefault();
    
    if (!email || !password) {
      error = "Email and password are required";
      return;
    }
    
    isLoading = true;
    error = null;
    
    try {
      const response = await fetch("http://localhost:8000/api/auth/login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ email, password })
      });
      
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.message || "Login failed");
      }
      
      // Store user data and token
      localStorage.setItem("token", data.token);
      localStorage.setItem("user", JSON.stringify(data.user));
      
      // Update user store
      userStore.set({
        isAuthenticated: true,
        user: data.user,
        token: data.token
      });
      
      // If user is a customer, fetch cart data
      if (data.user.role === "customer") {
        await fetchCart(data.token);
      }
      
      // Redirect based on role
      if (data.user.role === "seller") {
        navigate("/seller/dashboard");
      } else if (data.user.role === "admin") {
        navigate("/admin/dashboard");
      } else {
        navigate("/");
      }
      
    } catch (err) {
      console.error("Login error:", err);
      error = err.message || "Login failed. Please try again.";
    } finally {
      isLoading = false;
    }
  }
  
  async function fetchCart(token) {
    try {
      const response = await fetch("http://localhost:8000/api/cart", {
        headers: {
          "Authorization": `Bearer ${token}`
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

<div class="login-page">
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>QuickBuy</h1>
        <p>Your One-Stop for All you Need</p>
      </div>
      
      <div class="tab-container">
        <Link to="/login" class="tab active">Login</Link>
        <Link to="/register/customer" class="tab">Register</Link>
      </div>
      
      <form on:submit={handleLogin} class="login-form">
        {#if error}
          <div class="error-message">{error}</div>
        {/if}
        
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            placeholder="name@example.com" 
            bind:value={email}
            required
          />
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            bind:value={password}
            required
          />
        </div>
        
        <button type="submit" class="login-button" disabled={isLoading}>
          {isLoading ? 'Logging in...' : 'Login'}
        </button>
        
        <div class="register-link">
          Don't have an account? <Link to="/register/customer">Register</Link>
        </div>
      </form>
      
      <div class="terms-notice">
        By continuing, you agree to QuickBuy's Terms of Services and Privacy Policy
      </div>
    </div>
  </div>
</div>

<style>
  .login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #121212;
    padding: 1rem;
  }
  
  .login-container {
    width: 100%;
    max-width: 400px;
  }
  
  .login-card {
    background-color: #000;
    border-radius: 8px;
    padding: 2rem;
    color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .login-header {
    text-align: center;
    margin-bottom: 1.5rem;
  }
  
  .login-header h1 {
    margin: 0;
    font-size: 1.75rem;
  }
  
  .login-header p {
    margin: 0.5rem 0 0;
    font-size: 0.9rem;
    color: #aaa;
  }
  
  .tab-container {
    display: flex;
    margin-bottom: 1.5rem;
    border: 1px solid #333;
    border-radius: 4px;
    overflow: hidden;
  }
  
  .tab {
    flex: 1;
    text-align: center;
    padding: 0.75rem;
    background-color: transparent;
    color: white;
    transition: background-color 0.2s;
  }
  
  .tab.active {
    background-color: white;
    color: black;
  }
  
  .login-form {
    margin-bottom: 1.5rem;
  }
  
  .form-group {
    margin-bottom: 1rem;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
  }
  
  .form-group input {
    width: 100%;
    padding: 0.75rem;
    border-radius: 4px;
    border: 1px solid #333;
    background-color: #fff;
    font-size: 0.9rem;
  }
  
  .login-button {
    width: 100%;
    padding: 0.75rem;
    background-color: #000;
    color: white;
    border: 1px solid white;
    border-radius: 4px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background-color 0.2s;
    margin-top: 1rem;
  }
  
  .login-button:hover {
    background-color: #333;
  }
  
  .login-button:disabled {
    background-color: #333;
    cursor: not-allowed;
  }
  
  .register-link {
    text-align: center;
    margin-top: 1rem;
    font-size: 0.9rem;
  }
  
  .register-link a {
    color: #fff;
    text-decoration: underline;
  }
  
  .terms-notice {
    text-align: center;
    font-size: 0.8rem;
    color: #aaa;
  }
  
  .error-message {
    background-color: rgba(255, 77, 79, 0.1);
    border: 1px solid #ff4d4f;
    color: #ff4d4f;
    padding: 0.5rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    font-size: 0.9rem;
  }
</style>
