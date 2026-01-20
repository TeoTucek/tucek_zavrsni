<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

    class Kutija{
        public $boja;
    }
    $kutija1 = new Kutija();
    $kutija1->boja="crvena";

    echo $kutija1->boja;


    ?>
    
</body>
</html>