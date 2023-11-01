<?php
    session_start();

    $username = '';
    $loginStatus = '';

    function findUser(string $username){
        include('Scripts/DBConnect.php');

        // Finds a Staff or Customer Login based on given username
        $StaffQuery = " SELECT
                            s.StaffID AS UserID,
                            s.Forename AS Forename,
                            s.Surname AS Surname,
                            s.Image AS Image,
                            s.Username AS Username,
                            s.Password AS Password,
                            'Staff' AS UserType
                        FROM tblstaff s
                        WHERE s.Username = '$username'
                        LIMIT 1";

        $CustomerQuery = "  SELECT
                                cl.CustomerLoginID AS UserID,
                                cl.Forename AS Forename,
                                cl.Surname AS Surname,
                                c.Image AS Image,
                                cl.Username AS Username,
                                cl.Password AS Password,
                                'Customer' AS UserType
                            FROM tblcustomerlogin cl
                            JOIN tblcustomer c
                                ON cl.CustomerID = c.CustomerID
                            WHERE cl.Username = '$username'
                            LIMIT 1";

        $result = mysqli_query($db, $StaffQuery);

        if(!$result){
            $result = mysqli_query($db, $CustomerQuery);
            if(!$result){
                exit("User Doesn't Exist.");
            }
        }

        $userinfo = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        mysqli_close($db);
        return $userinfo;
    }

    function getUserPerms($user){
        if($user['UserType'] == 'Staff') {
            include('Scripts/DBConnect.php');

            $userID = $user['UserID'];

            $sql = "SELECT sp.PermissionID
                    FROM tblStaffPermissions sp
                    WHERE sp.StaffID = '$userID'";

            $result = mysqli_query($db, $sql);

            $userPermissions = array();

            while($row = mysqli_fetch_assoc($result)){
                array_push($userPermissions, $row['PermissionID']);
            }

            mysqli_free_result($result);
            mysqli_close($db);

            return $userPermissions;
        }
        else {
            return false;
        }
    }

    function login($user, $userPermissions){
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['Username'] = $user['Username'];
        $_SESSION['UserType'] = $user['UserType'];
        $_SESSION['Name'] = $user['Forename'] . ' ' . $user['Surname'];
        $_SESSION['Image'] = $user['Image'];

        if($userPermissions){
            $_SESSION['UserPermissions'] = $userPermissions;
        }
        
        return(true);
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
      
        $user = findUser($username);
      
        if($user){
            if(password_verify($password, $user['Password'])){
                $userPermissions = getUserPerms($user);
                login($user, $userPermissions);
                header("location: index.php");
                exit();
            }else{
                $loginStatus = 'Incorrect Password.';
            }
        }
        else{
            $loginStatus = 'User Not Found.';
        }
      }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Login</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <div>
        <form action="login.php" method="post">
            <fieldset>
                <legend><h2>Login:</h2></legend>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $username; ?>">
                
                <br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password">

                <p style="color: red;">
                    <?php echo $loginStatus ?>
                </p>

                <input type="submit" value="Submit">

            </fieldset>
        </form>
    </div>
</body>
</html>