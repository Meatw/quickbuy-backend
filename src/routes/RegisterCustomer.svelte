<script>
  import { Link, navigate } from "svelte-routing";
  import { userStore } from "../stores/userStore";
  
  let fullName = "";
  let email = "";
  let password = "";
  let confirmPassword = "";
  let isLoading = false;
  let error = null;
  
  async function handleRegister(e) {
    e.preventDefault();
    
    // Validate form
    if (!fullName || !email || !password || !confirmPassword) {
      error = "All fields are required";
      return;
    }
    
    if (password !== confirmPassword) {
      error = "Passwords do not match";
      return;
    }
    
    isLoading = true;
    error = null;
    
    try {
      const response = await fetch("http://localhost:8000/api/auth/register", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          name: fullName,
          email,
          password,
          role: "customer"
        })
      });
      
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.message || "Registration failed");
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
      
      // Redirect to home page
      navigate("/");
      
    } catch (err) {
      console.error("Registration error:", err);
      error = err.message || "Registration failed. Please try again.";
    } finally {
      isLoading = false;
    }
  }
</script>

<div class="register-page">
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h1>QuickBuy</h1>
        <p>Your One-Stop for All you Need</p>
      </div>
      
      <div class="tab-container">
        <Link to="/login" class="tab">Login</Link>
        <Link to="/register/customer" class="tab active">Register</Link>
      </div>
      
      <div class="account-type">
        <h3>Account Type</h3>
        <div class="account-options">
          <label class="account-option active">
            <input type="radio" name="accountType" value="customer" checked />
            <span>Register as Customer</span>
          </label>
          <Link to="/register/seller" class="account-option">
            <span>Register as Seller</span>
          </Link>
        </div>
      </div>
      
      <form on:submit={handleRegister} class="register-form">
        {#if error}
          <div class="error-message">{error}</div>
        {/if}
        
        <div class="form-group">
          <label for="fullName">Full Name</label>
          <input 
            type="text" 
            id="fullName" 
            placeholder="John Doe" 
            bind:value={fullName}
            required
          />
        </div>
        
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
        
        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input 
            type="password" 
            id="confirmPassword" 
            bind:value={confirmPassword}
            required
          />
        </div>
        
        <button type="submit" class="register-button" disabled={isLoading}>
          {isLoading ? 'Registering...' : 'Login'}
        </button>
        
        <div class="login-link">
          Already have an account? <Link to="/login">Login</Link>
        </div>
      </form>
      
      <div class="terms-notice">
        By continuing, you agree to QuickBuy's Terms of Services and Privacy Policy
      </div>
    </div>
  </div>
</div>

<style>
  .register-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #121212;
    padding: 1rem;
  }
  
  .register-container {
    width: 100%;
    max-width: 400px;
  }
  
  .register-card {
    background-color: #000;
    border-radius: 8px;
    padding: 2rem;
    color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .register-header {
    text-align: center;
    margin-bottom: 1.5rem;
  }
  
  .register-header h1 {
    margin: 0;
    font-size: 1.75rem;
  }
  
  .register-header p {
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
  
  .account-type {
    margin-bottom: 1.5rem;
  }
  
  .account-type h3 {
    font-size: 1rem;
    margin-bottom: 0.75rem;
  }
  
  .account-options {
    display: flex;
    gap: 0.5rem;
  }
  
  .account-option {
    flex: 1;
    padding: 0.75rem;
    border: 1px solid #333;
    border-radius: 4px;
    text-align: center;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .account-option input {
    position: absolute;
    opacity: 0;
  }
  
  .account-option.active {
    background-color: white;
    color: black;
  }
  
  .register-form {
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
  
  .register-button {
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
  
  .register-button:hover {
    background-color: #333;
  }
  
  .register-button:disabled {
    background-color: #333;
    cursor: not-allowed;
  }
  
  .login-link {
    text-align: center;
    margin-top: 1rem;
    font-size: 0.9rem;
  }
  
  .login-link a {
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
