<?php
    function ScaleImage(int $requiredSize, string $imagePath) {
        // Returns either image height or width set at the requiredSize based on which is bigger

        if($imagePath != ''){
            $imgSize = getimagesize($imagePath);

            if ($imgSize != false) {
                $width = $imgSize[0];
                $height = $imgSize[1];

                return(($width >= $height) ? "width=$requiredSize px" : "height=$requiredSize px");
            }
            else {
                return '';
            }
        }
        else{
            return '';
        }
        
    }

    function hideContent(int $PermissionID) {
        // If no permission, hide content
        if(isset($_SESSION['UserID'])){
            if(in_array($PermissionID, $_SESSION['UserPermissions'])) {
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

    function checkLoginPermissions(int $PermissionID){
        // if not logged in or no permission,
        // redirect to index.php
        if(isset($_SESSION['UserID'])){
            if(!in_array($PermissionID, $_SESSION['UserPermissions'])) {
                header("location: index.php");
            }
        }
        else {
            header("location: index.php");
        }
    }
?>