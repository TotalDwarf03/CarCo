<?php
    session_start();

    function getActivePage(string $PageName){
        return (basename($_SERVER['PHP_SELF']) == $PageName) ? "class='active'" : '';
    }

    function showOnPermissions(int $permissionID, string $PageName){
        if(isset($_SESSION['UserID'])) {
            if($PageName == 'Login'){
                return "style='display:none;'";
            }
            if($PageName == 'Logout'){
                return '';
            }

            if($_SESSION['UserType'] == 'Staff'){
                if(in_array($permissionID, $_SESSION['PermissionList'])){
                    return '';
                }
                else {
                    return "style='display:none;'";
                }
            }
            else {
                if($PageName == 'ViewOrders'){
                    return '';
                }
            }
        }
        else {
            if($PageName == 'Login'){
                return '';
            }
            if($PageName == 'Logout'){
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
            <li><a <?php echo(getActivePage('Login.php') . ' ' . showOnPermissions(-1, 'Login')); ?> href='Login.php'>Login</a></li>
            <li><a <?php echo(getActivePage('Logout.php') . ' ' . showOnPermissions(-1, 'Logout')); ?> href='Logout.php'>Logout</a></li>
        </ul>
    </nav>
</header>