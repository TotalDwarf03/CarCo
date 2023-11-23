<footer>
    &copy; <?php echo date('Y'); ?> CarCo
</footer>

<?php
    if(isset($db)){
        mysqli_close($db);
    }
?>