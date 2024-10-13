<?php

require_once('autoloader.php');

// Wysłanie plików
if( isset($_FILES['files']) )
{
	$upload = new Upload($_FILES['files']);
	$upload->uploadFiles();
	echo json_encode($upload->uploadInfo(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
