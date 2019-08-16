/****************************
 *	 Podstawowe funkcje JS  * 
 *	 ---------------------  *
 ****************************/

// Precyzyjne zaokrąglanie liczb
Number.prototype.round = function(miejsc)
{
	return +(Math.round(this+"e+"+miejsc)+"e-"+miejsc);
}


// Blokowanie pól formularza
function disabledForm(flag)
{
	// Zablokowanie formularza
	if(flag === true)
	{
		$('input[type="file"]').attr("disabled", true);
        $('input[type="submit"]').attr("disabled", true);
        $('input[type="number"]').attr("readonly", true);
		$('#form').find('input[type="radio"]:not(:checked)').each(function(){
			$(this).attr('disabled', true); 
		});
	}
	// Odblokowanie formularza
	else if(flag === false)
	{
		$('input').attr("disabled", false);
		$('div[style="display: none;"] input').attr("disabled", true);
		$('input').attr("readonly", false);
	}
}



// Czyści ekran z informacji
function cleanBoard()
{
	$("#errors").empty();
	$("#status").empty();
	$("#info").empty();

	$('#downloadBar').val(0);
	$('#uploadBar').val(0);
	$('#resizeBar').val(0);	
}



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
			$('#szer').attr("disabled", true);
			$('div#wysokosc').show();
			$('#wys').attr("disabled", false);
			break;

		case 'height':
			$('div#szerokosc').show();
			$('#szer').attr("disabled", false);
			$('div#wysokosc').hide();
			$('#wys').attr("disabled", true);
			break;

		case 'none':
		default:
			$('div#szerokosc').show();
			$('#szer').attr("disabled", false);
			$('div#wysokosc').show();
			$('#wys').attr("disabled", false);
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
	$("#errors").empty(); // Czyści ewentualne pozostałe błędy
	$("#status").empty();
	$("#status").empty().append(text);	
}



// Zwraca informacje
function setInfo(text)
{
	$("#info").append(text);
	$("#info").append('<br>');
}



// Ustawienie błędu
function setErrors(text)
{
	$("#status").empty();
	$("#errors").empty();
	$("#errors").append(text);
}



// Progres barr
function progresBarr(val, total, id)
{
	// Wrtość value tagu progress
	let progress = (val/total)*100;
	progress = progress.round(2);
	$(id).val(progress);
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
	// Dodajemy onclika
	$('a[download]').click(fileDownload);
}



// Wywołuje funkcje zależnie od statusu
function statusListener()
{
	let status = $('#status').text();

	switch (status)
	{
		case 'Sprawdzanie parametrów...':
			break;

		case 'Sprawdzanie zakończone.':
			filesUpload()
			break;

		case 'Przesyłanie plików...':
			break;
		
		case 'Przesyłanie zakończone.':
			setTimeout(checkFilesDir, 50);
			break;

		case 'Sprawdzanie plików...':
			break;

		case 'Zmiana wielkości plików...':
			break;

		case 'Zmieniono wielkość.':
		 	setTimeout(createZip, 100);
		 	break;

		case 'Tworzenie pliku zip...':
			$('#loader').addClass("loader");
		 	break;
		
		case 'Zip gotowy.':
			$('#loader').removeClass("loader");
			createLink();
		 	setTimeout(forceDownload, 100);
		 	break;

		case 'Pobieranie pliku...':
		 	break;

		case 'Pobieranie zakończone.':
		 	setTimeout(cleaner, 1000);
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
	setStatus('Sprawdzanie parametrów...');

	// Dodatkowe pola
	let totalSize = 0; // Rozmiar wszystkich plików
	let files = $('#file')[0].files;
	for (let i=0; i<files.length; i++) totalSize += files[i].size;

	// Przygotowanie danych
	let data = new FormData();
	let inputs = $('#form').serializeArray();
	inputs.push({ name:'size', value:totalSize });
	inputs.push({ name:'send', value:'true' });
	$.each(inputs, function (key, input){ data.append(input.name, input.value); });

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
	.done(result => {
        if(result.status == "success") 
        {
        	setStatus('Sprawdzanie zakończone.'); // Wysyła pliki
        	disabledForm(true);
        }
        else setErrors(result.info); // Wyświetla błędy  
    })
	.fail(error => {
		console.log(error);
		setErrors('Wystąpił nieokreślony błąd 0');
	});
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
		$('#uploadBar').val(0);
	}

	// Pasek postępu
	function progressTransfer(e)
	{
		progresBarr(e.loaded, e.total, '#uploadBar');
	}

	// Zakończenie przesyłania
	function loadTransfer() {
		if (this.status === 200) setInfo(this.response);
		else // Status inny niż 200 = błąd
		{
			if(this.status !== 'undefined') setErrors('Połączenie zakończyło się statusem '+this.status);
			else setErrors('Wystąpił nieokreślony błąd 1');
		} 
	}

	// Błędy i anulowanie
	function errorTransfer(){ setErrors('Błąd wysyłania'); }
	function abortTransfer(){ setErrors('Anulowanie wysyłania'); }

	// Koniec wysyłania
	function loadEnd() 
	{ 
		if($('#errors').text() != '')
		{
			$('#uploadBar').val(0);
			return false;
		}

		$("#errors").empty();
		setStatus('Przesyłanie zakończone.');
	}
}



// Sprawdzamy folder i liczbe plików
function checkFilesDir()
{
	setStatus('Sprawdzanie plików...');

	$.post( "sys/ajax.php", {checkdir:"true"}, data => {
		// Zwracamy wyniki
		if( data.status == "success" )
		{
			setStatus('Zmiana wielkości plików...');
			resize(data.info, 1);
		} 
		else if( data.status == "error" ) setErrors(data.info);	
	}, "json")
	.fail(error => {
		console.log(error);
		setErrors('Wystąpił nieokreślony błąd 2');
	});
}



// Włącza zmiane rozmiaru plików
function resize(countFiles, fileNo)
{
	// Przygotowanie danych
	let data = new FormData();
	let inputs = $('#form').serializeArray();
	inputs.push({ name:'resize', value:'true' });
	$.each(inputs, function (key, input){ data.append(input.name, input.value); });
	data.append('resizeNo', fileNo);

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
	.done(result => {
        if(result.status == "success") 
        {	
        	// Progres barr
        	progresBarr(fileNo, countFiles, '#resizeBar');
			
        	if(fileNo < countFiles)
        	{
				// Rekurencja dla kolejnych plików
				fileNo++;
        		resize(countFiles, fileNo);	
        	}
        	else setStatus('Zmieniono wielkość.');
        }
        else setErrors(result.info); // Wyświetla błędy  
    })
	.fail(error => {
		console.log(error);
		setErrors('Wystąpił nieokreślony błąd 3');
	});
}



// Przygotowanie zipa do pobrania
function createZip()
{
	setStatus('Tworzenie pliku zip...');

	$.post( "sys/ajax.php", {zip:"true"}, data => {
		// Zwracamy wyniki
		if( data.status == "error" ) {setErrors(data.info);}
		else if( data.status == "success" )
		{
			setInfo(data.info);
			setStatus('Zip gotowy.');
		}
	}, "json")
	.fail(error => {
		console.log(error);
		setErrors('Wystąpił nieokreślony błąd 4');
	});
}



// Postęp pobierania pliku
function fileDownload()
{
	// Wyłączamy bo użycie metody GET wywoła reload strony
	$(window).off("beforeunload"); 

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
	function progressTransfer(e) 
	{
		progresBarr(e.loaded, e.total, '#downloadBar');
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
			if(this.status !== 'undefined') setErrors('Połączenie zakończyło się statusem '+this.status);
			else setErrors('Wystąpił nieokreślony błąd 5');
				
		} 
	}

	// Błędy i anulowanie
	function errorTransfer(){ setErrors('Błąd pobierania'); }
	function abortTransfer(){ setErrors('Anulowanie pobierania'); }
}



// Wywołuje czyszczenie plików
function cleaner()
{
	$.post( "sys/ajax.php", {clean:"true"} );
	$('a[download]').remove();
	disabledForm(false);
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
		cleanBoard();
		sendForm();
	});

	// Nasłuchiwanie statusu
	$('#status').on('DOMSubtreeModified', statusListener);

	// Czyszczenie przy zamknięciu i odświerzeniu strony
	$(window).on( "beforeunload", cleaner);

});