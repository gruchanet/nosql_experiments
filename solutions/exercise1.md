[&#8810;](../README.md) powrót

## Rozwiązanie Zadania 1

<h3><a href="./exercise1/1a.md">Zadanie 1a)</a></h3>
> Import pliku [Train.csv](https://www.kaggle.com/c/facebook-recruiting-iii-keyword-extraction/download/Train.zip) do baz danych:
> - [MongoDB](./exercise1/1a.md#mongodb)
> - [PostgreSQL](./exercise1/1a.md#postgresql)

<h3><a href="./exercise1/1b.md">Zadanie 1b</a></h3>
> Zliczenie liczby zaimportowanych rekordów (Powinno ich być 6 034 195).

<h3><a href="./exercise1/1c.md">Zadanie 1c)</a></h3>
> Zamienić string zawierający tagi na tablicę napisów z tagami następnie zliczyć wszystkie tagi i wszystkie różne tagi.
>
> W tym zadaniu należy napisać program, który to zrobi. W przypadku [MongoDB](./exercise1/1c.md#mongodb) należy użyć jednego ze sterowników ze strony [MongoDB Ecosystem](http://docs.mongodb.org/ecosystem/). W przypadku [PostgreSQL](./exercise1/1c.md#postgresql) – należy to zrobić w jakikolwiek sposób.

<h3><a href="./exercise1/1d.md">Zadanie 1d)</a></h3>
> Wyszukać w sieci dane zawierające obiekty [GeoJSON](http://geojson.org/geojson-spec.html#examples). Następnie dane zapisać w bazie MongoDB.
>
> Dla zapisanych danych przygotować co najmniej 6 różnych [geospatial queries](http://docs.mongodb.org/manual/reference/operator/query-geospatial/) (w tym, co najmniej po jednym, dla obiektów Point, LineString i Polygon).