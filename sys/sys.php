<?php

require_once('class/helpers.php'); // Zbiór funkcji pomocniczych
require_once('class/validation.php'); // Klasa odpowiedzialna za validację
require_once('class/upload.php'); // Klasa odpowiedzialna za upload plików
require_once('class/resize.php'); // Klasa odpowiedzialna za zmianę rozmiarów
require_once('class/zipper.php'); // Klasa odpowiedzialna za zzipowanie plików

// Zbiera informacje podsumowujące
$totalInfo = '';


// Wysłanie formularza
if( isset($_FILES['file']) )
{
	// Wydłużamy limit czasu dla działania skryptu
	ini_set('max_execution_time', 800);
	set_time_limit(600);

	// Klasa sprawdzająca błędy
	$valid = new Validation;

	// Sprawdzenie parametrów	
	$valid->validateParams();

	// Wyświetlenie błędów
	if( !empty($_SESSION['err']) )
	{
		// Renderowanie błędów
		$valid->renderErrors();
	}
	else	// Przetwarzanie plików
	{
		// Prewencyje czyszczenie folderów
		clearDir('files_upload');
		clearDir('miniatures');
		
		// Przekształcenie tablicy z plikami
		$files = filesArray($_FILES['file']);
	
		// Upload plików
		$upload = new Upload($files);
		$result = $upload->upload();
		$totalInfo .= $uploadInfo = $valid->uploadInfo($result,'short',false);

		// Sprawdza czy katalog jest pusty
		if( $valid->emptyDir('files_upload') ) 
		{
			$_SESSION['err']['empty_dir'] = 'Brak plików do przetworzenia.';
			$valid->renderErrors();
			return false;
		}

		// Zmiana rozmiaru plików
		$resize = new Resize;
		$info = $resize->chengeSize();

		if($info === false)
		{
			$_SESSION['err']['resize'] = 'Błąd zmiany rozmiaru.';
			$valid->renderErrors();
			return false;
		}
		else $totalInfo .= '<p>Przetworzone pliki: '.$info.'</p>';

		// Dodanie plików do zipa
		$zipper = new Zipper;
		$info = $zipper->createZip();

		if($info === false)
		{
			$_SESSION['err']['resize'] = 'Błąd pakowania plików.';
			$valid->renderErrors();
			return false;
		}
		else 
		{
			$totalInfo .= '<p>Pliki dodane do archiwum: '.$info.'</p>';
			$totalInfo .= downloadZip();// Link do zipa
		}

		


		

		// Czyszczenie folderów
		clearDir('files_upload');
		clearDir('miniatures');
	}
}




//TODO: 5 - Sprzątanie, usuwanie zipów i plików po wykonaniu wszystkiego
//TODO: 6 - Wygląd, poprawa wyglądu
//TODO: 7 - Wodotryski, wygląd
//TODO: 7a - Po wybraniu jednostki jej symbol pojawia się obok pola Szer./Wys.
//TODO: 7b - Opisy pola po najechaniu na znak "?" w tooltipie
//TODO: 7c - bootstrapowe confirmy
?>