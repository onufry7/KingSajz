<?php
// Sprawdzenie maksymalnej ilości uplodowanych plików
if(isset($_POST['count'])&&($_POST['count']=="true")) echo ini_get('max_file_uploads');
// Czyszczenie katalogów
if(isset($_POST['clear'])&&($_POST['clear']=="true"))
{
	require_once('../class/helpers.php');
	clearDir('../files_upload');
	clearDir('../miniatures');
}
?>