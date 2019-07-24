<?php


class Zipper extends ZipArchive
{
	private $listFiles = []; // Lista plików
	private $srcIn = 'miniatures'; // Ścieszka odczytu
	private $srcOut = 'miniatures'; // Ścieszka zapisu


	public function __construct()
	{
		// Tworzy listę plików
		$this->createListFiles();
	}



	// Tworzy listę plików
	private function createListFiles()
	{
		if( is_dir($this->srcIn) )
		{	// Skanujemy folder w poszukiwaniu plików
			foreach (glob("$this->srcIn/*") as $file)
			{	
				// i zapisujemy pliki do tablicy
				if( is_file($file) ) $this->listFiles[] = basename($file);
			}
		}
		else    // Jeśli odwołamy się do czegoś innego niż folder
		{	
			echo 'Folder źródłowy nie jest katalogiem.';
			return false;
		}
	}





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
