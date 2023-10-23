<?php 
    function CloseDB($db){
        if(isset($db)){
            mysqli_close($db);
            // echo("DB Closed");
        }
    }

    function DropResults($results){
        //Drop dataset after table is created
        if($results){
            mysqli_free_result($results);
            // echo("Results Dropped");
        }
    }
?>