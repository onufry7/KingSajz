<?php

require_once('helpers.php'); // Pomocnicze funkcje
require_once('autoloader.php'); // class autoloader

// Wysłanie formularza
if( isset($_POST['send']) && $_POST['send']=="true" )
{    
	$valid = new Validation; // Klasa sprawdzająca błędy
	$errors = $valid->validateParams(); // Sprawdzenie parametrów
	if(!empty($errors))
	{
		// Przygotowanie danych do zwrotu
		$result['status'] = 'error';
		$result['info'] = $valid->renderErrors($errors);

		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}
	else
	{	
		// Prewencyjne czyszczenie katalogów
		clearDir('../files_upload');
		clearDir('../miniatures');

		// Przygotowanie danych do zwrotu
		$result['status'] = 'success';
		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}
}



// Zmiana rozmiaru plików
if( isset($_POST['resize']) && $_POST['resize']=='true' )
{    
	// Sprawdza czy katalog jest pusty
	if( emptyDir('../files_upload') ) 
	{
		$result['status'] = 'error';
		$result['info'] = 'Brak plików w folderze';
		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}
	else
	{
		$blad = ''; // Tekst błędu
		$info = 0; // Liczba zmienionych plików
		
		$resize = new Resize;

		if( $resize->getFiles() ) $info = $resize->chengeSize();
		else $blad = 'Nie można odczytać plików.';

		if( $blad == '' && $info == 0 ) $blad = 'Nie udało się zmienić żadnego pliku.';
		
		if( $blad != '' ) 
		{
			$result['status'] = 'error';
			$result['info'] = $blad;
		}
		else
		{
			$result['status'] = 'success';
			$result['info'] = 'Zmienione pliki: '.$info;
		}

		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}
}



// Przygotowanie pliku zip
if( isset($_POST['zip']) && $_POST['zip']=='true' )
{    
	$blad = '';

	$zipper = new Zipper;

	if( $zipper->createListFiles() ) $info = $zipper->createZip();
	else $blad = 'Nie udało się stworzyć listy plików.';

	if( $blad == '' && $info === false) $blad = 'Nie udało się stworzyć pliku zip.';
	
	if( $blad != '')
	{
		$result['status'] = 'error';
		$result['info'] = $blad;
	}
	else
	{
		$result['status'] = 'success';
		$result['info'] = 'Pliki dodane do archiwum: '.$info;
	}

	echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);	
}



// Czyszczenie katalogów
if( isset($_POST['clean']) && $_POST['clean']=="true" )
{
	clearDir('../files_upload');
	clearDir('../miniatures');
}


?>