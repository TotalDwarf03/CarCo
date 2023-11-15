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

    // Get Data for Main Products Table
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
    
    // Get data for New Product Form Drop Downs
    $Products = mysqli_query($db, $sqlProducts);

    $sqlProdStatus = "  SELECT
                            sps.ProductStatusID,
                            sps.Status
                        FROM tblSystemProductStatus sps
                        ORDER BY sps.Status";

    $ProductStatus = mysqli_query($db, $sqlProdStatus);

    $sqlProdType = "SELECT
                        spt.SystemProductTypeID,
                        spt.Type
                    FROM tblSystemProductType spt
                    ORDER BY spt.Type";
    
    $ProductType = mysqli_query($db, $sqlProdType);
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

    function ConfirmNewProduct(){
        document.getElementById("submit").disabled = false;
        document.getElementById("ConfirmProduct").disabled = true;
    }

    function UnConfirmNewProduct(){
        document.getElementById("submit").disabled = true;
        document.getElementById("ConfirmProduct").disabled = false;
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

            <p class="message"><?php echo($_GET['UploadStatus'] ?? '') ?></p>
        
            <button type="button" onclick="showNewForm()">Add New Product</button>

            <form id="NewProductForm" hidden action="addproduct.php" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend><h3>Add New Product:</h3></legend>

                    <!-- Product Name -->
                    <label for="ProductName">Product: </label>
                    <input type="text" id="ProductName" name="ProductName" required maxlength="40">
                    <br>
                    <!-- Product Description -->
                    <label for="Description">Description: </label>
                    <input type="text" id="Description" name="Description" required maxlength="255">
                    <br>
                    <!-- Cost -->
                    <label for="Cost">Cost: </label>
                    <input type="number" id="Cost" name="Cost" min="0" max="999.99" step="0.01" required>
                    <br>
                    <!-- Product Status -->
                    <label for="Status">Product Status: </label>
                    <select id="Status" name="Status">
                        <optgroup label="Product Status:">
                            <?php
                                if($ProductStatus->num_rows>0){
                                    while($row = mysqli_fetch_assoc($ProductStatus)){
                                        $ProductStatusID = $row['ProductStatusID'];
                                        $Status = $row['Status'];

                                        echo("
                                                <option value='$ProductStatusID'>$Status</option>
                                            ");
                                    }
                                }
                            ?>
                        </optgroup>
                    </select>
                    <br>
                    <!-- Product Type -->
                    <label for="Type">Product Type: </label>
                    <select id="Type" name="Type">
                        <optgroup label="Product Type:">
                            <?php
                                if($ProductType->num_rows>0){
                                    while($row = mysqli_fetch_assoc($ProductType)){
                                        $ProductTypeID = $row['SystemProductTypeID'];
                                        $Type = $row['Type'];

                                        echo("
                                                <option value='$ProductTypeID'>$Type</option>
                                            ");
                                    }
                                }
                            ?>
                        </optgroup>
                    </select>
                    <br>
                    <!-- Image -->
                    <label for="fileToUpload">Image: </label>
                    <input type="file" id="fileToUpload" name="fileToUpload" required>
                    <br>
                    <hr>
                    <!-- confirm -->
                    <input type="button" id="ConfirmProduct" name="ConfirmProduct" value="Confirm" onclick="ConfirmNewProduct()">
                    <!-- Submit -->
                    <input type="submit" id="submit" name="submit" value="Submit" disabled>
                    <!-- reset -->
                    <input type="reset" id="reset" name="reset" value="Clear" onclick="UnConfirmNewProduct()" style="float: right;">
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
                                $Img = $row['Image'] ?? '';
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

                <tfoot>
                    <tr>
                        <td colspan="7"><i><?php echo("$Products->num_rows Results.") ?></i></td>
                    </tr>
                </tfoot>
            </table>
        </section>
    </main>

    <?php include("Widgets/footer.php") ?>
</body>
</html>