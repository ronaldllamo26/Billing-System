<aside id="sidebar">
    <div class="sidebar-content mt-3">
        <!-- PC Stations -->
        <div class="sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" 
             onclick="window.location.href='dashboard.php'">
            <i data-lucide="monitor"></i>
            <span>PC Stations</span>
        </div>

        <!-- Member Accounts -->
        <div class="sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>" 
             onclick="window.location.href='users.php'">
            <i data-lucide="users"></i>
            <span>User Accounts</span>
        </div>

        <!-- AI Core (THE FUTURE) -->
        <div class="sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'ai_core.php') ? 'active' : ''; ?>" 
             onclick="window.location.href='ai_core.php'">
            <i data-lucide="brain-circuit" class="text-primary"></i>
            <span class="fw-bold">AI Intelligence</span>
        </div>

        <!-- Inventory -->
        <div class="sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'inventory.php') ? 'active' : ''; ?>" 
             onclick="window.location.href='inventory.php'">
            <i data-lucide="package"></i>
            <span>Inventory</span>
        </div>

        <!-- Revenue Reports -->
        <div class="sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>" 
             onclick="window.location.href='reports.php'">
            <i data-lucide="bar-chart-2"></i>
            <span>Revenue Reports</span>
        </div>
        
        <div style="margin-top: auto; padding: 20px;">
            <div style="background: #f8fafc; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 11px;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 10px #10b981;"></div>
                    <span class="fw-bold text-dark">STUXZ AI Active</span>
                </div>
                <span class="text-muted">Core Engine v1.0</span>
            </div>
        </div>
        
        <div class="sidebar-item text-danger border-top" style="border-left: none;" onclick="window.location.href='../index.php'">
            <i data-lucide="power"></i>
            <span>Exit System</span>
        </div>
    </div>
</aside>
