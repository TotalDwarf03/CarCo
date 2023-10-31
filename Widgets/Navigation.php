<?php
    function getActivePage(string $PageName){
        return (basename($_SERVER['PHP_SELF']) == $PageName) ? "class='active'" : '';
    }

    function showOnPermissions(int $permissionID, string $PageName){
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
                    // Put permission logic here :)
                }
                else {
                    // If Customer
                    return "style='display:none;'";
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
                return "style='display:none;'";
            }
        }
    }
?>

<header>
    <h1 id="SiteLogo">CarCo</h1>

    <nav>
        <ul>
            <li><a <?php echo(getActivePage('index.php')); ?> href='index.php'>Home</a></li>
            <li><a <?php echo(getActivePage('vieworders.php') . ' ' . showOnPermissions(-1, 'vieworders')); ?> href='vieworders.php'>Orders</a></li>
            <li><a <?php echo(getActivePage('viewproducts.php')); ?> href='viewproducts.php'>Products</a></li>
            <li><a <?php echo(getActivePage('viewcustomers.php') . ' ' . showOnPermissions(-1, 'viewcustomers')); ?> href='viewcustomers.php'>Customers</a></li>
            <li><a <?php echo(getActivePage('viewstaff.php') . ' ' . showOnPermissions(-1, 'viewstaff')); ?> href='viewstaff.php'>Staff</a></li>
            <li><a <?php echo(getActivePage('login.php') . ' ' . showOnPermissions(-1, 'login')); ?> href='login.php'>Login</a></li>
            <li><a <?php echo(getActivePage('logout.php') . ' ' . showOnPermissions(-1, 'logout')); ?> href='logout.php'>Logout</a></li>
        </ul>
    </nav>
</header>