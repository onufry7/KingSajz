<?php

require_once('autoloader.php');

// Wysłanie plików
if( isset($_FILES['files']) )
{
	$upload = new Upload($_FILES['files']);
	$info = $upload->uploadFiles();
	$info = $upload->uploadInfo($info,'short',false);
	echo $info;
}
