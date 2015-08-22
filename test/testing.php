<?php
echo "testing";
// $myFile = "testFile.txt";
// $fh = fopen($myFile, 'r') or die("can't open file");

// $stringData = "Some text in here\n";
// fwrite($fh, $stringData);

// $stringData = "Some more text in here\n";
// fwrite($fh, $stringData);

// fclose($fh);
$myFile = realpath(".")."\\test\\testFile.txt";
$fh = fopen($myFile, 'w+') or die("can't open file");

$stringData = "Some text in here\n";
fwrite($fh, $stringData);
file_put_contents(realpath(".")."\\test\\aa.txt",'hi there');

?>

