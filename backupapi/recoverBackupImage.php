<?php
/* Path: backupapi/createBackupImage.php
MySQL tables
OAuth
id, type, grade, okey, expiration, access_to, commento
this is an API backupapi/getBackups.php?
OAuth: string (the OAuth key)
file_id: string (the file id)
The OAuth grade must be less than or equal to 0. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.backup.recoverImage set to true or
any of the roots set to true for example administration.backup.* or administration.* or *
-check OAuth key
- create an image of the current state calling createBackupImage.php
- save the image to Google Drive
- download the new image from Google Drive if isset $_GET['file_id'], otherwise get the file from the POST upload by the user
- proceed to recover the system
*/
//display errors
ini_set('display_errors', -1);

//set timeout to 30 seconds
set_time_limit(30);

//import once the database connection
require_once 'mysqli.php';
require_once __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

//check if the OAuth key is valid
require_once "utils.php";
$permission_needed = "administration.backup.recoverImage";

if (!checkOAuthPermissionFor($permission_needed)) {
    echo json_encode(array("exit" => "error", 'error' => "Invalid OAuth key for \"$permission_needed\". Contact the administrator to resolve this issue."));
    exit;
}

// use createBackupImage.php to create the image and set the backupname to "backup-automatico-ricovero"
$suppress_success_message = true;
$_GET['backupname'] = "backup-automatico-ricovero";
require 'createBackupImage.php';

//check if file_id is set
if (isset($_GET['file_id'])) {
    $recover_file_id = $_GET['file_id'];

    
    //now theoretically we got also the varibles of google drive from createBackupImage.php
    //but we need to check if they are set
    if (!isset($file->id)) {
        echo json_encode(array("exit" => "error", 'error' => "The file id is not set. Contact the administrator to resolve this issue."));
        exit;
    }
    if (!isset($filename)) {
        echo json_encode(array("exit" => "error", 'error' => "The file name is not set. Contact the administrator to resolve this issue."));
        exit;
    }

    // Get the backup content from Google Drive
    $file = $service->files->get($recover_file_id, array('alt' => 'media'));
    $backup_content = $file->getBody()->getContents();

    $backup_temp_filename = 'backup.zip';
    file_put_contents($backup_temp_filename, $backup_content);

    // Set the directory for the backup extraction
    $backup_dir = 'backup-extraction';

    // Create a new zip archive from the backup content
    $zip = new ZipArchive();
    if ($zip->open($backup_temp_filename) !== TRUE) {
        die('Error opening zip file');
    }

    // Extract the backup to the backup directory
    $zip->extractTo($backup_dir);
    $zip->close();
    unlink($backup_temp_filename);
} else if(isset($_FILES['backup']) && $_FILES['backup']['error'] === UPLOAD_ERR_OK) {
    $max_file_size = 100 * 1024 * 1024; // 100MB in bytes
    if($_FILES['backup']['size'] > $max_file_size) {
        echo json_encode(array("exit" => "error", 'error' => "The file is too big. The maximum file size is $max_file_size bytes."));
        exit;
    }
    //move the temo to backup.zip
    move_uploaded_file($_FILES['backup']['tmp_name'], 'backup.zip');

    $backup_dir = 'backup-extraction';

    // Create a new zip archive from the backup content
    $zip = new ZipArchive();
    if ($zip->open("backup.zip") !== TRUE) {
        die('Error opening zip file');
    }
    $zip->extractTo($backup_dir);
    $zip->close();
    unlink("backup.zip");
} else {
    //echo all the files uploaded
    echo json_encode(array("exit" => "error", 'error' => "The file id is not set nor any file was uploaded. Contact the administrator to resolve this issue."));
    exit;
}

// Get a list of all the SQL files in the backup directory
$sql_files = glob("$backup_dir/*.sql");

$recovered_tables = array();

// Loop through all the SQL files in the backup directory
foreach ($sql_files as $sql_file) {
    // Get the table name from the SQL file name
    $table_name = basename($sql_file, '.sql');

    // set SET FOREIGN_KEY_CHECKS=0;
    $mysqli->query("SET FOREIGN_KEY_CHECKS=0");

    // Read the SQL statements from the file
    $sql_statements = file_get_contents($sql_file);

    // Execute the SQL statements for the table
    if ($mysqli->multi_query($sql_statements)) {
        do {
            // fetch and discard the result set
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());
        //ad table to recovered_tables
        $recovered_tables[] = $table_name;

    } else {
        echo json_encode(array("exit" => "error", 'error' => "Error recovering table $table_name: " . $mysqli->error));
        exit;
    }
    $mysqli->query("SET FOREIGN_KEY_CHECKS=1");
}

// Delete the backup directory
array_map('unlink', glob("$backup_dir/*"));
rmdir($backup_dir);

// return success in json
echo json_encode(array("exit" => "success", "recovered_tables" => $recovered_tables));

?>