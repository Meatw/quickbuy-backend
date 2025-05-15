<script>
  import { Link } from "svelte-routing";
</script>

<footer class="footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shopping-bag">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <path d="M16 10a4 4 0 0 1-8 0"></path>
        </svg>
        <span>QuickBuy</span>
        <p>Connecting local businesses with customers in their community.</p>
      </div>
      
      <div class="footer-links">
        <div class="footer-section">
          <h3>Shop</h3>
          <ul>
            <li><Link to="/shop">Categories</Link></li>
            <li><Link to="/businesses">Featured Businesses</Link></li>
            <li><Link to="/trending">Trending Products</Link></li>
            <li><Link to="/deals">Deals</Link></li>
          </ul>
        </div>
        
        <div class="footer-section">
          <h3>Shop</h3>
          <ul>
            <li><Link to="/start-selling">Start Selling</Link></li>
            <li><Link to="/seller-guidelines">Seller Guidelines</Link></li>
            <li><Link to="/seller-dashboard">Seller Dashboard</Link></li>
            <li><Link to="/success-stories">Success Stories</Link></li>
          </ul>
        </div>
        
        <div class="footer-section">
          <h3>Shop</h3>
          <ul>
            <li><Link to="/customer-support">Customer Support</Link></li>
            <li><Link to="/returns">Returns & Refunds</Link></li>
            <li><Link to="/shipping">Shipping Information</Link></li>
            <li><Link to="/faq">FAQ</Link></li>
          </ul>
        </div>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>© 2023 QuickBuy. All rights reserved.</p>
      <div class="footer-legal">
        <Link to="/terms">Terms</Link>
        <Link to="/privacy">Privacy</Link>
        <Link to="/cookies">Cookies</Link>
      </div>
    </div>
  </div>
</footer>

<style>
  .footer {
    background-color: #000;
    color: white;
    padding: 3rem 0 1rem;
    margin-top: auto;
  }
  
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
  }
  
  .footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-bottom: 2rem;
  }
  
  .footer-logo {
    display: flex;
    flex-direction: column;
    max-width: 300px;
    margin-bottom: 1.5rem;
  }
  
  .footer-logo span {
    display: flex;
    align-items: center;
    font-weight: bold;
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
  }
  
  .footer-logo p {
    font-size: 0.9rem;
    color: #aaa;
    margin-top: 0.5rem;
  }
  
  .footer-links {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
  }
  
  .footer-section h3 {
    font-size: 1rem;
    margin-bottom: 1rem;
  }
  
  .footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .footer-section li {
    margin-bottom: 0.5rem;
  }
  
  .footer-section a {
    color: #aaa;
    font-size: 0.9rem;
    transition: color 0.2s;
  }
  
  .footer-section a:hover {
    color: white;
  }
  
  .footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid #333;
    font-size: 0.8rem;
    color: #aaa;
  }
  
  .footer-legal {
    display: flex;
    gap: 1.5rem;
  }
  
  .footer-legal a {
    color: #aaa;
  }
  
  .footer-legal a:hover {
    color: white;
  }
  
  @media (max-width: 768px) {
    .footer-content {
      flex-direction: column;
    }
    
    .footer-links {
      margin-top: 1.5rem;
    }
    
    .footer-bottom {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
  }
</style>
