<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php

class BankovniRacun{
    public $vlasnik;
    private $stanje=0;

    public function uplati($iznos){
        $this->stanje+=$iznos;
    }
    public function getStanje(){
        return $this->stanje;
    }
}
$racun= new BankovniRacun();

$racun->vlasnik="Ivan Horvat";

echo "Vlasnik racuna je: ".$racun->vlasnik ."\n";

//uplata novaca
$racun->uplati(1000);
$racun->uplati(500);

echo "Stanje na računu: ". $racun->getStanje() ."\n";

//direktno pristupanje stanju nije moguće, samo preko metode
echo $racun->stanje;



?>
</body>
</html>