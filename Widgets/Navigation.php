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
            if($PageName == 'logout'){
                return '';
            }

            if($_SESSION['UserType'] == 'Staff'){
                // If Logged in User is a Staff Member
                if(in_array($permissionID, $_SESSION['PermissionList'])){
                    // Show if Passed Permission in Permission List (Gathered From Database)
                    return '';
                }
                else {
                    // Hide if not
                    return "style='display:none;'";
                }
            }
            else {
                // If Logged in User is a Customer
                // Show Orders Page
                if($PageName == 'ViewOrders'){
                    return '';
                }
            }
        }
        else {
            // If not Logged in

            // Hide Logout, Show Login
            if($PageName == 'login'){
                return '';
            }
            if($PageName == 'logout'){
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
            <li><a <?php echo(getActivePage('login.php') . ' ' . showOnPermissions(-1, 'login')); ?> href='login.php'>Login</a></li>
            <li><a <?php echo(getActivePage('logout.php') . ' ' . showOnPermissions(-1, 'logout')); ?> href='logout.php'>Logout</a></li>
        </ul>
    </nav>
</header>