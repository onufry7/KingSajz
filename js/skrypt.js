/***************************
 *	 Application Statuses  *
 *	 --------------------  *
 ***************************/

const Statuses = {
	SENDING_FORM: 'Przetwarzanie parametrów.',
	PREPARING_FILES: 'Przygotowanie plików.',
	UPLOADING_FILES: 'Przesyłanie plików.',
	CHECKING_DIR: 'Zliczanie plików.',
	RESIZING_FILES: 'Zmiana wielkości plików.',
	CREATING_ZIP: 'Tworzenie pliku zip.',
	PREPARING_DOWNLOAD: 'Przygotowanie do pobrania.',
	DOWNLOADING_FILE: 'Pobieranie pliku.',
	CLEANING: 'Pobieranie zakończone.'
};

let countFilesOnServer = 0;




/***********************
 *	 Helper Functions  *
 *	 ----------------  *
 ***********************/

function getSysPath(fileName) {
	return `${window.location.pathname}sys/${fileName}`;
}


function roundNumber(num, decimals) {
	if (typeof num !== 'number' || typeof decimals !== 'number') {
		throw new TypeError('Both arguments must be of type number');
	}

	return +(Math.round(num * Math.pow(10, decimals)) / Math.pow(10, decimals)).toFixed(decimals);
}


function delay(ms) {
	return new Promise(resolve => setTimeout(resolve, ms));
}




/*************************************
 *	 ProgresBarr, Spinner Functions  *
 *	 ------------------------------  *
 *************************************/

function updateProgressBar(loaded, total, id) {
	const progress = total === 0 ? 0 : roundNumber((loaded / total) * 100, 0);
	$(id).val(progress);
}


function resetProgressBar(id) {
	$(id).val(0);
}


function filesProgressBar({ lengthComputable, loaded, total }, barName) {
	lengthComputable && updateProgressBar(loaded, total, barName);
}


function showLoadingSpinner() {
	const loader = $('#loader');
	loader.length && loader.addClass("loader");
}


function hideLoadingSpinner() {
	const loader = $('#loader');
	loader.length && loader.removeClass("loader");
}




/***************************************
 *	 Preparing The Form Data Function  *
 *	 --------------------------------  *
 ***************************************/

function prepareFormData() {
	// Sum size all files
	const totalSize = Array.from($('#file')[0].files).reduce((sum, file) => sum + file.size, 0);
	const countFiles = $('#file').prop('files')['length'];

	// Prepare data
	const data = new FormData($('#form')[0]);
	data.append('size', totalSize);
	data.append('count', countFiles);
	data.append('send', 'true');

	return data;
}


function prepareFormDataWithFiles() {
	let data = new FormData();

	// Add files to data
	Array.from($('#file')[0].files).forEach(file => data.append("files[]", file));

	return data;
}


function prepareResizeData(fileNumber) {
	const data = new FormData($('#form')[0]);

	data.append('resize', 'true');
	data.append('resizeNumber', fileNumber);

	return data;
}





/**********************************
 *	 Actions Related To The Form  *
 *	 ---------------------------  *
 **********************************/

function disabledFormInputs(isDisabled) {
	const form = $('#form');

	form.find('input[type="number"]')
		.prop('readonly', isDisabled);

	form.find('input[type="radio"]:not(:checked), input[type="file"], input[type="submit"]')
		.prop('disabled', isDisabled);

	form.find('div[style="display: none;"] input')
		.prop('disabled', true);
}


function updateScaleVisibility() {
	const value = $('input[name="scale"]:checked').val();

	const visibilityMap = {
		'width': [false, true],
		's-width': [false, true],
		'height': [true, false],
		's-height': [true, false],
		'none': [true, true],
	};

	const [showWidth, showHeight] = visibilityMap[value] || visibilityMap['none'];

	toggleVisibility('#szerokosc', showWidth, '#szer');
	toggleVisibility('#wysokosc', showHeight, '#wys');

	function toggleVisibility(elementSelector, shouldShow, inputSelector) {
		$(elementSelector).toggle(shouldShow);
		$(inputSelector).attr("disabled", !shouldShow);
	}
}


function setSelectedUnit() {
	const value = $('input[name="unit"]:checked').val();
	const units = {
		percent: ' % ',
		px: ' px ',
		cm: ' cm ',
		mm: ' mm '
	};

	const unit = units[value] || units['px'];

	$('#szer + span, #wys + span').text(unit);
}


function clearValidationErrors() {
	$(".valid").empty();
}


function renderValidationErrors(errors) {
	clearValidationErrors();

	const errorKeys = ['scale', 'files', 'unit', 'size'];
	errorKeys.forEach(key => {
		if (errors[key] !== undefined) {
			$(`#v${key.charAt(0).toUpperCase() + key.slice(1)}`).append(errors[key]);
		}
	});
}




/***************************************
 *	 Actions Related To The Dashboard  *
 *	 --------------------------------  *
 ***************************************/

function resetDashboard() {
	$('#status, #messages').empty();
	$('#downloadBar, #uploadBar, #resizeBar').val(0);
	$('#status').text('Status');
}


function setStatus(text) {
	if (text) {
		$("#status").empty().append(text);
	}
}


function updateMessages(text) {
	if (text) {
		$("#messages").append(text);
	}
}


function handleError(text) {
	hideLoadingSpinner();
	$('#form').addClass("errorsForm");
	$("#status").addClass("errorsActive").html('! ERROR !');
	$("#messages").addClass("errorsActive").html(text);
}


function handleErrorDownload(error) {
	handleError(error);
	createDownloadLink();
}


function createDownloadLink() {
	const el = document.querySelector("#link");
	el.innerHTML = "";
	const a = document.createElement("a");
	a.setAttribute("href", "miniatures/images.zip");
	a.setAttribute("download", "images.zip");
	a.innerText = "Pobierz pliki";
	a.addEventListener("click", (event) => {
		event.preventDefault();
		fileDownload();
	});
	el.appendChild(a);
	el.classList.add("manualDownload");
}


function summaryResizeResult(resizeOk, resizeErr) {
	updateMessages(`<p> Pliki zmienione: ${resizeOk} </p>`);
	if (resizeErr) {
		updateMessages(`<p class="textWarning"> Pliki niezmienione: ${resizeErr} </p>`);
	}
}


function summaryUploadResult(response) {

	const {
		all: { count: allCount },
		errors: { count: errorCount },
		success: { count: successCount },
	} = response;

	if (allCount == successCount) {
		updateMessages(`<p> Pliki przesłane: ${allCount} </p>`);
	} else {
		updateMessages(`<p class="textWarning"> Pliki przesłane: ${allCount} </p>`);
		updateMessages(`<p class="textCorrect"> Pliki poprawne: ${successCount} </p>`);
		updateMessages(`<p class="textError"> Pliki odrzucone: ${errorCount} </p>`);
	}
}




/**********************************
 *	 Main Application Controller  *
 *	 ---------------------------  *
 **********************************/

async function triggerActionByStatus(status) {
	setStatus(status);

	switch (status) {
		case Statuses.SENDING_FORM:
			sendForm();
			break;

		case Statuses.PREPARING_FILES:
			disabledFormInputs(true);
			clearValidationErrors();
			showLoadingSpinner();
			filesUpload();
			break;

		case Statuses.UPLOADING_FILES:
			hideLoadingSpinner();
			resetProgressBar('#uploadBar');
			break;

		case Statuses.CHECKING_DIR:
			showLoadingSpinner();
			await delay(1000);
			checkFilesDir();
			break;

		case Statuses.RESIZING_FILES:
			hideLoadingSpinner();
			resetProgressBar('#resizeBar');
			resize();
			break;

		case Statuses.CREATING_ZIP:
			showLoadingSpinner();
			await delay(1000);
			createZip();
			break;

		case Statuses.PREPARING_DOWNLOAD:
			await delay(1000);
			fileDownload();
			break;

		case Statuses.DOWNLOADING_FILE:
			hideLoadingSpinner();
			break;

		case Statuses.CLEANING:
			await delay(100);
			cleanUp();
			break;
	}
}




/*************************
 *  Form Handling, AJAX  *
 *  -------------------  *
 *************************/

function handleFormSubmit(event) {
	event.preventDefault();
	resetDashboard();
	triggerActionByStatus(Statuses.SENDING_FORM);
}


function handleFormResult(result) {
	const { status, info } = result;
	try {
		if (status !== "success") {
			info
				? renderValidationErrors(info)
				: handleError('Brak informacji o błędach.');
			return resetDashboard();
		}

		triggerActionByStatus(Statuses.PREPARING_FILES);

	} catch (error) {
		handleError(error.message);
	}
}

function sendForm() {
	const data = prepareFormData();

	$.ajax({
		url: getSysPath('ajax.php'),
		method: "POST",
		processData: false,
		contentType: false,
		dataType: 'json',
		data: data
	}).done(
		handleFormResult
	).fail((jqXHR, textStatus, errorThrown) => {
		console.error('Błąd przy przesyłaniu formularza:', jqXHR);
		handleError(`Wystąpił błąd podczas przetwarzania formularza: ${textStatus} - ${errorThrown} !`);
	});
}


function handleUpload(xhr) {
	const { status, statusText, response } = xhr;
	try {
		if (status === 200) {
			summaryUploadResult(JSON.parse(response));
			triggerActionByStatus(Statuses.CHECKING_DIR);
		} else {
			throw new Error(`${status} - ${statusText}`);
		}
	} catch (error) {
		handleError(error);
	}
}

function filesUpload() {
	const xhr = new XMLHttpRequest();
	const data = prepareFormDataWithFiles();

	xhr.open("POST", getSysPath('upload-files.php'));
	xhr.onloadstart = () => triggerActionByStatus(Statuses.UPLOADING_FILES);
	xhr.upload.onprogress = (event) => filesProgressBar(event, '#uploadBar');
	xhr.onload = () => handleUpload(xhr);
	xhr.onerror = () => handleError('Błąd wysyłania!');
	xhr.onabort = () => handleError('Anulowanie wysyłania!');
	xhr.send(data);
}


function checkFilesDir() {
	$.post(getSysPath('ajax.php'), { checkDir: "true" }, data => {
		const { status, info, fileCount } = data;

		if (status === "success") {
			countFilesOnServer = fileCount;
			triggerActionByStatus(Statuses.RESIZING_FILES);
		} else {
			handleError(info || `Nieoczekiwany status odpowiedzi: ${status}`);
		}
	}, "json"
	).fail(() => {
		handleError('Wystąpił błąd podczas sprawdzania katalogu z przesłanymi plikami!');
	});
}


function resize(fileNumber = 0, resizeOk = 0, resizeErr = 0) {
	const countFiles = countFilesOnServer - 1;
	const data = prepareResizeData(fileNumber);

	$.ajax({
		url: getSysPath('ajax.php'),
		method: "POST",
		processData: false,
		contentType: false,
		dataType: 'json',
		data: data
	}).done(result => {
		result.status === "success" ? resizeOk++ : resizeErr++;
		updateProgressBar(fileNumber, countFiles, '#resizeBar');
		if (fileNumber < countFiles) {
			resize(fileNumber + 1, resizeOk, resizeErr);
		}
		else {
			summaryResizeResult(resizeOk, resizeErr);
			triggerActionByStatus(Statuses.CREATING_ZIP);
		}
	}).fail(error => {
		console.error('Wystąpił błąd:', error);
		handleError('Wystąpił błąd podczas zmiany wielkości plików!');
	});
}



function createZip() {
	$.post(getSysPath('ajax.php'), { zip: "true" }, data => {
		const { status, info } = data;

		if (status === "success") {
			updateMessages(`<p>${info}</p>`);
			triggerActionByStatus(Statuses.PREPARING_DOWNLOAD);
		} else {
			handleError(info || `Nieoczekiwany status odpowiedzi: ${status}`);
		}
	}, "json"
	).fail(error => {
		console.error('Błąd podczas generowania pliku zip:', error);
		handleError('Wystąpił błąd podczas generowania pliku zip!');
	});
}



function handleDownload(xhr) {
	try {
		if (xhr.status === 200) {
			const blob = xhr.response;
			const link = document.createElement("a");
			link.href = window.URL.createObjectURL(blob);
			link.download = "images.zip";
			link.click();
			triggerActionByStatus(Statuses.CLEANING);
		} else {
			throw new Error(`${xhr.status} - ${xhr.statusText}`);
		}
	} catch (error) {
		handleErrorDownload(error);
	}
}


async function fileDownload() {
	let xhr = new XMLHttpRequest();

	xhr.open("GET", `${getSysPath('ajax.php')}?download=true`, true);
	xhr.responseType = "blob";
	xhr.onloadstart = () => triggerActionByStatus(Statuses.DOWNLOADING_FILE);
	xhr.onprogress = (event) => filesProgressBar(event, '#downloadBar');
	xhr.onload = () => handleDownload(xhr);
	xhr.onerror = () => handleErrorDownload("Wystąpił błąd podczas pobierania pliku!");
	xhr.onabort = () => handleErrorDownload('Pobieranie zostało anulowane!');
	xhr.send();
};



function cleanUp() {
	$.post(getSysPath('ajax.php'), { clean: "true" });
	disabledFormInputs(false);
}




/********************************
 *	Application Initialization  *
 *	--------------------------  *
 ********************************/

function setupEventListeners(selector, callback, eventType = 'click', callOnStart = true) {
	const elements = document.querySelectorAll(selector);

	if (elements.length === 0) {
		console.warn(`Brak elementów do podłączenia nasłuchiwacza dla selektora: "${selector}"`);
		return;
	}

	elements.forEach(input => input.addEventListener(eventType, callback));
	callOnStart && callback();
}


document.addEventListener('DOMContentLoaded', function () {
	setupEventListeners('input[name="unit"]', setSelectedUnit);
	setupEventListeners('input[name="scale"]', updateScaleVisibility);
	setupEventListeners('#form', handleFormSubmit, 'submit', false);

	$(window).on("beforeunload", cleanUp);
});
