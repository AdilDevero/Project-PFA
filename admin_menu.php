<?php
// Admin Menu Component
// Include this file in your admin pages after the opening body tag
?>
<style>
  .admin-sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    height: 100vh;
    background: linear-gradient(135deg, #1e3a5f 0%, #2c5aa0 100%);
    padding: 30px 0;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    z-index: 999;
    overflow-y: auto;
  }

  .admin-sidebar .menu-header {
    text-align: center;
    color: white;
    margin-bottom: 40px;
    padding: 0 20px;
  }

  .admin-sidebar .menu-header h3 {
    font-size: 20px;
    font-weight: bold;
    margin: 0;
  }

  .admin-menu {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .admin-menu li {
    margin: 0;
  }

  .admin-menu a {
    display: flex;
    align-items: center;
    padding: 15px 25px;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    font-size: 15px;
  }

  .admin-menu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    border-left-color: #4db8ff;
  }

  .admin-menu a.active {
    background-color: rgba(255, 255, 255, 0.15);
    color: white;
    border-left-color: #ffffff;
    font-weight: 600;
  }

  .admin-menu i {
    margin-right: 12px;
    font-size: 18px;
    width: 20px;
    text-align: center;
  }

  .admin-menu-divider {
    height: 1px;
    background-color: rgba(255, 255, 255, 0.2);
    margin: 15px 0;
  }

  .admin-logout-btn {
    position: absolute;
    bottom: 30px;
    left: 0;
    right: 0;
    padding: 0 25px;
  }

  .admin-logout-btn a {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 15px;
    background-color: #e74c3c;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .admin-logout-btn a:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
  }

  .admin-content {
    margin-left: 260px;
    padding-top: 20px;
    transition: margin-left 0.3s ease;
  }

  .admin-sidebar.hidden {
    transform: translateX(-100%);
  }

  .admin-content.sidebar-hidden {
    margin-left: 0;
  }

  .sidebar-toggle-btn {
    position: fixed;
    top: 90px;
    left: 20px;
    z-index: 1000;
    background-color: #1e3a5f;
    color: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }

  .sidebar-toggle-btn:hover {
    background-color: #2c5aa0;
    transform: scale(1.1);
  }

  .admin-sidebar.hidden ~ .sidebar-toggle-btn {
    display: flex;
    left: 20px;
  }

</style>

<aside class="admin-sidebar" id="adminSidebar">
  <button class="sidebar-close-btn" onclick="toggleSidebar()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; z-index: 1001;">
    <i class="ri-close-line"></i>
  </button>
  
  <div class="menu-header">
    <h3><i class="ri-admin-line"></i> Admin Panel</h3>
  </div>

  <ul class="admin-menu">
    <li>
      <a href="admin_cars.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'admin_cars.php' ? 'active' : ''; ?>">
        <i class="ri-car-line"></i>
        <span>Manage Cars</span>
      </a>
    </li>
    <li>
      <a href="admin_brands.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'admin_brands.php' ? 'active' : ''; ?>">
        <i class="ri-trademark-line"></i>
        <span>Manage Brands</span>
      </a>
    </li>
    <li>
      <a href="admin_tickets.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'admin_tickets.php' ? 'active' : ''; ?>">
        <i class="ri-ticket-2-line"></i>
        <span>Tickets & Reservations</span>
      </a>
    </li>
    <li>
      <a href="admin_actions.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'admin_actions.php' ? 'active' : ''; ?>">
        <i class="ri-settings-line"></i>
        <span>Admin Actions</span>
      </a>
    </li>
    <li>
      <a href="admin_stats.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'admin_stats.php' ? 'active' : ''; ?>">
        <i class="ri-bar-chart-line"></i>
        <span>Statistics</span>
      </a>
    </li>
  </ul>

  <div class="admin-menu-divider" style="position: absolute; bottom: 100px; width: 100%;"></div>

  <div class="admin-logout-btn">
    <a href="admin_logout.php" onclick="return confirm('Are you sure you want to exit the staff panel?');">
      <i

<button class="sidebar-toggle-btn" id="toggleBtn" onclick="toggleSidebar()" title="Show/Hide Menu">
  ☰
</button>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const content = document.querySelector('.admin-content');
    
    sidebar.classList.toggle('hidden');
    content.classList.toggle('sidebar-hidden');
    
    // Save state to localStorage
    localStorage.setItem('sidebarHidden', sidebar.classList.contains('hidden'));
  }
  
  // Restore sidebar state on page load
  window.addEventListener('DOMContentLoaded', function() {
    const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
    if (sidebarHidden) {
      const sidebar = document.getElementById('adminSidebar');
      const content = document.querySelector('.admin-content');
      sidebar.classList.add('hidden');
      content.classList.add('sidebar-hidden');
    }
  });
</script> class="ri-logout-box-line" style="margin-right: 8px;"></i>
      <span>Exit Staff Panel</span>
    </a>
  </div>
</aside>
