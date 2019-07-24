<?php


class Upload
{
	private $tooMuch = false; // Czy zadużo plików
	private $info = []; // Info przesyłania plików
	private $files; // Tablica z plikami
	private $extensions = ['jpeg','jpg','png','gif']; // Dozwolone rozszerzenia

	public function __construct($files)
	{
		// Zapisujemy tablice files
		$this->files = $files;

		// Ustawiamy błąd rozszerzenia
		$this->checkExtensions();
	}


	// Ustawia błąd rozszerzenie pliku
	private function checkExtensions()
	{
		// Dozwolone rozszerzenia
		$extensions = $this->extensions;

		# Dodanie rozszerzenia w tablicy $extensions 
		# wymaga dodania odpowiedniej metody do obsługi
		# tego rozszerzenia w klasie Resize.

		// Sprawdzamy rozszerzenie pliku
		for($i=0; $i<count($this->files); $i++)
		{
			// Pobieramy rozszerzenie z nazwy pliku
			$fileExt = explode('.', $this->files[$i]['name']);
			$fileExt = end($fileExt);
			// Jeśli nie ma na liście to error na 9
			if( !in_array($fileExt,$extensions) ) $this->files[$i]['error'] = 9;
		}
	}



	// Upload plików
	public function upload()
	{
		for($i=0; $i<count($this->files); $i++)
		{
			$err = $this->files[$i]['error'];
			$file = $this->files[$i]['name'];
			$status = 'ERR'; // Domyślny status ustawiony na błąd
			
			// Sprawdzamy błędy
			if( $err == 0 )
			{
				// Przesłanie pliku na server
				$result = $this->uploadFile($i);
				// Jeśli wystąpił błąd
				if ($result != 1) $err = 10;
				else $status = 'OK';
			}
							
			// Zapisanie info do tablicy zerowanie zmiennej błędu
			$message = $this->getMessage($err);
			$this->info[] = ['file'=>$file,'status'=>$status,'info'=>$message];
			unset($message);			
		} 
		return $this->info;
	}
	

	// Możliwe błędy przesyłania plików
	private function getMessage($nr)
	{
		$uploadError[0] = 'Przesyłanie zakończone sukcesem.';
		$uploadError[1] = 'Plik jest większy niż pozwalają ustawienia pliku ini.';
		$uploadError[2] = 'Plik jest większy niż pozwalają ustawienia formularza.';
		$uploadError[3] = 'Plik został przesłany częściow.';
		$uploadError[4] = 'Nie przesłano żadnego pliku.';
		$uploadError[6] = 'Brak folderu tymczasowego.';
		$uploadError[7] = 'Nie udało się zapisać pliku na dysku.';
		$uploadError[8] = 'Rozszerzenie PHP zatrzymało przesyłanie plików.';
		$uploadError[9] = 'Nieprawidłowe rozszerzenie pliku.';
		$uploadError[10] = 'Nie udało się przenieść pliku.';

		return $uploadError[$nr];
	}



	// Upload pliku
	private function uploadFile($nr)
	{
		$uploadDir = './files_upload/';
		$file = $this->files[$nr];
		$name = basename($file['name']);	

		$result = move_uploaded_file($file['tmp_name'], $uploadDir.$name);

		return $result;
	}
	
}



?>