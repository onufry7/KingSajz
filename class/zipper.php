<?php

class Zipper extends ZipArchive
{
	private $listFiles = []; // Lista plików
	private $srcIn = '../miniatures'; // Ścieszka odczytu
	private $srcOut = '../miniatures'; // Ścieszka zapisu


	// Tworzy listę plików
	public function createListFiles()
	{
		// Skanujemy folder w poszukiwaniu plików
		// i zapisujemy pliki do tablicy
		if( is_dir($this->srcIn) ) {
			foreach (glob($this->srcIn.'/*') as $file) {
				if ( is_file($file) ) {
					$this->listFiles[] = basename($file);
				}
			}
			return true;
		}
		return false;
	}



	// Tworzenie pliku zip
	public function createZip()
	{
		if ($this->open("$this->srcOut/images.zip", ZipArchive::CREATE) === true) {
			foreach($this->listFiles as $file) {
				$this->addFile("$this->srcIn/$file", $file);
			}
			$numFiles = $this->numFiles;
			$this->close();
		    return $numFiles;
		} else {
			return false;
		}
	}

}

