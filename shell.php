<?php
/**
 * Created by PhpStorm.
 * User: Arshad
 * Date: 02-Mar-19
 * Time: 2:02 PM
 */
require_once __DIR__.'/vendor/autoload.php';



//TODO use containers to manage dependency injection if project scales.

$flowersModel = new \Bloomon\models\Flowers();
$bouquets = new \Bloomon\models\Bouquet($flowersModel);


//Open stdin to read input
$file = fopen('php://stdin','r');
$line = "";
while(($line = fgets($file)))
{

    // clean extra spaces and line ends
    $line = trim($line);

    // If line matches bouquet spec, add to bouquets.
    if(preg_match_all("/^([A-Z])([S|L])(([0-9]+[a-z])+)([0-9]+)$/",$line))
        $bouquets->addBouquet($line);

    //If line matches flower spec, add to flowers
    else if(preg_match_all('/^([a-z])+([L|S])$/',$line))
        $flowersModel->addFlower($line);
}

// process bouquets
$bouquets->processBouquets();

// get processed bouquets
$results = $bouquets->getResults();

// if results are returned, print them
if(count($results) > 0) {
    foreach ($bouquets->getResults() as $bouquet)
        echo $bouquet . PHP_EOL;
}
else
    // if no results are returned, throw error message
    exit("Cant create Bouquets");



