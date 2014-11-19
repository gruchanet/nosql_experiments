<?php

include_once('Mongo\Adapter\ConnectionAdapter.php');

use Mongo\Adapter\ConnectionAdapter as MongoAdapter;

/* Config */
$db = 'experiment';
$coll = 'sites';

/* Sets charset encoding **/
header('Content-Type: text/html; charset=utf-8');

try {
    // makes connection
    $mongoAdapter = new MongoAdapter('localhost', 27017, $db);
    $database = $mongoAdapter->getDatabase();
    $collection = $database->selectCollection($coll);

    /** Aggregation 1 **/
    $topRefIPs = $collection->aggregate(array(
            array('$project' => array('_id' => 0, 'GlobalRank' => 1, 'Domain' => 1, 'RefIPs' => '$RefIPs')),
            array('$sort' => array('RefIPs' => -1)),
            array('$limit' => 10)
        )
    );

    printResults('Aggregation 1 - Top 10 RefIPs', $topRefIPs);

    /** Aggregation 2 **/
    $topTLD = $collection->aggregate(array(
            array('$group' => array('_id' => array('TLD' => '$TLD'))),
            array('$sort' => array('count' => -1)),
            array('$limit' => 10)
        )
    );

    printResults('Aggregation 2 - Top 10 TLD', $topTLD);

    /** Aggregation 3 **/
    $ranking = array(
        'ups' => array(
            $collection->aggregate(
                array(
                    array('$group' => array('_id' => array(
                        'domain' => '$Domain',
                        'global_rank' => array('current' => '$GlobalRank', 'previous' => '$PrevGlobalRank'),
                        'difference' => array('$subtract' => array('$PrevGlobalRank', '$GlobalRank'))
                    ))),
                    array('$match' => array('_id.difference' => array('$gt' => 0))),
                    array('$sort' => array('_id.difference' => -1)),
                    array('$limit' => 10)
                ),
                array('allowDiskUse' => true)
            )
        ),
        'drops' => array(
            $collection->aggregate(
                array(
                    array('$match' => array('PrevGlobalRank' => array('$gt' => 0))),
                    array('$group' => array('_id' => array(
                        'domain' => '$Domain',
                        'global_rank' => array('current' => '$GlobalRank', 'previous' => '$PrevGlobalRank'),
                        'difference' => array('$subtract' => array('$GlobalRank', '$PrevGlobalRank'))
                    ))),
                    array('$match' => array('_id.difference' => array('$gt' => 0))),
                    array('$sort' => array('_id.difference' => -1)),
                    array('$limit' => 10)
                ),
                array('allowDiskUse' => true)
            )
        )
    );

    printResults('Aggregation 3 - TOP 10 - Biggest drops...', $ranking['drops']);
    printResults('... and highs.', $ranking['ups']);

    /** Aggregation 4 **/
    $digitsOnlyTLD = $collection->aggregate(array(
            array('$match' => array('Domain' => new MongoRegex('/^(\d+\.)+[a-z]+$/'))),
            array('$group' => array('_id' => null, 'count' => array('$sum' => 1)))
        )
    );

    printResults('Aggregation 4 - digits only TLD', $digitsOnlyTLD);

} catch (Exception $e) {
    echo $e->getMessage();
}

function printResults($title = '', $results)
{
    echo '<h3>' . $title . '</h3>';
    echo '<pre>';
    print_r($results);
    echo '</pre>';
    echo '<hr>';
}