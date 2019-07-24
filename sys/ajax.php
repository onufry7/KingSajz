<?php
if(isset($_POST['count'])&&($_POST['count']=="true")) echo ini_get('max_file_uploads');
?>