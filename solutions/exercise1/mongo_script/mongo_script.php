<?php

include_once('Mongo\Adapter\ConnectionAdapter.php');
include_once('Stats\ArrayStats.php');
include_once('Stats\ArrayStatsService.php');
include_once('StopWatch\StopWatch.php');

use Mongo\Adapter\ConnectionAdapter as MongoAdapter;
use Stats\ArrayStats as TagsStats;
use Stats\ArrayStatsService as TagsStatsService;
use StopWatch\StopWatch;

/* Config */
$db = 'experiment';
$coll = 'train';

/* Sets charset encoding **/
header('Content-Type: text/html; charset=utf-8');

try {
    $mongoAdapter = new MongoAdapter('localhost', 27017, $db);
    $database = $mongoAdapter->getDatabase();
    $collection = $database->selectCollection($coll);

    // hols all tags occurences
    $tagsOccurences = array();
    // holds loop iterations
    $loopCounter = 0;

    // cursor definition
    // snapshot mode is ON to isolate cursor write operations,
    // eg. without that option cursor iterated same document more than once
    $cursor = $collection->find()->snapshot();

    $stopWatch = new StopWatch();

    $stopWatch->startTimer();
    // iterate over documents in collection
    foreach ($cursor as $id => $document) {
        // convert tags to array
        if (!empty($document['Tags'])) {
            if (!is_array($document['Tags'])) {
                $document['Tags'] = explode(' ', $document['Tags']);
            }
        } else {
            $document['Tags'] = array();
        }

        // count tags occurences
        foreach ($document['Tags'] as $tag) {
            if (isset($tagsOccurences[$tag])) {
                $tagsOccurences[$tag]++;
            } else {
                $tagsOccurences[$tag] = 1;
            }
        }

        // save document
        $collection->save($document);

        // to check if it doesn't iterate same element TWICE
        $loopCounter++;
    }
    $stopWatch->stopClick('convertion_end');

    $tagsStats = new TagsStats($tagsOccurences);
    $stopWatch->stopTimer();

    echo 'Uruchomiono na bazie <b>' . $db . '</b> dla kolekcji <b>' . $coll .  '</b><br /><br />';
    echo '<b>Całkowity czas wykonania (brutto):</b> ' . $stopWatch->getTimeDifference() . ' sek. <br />';
    echo '<b>Czas wykonania samego skryptu (netto):</b> ' . ($stopWatch->getStopClickTime('convertion_end') - $stopWatch->getStartTime()) . ' sek. <br />';
    echo '<b>Ilość wszystkich tagów:</b> ' . $tagsStats->getAllKeysCounter() . '<br />';
    echo '<b>Ilość różnych tagów:</b> ' . $tagsStats->getDifferentKeysCounter() . '<br />';
    echo '<b>Ilość unikalnych tagów (1-time):</b> ' . $tagsStats->getUniqueKeysCounter(). '<br />';
    echo '<b>Najbardziej popularne tagi:</b> ' . TagsStatsService::keysToString($tagsStats->getTopKeys(3), ', ', true) . '<br />';
    echo '<b>Przykładowy unikalny tag:</b> ' . $tagsStats->getRandomUniqueKey() . '<br />';
    echo '<b>Skrypt wykonano dla:</b> ' . $loopCounter . " dokumentów \n";
} catch (Exception $e) {
    echo $e->getMessage();
}
