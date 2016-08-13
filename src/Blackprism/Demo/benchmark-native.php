<?php

$cas = 1;

$data_string = '{ "type": "city", "name": "Paris", "country": { "type": "country", "name": "France" } }';

if ($cas === 1) {
    $data = json_decode($data_string, true);

    $start = microtime(true);
    $cluster = new \CouchbaseCluster('couchbase://localhost');
    $bucket = $cluster->openBucket('odm-test');

    $bucket
        ->mutateIn('odwalla-juice1')
        ->upsert('name.firstname', 'Toto')
        ->upsert('name.lastname', 'Toto.famille')
        ->execute();
exit;
    for ($id = 10010; $id <= 20000; $id++) {
        $bucket->insert((string)$id, $data, []);
    }
// fermeture des ressources
    $end = microtime(true);

    echo ($end - $start) . "\n";
}
// time 0.45823001861572


if ($cas === 2) {

    $start = microtime(true);
    $cluster = new \CouchbaseCluster('couchbase://localhost');
    $bucket = $cluster->openBucket('benchmark');
    for ($id = 20010; $id <= 30000; $id++) {
        $bucket->query(\CouchbaseN1qlQuery::fromString('INSERT INTO benchmark (KEY, VALUE) VALUES ("' . $id . '", ' . $data_string . ')'));
    }
// fermeture des ressources
    $end = microtime(true);

    echo ($end - $start) . "\n";
}
