/****************************
 *	 Podstawowe funkcje JS  * 
 *	 ---------------------  *
 ****************************/

// Ustawia liczbę wybranych plików
function setCountFiles()
{
	let countFiles = $('#file').prop('files')['length'];
	$('input#count').val(countFiles);
}	



// Obsługa przycisków skali
function setScale()
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



// Zmienia status zadania
function setStatus(text)
{
	$("#status").empty().append(text);	
}



// Wymusza pobranie pliku
function forceDownload()
{
	fileDownload();
	window.location = $('a[download]').attr('href');	
}



// Tworzy link do pobrania
function createLink()
{
	const body = document.querySelector("body");
	const a = document.createElement("a");
	a.setAttribute("href", "miniatures/your-images.zip");
	a.setAttribute("download", "images.zip");
	a.setAttribute("type", "application/zip");
	a.innerText = "images.zip";
	//a.classList.add("module");
	body.appendChild(a);
	$('a[download]').click(fileDownload);
}



// Wywołuje funkcje zależnie od statusu
function statusListener()
{
	let status = $('#status').text();

	switch (status)
	{
		case 'Sprawdzam dane...':
			break;

		case 'Przesyłanie plików...':
			break;
		
		case 'Zakończono przesyłanie.':
			setTimeout(resizeFile, 1000);
			break;

		case 'Zmiana wielkości plików...':
			break;

		case 'Zmieniono wielkość.':
		 	setTimeout(createZip, 800);
		 	break;

		case 'Tworzenie pliku zip...':
		 	break;

		
		case 'Zip gotowy.':
			createLink();
		 	setTimeout(forceDownload, 500);
		 	break;

		case 'Pobieranie pliku...':
		 	break;

		case 'Pobieranie zakończone.':
		 	setTimeout(cleaner, 2000);
		 	break;
	}
}



/********************************************
 * Obsługa formularza, paski postępu, ajaxy *     
 * ---------------------------------------- *
 ********************************************/

// Wysłanie formularza
function sendForm()
{
	// Dodatkowe pola
	let totalSize = 0; // Rozmiar wszystkich plików
	let files = $('#file')[0].files;
	for (let i=0; i<files.length; i++) totalSize += files[i].size;

	// Przygotowanie danych
	let data = new FormData();
	let inputs = $('#form').serializeArray();
	inputs.push({name:'size', value:totalSize, name:'send', value:'true' });
	$.each(inputs, function (key, input){ data.append(input.name, input.value); });
	data.append('key', 'value'); 

	// Zapytanie ajax
	$.ajax(
	{
	    url: "sys/ajax.php",
	    method: "POST",
	    processData: false,
	    contentType: false,
	    dataType:'json',
	    data: data
	})
	.done(function(result)
    {
        $("#errors").empty(); // Czyści błędy
        if(result.status == "success") filesUpload(); // Wysyła pliki
        else $("#errors").append(result.info); // Wyświetla błędy
    })
	.fail(error => console.log(error));
}



// Wysłanie plików
function filesUpload()
{
	let ajax = new XMLHttpRequest();
	let data = new FormData();
	let error = false;
	
	// Przetwarza pliki z formularza
	let files = $('#file')[0].files;
	for (let i=0; i<files.length; i++) data.append("files[]", files[i]);
	data.append('key', 'value');
	
	// Ajax xhr
	ajax.open("POST", "sys/upload-files.php");
	ajax.addEventListener("loadstart", loadStart, false);
	ajax.upload.addEventListener("progress", progressTransfer, false);
	ajax.addEventListener("load", loadTransfer, false);
	ajax.addEventListener("error", errorTransfer, false);
	ajax.addEventListener("abort", abortTransfer, false);
	ajax.addEventListener("loadend", loadEnd, false);
	ajax.send(data);

	// Rozpoczęcie pobierania
	function loadStart() {
		setStatus('Przesyłanie plików...');
	}

	// Pasek postępu
	function progressTransfer(e) {
		const progress = (e.loaded / e.total)*100;
		$('#uploadBar').val(progress);
	}

	// Zakończenie przesyłania
	function loadTransfer() {
		if (this.status === 200) $("#info").append(this.response);
		else // Status inny niż 200 = błąd
		{
			if(this.status !== 'undefined')
				$("#errors").empty().append('Połączenie zakończyło się statusem '+this.status);
			else 
				$("#errors").empty().append('Wystąpił nieokreślony błąd');
			error=true;
		} 
	}

	// Błędy i anulowanie
	function errorTransfer(){$("#errors").empty().append('Błąd wysyłania');error=true;}
	function abortTransfer(){$("#errors").empty().append('Anulowanie wysyłania');error=true;}

	// Koniec wysyłania
	function loadEnd() 
	{ 
		if(error)
		{
			$('#uploadBar').val(0);
			return false;
		}

		$("#errors").empty();
		setStatus('Zakończono przesyłanie.');
	}
}



// Włącza zmiane rozmiaru plików
function resizeFile()
{
	setStatus('Zmiana wielkości plików...');
	
	let inputs = $('#form').serializeArray();
	inputs.push({name:'resize', value:'true'});

	$.post( "sys/ajax.php", inputs, function( data ){
		
		// Czyścimy błędy
		$("#errors").empty();
		
		// Zwracamy wyniki
		if( data.status == "error" ) $("#errors").append(data.info);
		else if( data.status == "success" )
		{
			$("#info").append(data.info);
			setStatus('Zmieniono wielkość.');
		} 
	}, "json");
}



// Przygotowanie zipa do pobrania
function createZip()
{
	setStatus('Tworzenie pliku zip...');

	$.post( "sys/ajax.php", {zip:"true"}, function( data ){
		
		// Czyścimy błędy
		$("#errors").empty();
		
		// Zwracamy wyniki
		if( data.status == "error" )
		{
			$("#errors").append(data.info);
			$("#status").empty();
		}
		else if( data.status == "success" )
		{
			$("#info").append(data.info);
			setStatus('Zip gotowy.');
		} 
	}, "json");
}



// Postęp pobierania pliku
function fileDownload()
{
	// Wyłączamy bo użycie metody GET wywoła reload strony
	$(window).off( "beforeunload"); 

	let xhr = new XMLHttpRequest();
	
	xhr.open("GET", "miniatures/your-images.zip", true);
	xhr.addEventListener("loadstart", loadStart, false);
	xhr.addEventListener("progress", progressTransfer, false);
	xhr.addEventListener("load", loadTransfer, false);
	xhr.addEventListener("error", errorTransfer, false);
	xhr.addEventListener("abort", abortTransfer, false);
	xhr.send();

	// Rozpoczęcie pobierania
	function loadStart() {
		setStatus('Pobieranie pliku...');
	}

	// Pasek postępu
	function progressTransfer(e) {
		const progress = (e.loaded / e.total)*100;
		$('#downloadBar').val(progress);
	}

	// Zakończenie przesyłania
	function loadTransfer() {
		if (this.status === 200)
		{
			setStatus('Pobieranie zakończone.');
			// Pobraliśmy już plik więc włączamy czyszczenie z powrotem
			$(window).on( "beforeunload", cleaner);
		}
		else // Status inny niż 200 = błąd
		{
			if(this.status !== 'undefined')
				$("#errors").empty().append('Połączenie zakończyło się statusem '+this.status);
			else 
				$("#errors").empty().append('Wystąpił nieokreślony błąd');
		} 
	}

	// Błędy i anulowanie
	function errorTransfer(){$("#errors").empty().append('Błąd pobierania');}
	function abortTransfer(){$("#errors").empty().append('Anulowanie pobierania');}
}



// Wywołuje czyszczenie plików
function cleaner()
{
	$.post( "sys/ajax.php", {clean:"true"} );
	$('a[download]').remove();
}







/*************************************
 *	Wywołania po załadowaniu strony  * 
 *	-------------------------------  *
 *************************************/
document.addEventListener('DOMContentLoaded', function(event) {
	
	// Selectboxy ustawienia jednostki
	$('input[name="unit"]').click(setUnit); setUnit();
	
	// Selectboxy ustawienia skali
	$('input[name="scale"]').click(setScale); setScale();

	// Liczba wybranych plików
	$('#file').change(setCountFiles);

	// Wysłanie plików
	$('#form').submit(function(event){
		event.preventDefault();
		sendForm();
	});

	// Nasłuchiwanie statusu
	$('#status').on('DOMSubtreeModified', statusListener);

	// Czyszczenie przy zamknięciu i odświerzeniu strony
	$(window).on( "beforeunload", cleaner);

});