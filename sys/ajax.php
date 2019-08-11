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



// Sprawdza folder
if( isset($_POST['checkdir']) && $_POST['checkdir']=='true' )
{
	// Sprawdza czy katalog jest pusty
	if( emptyDir('../files_upload') ) 
	{
		$status = 'error';
		$info = 'Brak plików w folderze';
	}
	// Zwracamy liczbę plików z katalogu
	else 
	{
		$status = 'success';
		$countFiles = count(glob('../files_upload/*'));
		$info = $countFiles;
	}

	//Zwracamy wyniki
	$result['status'] = $status;
	$result['info'] = $info;
	echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}



// Zmiana rozmiaru plików
if(isset($_POST['resize']) && $_POST['resize']=='true' && !empty($_POST['resizeNo']))
{	
	$resize = new Resize;
	if( $resize->getFiles() )
	{
		$info = $resize->chengeSize($_POST['resizeNo']);
		$result['status'] = 'success';
	} 
	else
	{
		$result['status'] = 'errors';
		$result['info'] = 'Nie udało się zmienić rozmiaru plikó.';
	} 

	echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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