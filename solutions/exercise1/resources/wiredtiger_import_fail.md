    ﻿Measure-Command -Expression {mongoimport --db experiment --collection train --type csv --file ~\Downloads\Train\Train.csv --headerline}
    ...
    2014-11-19T02:35:59.771+0100    Progress: 520000 documents inserted...
    2014-11-19T02:36:00.857+0100    error inserting documents: WiredTigerRecordStore::insertRecord 12: Not enough space
    2014-11-19T02:36:00.858+0100    Progress: 530000 documents inserted...
    2014-11-19T02:36:02.689+0100    Progress: 540000 documents inserted...
    2014-11-19T02:36:30.393+0100    error inserting documents: WSARecv tcp 127.0.0.1:1049: Istniejące połączenie zostało gwałtownie zamknięte przez zdalnego hosta.
    2014-11-19T02:36:30.395+0100    Progress: 550000 documents inserted...
    2014-11-19T02:36:30.550+0100    error inserting documents: WSARecv tcp 127.0.0.1:1049: Istniejące połączenie zostało gwałtownie zamknięte przez zdalnego hosta.
    2014-11-19T02:36:30.551+0100    Progress: 560000 documents inserted...
    ...

<h4>Konfiguracja <b>mongodb.conf</b></h4>

<pre>
storage:
    dbPath: "D:/MongoDB_2.8.0-rc0/data/db"
    engine: "wiredtiger"
    wiredtiger:
        collectionConfig: "block_compressor="
systemLog:
    destination: file
    path: "D:/MongoDB_2.8.0-rc0/logs/mongodb.log"
</pre>
