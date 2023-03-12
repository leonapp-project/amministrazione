<?php

// Create a new ZIP archive object
$zip = new ZipArchive();

// Set the name of the ZIP archive
$filename = "test112.zip";

// Create a new ZIP archive, or open an existing one for writing
if ($zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    exit("Cannot create ZIP archive <$filename>\n");
}

// Add a file to the ZIP archive using a string as its contents
$zip->addFromString("testfilephp.txt", "#1 This is a test string added as testfilephp.txt.\n");

// Add another file to the ZIP archive using a string as its contents
$zip->addFromString("testfilephp2.txt", "#2 This is a test string added as testfilephp2.txt.\n");

// Add a file to the ZIP archive using a file on disk
$thisdir = dirname(__FILE__);
//$zip->addFile($thisdir . "/too.php", "testfromfile.php");

// Close the ZIP archive
$zip->close();

// Set the headers to make the file download
header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=$filename");
header("Content-length: " . filesize($filename));
header("Pragma: no-cache");

// Output the contents of the ZIP archive to the browser
readfile($filename);

// Delete the ZIP archive file
unlink($filename);
?>
