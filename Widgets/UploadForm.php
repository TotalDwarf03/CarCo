<form action="Scripts/UploadPhoto.php" method="post" enctype="multipart/form-data">
    <fieldset>
        <legend>Update Image:</legend>

        <input type="file" id="fileToUpload" name="fileToUpload">
        <input type="hidden" id="PageName" name="PageName" value="<?php echo(basename($_SERVER['PHP_SELF'])) ?>">
        <input type="submit" value="Confirm" name="submit">
    </fieldset>
</form>