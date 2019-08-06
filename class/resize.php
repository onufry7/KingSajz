<?php

/**
 * Klasa do zmiany wielkości pliku
 */
class Resize
{
	private $srcIn = '../files_upload'; // Ścieszka odczytu
	private $srcOut = '../miniatures'; // Ścieszka zapisu
	private $scale = null; // Co skalujemy
	private $unit = null; // W jakich jednostkach
	private $height = null; // Do jakiej wysokości
	private $width = null; // Do jakiej szerokości
	private $files = null; // Lista plików

	public function __construct()
	{
		// Inicjuje zmienne //TODO NIe czyta $_POST

		if( isset($_POST['scale']) && !empty($_POST['scale']) ) 
			$this->scale = $_POST['scale'];
		
		if( isset($_POST['height']) && !empty($_POST['height']) ) 
			$this->height = $_POST['height'];
		
		if( isset($_POST['width']) && !empty($_POST['width']) ) 
			$this->width = $_POST['width'];
		
		if( isset($_POST['unit']) && !empty($_POST['unit']) ) 
			$this->unit = $_POST['unit'];
	}



	// Tworzy listę plików z informacjami o nich
	public function getFiles()
	{ 
		if( is_dir($this->srcIn) )
		{	// Skanujemy folder w poszukiwaniu plików
			foreach (glob("$this->srcIn/*") as $file)
			{	
				// i zapisujemy pliki do tablicy
				if( is_file($file) ) $this->files[] = $file;
			}
			return true;
		}
		else return false; // Jeśli odwołamy się do czegoś innego niż folder
	}



	// Zmienia rozmiar pliku
	public function chengeSize()
	{
		$i = 0; // Licznik plików
		// Pętla po plikach z tablicy
		foreach($this->files as $file)
		{
			if( is_file($file) )
			{
				// Pobieramy info o pliku
				$info = pathinfo($file);
				$filename = $info['filename'];
				$extension = $info['extension'];

				// Flaga dla błędów
				$skip = false;

				// Tworzymy obraz odpowiedniego typu
				switch ($extension) {
					
					case 'jpeg':
					case 'jpg': $img = imagecreatefromjpeg($file);
						break;
					
					case 'png': $img = imagecreatefrompng ($file);
						break;
					
					case 'gif': $img = imagecreatefromgif($file);
					 	break;
					
					default: $skip = true;
						break;
				}

				// Jeśli plik jest błędny to go pomija
				if($skip == true) continue;


				//Pobranie orginalnych rozmiarów
				$oldWidth = imagesx($img);
				$oldHeight = imagesy($img);

				
				// Ustalanie i przeliczanie jednostek
				switch ($this->unit) 
				{
					case 'percent':
						// Szerokość
						$width = ( is_null($this->width) ) ? $this->height : $this->width;
						$width = round( ($oldWidth*$width)/100 );
						// Wysokość
						$height = ( is_null($this->height) ) ? $this->width : $this->height;
						$height = round( ($oldHeight*$height)/100 );
						break;

					case 'mm':
						// Pobiera dpi obrazu (x i y)
						list($dpix, $dpiy) = imageresolution($img); 
						// Szerokość
						if( is_null($this->width) ) $width = round($oldWidth);
						else $width = round( ($dpix/2.54)*($this->width/10) );
						// Wysokość
						if( is_null($this->height) ) $height = round($oldHeight);
						else $height = round( ($dpiy/2.54)*($this->height/10) );
						break;

					case 'cm':
						// Pobiera dpi obrazu (x i y)
						list($dpix, $dpiy) = imageresolution($img); 
						// Szerokość
						if( is_null($this->width) ) $width = round($oldWidth);
						else $width = round( ($dpix/2.54)*$this->width );
						// Wysokość
						if( !is_null($this->height) ) $height = round($oldHeight);
						else $height = round( ($dpiy/2.54)*$this->height );
						break;

					case 'px':
					default:
						// Szerokość
						$width = ( is_null($this->width) ) ? $oldWidth : $this->width;
						$width = round($width);
						// Wysokość
						$height = ( is_null($this->height) ) ? $oldHeight : $this->height;
						$height = round($height);
						break;
				}


				// Ttworzymy "pusty" obraz
				$newImg = imagecreatetruecolor($width, $height);

				// Kopiujemy orginalny obraz do "pustego" obrazu
				imagecopyresampled($newImg, $img, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);

 
 				// Przygotowanie ścieżki zapisu
				$srcOut = $this->srcOut.'/'.$filename.'.'.$extension;

				// Zapis nowego obrazu
				switch ($extension) 
				{
					case 'jpg':
					case 'jpeg': imagejpeg($newImg, $srcOut, 80);
						break;
					
					case 'gif': imagegif($newImg, $srcOut);
						break;
					
					case 'png':
					default: imagepng($newImg, $srcOut);
						break;
				}

				// Czyszczenie
				imagedestroy($img);
				imagedestroy($newImg);	

				$i++; // Zliczanie plików	
			}
		}
		return $i;
	}



}

?>