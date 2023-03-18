<?php
/* Path: backupapi/getBackups.php
This is an API that displays the backups of the databases saved on Google Drive.

! ATTENZIONE: non bisogna dare a nessuno la mail del Google Drive API perché potrebbero condividergli una cartella con lo stesso nome e compromettere il sistema
==> una possibile soluzione in quel caso è impostare manualmente il folderId che si trova andando su Google Drive e vedendo la barra in alto
--- https://drive.google.com/drive/u/0/folders/FOLDER_ID_E_QUI

MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento

this is an API backupapi/getBackups.php?
OAuth: string (the OAuth key)

The OAuth grade must be less than or equal to 0. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.backup.view set to true or
any of the roots set to true for example administration.backup.* or administration.* or *.
*/

//import once the database connection
require_once 'mysqli.php';
require_once __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

//check if the OAuth key is valid
require_once "utils.php";
$permission_needed = "administration.backup.view";
if(!checkOAuthPermissionFor($permission_needed)) {
    echo json_encode(array("exit"=> "error", 'error' => "Invalid OAuth key for \"$permission_needed\". Contact the administrator to resolve this issue."));
    exit;
}

//check if /VPS_host/secrets/leonapp-drive.json exists
if (!file_exists('/VPS_host/secrets/leonapp-drive.json')) {
    echo json_encode(array("exit"=> "error", 'error' => 'Google Drive auth key (.json) not found. Contact the administrator to resolve this issue.'));
    exit;
}
putenv('GOOGLE_APPLICATION_CREDENTIALS=/VPS_host/secrets/leonapp-drive.json');

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes([Google_Service_Drive::DRIVE]);

$service = new Google_Service_Drive($client);

$folderName = 'backups';
$folderId = null;
$pageToken = null;

do {
    
    $query = "mimeType='application/vnd.google-apps.folder' and trashed=false and name='$folderName'";
    $optParams = [
        'q' => $query,
        'fields' => 'files(id, name)',
        'pageToken' => $pageToken,
    ];
    $results = $service->files->listFiles($optParams);

    if (count($results->getFiles()) == 0) {
        //echo a json object with error message
        echo json_encode(array("exit"=> "error", 'error' => 'Folder not found. Contact the administrator to resolve this issue.'));
    } else {
        foreach ($results->getFiles() as $file) {
            $folderId = $file->getId();
        }
    }

    $pageToken = $results->getNextPageToken();
} while ($pageToken != null);

if ($folderId == null) {
    echo json_encode(array("exit"=> "error", 'error' => 'Folder not found. Contact the administrator to resolve this issue.'));
    exit;
}

$files = [];

$pageToken = null;
do {
    $optParams = [
        
        'q' => "mimeType='application/zip' and trashed=false and parents='$folderId'",
        'fields' => 'nextPageToken, files(id, name, createdTime, size)',
        'pageToken' => $pageToken,
    ];
    $results = $service->files->listFiles($optParams);

    $files = array_merge($files, $results->getFiles());

    $pageToken = $results->getNextPageToken();
} while ($pageToken != null);

if (count($files) == 0) {
    if($suppress_success_message!=true) {
        echo json_encode(array("exit"=> "success", 'data' => []));
    }
    exit();
}

// echo json_encode(array("exit"=> "success", 'data' => $files));
//each file should have the field: file_id, display_name, upload_date
$filesArray = [];
foreach ($files as $file) {
    $fileId = htmlspecialchars($file->getId());
    $fileName = htmlspecialchars($file->getName());
    $lastModifiedTime = $file->getModifiedTime();
    if (empty($lastModifiedTime)) {
        $lastModifiedTime = $file->getCreatedTime();
    }
    $fileModifiedTime = htmlspecialchars(date('d/m/Y H:i:s', strtotime($lastModifiedTime)));
    $fileSize = htmlspecialchars($file->getSize());
    $filesArray[] = array('file_id' => $fileId, 'display_name' => $fileName, 'last_modified' => $fileModifiedTime, 'size' => $fileSize);

}
if($suppress_success_message!=true) {
    echo json_encode(array("exit"=> "success", 'data' => $filesArray));
}
/*
echo '<ul>';
foreach ($files as $file) {
    $fileId = htmlspecialchars($file->getId());
    $fileName = htmlspecialchars($file->getName());
    echo "<li><a href='https://drive.google.com/file/d/$fileId/view' target='_blank'>$fileName</a></li>";
}
echo '</ul>';*/

?>