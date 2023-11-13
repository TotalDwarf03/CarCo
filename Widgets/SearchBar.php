<script>
    function CheckSearchContents() {
        if(document.getElementById("SearchBar").value == ""){
            document.getElementById("SearchButton").disabled = true;
        }
        else {
            document.getElementById("SearchButton").disabled = false;
        }
    }

    function UpdateResultSet() {
        SearchText = document.getElementById("SearchBar").value;
        ResultLimit = document.getElementById("ResultLimit").value;

        window.location.replace(`${location.pathname}?SearchText=${SearchText}&ResultLimit=${ResultLimit}`);
    }

    function ClearSearch() {
        window.location.replace(`${location.pathname}`);
    }
</script> 

<?php
    $resultLimit = $_GET['ResultLimit'] ?? 10
?>

<form id="Searchbar" action="<?php echo basename($_SERVER['PHP_SELF']) ?>">    
    <input 
        type="search" 
        id="SearchBar" 
        name="SearchText" 
        placeholder="Search..." 
        value="<?php echo ($_GET['SearchText'] ?? '' != '') ? $_GET['SearchText'] : '' ?>" 
        onkeyup="CheckSearchContents()"
    >
    <button type="submit" id="SearchButton" disabled>&#x1F50E;&#xFE0E;</button>
    <button type="button" onclick="ClearSearch()">&#10006;</button>

    <br>

    <label for="ResultLimit">Show Results: </label>
    <select id="ResultLimit" name="ResultLimit" onchange='UpdateResultSet()'>
        <optgroup label="Show Results:">
            <option value="10" <?php echo(($resultLimit == 10) ? 'selected' : '') ?>>10</option>
            <option value="20" <?php echo(($resultLimit == 20) ? 'selected' : '') ?>>20</option>
            <option value="50" <?php echo(($resultLimit == 50) ? 'selected' : '') ?>>50</option>
            <option value="-1" <?php echo(($resultLimit == -1) ? 'selected' : '') ?>>All</option>
        </optgroup>
    </select>
</form>