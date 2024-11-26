<nav class="sidebar <?php echo $darkMode ? 'dark' : ''; ?>">
    <header>
        <div class="image-text">
            <div class="text header-text">
                <span class="name">CraftHub</span>
                <span class="profession">Admin Panel</span>
            </div>
        </div>
        <i class='bx bx-menu toggle'></i>
    </header>

    <div class="menu-bar">
        <div class="menu">
            <ul class="menu-links">
                <li class="nav-link">
                    <a href="adminhomepage.php">
                        <i class='bx bxs-dashboard icon'></i>
                        <span class="text nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="adminprocessing.php">
                        <i class='bx bx-list-check icon'></i>
                        <span class="text nav-text">Application Processing</span>
                    </a>
                </li>
                <li class="nav-link">
                    <a href="adminaccsetup.php">
                        <i class='bx bxs-user-account icon'></i>
                        <span class="text nav-text">Account Set Up</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="bottom-content">
            <li class="">
                <a href="logout.php">
                    <i class='bx bx-log-out icon'></i>
                    <span class="text nav-text">Logout</span>
                </a>
            </li>
            <li class="mode">
                <div class="moon-sun">
                    <i class='bx bx-moon icon moon'></i>
                    <i class='bx bx-sun icon sun'></i>
                </div>
                <span class="mode-text text">Dark Mode</span>
                <div class="toggle-switch">
                    <span class="switch"></span>
                </div>
            </li>
        </div>
    </div>
</nav>