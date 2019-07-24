
//Sprawdza liczbę wybranych plików
function countFiles()
{
	let count = $('#file').prop('files')['length'];
	$('#count').val(count);

	$.post( "sys/ajax.php", {count:"true"}, function( data ){
		if(count > data) alert("Maksymalna liczba plików to: "+data);
	}, "text");
}


//Obsługa selectboxów (zachowania proporcji)
//Blokowanie - odblokowanie pól
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


// Wymusza pobranie pliku
function forceDownload()
{
	setTimeout(function(){
		window.location = $('a[download]').attr('href');
	}, 2000);
}



document.addEventListener('DOMContentLoaded', function(event) {

	scale() //Selectboxy ustawienia skali
	$('input[name="scale"]').click(scale); //Selectboxy ustawienia skali
	$('#file').change(countFiles); //Liczba wybranych plików
	$('a[download]').each(forceDownload);

})