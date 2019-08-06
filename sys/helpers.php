<?php

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


// Sprawdza czy folder jest pusty
function emptyDir($src)
{
	if( count(glob("$src/*")) === 0 ) return true;
	else return false;
}

?>