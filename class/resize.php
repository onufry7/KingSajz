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
		// Inicjuje zmienne

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
		else return false; // Jeśli inne niż folder
	}



	// Zmienia rozmiar pliku
	public function changeSize($no)
	{
		$no = $no-1;
		$file = $this->files[$no];

		if( is_file($file) )
		{
			// Pobieramy info o pliku
			$info = pathinfo($file);
			$filename = $info['filename'];
			$extension = $info['extension'];

			// Flaga dla błędów
			$skip = false;

			// Tworzymy obraz odpowiedniego typu
			switch ($extension)
			{

				case 'jpeg':
				case 'jpg': $img = imagecreatefromjpeg($file);
					break;

				case 'png': $img = imagecreatefrompng($file);
					break;

				case 'gif': $img = imagecreatefromgif($file);
				 	break;

				default: $skip = true;
					break;
			}

			// Jeśli plik jest błędny to go pomija
			if($skip == true) return false;


			//Pobranie oryginalnych rozmiarów
			$oldWidth = imagesx($img);
			$oldHeight = imagesy($img);


			// Ustalanie i przeliczanie jednostek
			switch ($this->unit)
			{
				case 'percent':

					// Szerokość
					if( !is_null($this->width) ) $width = round( ($oldWidth*$this->width)/100 );
					else if( $this->scale == "width" ) $width = round($oldWidth);
					else if( $this->scale == "s-width" ) $width = round( ($oldWidth*$this->height)/100 );

					// Wysokość
					if( !is_null($this->height) ) $height = round( ($oldHeight*$this->height)/100 );
					else if( $this->scale == "height" ) $height = round($oldHeight);
					else if( $this->scale == "s-height" ) $height = round( ($oldHeight*$this->width)/100 );

					break;



				case 'mm':

					// Pobiera dpi obrazu (x i y)
					list($dpix, $dpiy) = imageresolution($img);

					// Szerokość
					if( !is_null($this->width) ) $width = round( ($dpix/2.54)*($this->width/10) );
					else if( $this->scale == "width" ) $width = round($oldWidth);
					else if( $this->scale == "s-width" )
					{
						$skala = ($this->height*100)/$oldHeight;
						$width = round( ((($dpix/2.54)*($oldWidth/10) )*$skala)/100 );
					}

					// Wysokość
					if( !is_null($this->height) ) $height = round( ($dpiy/2.54)*($this->height/10) );
					else if( $this->scale == "height" ) $height = round($oldHeight);
					else if( $this->scale == "s-height" )
					{
						$skala = ($this->width*100)/$oldWidth;
						$height = round( ((($dpiy/2.54)*($oldHeight/10))*$skala)/100 );
					}

					break;



				case 'cm':

					// Pobiera dpi obrazu (x i y)
					list($dpix, $dpiy) = imageresolution($img);

					// Szerokość
					if( !is_null($this->width) ) $width = round( ($dpix/2.54)*$this->width );
					else if( $this->scale == "width" ) $width = round($oldWidth);
					else if( $this->scale == "s-width" )
					{
						$skala = ($this->height*100)/$oldHeight;
						$width = round( ((($dpix/2.54)*$oldWidth)*$skala)/100 );
					}

					// Wysokość
					if( !is_null($this->height) ) $height = round( ($dpiy/2.54)*$this->height );
					else if( $this->scale == "height" ) $height = round($oldHeight);
					else if( $this->scale == "s-height" )
					{
						$skala = ($this->width*100)/$oldWidth;
						$height = round( ((($dpiy/2.54)*$oldHeight)*$skala)/100 );
					}

					break;



				case 'px':
				default:

					// Szerokość
					if( !is_null($this->width) ) $width = round($this->width);
					else if( $this->scale == "width" ) $width = round($oldWidth);
					else if( $this->scale == "s-width" )
					{
						$skala = ($this->height*100)/$oldHeight;
						$width = round( ($oldWidth*$skala)/100 );
					}

					// Wysokość
					if( !is_null($this->height) ) $height = round($this->height);
					else if( $this->scale == "height" ) $height = round($oldHeight);
					else if( $this->scale == "s-height" )
					{
						$skala = ($this->width*100)/$oldWidth;
						$height = round( ($oldHeight*$skala)/100 );
					}

					break;
			}


			// Ttworzymy "pusty" obraz
			$newImg = imagecreatetruecolor($width, $height);

			// Przezroczyste tło w PNG
			$transparent = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
			imagefill($newImg, 0, 0, $transparent);
			imagesavealpha($newImg, true);


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

			return true;
		}
		return false;
	}

}

?>