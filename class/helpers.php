<?php


// Przekształca tablice plików do postaci:
// array( file1(params-file1), ..., fileN(params-fileN) )
function filesArray($array)
{
	$filesArray = [];

	foreach($array as $key => $value) 
	{
		foreach($value as $k => $v)
		{
			$filesArray[$k][$key] = $v;
		}
	}
	return $filesArray;
}


// Czyszczenie katalogów
function clearDir($path) 
{
	$dir = new DirectoryIterator($path);
	
	foreach ($dir as $fileinfo) 
	{
		if ( $fileinfo->isFile() || $fileinfo->isLink() )
		{
			unlink($fileinfo->getPathName());
		} 		
		elseif ( !$fileinfo->isDot() && $fileinfo->isDir() ) 
		{
			rmdir( $fileinfo->getPathName() );
		}
	}
	
}



// Zachowuje wartości z pól formularza
function keep($f)
{
	$i = count($f);
	$value = '';

	if($i == 1)
	{
		$value = ( isset($_POST[$f[0]]) ) ? htmlentities($_POST[$f[0]]) : "";
	}
	else if($i > 1)
	{
		$value = ( isset($_POST[$f[0]]) &&  $_POST[$f[0]] == $f[1] ) ? 'checked' : "";
	}
	return $value;
}



// link do pliku zip
function downloadZip()
{
	return '<a href="miniatures/your-images.zip" download >images.zip</a>';
}






?>