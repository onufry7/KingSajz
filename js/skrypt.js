/************************
 *	Podstawowe funkcje	* 
 *	------------------	*
 ************************/

// Sprawdza liczbę wybranych plików
function countFiles()
{
	let count = $('#file').prop('files')['length'];
	$('#count').val(count);

	$.post( "sys/ajax.php", {count:"true"}, function( data ){
		if(count > data) alert("Maksymalna liczba plików to: "+data);
	}, "text");
}


// Obsługa przycisków skali
function scale()
{
	let value = $('input[name="scale"]:checked').val();
	switch(value)
	{
		case 'width':
			$('div#szerokosc').hide();
			$('div#wysokosc').show();
			break;

		case 'height':
			$('div#szerokosc').show();
			$('div#wysokosc').hide();
			break;

		case 'none':
		default:
			$('div#szerokosc').show();
			$('div#wysokosc').show();
	}
}


// Dodanie jednostki do szer. i wys.
function setUnit()
{
	let value = $('input[name="unit"]:checked').val();
	switch(value)
	{
		case 'percent':
			$('input#szer + span').text( ' % ' );
			$('input#wys  + span').text( ' % ' );
			break;

		case 'px':
			$('input#szer + span').text( ' px ' );
			$('input#wys  + span').text( ' px ' );
			break;

		case 'cm':
			$('input#szer + span').text( ' cm ' );
			$('input#wys  + span').text( ' cm ' );
			break;

		case 'mm':
			$('input#szer + span').text( ' mm ' );
			$('input#wys  + span').text( ' mm ' );
			break;
	}
}


// Wywołuje czyszczenie plików
function clearDir()
{
	setTimeout(function(){
		$.post( "sys/ajax.php", {clear:"true"} );
		$('a[download]').remove();
	}, 2000);
}


// Wymusza pobranie pliku
function forceDownload()
{
	setTimeout(function(){
		progressBarrDownload();
		window.location = $('a[download]').attr('href');
	}, 2000);
}







/********************************************
 *	Okienka dialogowe i wskaźniki postępu	* 
 *	-------------------------------------	*
 ********************************************/

// Postęp pobierania pliku
function progressBarrDownload()
{
	let xhr = new XMLHttpRequest(); // Tworzymy obiekt

	//typ połączenia, url, czy połączenie asynchroniczne
	xhr.open("GET", "http://localhost/kingsajz/miniatures/your-images.zip", true);

	// Sprawdzamy postęp
	xhr.addEventListener('progress', function(e) {
	    if (e.lengthComputable) {
	        const progress = (e.loaded / e.total)*100;
	        $('progress').val(progress);
	    }
	});

	// Koniec połączenia
	xhr.addEventListener('load', function() {
        if (this.status === 200) {
            console.log('Plik został pobrany');
            clearDir();
        } else {
            console.log('Połączenie zakończyło się statusem ' + this.status)
        }
    });

	// Wyświetlamy błędy
	xhr.addEventListener('error', function(e) {
        console.log('Wystąpił błąd połączenia');
    });

	//wysyłamy połączenie
	xhr.send();
}







/************************************
 *	Wywołania po załadowaniu strony	* 
 *	-------------------------------	*
 ************************************/
document.addEventListener('DOMContentLoaded', function(event) {
	
	// Selectboxy ustawienia jednostki
	$('input[name="unit"]').click(setUnit);
	setUnit();

	// Selectboxy ustawienia skali
	$('input[name="scale"]').click(scale);
	scale();

	// Liczba wybranych plików
	$('#file').change(countFiles); 

	// Automatyczne pobieranie i progres bar
	$('a[download]').each(forceDownload); 
	$('a[download]').click(progressBarrDownload);
});



