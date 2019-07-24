<?php session_start(); require_once('sys/sys.php'); ?>

<!doctype html>
<html lang="pl-PL">
	<head>
		<meta charset="utf-8">
		<meta name="description" content="">
		<meta name="keywords" content="">	
		<link rel="stylesheet" href="css/style.css">
		<script src="js/jquery-3.4.1.min.js"></script>
		<script async src="js/skrypt.js"></script>
		<title>KingSajz</title>
	</head>

	<body>

		<header>
			<h1>KING<span>Sajz</span></h1>
		</header>

		<main>

			<form enctype="multipart/form-data" action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST" id="form">
				
				<input type="hidden" name="count" id="count" value="">

				<div class="fild">
					Jednostka: 
					<label><input type="radio" name="unit" value="percent" <?=keep(['unit','percent']);?> > %</label>
					<label><input type="radio" name="unit" value="px" checked <?=keep(['unit','px']);?> > px</label>
					<label><input type="radio" name="unit" value="cm" <?=keep(['unit','cm']);?> > cm</label>
					<label><input type="radio" name="unit" value="mm" <?=keep(['unit','mm']);?> > mm</label>
				</div>
				
				<div class="fild">
					Skala: 
					<label><input type="radio" name="scale" value="none" checked <?=keep(['scale','none']);?> > Brak</label>
					<label><input type="radio" name="scale" value="width" <?=keep(['scale','width']);?> > Zachowaj szerokość</label>
					<label><input type="radio" name="scale" value="height" <?=keep(['scale','height']);?> > Zachowaj wysokość</label>
				</div>

				<div class="fild" id="szerokosc">
					<label for="szer">Szerokość: </label>
					<input type="number" id="szer" name="width" min="1" value="<?=keep(['width']);?>">

				</div>

				<div class="fild" id="wysokosc">
					<label for="wys">Wysokość: </label>
					<input type="number" id="wys" name="height" min="1" value="<?=keep(['height']);?>">
				</div>

				<div class="fild">
					<label for="file">Plik/i: </label>
					<input type="file" id="file" name="file[]" value="" multiple>
				</div>

				<div class="fild">
					<input type="submit" name="submit" value="Wyślij">
				</div>
				
			</form>

			<?=$totalInfo;?>

		</main>

		<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>

	</body>
</html>