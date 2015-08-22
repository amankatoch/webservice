<?php
echo "testing";
$myFile = "testFile.txt";
$fh = fopen($myFile, 'w') or die("can't open file");

$stringData = "Some text in here\n";
fwrite($fh, $stringData);

?>
