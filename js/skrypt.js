/****************************
 *	 Podstawowe funkcje JS  *
 *	 ---------------------  *
 ****************************/

const ajaxPath = window.location.pathname + "sys/ajax.php"

// Precyzyjne zaokrąglanie liczb
Number.prototype.round = function (miejsc) {
	return +(Math.round(this + "e+" + miejsc) + "e-" + miejsc);
}


// Blokowanie pól formularza
function disabledForm(flag) {
	// Zablokowanie formularza
	if (flag === true) {
		$('input[type="file"]').attr("disabled", true);
		$('input[type="submit"]').attr("disabled", true);
		$('input[type="number"]').attr("readonly", true);
		$('#form').find('input[type="radio"]:not(:checked)').each(function () {
			$(this).attr('disabled', true);
		});
	}
	// Odblokowanie formularza
	else if (flag === false) {
		$('input').attr("disabled", false);
		$('div[style="display: none;"] input').attr("disabled", true);
		$('input').attr("readonly", false);
	}
}



// Czyści ekran z informacji
function cleanBoard() {
	$("#errors").empty();
	$("#status").empty();
	$("#info").empty();
	$('#downloadBar').val(0);
	$('#uploadBar').val(0);
	$('#resizeBar').val(0);
}



// Ustawia liczbę wybranych plików
function setCountFiles() {
	let countFiles = $('#file').prop('files')['length'];
	$('input#count').val(countFiles);
}



// Obsługa przycisków skali
function setScale() {
	let value = $('input[name="scale"]:checked').val();
	switch (value) {
		case 'width':
		case 's-width':
			$('#szerokosc').hide();
			$('#szer').attr("disabled", true);
			$('#wysokosc').show();
			$('#wys').attr("disabled", false);
			break;

		case 'height':
		case 's-height':
			$('#szerokosc').show();
			$('#szer').attr("disabled", false);
			$('#wysokosc').hide();
			$('#wys').attr("disabled", true);
			break;

		case 'none':
		default:
			$('#szerokosc').show();
			$('#szer').attr("disabled", false);
			$('#wysokosc').show();
			$('#wys').attr("disabled", false);
	}
}



// Dodanie jednostki do szer. i wys.
function setUnit() {
	let value = $('input[name="unit"]:checked').val();
	switch (value) {
		case 'percent':
			$('input#szer + span').text(' % ');
			$('input#wys  + span').text(' % ');
			break;

		case 'px':
			$('input#szer + span').text(' px ');
			$('input#wys  + span').text(' px ');
			break;

		case 'cm':
			$('input#szer + span').text(' cm ');
			$('input#wys  + span').text(' cm ');
			break;

		case 'mm':
			$('input#szer + span').text(' mm ');
			$('input#wys  + span').text(' mm ');
			break;
	}
}



// Zmienia status zadania
function setStatus(text) {
	$(".valid").empty(); // Czyści ewentualne błędy walidacji
	$("#errors").empty(); // Czyści ewentualne pozostałe błędy
	$("#status").empty();
	$("#status").empty().append(text);
}



// Zwraca informacje
function setInfo(text) {
	$("#info").append(text);
}



// Ustawienie błędu
function setErrors(text) {
	$("#info").empty();
	$('#form').addClass("errorsForm");

	$('#status').addClass("errorsActiv");
	$("#status").empty().append('! ERROR !');

	$('#errors').addClass("errorsActiv");
	$("#errors").empty().append(text);
}



// Błędy walidacji
function setValid(errors) {
	$(".valid").empty();
	if (errors.scale != undefined) $("#vScale").append(errors.scale);
	if (errors.files != undefined) $("#vFiles").append(errors.files);
	if (errors.unit != undefined) $("#vUnit").append(errors.unit);
	if (errors.size != undefined) $("#vSize").append(errors.size);
}



// Progres bar
function progresBarr(val, total, id) {
	// Wrtość value tagu progress
	let progress = (val / total) * 100;
	progress = progress.round(2);
	$(id).val(progress);
}



// Wymusza pobranie pliku
function forceDownload() {
	fileDownload();
	window.location = $('a[download]').attr('href');
}



// Tworzy link do pobrania
function createLink() {
	const el = document.querySelector("#link");
	const a = document.createElement("a");
	a.setAttribute("href", "miniatures/images.zip");
	a.setAttribute("download", "images.zip");
	a.setAttribute("type", "application/zip");
	a.innerText = "Pobierz pliki";
	el.classList.add("brokenLink");
	el.appendChild(a);
	// Dodajemy onclika
	$('a[download]').click(fileDownload);
}



// Wywołuje funkcje zależnie od statusu
function statusListener() {
	let status = $('#status').text();

	switch (status) {
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
function sendForm() {
	setStatus('Sprawdzanie parametrów...');

	// Dodatkowe pola
	let totalSize = 0; // Rozmiar wszystkich plików
	let files = $('#file')[0].files;
	for (const element of files) totalSize += element.size;

	// Przygotowanie danych
	let data = new FormData();
	let inputs = $('#form').serializeArray();
	inputs.push({ name: 'size', value: totalSize });
	inputs.push({ name: 'send', value: 'true' });
	$.each(inputs, function (key, input) { data.append(input.name, input.value); });

	// Zapytanie ajax
	$.ajax(
		{
			url: ajaxPath,
			method: "POST",
			processData: false,
			contentType: false,
			dataType: 'json',
			data: data
		})
		.done(result => {
			if (result.status == "success") {
				setStatus('Sprawdzanie zakończone.'); // Wysyła pliki
				disabledForm(true);
			}
			else setValid(result.info); // Wyświetla błędy
		})
		.fail(error => {
			console.log(error);
			console.log(error.responseText);
			setErrors('Wystąpił błąd zapytania ajax!');
		});
}



// Wysłanie plików
function filesUpload() {
	let ajax = new XMLHttpRequest();
	let data = new FormData();

	// Przetwarza pliki z formularza
	let files = $('#file')[0].files;
	for (const element of files) data.append("files[]", element);
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
	function progressTransfer(e) {
		progresBarr(e.loaded, e.total, '#uploadBar');
	}

	// Zakończenie przesyłania
	function loadTransfer() {
		if (this.status === 200) setInfo(this.response);
		else if (this.status !== 'undefined') setErrors('Połączenie zakończyło się statusem ' + this.status);
		else setErrors('Wystąpił nieokreślony błąd 1');
	}

	// Błędy i anulowanie
	function errorTransfer() { setErrors('Błąd wysyłania'); }
	function abortTransfer() { setErrors('Anulowanie wysyłania'); }

	// Koniec wysyłania
	function loadEnd() {
		if ($('#errors').text() != '') {
			$('#uploadBar').val(0);
			return false;
		}

		$("#errors").empty();
		setStatus('Przesyłanie zakończone.');
	}
}



// Sprawdzamy folder i liczbe plików
function checkFilesDir() {
	setStatus('Sprawdzanie plików...');

	$.post(ajaxPath, { checkdir: "true" }, data => {
		// Zwracamy wyniki
		if (data.status == "success") {
			setStatus('Zmiana wielkości plików...');
			resize(data.info, 1);
		}
		else if (data.status == "error") setErrors(data.info);
	}, "json")
		.fail(error => {
			console.log(error);
			setErrors('Wystąpił nieokreślony błąd 2');
		});
}



// Włącza zmiane rozmiaru plików
let resizeErr = 0;
let resizeOk = 0;

function resize(countFiles, fileNo) {
	// Przygotowanie danych
	let data = new FormData();
	let inputs = $('#form').serializeArray();
	inputs.push({ name: 'resize', value: 'true' });
	$.each(inputs, function (key, input) { data.append(input.name, input.value); });
	data.append('resizeNo', fileNo);

	// Zapytanie ajax
	$.ajax(
		{
			url: ajaxPath,
			method: "POST",
			processData: false,
			contentType: false,
			dataType: 'json',
			data: data
		})
		.done(result => {

			progresBarr(fileNo, countFiles, '#resizeBar'); // Progres barr

			if (result.status == "success") {
				resizeOk++;
			}
			else {
				resizeErr++;
				console.log(result.info); // Wyświetla błędy
			}

			if (fileNo < countFiles) {
				// Rekurencja dla kolejnych plików
				fileNo++;
				resize(countFiles, fileNo);
			}
			else {
				setInfo('<p>Pliki zmienione: ' + resizeOk + '</p>');
				resizeOk = 0;
				setInfo('<p>Pliki niezmienione: ' + resizeErr + '</p>');
				resizeErr = 0;
				setStatus('Zmieniono wielkość.');
			}
		})
		.fail(error => {
			console.log(error);
			console.log(error.responseText);
			setErrors('Wystąpił nieokreślony błąd 3');
		});
}



// Przygotowanie zipa do pobrania
function createZip() {
	setStatus('Tworzenie pliku zip...');

	$.post(ajaxPath, { zip: "true" }, data => {
		// Zwracamy wyniki
		if (data.status == "error") { setErrors(data.info); }
		else if (data.status == "success") {
			setInfo('<p>' + data.info + '</p>');
			setStatus('Zip gotowy.');
		}
	}, "json")
		.fail(error => {
			console.log(error);
			setErrors('Wystąpił nieokreślony błąd 4');
		});
}



// Postęp pobierania pliku
function fileDownload() {
	// Wyłączamy bo użycie metody GET wywoła reload strony
	$(window).off("beforeunload");

	let xhr = new XMLHttpRequest();

	xhr.open("GET", "miniatures/images.zip", true);
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
		progresBarr(e.loaded, e.total, '#downloadBar');
	}

	// Zakończenie przesyłania
	function loadTransfer() {
		if (this.status === 200) {
			setStatus('Pobieranie zakończone.');
			// Pobraliśmy już plik więc włączamy czyszczenie z powrotem
			$(window).on("beforeunload", cleaner);
		}
		else if (this.status !== 'undefined') setErrors('Połączenie zakończyło się statusem ' + this.status);
		else setErrors('Wystąpił nieokreślony błąd 5');
	}

	// Błędy i anulowanie
	function errorTransfer() { setErrors('Błąd pobierania'); }
	function abortTransfer() { setErrors('Anulowanie pobierania'); }
}



// Wywołuje czyszczenie plików
function cleaner() {
	$.post(ajaxPath, { clean: "true" });
	$('a[download]').remove();
	$('#link').removeClass("brokenLink");
	$('#errors').removeClass("errorsActiv");
	$('#status').removeClass("errorsActiv");
	$('#form').removeClass("errorsForm");
	disabledForm(false);
}






/*************************************
 *	Wywołania po załadowaniu strony  *
 *	-------------------------------  *
 *************************************/
document.addEventListener('DOMContentLoaded', function (event) {

	// Selectboxy ustawienia jednostki
	$('input[name="unit"]').click(setUnit); setUnit();

	// Selectboxy ustawienia skali
	$('input[name="scale"]').click(setScale); setScale();

	// Liczba wybranych plików
	$('#file').change(setCountFiles);

	// Wysłanie plików
	$('#form').submit(function (event) {
		event.preventDefault();
		cleanBoard();
		sendForm();
	});

	// Utworzenie obserwatora z funkcją zwrotną
	const observer = new MutationObserver(function (mutationsList, observer) {
		for (let mutation of mutationsList) {
			if (mutation.type === 'childList' || mutation.type === 'subtree') {
				statusListener();
			}
		}
	});

	// Konfiguracja obserwatora - nasłuchiwanie na zmiany w poddrzewie
	const config = { subtree: true, childList: true };

	// Rozpoczęcie obserwacji na elemencie o id 'status'
	observer.observe(document.getElementById('status'), config);

	// Czyszczenie przy zamknięciu i odświerzeniu strony
	$(window).on("beforeunload", cleaner);

});