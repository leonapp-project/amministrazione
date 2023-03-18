<?php
/* Path: backupapi/createBackupImage.php
MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento

this is an API backupapi/getBackups.php?
OAuth: string (the OAuth key)

The OAuth grade must be less than or equal to 0. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.backup.createImage set to true or
any of the roots set to true for example administration.backup.* or administration.* or *.
*/
require_once 'mysqli.php';

header('Content-Type: application/json');

require_once "utils.php";
$permission_needed = "administration.backup.createImage";
if(!checkOAuthPermissionFor($permission_needed, null, 0)) {
    echo json_encode(array("exit"=> "error", 'error' => "Invalid OAuth key for \"$permission_needed\". Contact the administrator to resolve this issue."));
    exit;
}

// Get a list of all the tables in the database
$tables = array();
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Set the backup filename and directory
$date = date('d-m-Y H:i');

if(!isset($_GET['backupname'])){
    $filename = "backup-{$date}.zip";
}else{
    $filename = $_GET['backupname'];
}
$dir = 'backups';

// Set the backup info file name and contents
$infoFilename = 'backup-info.yml';
//$infoContents = "backup_time: $date\nbackup_info: Leonapp CC2023 Niccolo Pagano e Daniele Lin. Tutti i diritti riservati. Vedi GitHub.com/leonapp-project per maggiori informazioni";
//set the yml file contents like this
/*
# Questo file è stato generato automaticamente da backupapi/createBackupImage.php
# e contiene informazioni sul backup e le tabelle che contiene.
# Tutti i backups sono compressi in un file .zip e caricati su Google Drive e possono essere utilizzati per ripristinare il database da amministrazione.leonapp.it/sistema.php
name: $filename
backup_time: $date
backup_info: Leonapp CC2023 Niccolo Pagano e Daniele Lin. Tutti i diritti riservati. Vedi GitHub.com/leonapp-project per maggiori informazioni
table_list:
    - utenti
    - OAuth
    etc...
*/
$infoContents = "# Questo file è stato generato automaticamente da backupapi/createBackupImage.php\n";
$infoContents .= "# e contiene informazioni sul backup e le tabelle che contiene.\n";
$infoContents .= "# Tutti i backups sono compressi in un file .zip e caricati su Google Drive e possono essere utilizzati per ripristinare il database da amministrazione.leonapp.it/sistema.php\n";
$infoContents .= "name: $filename\n";
$infoContents .= "backup_time: $date\n";
$infoContents .= "backup_info: Leonapp CC2023 Niccolo Pagano e Daniele Lin. Tutti i diritti riservati. Vedi GitHub.com/leonapp-project per maggiori informazioni\n";
$infoContents .= "table_list:\n";
foreach ($tables as $table) {
    $infoContents .= "    - $table\n";
}


// Create a new zip archive
$zip = new ZipArchive();
if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
    die('Error creating zip file');
}

// Add the backup info file to the zip archive
$zip->addFromString($infoFilename, $infoContents);

// Loop through all the tables in the database
foreach ($tables as $table) {
    // Get the table data
    $result = $mysqli->query("SELECT * FROM $table");
    $numRows = $result->num_rows;

    // Generate the SQL for the table
    $sql = "DROP TABLE IF EXISTS `$table`;\n";
    $row = $mysqli->query("SHOW CREATE TABLE $table")->fetch_assoc();
    $sql .= $row['Create Table'] . ";\n";

    // Add the SQL to the zip archive
    $zip->addFromString("$table.sql", $sql);

    // Loop through all the rows in the table and add them to the SQL
    for ($i = 0; $i < $numRows; $i++) {
        $row = $result->fetch_assoc();
        $sql .= "INSERT INTO `$table` VALUES (";
        foreach ($row as $value) {
            $sql .= "'" . $mysqli->real_escape_string($value) . "',";
        }
        $sql = rtrim($sql, ',') . ");\n";
    }

    // Add the SQL to the zip archive
    $zip->addFromString("$table.sql", $sql);
}


// Close the zip archive
$zip->close();

//require the google api
require_once 'vendor/autoload.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS=/VPS_host/secrets/leonapp-drive.json');

//upload the file to Google Drive
$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes([Google_Service_Drive::DRIVE]);

$service = new Google_Service_Drive($client);

$folderId = "1H40Lge_gbHPY88upl3yXl95ccx4lTebM";

$fileMetadata = new Google_Service_Drive_DriveFile(array(
    'name' => $filename,
    'parents' => array($folderId)
));
$content = file_get_contents($filename);
$file = $service->files->create($fileMetadata, array(
    'data' => $content,
    'mimeType' => 'application/zip',
    'uploadType' => 'multipart',
    'fields' => 'id'
));

//delete the file from the server
unlink($filename);

// return success in json
if($suppress_success_message!=true) {
    echo json_encode(array("exit" => "success", "message" => "Backup created successfully", "file_id" => $file->id));
}
