<?php


class Zipper extends ZipArchive
{
	private $listFiles = []; // Lista plików
	private $srcIn = '../miniatures'; // Ścieszka odczytu
	private $srcOut = '../miniatures'; // Ścieszka zapisu


	// Tworzy listę plików
	public function createListFiles()
	{
		if( is_dir($this->srcIn) )
		{	// Skanujemy folder w poszukiwaniu plików
			foreach (glob("$this->srcIn/*") as $file)
			{	
				// i zapisujemy pliki do tablicy
				if( is_file($file) ) $this->listFiles[] = basename($file);
			}
			return true;
		}
		return false;
	}



	// Tworzenie pliku zip
	public function createZip()
	{
		if($this->open("$this->srcOut/your-images.zip", ZipArchive::CREATE) === TRUE)
		{
			foreach($this->listFiles as $file)
			{
				$this->addFile("$this->srcIn/$file", $file);
			}
			$numFiles = $this->numFiles;
			$this->close();
		    return $numFiles;
		}
		else return false;
	}

}


?>
