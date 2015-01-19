var connection = new Mongo();
var db = connection.getDB('experiment');

// inicjalizacja "wstrzykiwania" wątków
var numThreads = 2; // z ilu wątków będziemy korzystali

Thread = function() {
	this.init.apply( this, arguments );
}
_threadInject( Thread.prototype );

ScopedThread = function() {
	this.init.apply( this, arguments );
}
ScopedThread.prototype = new Thread( function() {} );
_scopedThreadInject( ScopedThread.prototype );

// operacja MapReduce
var mapReduce = function (min, max) {
	db.runCommand(
		{
			mapReduce: "wiki_articles",
			map: function () {
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
			},
			reduce: function (word, counts) {
				return Array.sum(counts);
			},
			out: { reduce: "wiki_words" },
			query: { _id: { "$gte": min, "$lte": max } },
			jsMode: true 
		}
	)
};

var maxKey = db.wiki_articles.findOne({ $query:{}, $orderby:{ _id:-1 } })._id;
var inc = Math.floor(maxKey / numThreads);
var threads = [];

// wywołanie wątków z odpowiednimi parametrami granicznymi
for (var i = 0; i < numThreads; ++i) {
	var min = (i == 0) ? 0 : i * inc + 1;
	var max = i * inc + inc;
    print("min:" + min + " max:" + max);
	
    var t = new ScopedThread(mapReduce, min, max);
    threads.push(t);
    t.start()
}

for (var i in threads) {
    var t = threads[i];
    t.join();
    printjson(t.returnData());
}