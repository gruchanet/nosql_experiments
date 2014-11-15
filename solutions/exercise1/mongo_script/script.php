<?php
/* Config */
$database = 'experiment';
$collection = 'train_test';

/* Sets charset encoding **/
header('Content-Type: text/html; charset=utf-8');

/* Sets limitless execution timeout */
set_time_limit(0);

try {
    // make connection
    $connection = new MongoClient();

    // select database
    $db = $connection->selectDB($database); // $connection->$database

    // select collection
    $coll = $db->selectCollection($collection); // $connection->$collection

    // variable that holds all tags
    $tags = array();

    // test counter, to check if cursor doesn't iterate same element TWICE
    $testCounter = 0;

    // define the cursor
    // snapshot mode is ON to isolate cursor write operations,
    // eg. without that option cursor iterated same document more than once
    $cursor = $coll->find()->snapshot();

    $time_start = getmicrotime();
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
            if (isset($tags[$tag])) {
                $tags[$tag]++;
            } else {
                $tags[$tag] = 1;
            }
        }

        // save document
        $coll->save($document);

        // to check if it doesn't iterate same element TWICE
        $testCounter++;
    }
    $convert_end = getmicrotime();

    // tags counters definition
    $tagsCounters = array(
        'all' => 0,
        'different' => count($tags)
    );
    $uniqueTags = array();

    foreach ($tags as $tag => $counter) {
        // accumulate ALL counter
        $tagsCounters['all'] += $counter;

        // accumulate UNIQUE tags
        if ($counter === 1) {
            $uniqueTags[] = $tag;
        }
    }

    // sort DESC to get most popular tags
    arsort($tags);

    $time_end = getmicrotime();

    echo 'Uruchomiono na bazie <b>' . $database . '</b> dla kolekcji <b>' . $collection .  '</b><br /><br />';
    echo '<b>Całkowity czas wykonania (brutto):</b> ' . getExecutionTime($time_start, $time_end) . ' sek. <br />';
    echo '<b>Czas wykonania samego skryptu (netto):</b> ' . getExecutionTime($time_start, $convert_end) . ' sek. <br />';
    echo '<b>Ilość wszystkich tagów:</b> ' . $tagsCounters['all'] . '<br />';
    echo '<b>Ilość różnych tagów:</b> ' . $tagsCounters['different'] . '<br />';
    echo '<b>Ilość unikalnych tagów (1-time):</b> ' . count($uniqueTags) . '<br />';
    echo '<b>Najbardziej popularne tagi:</b> ' . implode(", ", array_map(function ($key, $value) { return $value . '(' . $key . ')'; }, array_slice($tags, 0, 3), array_slice(array_keys($tags), 0, 3))) . '<br />';
    echo '<b>Przykładowy unikalny tag:</b> ' . $uniqueTags[array_rand($uniqueTags)] . '<br />';
    echo '<b>Skrypt wykonano dla:</b> ' . $testCounter . " dokumentów \n";
} catch (Exception $e) {
    echo $e->getMessage();
}

function getmicrotime() {
    return array_sum(explode(' ', microtime(true)));
}

function getExecutionTime($start, $end, $rounding = 10) {
    return round(($end - $start), $rounding);
}