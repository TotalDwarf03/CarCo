<?php
    function ScaleImage(int $requiredSize, string $imagePath) {
        // Returns either image height or width set at the requiredSize based on which is bigger

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
?>