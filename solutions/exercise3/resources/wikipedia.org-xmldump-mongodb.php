#!/bin/env php
<?php

/**
 * @copyright 2013 James Linden <kodekrash@gmail.com>
 * @author James Linden <kodekrash@gmail.com>
 * @link http://jameslinden.com/dataset/wikipedia.org/xml-dump-import-mongodb
 * @link https://github.com/kodekrash/wikipedia.org-xmldump-mongodb
 * @license BSD (2 clause) <http://www.opensource.org/licenses/BSD-2-Clause>
 * @rewritten 2015 Roman Józwiak <grucha.net@gmail.com>
 */

$dsname = 'mongodb://localhost/experiment';
$file = 'plwiki-20141228-pages-articles.xml';
$logpath = './';

/*************************************************************************/

date_default_timezone_set( 'Europe/Warsaw' );

function abort( $s ) {
	die( 'Aborting. ' . trim( $s ) . "\n" );
}

if( !is_file( $file ) || !is_readable( $file ) ) {
	abort( 'Data file is missing or not readable.' );
}

if( !is_dir( $logpath ) || !is_writable( $logpath ) ) {
	abort( 'Log path is missing or not writable.' );
}

$in = fopen($file, 'r');
if( !$in ) {
	abort( 'Unable to open input file.' );
}

$out = fopen( rtrim( $logpath, '/' ) . '/wikipedia.org_xmldump-' . date( 'YmdH' ) . '.log', 'w' );
if( !$out ) {
	abort( 'Unable to open log file.' );
}

try {
	$dc = new MongoClient(
        $dsname,
        array(
            'connect' => true,
            'socketTimeoutMS' => -1
        )
    );
	$ds = $dc->selectdb( trim( parse_url( $dsname, PHP_URL_PATH ), '/' ) );
} catch( MongoConnectionException $e ) {
	abort( $e->getmessage() );
}
$ds_page = new MongoCollection( $ds, 'wiki_articles' );

$time = microtime( true );

$start = false;
$chunk = null;
$count = 0;
$line = null;
while (($line = fgets($in)) !== false) {
	$line = trim( $line );
	if( $line == '<page>' ) {
		$start = true;
	}
	if( $start === true ) {
		$chunk .= $line;
	}
	if( $line == '</page>' ) {
		$start = false;
		$x = simplexml_load_string( $chunk );
		$chunk = $line = null;
		if( $x ) {
			$dpage = [ '_id' => (int)$x->id, 'title' => (string)$x->title ];
			if( $x->redirect ) {
				$y = (array)$x->redirect;
				$dpage['redirect'] = $y['@attributes']['title'];
			} else {
				$dpage['redirect'] = false;
			}
			if( $x->revision ) {
				$drev = [ 'id' => (int)$x->revision->id, 'parent' => (int)$x->revision->parentid ];
				$dpage['text'] = (string)$x->revision->text;
				$dpage['revision'] = $drev;
				unset( $drev );
			}
			try {
				if( $ds_page->save( $dpage ) ) {
					$count ++;
					$m = date( 'Y-m-d H:i:s' ) . chr(9) . $dpage['_id'] . chr(9) . $dpage['title'] . "\r\n";
					fwrite( $out, $m );
				}
			} catch( MongoCursorException $e ) {
				abort( $e->getmessage() );
			}
		}
	}
	$line = null;
}

fclose( $out );

echo "\n";

?>