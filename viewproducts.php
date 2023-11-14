<?php 
    session_start(); 

    include('Scripts/GeneralScripts.php');
    include('Scripts/DBConnect.php');

    function getProductsWidth() {
        // If logged in and have product manager permission (id=3),
        // Product Manager aside will display therefore width should be 70%
        // Else, use 100%
        if(isset($_SESSION['UserID'])){
            if(in_array(3, $_SESSION['UserPermissions'])) {
                return '70%';
            }
            else {
                return "100%";
            }
        }
        else {
            return "100%";
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

<script>
    function showNewForm(){
        NewProductForm = document.getElementById("NewProductForm")

        if(NewProductForm.hidden){
            NewProductForm.hidden = false;
        }
        else{
            NewProductForm.hidden = true;
        }
    }
</script>

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
        <aside class="ManagerTools" <?php echo(hideContent(3)); ?>>
            <h2>Manager Tools</h2>
        
            <button type="button" onclick="showNewForm()">Add New Product</button>

            <form id="NewProductForm" hidden>
                <fieldset>
                    <legend><h3>Add New Product:</h3></legend>
                </fieldset>
            </form>

        </aside>

        <!-- Products -->
        <section id="Products" style="width: <?php echo(getProductsWidth()); ?>;">
            <?php include("Widgets/SearchBar.php"); ?>

            <br>

            <table>
                <caption><h2>Products</h2></caption>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Cost</th>
                        <th>Status</th>

                        <!-- Edit and Delete -->
                        <th <?php echo(hideContent(3)); ?>>Edit</th>
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
                                $permissionCheck = hideContent(3);

                                echo("  
                                        <tr>
                                            <td>$Product</td>
                                            <td>$Type</td>
                                            <td>$Desc</td>
                                            <td><img src='$Img' alt='Product Image' $imgSize onclick='maximiseImage()'></td>
                                            <td>£$Cost</td>
                                            <td $stockHighlight>$Status</td>
                                            <td class='edit' $permissionCheck>&#128393;</td>
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