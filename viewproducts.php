<?php 
    session_start(); 

    include('Scripts/ScaleImage.php');
    include('Scripts/DBConnect.php');


    function hideContent() {
        // If no permission, hide content
        if(isset($_SESSION['UserID'])){
            if(in_array(3, $_SESSION['UserPermissions'])) {
                // If have Product Manager Permission (id = 3)
                return '';
            }
            else {
                return "style='display:none;'";
            }
        }
        else {
            return "style='display:none;'";
        }
    }


    $SearchText = $_GET['SearchText'] ?? '';
    $ResultLimit = $_GET['ResultLimit'] ?? 10;
    $ResultLimit = ($ResultLimit == -1) ? "" : "LIMIT $ResultLimit";

    $sqlProducts = "SELECT 
                        sp.ProductName,
                        sp.Image,
                        sp.Description,
                        sp.CostPerUnit,
                        spt.Type,
                        sps.Status
                    FROM tblSystemProduct sp
                    JOIN tblSystemProductType spt
                        ON sp.SystemProductTypeID = spt.SystemProductTypeID
                    JOIN tblSystemProductStatus sps
                        ON sp.SystemProductStatusID = sps.ProductStatusID
                    WHERE sp.ProductName LIKE '%$SearchText%'
                        OR sp.Description LIKE '%$SearchText%'
                    ORDER BY 
                        spt.Type,
                        sp.ProductName
                    $ResultLimit";
    
    $Products = mysqli_query($db, $sqlProducts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Products</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <h2>Our Products:</h2>

        <!-- Products -->
        <section>
            <?php include("Widgets/SearchBar.php"); ?>

            <br>

            <table>
                <caption><h3>Products</h3></caption>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Cost</th>
                        <th>Status</th>

                        <!-- Edit and Delete -->
                        <th <?php echo(hideContent()); ?>>Edit</th>
                        <th <?php echo(hideContent()); ?>>Delete</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                        if($Products->num_rows>0){
                            while($row = mysqli_fetch_assoc($Products)) {
                                $Product = $row['ProductName'];
                                $Type = $row['Type'];
                                $Desc = $row['Description'];
                                $Img = $row['Image'];
                                $Cost = $row['CostPerUnit'];
                                $Status = $row['Status'];

                                $imgSize = ScaleImage(150, $Img);
                                $stockHighlight = $Status == 'In Stock' ? "style='background-color: lightgreen;'" : "style='background-color: lightcoral;'";
                                $permissionCheck = hideContent();

                                echo("  
                                        <tr>
                                            <td>$Product</td>
                                            <td>$Type</td>
                                            <td>$Desc</td>
                                            <td><img src='$Img' alt='Product Image' $imgSize onclick='maximiseImage()'></td>
                                            <td>£$Cost</td>
                                            <td $stockHighlight>$Status</td>
                                            <td class='edit' $permissionCheck>&#128393;</td>
                                            <td class='delete' $permissionCheck>&#128465;</td>
                                        </tr>
                                    ");
                            }
                        }
                        else {
                            echo("No Results Found.");
                        }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php include("Widgets/footer.php") ?>
</body>
</html>