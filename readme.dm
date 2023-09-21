# KingSajz
Aplikacja do zmiany rozmiaru plików graficznych.
Obsługiwane formaty plików: PNG, JPG, GIF.
Wymaga do działania biblioteki gd
 - w php.ini oddblokować: z ;extension=gd na extension=gd
Wersji PHP >= 8.0


## Obsługa
Możemy ustawić jednostkę wielkości dla plików wynikowych.
Możemy ustawić typ skali tj. w jaki sposób zmienić rozmiar plików.
Wymiary tu nalezy określić docelowy wymiar dla plików wynikowych
Możemy przekazać wiele plików naraz dla wszystkich zastosowane zostaną
te same ustawienia. Program zwraca spakowane archiwum zip.
Jego zawartość to zmodyfikowane pliki.
Program stara sie wymusić na przeglądarce automatyczne pobranie
zwróconego pliku a w przypadku niepowodzenia pozostawi link do ręcznego
pobrania wygenerowanego archiwum zip.


### Pliki/ini
Pole służące do przekazania plików które chcemy zmodyfikować.
możemy przekazać jeden bądź wiele plików niezależnie od tego
co wybierzemy program zwruci archiwum zip zawierające plik/i.


### Wymiary
Tutaj określamy jaki wymiary chcemy uzyskać - na plikach wynikowych.

Jeśli pliki źródłowe mają większy rozmiar niż tutaj ustalony to
nastąpi pomniejszenie rozmiaru plików.

Jeśli pliki źródłowe mają mniejszy rozmiar niż tutaj ustalony to
nastąpi powiększenie rozmiaru plików.


### Jednostka
Tutaj określamy w jakiej jednostce wielkości podajemy wymiary.

Dostępne wielkości to:
px - piksele
cm - centymetry
mm - milimetry
% - procenty (Określamy w procentach o ile powiększyć/zmniejszyć grafikę)
Przyjmujemy tutaj że oryginalna wielkość plików źródłowych to 100%.

Jednostka dotyczy plików wynikowych i jest niezależna od wielkości
plików wejściowych. Zawsze program będzie przeliczał z pikseli na pozostałe
jednostki wielkości. Czyli możemy przekazać pliki które mają 20px na 20px i określić
że chcemy otrzymać pliki które będą mieć 10cm na 10cm.


### Skala
Tutaj określamy w jaki sposób chemy przeskalować przekazane pliki.
Możemy wybrać z poniższych opcji:

Brak - na sztywno w sekcji wymiary ustawiamy jaką wielkość mają mieć pliki wynikowe.
Np. Dajemy plik wysokość = 10px, szerokość = 10px i ustalamy wymiar wysokość = 20px, szerokość = 15px
uzyskamy plik który będzie mieć wysokość 20px i szerokość 15px. (Uwaga - Nastąpi zniekształcenie obrazu).

Zachowaj szerokość - ustalamy jaką wysokość mają mieć pliki wynikowe a szerokość pozostanie niezmieniona.
Np. Przekazujemy pliki o wymiarze 100px wysokosći i 200px szerokości a w wymiarach ustawimy 200px to otrzymamy
plik o wymiarach 200px wysokości i 200px szerkości. (Uwaga - Nastąpi zniekształcenie obrazu).

Zachowaj wysokość - ustalamy jaką szerokość mają mieć pliki wynikowe a wysokość pozostanie niezmieniona.
Np. Przekazujemy pliki o wymiarze 250px wysokosći i 100px szerokości a w wymiarach ustawimy 300px to otrzymamy
plik o wymiarach 250px wysokości i 300px szerkości. (Uwaga - Nastąpi zniekształcenie obrazu).

Skaluj szerokość - ustalamy jaką ma mieć wysokość plik wynikowy a szerokość zostanie odpowiednio przeskalowana.
Np. Przekazując plik o wymiarach 100px szerokości i 50px wysokości oraz ustawiając wymiar na 100px otrzymamy
plik o wymiarach 100px wysokosci i 200px szerokości. (Brak zniekształceń obrazu).

Skaluj wysokość - ustalamy jaką ma mieć szerokość plik wynikowy a wysokość zostanie odpowiednio przeskalowana.
Np. Przekazując plik o wymiarach 150px szerokości i 100px wysokości oraz ustawiając wymiar na 75px otrzymamy
plik o wymiarach 50px wysokosci i 75px szerokości. (Brak zniekształceń obrazu).