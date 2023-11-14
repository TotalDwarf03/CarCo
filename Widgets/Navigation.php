<?php
    function getActivePage(string $PageName){
        // Checks if the current page name is equal to $PageName
        return (basename($_SERVER['PHP_SELF']) == $PageName) ? "class='active'" : '';
    }

    function showOnPermissions(int $permissionID, string $PageName){
        // Controls which pages show based on user permissions
        if(isset($_SESSION['UserID'])) {
            // If Logged in

            // Show Logout, Hide Login
            if($PageName == 'login'){
                return "style='display:none;'";
            }
            elseif($PageName == 'logout'){
                return '';
            }
            else{
                if($_SESSION['UserType'] == 'Staff'){
                    // If Staff
                    if(in_array($permissionID, $_SESSION['UserPermissions'])){
                        // If passed $permissionID in the permission list, show
                        return '';
                    }
                    else{
                        // If not, hide
                        return "style='display:none;'";
                    }
                }
                else {
                    // If Customer
                    if($PageName == 'vieworders'){
                        // If Order Page, show
                        return '';
                    }
                    else {
                        // If not, hide
                        return "style='display:none;'";
                    }
                }
            }
        }
        else {
            // If not Logged in

            // Hide Logout, Show Login
            if($PageName == 'login'){
                return '';
            }
            elseif($PageName == 'logout'){
                return "style='display:none;'";
            }
            else {
                // Hide other pages
                return "style='display:none;'";
            }
        }
    }
?>

<header class="navbar">
    <h1 id="SiteLogo">CarCo</h1>

    <nav class="mainNav">
        <ul>
            <li><a <?php echo(getActivePage('index.php')); ?> href='index.php'>Home</a></li>
            <li><a <?php echo(getActivePage('vieworders.php') . ' ' . showOnPermissions(5, 'vieworders')); ?> href='vieworders.php'>Orders</a></li>
            <li><a <?php echo(getActivePage('viewproducts.php')); ?> href='viewproducts.php'>Products</a></li>
            <li><a <?php echo(getActivePage('viewcustomers.php') . ' ' . showOnPermissions(4, 'viewcustomers')); ?> href='viewcustomers.php'>Customers</a></li>
            <li><a <?php echo(getActivePage('viewstaff.php') . ' ' . showOnPermissions(2, 'viewstaff')); ?> href='viewstaff.php'>Staff</a></li>
            <li><a <?php echo(getActivePage('login.php') . ' ' . showOnPermissions(-1, 'login')); ?> href='login.php'>Login</a></li>
            <li><a <?php echo(getActivePage('logout.php') . ' ' . showOnPermissions(-1, 'logout')); ?> href='logout.php'>Logout</a></li>
        </ul>
    </nav>
</header>