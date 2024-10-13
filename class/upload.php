<?php


class Upload
{
	private $files;
	private $info = []; // Info z przesyłania plików
	private $allowExtensions = ['jpeg', 'jpg', 'png', 'gif'];
	private $fileUploadPath = '../files_upload/';

	public function __construct($files)
	{
		$this->files = $this->convertFilesArray($files);
		$this->checkExtensions();
	}



	// Upload plików
	public function uploadFiles()
	{
		foreach($this->files as $file)
		{
			$err = $file['error'];
			$name = $file['name'];
			$tmp_name = $file['tmp_name'];
			$status = 'ERR';

			// Sprawdzamy błędy
			if ( $err == 0 && move_uploaded_file($tmp_name, $this->fileUploadPath.$name)) {
				$status = 'OK';
			} else {
				$err = 10;
			}

			// Zapisanie info do tablicy zerowanie zmiennej błędu
			$message = $this->getMessage($err);
			$this->info[] = ['name'=>$name,'status'=>$status,'info'=>$message];
			unset($message);
		}
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
		foreach($array as $key => $value) {
			foreach($value as $k => $v) {
				$filesArray[$k][$key] = $v;
			}
		}
		return $filesArray;
	}



	// Ustawia błąd rozszerzenie pliku
	private function checkExtensions()
	{
		# Dodanie rozszerzenia w tablicy $this->allowExtensions
		# wymaga dodania odpowiedniej metody do obsługi
		# tego rozszerzenia w klasie Resize.

		foreach ($this->files as &$file) {
			$fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

			if (!in_array($fileExt, $this->allowExtensions)) {
				$file['error'] = 9;
			}
		}
	}


	public function uploadInfo()
	{
		$result['all']['count'] = 0;
		$result['errors']['count'] = 0;
		$result['errors']['files'] = [];
		$result['success']['count'] = 0;
		$result['success']['files'] = [];

		foreach($this->info as $fileInfo)
		{
			if($fileInfo['status'] == 'ERR') {

				$result['errors']['files'][] = $fileInfo['name'];
				$result['errors']['count']++;

			} elseif ($fileInfo['status'] == 'OK') {

				$result['success']['files'][] = $fileInfo['name'];
				$result['success']['count']++;

			}
		}

		$result['all']['count'] = $result['errors']['count'] + $result['success']['count'];

		return $result;
	}

}
