<?php


class Upload
{
	private $info = []; // Info przesyłania plików
	private $files; // Tablica z plikami

	public function __construct($files)
	{
		// Zapisujemy tablice files
		$this->files = $this->convertFilesArray($files);
		// Ustawiamy błąd rozszerzenia 
		$this->checkExtensions();
	}



	// Upload plików
	public function uploadFiles()
	{
		for($i=0; $i<count($this->files); $i++)
		{
			$file = $this->files[$i];
			$err = $this->files[$i]['error'];
			$name = $this->files[$i]['name'];
			$tmp_name = $this->files[$i]['tmp_name'];
			$status = 'ERR'; // Domyślny status ustawiony na błąd
			$uploadDir = '../files_upload/'; // Folder uploadu
	
			// Sprawdzamy błędy
			if( $err == 0 )
			{
				// Przesłanie pliku na server
				$result = move_uploaded_file($tmp_name, $uploadDir.$name);
				// Jeśli wystąpił błąd
				if ($result != 1) $err = 10;
				else $status = 'OK';
			}
							
			// Zapisanie info do tablicy zerowanie zmiennej błędu
			$message = $this->getMessage($err);
			$this->info[] = ['name'=>$name,'status'=>$status,'info'=>$message];
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



	// Przekształca tablice plików
	private function convertFilesArray($array)
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



	// Ustawia błąd rozszerzenie pliku
	private function checkExtensions()
	{
		# Dodanie rozszerzenia w tablicy $extensions 
		# wymaga dodania odpowiedniej metody do obsługi
		# tego rozszerzenia w klasie Resize.

		// Dozwolone rozszerzenia
		$extensions = ['jpeg','jpg','png','gif'];

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






	// Podsumowanie uploadu plików TODO Do weryfikacji, uproszczenia refraktoryzacji
	public function uploadInfo($files, $typ = 'all', $info = false)
	{
		$fileError = []; //  Pliki z błędami
		$fileSucces = []; // Pliki bez błędów

		// Przypisanie do typów
		for($i=0;$i<count($files);$i++)
		{
			if($files[$i]['status'] == 'ERR') $fileError[] = $files[$i];
			else if($files[$i]['status'] == 'OK') $fileSucces[] = $files[$i];
		}

		// Liczba poszczególnych typów
		$errCount = count($fileError);
		$okCount = count($fileSucces);
		$allCount = count($files);
		
		// Typ podsumowania
		switch ($typ) 
		{
			// dla odrzuconyvch i błędnych plików
			case 'err':
				$result = '<div class="upload-summary-err">Pliki odrzucone: ';
				$result .= $errCount.'<ul>';

				if($errCount > 0)
				{
					foreach ($fileError as $key => $value) 
						$result .= '<li>'.$value['file'].' => '.$value['info'].'</li>';
				}

				$result .= '</ul><div>';
				break;


			// Dla poprawnie przetworzonych 	
			case 'ok':
				$result = '<div class="upload-summary-ok">Pliki poprawne: ';
				$result .= $okCount.'<ul>';

				if($okCount > 0)
				{
					foreach ($fileSucces as $key => $value) 
						$result .= '<li>'.$value['file'].' => '.$value['info'].'</li>';
				}

				$result .= '</ul><div>';
				break;


			// Krótkie podsumowanie o przeslanych i odrzuconych razem
			case 'short':
				$result = '<div class="upload-summary-short"><ul>';
				$result .= '<li>Pliki przesłane: '.$allCount.'</li>';
				$result .= '<li>Pliki odrzucone: '.$errCount.'</li>';
				$result .= '<li>Pliki poprawne: '.$okCount.'</li>';
				$result .= '</ul></div>';
				return  $result; // Odrazu zwracamy wynik
				break;
			

			default:
				$result = '<div class="upload-summary-all">Przesłane pliki: ';
				$result .= $allCount.'<ul>';
				
				if($allCount > 0)
				{
					foreach ($files as $key => $value) 
						$result .= '<li>'.$value['file'].' => '.$value['info'].'</li>';
				}

				$result .= '</ul><div>';
				break;
		}


		// Dodatkowe podsumowanie
		if($info == true)
		{
			$summaryEx = '<div class="upload-summary-extra"><ul>';
			$summaryEx .= '<li>Wszystkich plików: '.$allCount.'</li>';
			$summaryEx .= '<li>Poprawnych plików: '.$okCount.'</li>';	
			$summaryEx .= '<li>Plików z błędami: '.$errCount.'</li>';
			$summaryEx .= '</ul></div>';
		}

		// Wynik
		$summary = '<div  class="upload-summary">';
		$summary .= $result;
		if(isset($summaryEx)) $summary .= $summaryEx;
		$summary .= '</div>';

		return $summary;
	}


}



?>