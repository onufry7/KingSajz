<?php


class Validation
{
	// Podsumowanie uploadu plików
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


	// Walidacja  parametrów
	public function validateParams()
	{
		$file = $scale = $height = $width = $unit = null;

		if(isset($_POST['submit']))
		{
			if( !empty($_POST['scale']) ) $scale = $_POST['scale'];
			if( !empty($_POST['height']) ) $height = (int)$_POST['height'];
			if( !empty($_POST['width']) ) $width = (int)$_POST['width'];
			if( !empty($_POST['unit']) ) $unit = $_POST['unit'];
			if( !empty($_POST['count']) ) $file = $_POST['count'];
		}

		// Walidacja jednostki
		switch ($unit) 
		{
			case 'px':
			case 'cm':
			case 'mm':
			case 'percent':
				break;
			default: $_SESSION['err']['unit'] = 'Nie wybrano jednostki!'; break;
		}

		// Walidacja skali
		switch ($scale) 
		{
			case 'none': 
				break;
			case 'width': 
				$width = 'false'; 
				unset($_POST['width']);
				break;
			case 'height': 
				$height = 'false'; 
				unset($_POST['height']);
				break;
			default: $_SESSION['err']['scale'] = 'Nie wybrano skali!'; break;		
		}

		// Walidacja szerokości
		if($width != 'false')
		{
			// Jeśli NULL
			if( is_null($width) ) 
				$_SESSION['err']['width'] = 'Nie podano szerokość!';

			// Jeśli procenty
			if( $unit=='percent' &&  !(0 < $width && $width <= 200) )
				$_SESSION['err']['width'] = 'Nieprawidłowy zakres szerokości!';

			// Jeśli centymetry
			if( $unit=='cm' &&  !(0 < $width && $width <= 100) )
				$_SESSION['err']['width'] = 'Nieprawidłowy zakres szerokości!';

			// Jeśli milimetry
			if( $unit=='mm' &&  !(0 < $width && $width <= 1000) )
				$_SESSION['err']['width'] = 'Nieprawidłowy zakres szerokości!';

			// Jeśli piksele
			if( $unit=='px' &&  !(0 < $width && $width <= 3500) )
				$_SESSION['err']['width'] = 'Nieprawidłowy zakres szerokości!';
		}

		// Walidacja wysokości
		if($height != 'false')
		{
			// Jeśli NULL
			if( is_null($height) ) 
				$_SESSION['err']['height'] = 'Nie Podano wysokość!';

			// Jeśli procenty
			if( $unit=='percent' &&  !(0 < $height && $height <= 200) )
				$_SESSION['err']['height'] = 'Nieprawidłowy zakres wysokości!';

			// Jeśli centymetry
			if( $unit=='cm' &&  !(0 < $height && $height <= 100) )
				$_SESSION['err']['height'] = 'Nieprawidłowy zakres wysokości!';

			// Jeśli milimetry
			if( $unit=='mm' &&  !(0 < $height && $height <= 1000) )
				$_SESSION['err']['height'] = 'Nieprawidłowy zakres wysokości!';

			// Jeśli piksele
			if( $unit=='px' &&  !(0 < $height && $height <= 3500) )
				$_SESSION['err']['height'] = 'Nieprawidłowy zakres wysokości!';
		}

		// Walidacja liczby przesłanych plików
		if( $file == null || $file < '1')
			$_SESSION['err']['file'] = 'Nie wybrano pliku!';

		// Wartość z pliku ini.php
		$maxFiles = ini_get('max_file_uploads'); 

		// Przesłano za duzo plików
		if( $file >  $maxFiles ) 
			$_SESSION['err']['fileCount'] = 'Wybrano za dużo plików!';
	}


	// Renderowanie błędów dla parametrów
	public function renderErrors()
	{
		if( isset($_SESSION['err']) )
		{
			$result = '<ul class="errors">';

			foreach($_SESSION['err'] as $key => $value)
			{
				$result .= '<li>'.$value.'</li>';	
			} 
			
			$result .= '</ul>';
		
			unset($_SESSION['err']); // Usówa błędy z pamięci sesji

			echo $result;
		}	
	}


	// Sprawdza czy folder jest pusty
	public function emptyDir($src)
	{
		if( count(glob("$src/*")) === 0 ) return true;
		else return false;
	}

	// Sprawdza liczbę przesłanych plików
	public function checkCountFiles($count)
	{	
		// Wartość z pliku ini.php
		$maxFiles = ini_get('max_file_uploads'); 

		// Jeśli przesłano za durzo plików to false
		if( $count >  $maxFiles ) return false;
		else return true;
	}

}

?>