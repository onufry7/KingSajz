<?php

require_once('helpers.php'); // Pomocnicze funkcje
require_once('autoloader.php'); // class autoloader

// Wysłanie formularza
if( isset($_POST['send']) && $_POST['send'] == "true" ) {
	$validation = new Validation;
	$errors = $validation->validateParams();
	if(!empty($errors)) {
		// Przygotowanie danych do zwrotu
		$result['status'] = 'error';
		$result['info'] = $validation->renderErrors($errors);

		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	} else {
		// Prewencyjne czyszczenie katalogów
		clearDir('../files_upload');
		clearDir('../miniatures');

		// Przygotowanie danych do zwrotu
		$result['status'] = 'success';
		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}
}



// Sprawdza folder
if( isset($_POST['checkDir']) && $_POST['checkDir'] == 'true' )
{
	$pathToUploadDir = '../files_upload';

	$result = [
		'fileCount' => 0,
		'status' => 'error',
    	'info' => 'Folder nie istnieje.',
	];

	if (is_dir($pathToUploadDir)) {
		$countFiles = count(glob("$pathToUploadDir/*"));
		$result['fileCount'] = $countFiles;

		if ($countFiles > 0) {
			$result['status'] = 'success';
			$result['info'] = "Liczba plików: $countFiles";
		} else {
			$result['info'] = 'Brak plików w folderze.';
		}
	}

	echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}



// Zmiana rozmiaru plików
if(isset($_POST['resize']) && $_POST['resize'] == 'true' && $_POST['resizeNumber'] >= 0) {
	$resize = new Resize;

	if( $resize->getFiles() ) {
		$info = $resize->changeSize($_POST['resizeNumber']);
		$result['status'] = 'success';
	} else {
		$result['status'] = 'errors';
		$result['info'] = 'Nie udało się zmienić rozmiaru plików.';
	}

	echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}


// Przygotowanie pliku zip
if( isset($_POST['zip']) && $_POST['zip'] == 'true' ) {
	$blad = '';

	$zipper = new Zipper;

	if ( $zipper->createListFiles() ) {
		$info = $zipper->createZip();
	} else {
		$blad = 'Nie udało się stworzyć listy plików.';
	}

	if ( $blad == '' && $info === false) {
		$blad = 'Nie udało się stworzyć pliku zip.';
	}

	if( $blad != '') {
		$result['status'] = 'error';
		$result['info'] = $blad;
	} else {
		$result['status'] = 'success';
		$result['info'] = 'Pliki dodane do archiwum: '.$info;
	}

	echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}


// Pobranie pliku
if (isset($_GET['download']) && $_GET['download'] == 'true') {
    $file = '../miniatures/images.zip';

    if (file_exists($file)) {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="images.zip"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        http_response_code(404);
        echo "Plik nie istnieje!";
    }
}



// Czyszczenie katalogów
if( isset($_POST['clean']) && $_POST['clean'] == "true" ) {
	clearDir('../files_upload');
	clearDir('../miniatures');
}
