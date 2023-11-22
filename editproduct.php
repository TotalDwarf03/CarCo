<?php 
    session_start(); 

    include('Scripts/DBConnect.php');
    include('Scripts/GeneralScripts.php');

    // if not logged in or product manager permission,
    // redirect to index.php
    if(isset($_SESSION['UserID'])){
        if(!in_array(3, $_SESSION['UserPermissions'])) {
            header("location: index.php");
        }
    }
    else {
        header("location: index.php");
    }


    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // Post once edit form submitted

        $message;

        // Get Posted Values
        $ProductID = $_POST['ProductID'];
        $ProductName = $_POST['ProductName'];
        $Image = $_POST['Image'];
        $Description = $_POST['Description'];
        $CostPerUnit = $_POST['Cost'];
        $ProductStatus = $_POST['Status'];
        
        // 1. Update Database (Excl. Image)
        $sqlUpdate = "  UPDATE tblSystemProduct sp
                        SET
                            sp.ProductName = '$ProductName',
                            sp.Description = '$Description',
                            sp.CostPerUnit = $CostPerUnit,
                            sp.SystemProductStatusID = $ProductStatus
                        WHERE sp.SystemProductID = $ProductID
                        LIMIT 1";
        
        if(mysqli_query($db, $sqlUpdate)){
            // 2. Check if new image uploaded

            if($_FILES['fileToUpload']['name'] != ''){
                // 3. Upload Image in File Structure (Images/Product/)
                $UploadOk = true;

                $dir = "Images/Products/";
                $imgFileType = pathinfo(basename($_FILES['fileToUpload']['name']), PATHINFO_EXTENSION);
                $target = $dir.$ProductID.'.'.$imgFileType;

                // If Uploaded Image not real image, flag it
                if(getimagesize($_FILES['fileToUpload']['tmp_name']) == false){
                    $UploadOk = false;
                }

                if(!$UploadOk){
                    $message = 'Error Uploading Image, Please Edit the Record and Try Again.';
                }
                else {
                    if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target)){
                        // 4. Remove Old Image and Change Image Value in DB
                        if($Image != $target){
                            unlink($Image);
                        }

                        $sql = "UPDATE tblSystemProduct sp
                                SET sp.Image = '$target'
                                WHERE sp.SystemProductID = $ProductID
                                LIMIT 1";

                        $result = mysqli_query($db, $sql);

                        $message = "Record Updated";
                    }
                    else {
                        $message = "Error Uploading Image, Please Edit the Record and Try Again.";
                    }
                }
            }
            else {
                $message = "Record Updated";
            }
        }
        else {
            $message = "Error Updating Database";
        }
        
        header("location: viewproducts.php?UploadStatus=$message&SearchText=$ProductName");
    }
    elseif($_SERVER['REQUEST_METHOD'] == "GET"){
        // Get when coming from viewproducts.php

        $ProductID = $_GET['ProductID'];

        // Get Product Statuses for drop down
        $sqlProdStatus = "  SELECT
                                sps.ProductStatusID,
                                sps.Status
                            FROM tblSystemProductStatus sps
                            ORDER BY sps.Status";

        $ProductStatuses = mysqli_query($db, $sqlProdStatus);

        // Get Product Information
        $sqlProductInfo = " SELECT 
                                sp.SystemProductID,
                                sp.ProductName,
                                sp.Image,
                                sp.Description,
                                sp.CostPerUnit,
                                sps.Status
                            FROM tblSystemProduct sp
                            JOIN tblSystemProductStatus sps
                                ON sp.SystemProductStatusID = sps.ProductStatusID
                            WHERE sp.SystemProductID = $ProductID
                            ORDER BY 
                                sp.ProductName
                            LIMIT 1";
    
        $result = mysqli_query($db, $sqlProductInfo);
        $ProductInfo = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        // Get Values for Form
        $ProductName = $ProductInfo['ProductName'];
        $Image = $ProductInfo['Image'];
        $Description = $ProductInfo['Description'];
        $CostPerUnit = $ProductInfo['CostPerUnit'];
        $ProductStatus = $ProductInfo['Status'];

    }
    else {
        header("location: index.php");
    }
?>

<script>
    function ConfirmChanges(){
        document.getElementById("submit").disabled = false;
        document.getElementById("ConfirmProduct").disabled = true;
    }

    function UnConfirmChanges(){
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
    <title>CarCo - Edit Product</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <form id="EditProductForm" method="post" action="editproduct.php" enctype="multipart/form-data">
        <fieldset class="inputs">
            <legend><h3>Edit Product:</h3></legend>

            <!-- Product ID (Hidden) -->
            <input type="hidden" id="ProductID" name="ProductID" value="<?php echo($ProductID); ?>">
            <!-- Product Name -->
            <label for="ProductName">Product: </label>
            <input type="text" id="ProductName" name="ProductName" required maxlength="40" value="<?php echo($ProductName); ?>">
            <!-- Product Description -->
            <label for="Description">Description: </label>
            <textarea id="Description" name="Description" form="EditProductForm" required maxlength="255"><?php echo($Description); ?></textarea>
            <!-- Cost -->
            <label for="Cost">Cost: </label>
            <input type="number" id="Cost" name="Cost" min="0" max="999.99" step="0.01" required value="<?php echo($CostPerUnit); ?>">
            <!-- Product Status -->
            <label for="Status">Product Status: </label>
            <select id="Status" name="Status">
                <optgroup label="Product Status:">
                    <?php
                        while($row = mysqli_fetch_assoc($ProductStatuses)){
                            $ProductStatusID = $row['ProductStatusID'];
                            $Status = $row['Status'];
                            $selected = ($Status == $ProductStatus) ? 'selected' : '';

                            echo("
                                    <option value='$ProductStatusID' $selected>$Status</option>
                                ");
                        }
                    ?>
                </optgroup>
            </select>
            <!-- Image -->
            <label for="fileToUpload">Image: </label>
            <input type="file" id="fileToUpload" name="fileToUpload">
            <!-- Preview of Current Image and Hidden Input with old path -->
            <h4>Current Image:</h4>
            <img src="<?php echo($Image); ?>" <?php echo(ScaleImage(150, $Image)); ?>>
            <input type="hidden" id="Image" name="Image" value="<?php echo($Image); ?>">
            <hr>
        </fieldset>
        <fieldset>
            <!-- confirm -->
            <input type="button" id="ConfirmProduct" name="ConfirmProduct" value="Confirm" onclick="ConfirmChanges()">
            <!-- Submit -->
            <input type="submit" id="submit" name="submit" value="Submit" disabled>
            <!-- reset -->
            <input type="reset" id="reset" name="reset" value="Reset" onclick="UnConfirmChanges()" style="float: right;">
        </fieldset>
        </form>
    </main>
    
    <?php include("Widgets/footer.php"); ?>
</body>
</html>