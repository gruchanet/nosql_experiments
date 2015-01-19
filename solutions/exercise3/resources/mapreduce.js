var connection = new Mongo();
var db = connection.getDB('experiment');

var mapFn = function () {
	// eliminacja niepotrzebnych ciągów znaków
	this.text = this.text.replace(/(<([^>]+)>)|\n|\\n|-align="?(right|center|left)"?|class="?wikitable"?|style=".+"|url = http:\/\/.+ |{{Cytuj (książkę|stronę) \||\[\[Plik\:[^\|]+\|/g, "");
	this.text = this.text.replace(/&nbsp;/g, " ");
	
	// wyłuskanie tego co potrzebne
	this.text = this.text.replace(/\[https?:\/\/[^ ]* (.*?)\]/g, "$1");
	
	var matches = this.text.match(/([a-zA-ZÀ-ÿąćęłńóśźżĄĆĘŁŃÓŚŹŻäöüßÄÖÜẞ]+)/g);
	
	// emit(new ObjectId(), text); // debug
	if (matches) {
		for (var i = 0; i < matches.length; i++) {
			emit(matches[i].toLowerCase(), 1);
		}
	}
	
	// zwolnienie zmiennych (pamięci)
	matches = undefined;
};

var reduceFn = function (word, counts) {
	return Array.sum(counts);
};

db.wiki_articles.mapReduce(
	mapFn,
	reduceFn,
	{ out: "wiki_words" }
);