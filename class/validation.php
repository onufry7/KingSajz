<?php


class Validation
{
	// Walidacja  parametrów
	public function validateParams()
	{
		$errors = []; // Tablica z błędami
		$size = $count = $scale = $height = $width = $unit = null;

		// Przypisanie zmiennych
		if( !empty($_POST['scale']) ) $scale = $_POST['scale'];
		if( !empty($_POST['height']) ) $height = (int)$_POST['height'];
		if( !empty($_POST['width']) ) $width = (int)$_POST['width'];
		if( !empty($_POST['unit']) ) $unit = $_POST['unit'];
		if( !empty($_POST['count']) ) $count = $_POST['count'];
		if( !empty($_POST['size']) ) $size = ($_POST['size']/(1024*1024));

		// Walidacja jednostki
		switch ($unit) 
		{
			case 'px':
			case 'cm':
			case 'mm':
			case 'percent':
				break;
			default: $errors['unit'] = 'Nie wybrano jednostki!'; break;
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
			default: $errors['scale'] = 'Nie wybrano skali!'; break;		
		}

		// Walidacja szerokości
		if($width != 'false')
		{
			// Jeśli NULL
			if( is_null($width) ) 
				$errors['width'] = 'Nie podano szerokość!';

			// Jeśli procenty
			if( $unit=='percent' &&  !(0 < $width && $width <= 200) )
				$errors['width'] = 'Nieprawidłowy zakres szerokości!';

			// Jeśli centymetry
			if( $unit=='cm' &&  !(0 < $width && $width <= 100) )
				$errors['width'] = 'Nieprawidłowy zakres szerokości!';

			// Jeśli milimetry
			if( $unit=='mm' &&  !(0 < $width && $width <= 1000) )
				$errors['width'] = 'Nieprawidłowy zakres szerokości!';

			// Jeśli piksele
			if( $unit=='px' &&  !(0 < $width && $width <= 3500) )
				$errors['width'] = 'Nieprawidłowy zakres szerokości!';
		}

		// Walidacja wysokości
		if($height != 'false')
		{
			// Jeśli NULL
			if( is_null($height) ) 
				$errors['height'] = 'Nie Podano wysokość!';

			// Jeśli procenty
			if( $unit=='percent' &&  !(0 < $height && $height <= 200) )
				$errors['height'] = 'Nieprawidłowy zakres wysokości!';

			// Jeśli centymetry
			if( $unit=='cm' &&  !(0 < $height && $height <= 100) )
				$errors['height'] = 'Nieprawidłowy zakres wysokości!';

			// Jeśli milimetry
			if( $unit=='mm' &&  !(0 < $height && $height <= 1000) )
				$errors['height'] = 'Nieprawidłowy zakres wysokości!';

			// Jeśli piksele
			if( $unit=='px' &&  !(0 < $height && $height <= 3500) )
				$errors['height'] = 'Nieprawidłowy zakres wysokości!';
		}

		// Walidacja liczby przesłanych plików

		$maxFiles = ini_get('max_file_uploads'); // Wartość z pliku ini.php

		// Jeśli plików za duzo 
		if( $count >  $maxFiles ) 
			$errors['count'] = 'Wybrano za dużo plików!';
		
		// Jeśli plików za mało
		if( $count == null || $count < '1')
			$errors['count'] = 'Nie wybrano pliku!';	


		// Jeśli przekroczono post_max_size
		if( $size != null && $size > (int)ini_get('post_max_size') ) 
			$errors['size'] = 'Rozmiar plików jest za duży!';

		// Zwracamy tablice z błędami
		return $errors;
	}




	// Renderowanie błędów dla parametrów
	public function renderErrors($errors)
	{
		$result = '<ul>';

		foreach($errors as $error)
		{
			$result .= '<li>'.$error.'</li>';	
		} 
		
		$result .= '</ul>';

		return $result;
	}

}

?>