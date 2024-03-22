<?php


class Validation
{
	// Walidacja  parametrów
	public function validateParams()
	{
		$errors = []; // Tablica z błędami
		$size = $count = $scale = $height = $width = $unit = null;

		// Przypisanie zmiennych
		if (!empty($_POST['scale'])) { $scale = $_POST['scale']; }
		if (!empty($_POST['height'])) { $height = (int)$_POST['height']; }
		if (!empty($_POST['width'])) { $width = (int)$_POST['width']; }
		if (!empty($_POST['unit'])) { $unit = $_POST['unit']; }
		if (!empty($_POST['count'])) { $count = $_POST['count']; }
		if (!empty($_POST['size'])) { $size = ($_POST['size']/(1024*1024)); }

		// Walidacja jednostki
		switch ($unit)
		{
			case 'px':
			case 'cm':
			case 'mm':
			case 'percent':
				break;
			default: $errors['unit'] = 'Wybierz jednostkę !'; break;
		}

		// Walidacja skali
		switch ($scale)
		{
			case 'none':
				break;
			case 'width':
			case 's-width':
				$width = 'false';
				unset($_POST['width']);
				break;
			case 'height':
			case 's-height':
				$height = 'false';
				unset($_POST['height']);
				break;
			default: $errors['scale'] = 'Wybierz skale !'; break;
		}

		// Walidacja szerokości
		if($width != 'false')
		{
			if( is_null($width) ) { // Jeśli NULL
				$errors['width'] = 'Podaj szerokość !';
			} elseif( $unit=='percent' &&  !(0 < $width && $width <= 200) ) { // Jeśli procenty
				$errors['width'] = 'Popraw szerokość ! (max 200)';
			} elseif( $unit=='cm' &&  !(0 < $width && $width <= 100) ) { // Jeśli centymetry
				$errors['width'] = 'Popraw szerokość ! (max 100)';
			} elseif( $unit=='mm' &&  !(0 < $width && $width <= 1000) ) { // Jeśli milimetry
				$errors['width'] = 'Popraw szerokość ! (max 1000)';
			} elseif( $unit=='px' &&  !(0 < $width && $width <= 3500) ) { // Jeśli piksele
				$errors['width'] = 'Popraw szerokość ! (max 3500)';
			}
		}

		// Walidacja wysokości
		if($height != 'false')
		{
			if ( is_null($height) ) { // Jeśli NULL
				$errors['height'] = 'Podaj wysokość !';
			} elseif( $unit=='percent' &&  !(0 < $height && $height <= 200) ) { // Jeśli procenty
				$errors['height'] = 'Popraw wysokość ! (max 200)';
			} elseif( $unit=='cm' &&  !(0 < $height && $height <= 100) ) { // Jeśli centymetry
				$errors['height'] = 'Popraw wysokość ! (max 100)';
			} elseif( $unit=='mm' &&  !(0 < $height && $height <= 1000) ) { // Jeśli milimetry
				$errors['height'] = 'Popraw wysokość ! (max 1000)';
			} elseif( $unit=='px' &&  !(0 < $height && $height <= 3500) ) { // Jeśli piksele
				$errors['height'] = 'Popraw wysokość ! (max 3500)';
			}
		}

		// Walidacja liczby przesłanych plików

		$maxFiles = ini_get('max_file_uploads'); // Wartość z pliku ini.php

		if ( $count >  $maxFiles ) { // Jeśli plików za duzo
			$errors['count'] = 'Wybrano za dużo plików !';
		} else if( $count == null || $count < '1') { // Jeśli plików za mało
			$errors['count'] = 'Wybierz pliki !';
		} elseif( $size != null && $size > (int)ini_get('post_max_size') ) { // Jeśli przekroczono post_max_size
			$errors['size'] = 'Rozmiar plików jest za duży ! (max '.(int)ini_get('post_max_size').' MB)';
		}

		// Zwracamy tablice z błędami
		return $errors;
	}




	// Renderowanie błędów dla parametrów
	// public function renderErrors($errors)
	// {
		// $result = '<ul>';

		// foreach($errors as $error)
		// {
			// $result .= '<li>'.$error.'</li>';
		// }

		// $result .= '</ul>';

		// return $result;
	// }

	// Renderowanie błędów dla parametrów
	public function renderErrors($errors)
	{
		$size = "";
		$scale = "";
		$unit = "";
		$files = "";

		foreach($errors as $type => $error) {
			if ($type == 'height' || $type == 'width') {
				$size .= '<li>'.$error.'</li>';
			} elseif ($type == 'count' || $type == 'size') {
				$files .= '<li>'.$error.'</li>';
			} elseif ($type == 'unit') {
				$unit .= '<li>'.$error.'</li>';
			} elseif ($type == 'scale') {
				$scale .= '<li>'.$error.'</li>';
			}
		}

		if (!empty($size)) {
			$result['size'] = '<ul>'.$size.'</ul>';
		}
		if (!empty($scale)) {
			$result['scale'] = '<ul>'.$scale.'</ul>';
		}
		if (!empty($unit)) {
			$result['unit'] = '<ul>'.$unit.'</ul>';
		}
		if (!empty($files)) {
			$result['files'] = '<ul>'.$files.'</ul>';
		}

		return $result;
	}

}
