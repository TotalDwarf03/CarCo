<?php 
    session_start(); 

    require_once("Scripts/DBConnect.php");
    require_once('Scripts/GeneralScripts.php');

    checkLoginPermissions(5);

    if($_SERVER['REQUEST_METHOD'] == "GET"){
        // Post when adding to basket

        // Textfile called basket.txt contains ProductID's and QTY's
        // Each entity is in the format: 
        //      <ProductID>,<QTY>;

        if(isset($_GET['ProductID'])){
            // Get Inputed Product and QTY
            $ProductID = $_GET['ProductID'];
            $qty = $_GET['qty'];

            // Open basket.txt
            $basket = fopen("basket.txt", "a");

            // Add new Product and QTY to basket.txt
            fwrite($basket, "$ProductID,$qty;");

            // Close basket.txt
            fclose($basket);

            // Reopen basket.txt to account for changes
            $basket = fopen("basket.txt", "r");

            // Get contents of basket.txt as a string
            $basketContents = fread($basket, filesize("basket.txt"));

            // Split contents into an array (i.e [Product1, Product2, Product3])
            $basketContents = explode(";", $basketContents);
            
            // Split each element into an array of [ProductID, QTY]
            foreach($basketContents as &$value){
                $value = explode(",", $value);
            }

            // Remove last element in array due to final basket item ending with a ;
            array_pop($basketContents);

            // Final array structure is:
            //      [[ProductID1, QTY1]], [[ProductID2, QTY2]], [[ProductID3, QTY3]]

            // Close Text File
            fclose($basket);
        }
        else{
            // If not passed any values, new order so remove basket.txt to empty it
            if(file_exists("basket.txt")){
                unlink("basket.txt");
            }
        }

    }
    elseif($_SERVER['REQUEST_METHOD'] == "POST"){
        // Post when submitting order to DB
    }
    else{
        header("location: index.php");
    }

    // Get List of Customers
    $sqlCustomers = "   SELECT
                            c.CustomerID,
                            c.CustomerName
                        FROM tblCustomer c";
    
    $Customers = mysqli_query($db, $sqlCustomers);

    // Get List of Products (Excl. Out of Stock - ID = 2)
    $sqlProducts = "SELECT
                        sp.SystemProductID,
                        sp.ProductName
                    FROM tblSystemProduct sp
                    WHERE sp.SystemProductStatusID != 2";
    
    $Products = mysqli_query($db, $sqlProducts);
?>

<script>
    function ConfirmNew(){
        document.getElementById("submit").disabled = false;
        document.getElementById("Confirm").disabled = true;
    }

    function AddToBasket(){
        if(document.getElementById("Product") != -1){
            ProductID = document.getElementById("Product").value;
            qty = document.getElementById("qty").value;

            document.location.replace(`addorder.php?ProductID=${ProductID}&qty=${qty}`);
        }
    }
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="all" href="css/style.css">
    <title>CarCo - Add Order</title>
</head>
<body class="CentrePage">
    <?php include("Widgets/navigation.php") ?>

    <main class="content">
        <section id="NewOrder" style="width:50%; float:left;">
            <h2>Add New Order:</h2>
            <form id="NewOrderForm" action="addorder.php" method="post">
                <fieldset class="inputs">
                    <legend><h3>Order Information:</h3></legend>

                    <!-- Customer Drop Down -->
                    <label for="Customer">Customer:</label>
                    <select id="Customer" name="Customer">
                        <optgroup label="Customer">
                            <?php
                                while($row = mysqli_fetch_assoc($Customers)){
                                    $CustomerID = $row['CustomerID'];
                                    $CustomerName = $row['CustomerName'];

                                    echo("
                                            <option value='$CustomerID'>$CustomerName</option>
                                        ");
                                }
                            ?>
                        </optgroup>
                    </select>

                    <!-- Delivery Date -->
                    <label for="DeliveryDate">Delivery Date:</label>
                    <input type="date" id="DeliveryDate" name="DeliveryDate" min="<?php echo date('Y-m-d'); ?>">
                </fieldset>

                <fieldset class="inputs">
                    <legend><h3>Add Product to Order:</h3></legend>

                    <!-- Product Drop Down -->
                    <label for="Product">Product:</label>
                    <select id="Product" name="Product" onchange="document.getElementById('qty').value = 1;">
                        <optgroup label="Product">
                            <option disabled selected value=-1>-- Select a Product --</option>

                            <?php
                                while($row = mysqli_fetch_assoc($Products)){
                                    $ProductID = $row['SystemProductID'];
                                    $ProductName = $row['ProductName'];

                                    echo("
                                            <option value='$ProductID'>$ProductName</option>
                                        ");
                                }
                            ?>
                        </optgroup>
                    </select>

                    <!-- Quantity -->
                    <label for="qty">Quantity:</label>
                    <input type="number" id="qty" name="qty" min=1>

                <br>

                <!-- Add to Basket -->
                <input type="button" id="addToBasket" name="addToBasket" value="Add to Basket" onclick="AddToBasket();">
                </fieldset>

                <fieldset>
                    <!-- confirm -->
                    <input type="button" id="Confirm" name="Confirm" value="Confirm" onclick="ConfirmNew()">
                    <!-- Submit -->
                    <input type="submit" id="submit" name="submit" value="Submit" disabled>
                    <!-- reset -->
                    <input type="button" id="cancel" name="cancel" value="Cancel" onclick="window.location.replace('vieworders.php');" style="float: right;">
                </fieldset>
            </form>
        </section>

        <section id="basket" style="width:50%; float:right;">
            <h2>Basket:</h2>

            <?php 
                if(isset($basketContents)){
                    echo("<ul>");
                    foreach($basketContents as &$value){
                        echo("<li>Product: $value[0], QTY: $value[1]</li>");
                    }
                    echo("</ul>");
                }
                else{
                    echo("<p>Basket Empty</p>");
                }
            ?>
        </section>
    </main>

    <?php include("Widgets/footer.php"); ?>
</body>
</html>