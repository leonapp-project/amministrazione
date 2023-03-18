<?php
// path: api/getStudentList.php
/* MySQL tables

utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento
*/
/* 
this is an API api/getStudentList.php?
OAuth: 32 chars
sortBy: "nome" --> first_name, "cognome" --> last_name
sort: "ASC" or "DESC"
limit: 25 (default)
startBy: 0 (default)

and optional
"nome": all default, if set, filter by first_name LIKE %nome%
"cognome": all default, if set, filter by last_name LIKE %cognome%
"sezione": all default, if set, filter by class_section LIKE %sezione%
"anno": all default, if set, filter by class_number LIKE %anno%

The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp). 
The access_to, which is a JSON, should have the key/permission administration.studenti.getList set to true or
any of the roots set to true for example administration.studenti.* or administration.* or *.
*/

//import once the database connection
require_once 'mysqli.php';
header('Content-Type: application/json');

//get the OAuth token
$OAuth = $_GET['OAuth'];
//if the OAuth token is not set, get it from the cookie
if (!isset($OAuth)) {
    $OAuth = $_COOKIE['OAuth_key'];
}

//get the sorting parameters
$sortBy = $_GET['sortBy'];
$sort = $_GET['sort'];

//get the pagination parameters
$limit = $_GET['limit'];
$startBy = $_GET['startBy'];

//get the optional filters
$nome = $_GET['nome'];
$cognome = $_GET['cognome'];
$sezione = $_GET['sezione'];
$anno = $_GET['classe'];

//check if the OAuth token is valid
$stmt = $mysqli->prepare("SELECT * FROM OAuth WHERE okey = ?");
$stmt->bind_param("s", $OAuth);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}
//the OAuth token is valid
$OAuth = $result->fetch_assoc();
if ($OAuth['grade'] > 1) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}
//check if the current timestamp is greater than the expiration timestamp
if (time() > strtotime($OAuth['expiration'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token expired"));
    exit();
}
//check if the access_to is valid
$access_to = json_decode($OAuth['access_to'], true);
if (!isset($access_to['administration.studenti.getList']) && !isset($access_to['administration.*']) && !isset($access_to['administration.studenti.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not allowed to administration.studenti.getList"));
    exit();
}

//check if the sorting parameters are valid
if ($sortBy !== 'nome' && $sortBy !== 'cognome') {
    //the sorting parameters are not valid
    echo json_encode(array("exit" => "error", "error" => "Sorting parameters not valid"));
    exit();
}
if ($sort !== 'ASC' && $sort !== 'DESC') {
    //the sorting parameters are not valid
    echo json_encode(array("exit" => "error", "error" => "Sorting parameters not valid"));
    exit();
}

//check if the pagination parameters are valid
if (!is_numeric($limit) || !is_numeric($startBy)) {
    //the pagination parameters are not valid
    echo json_encode(array("exit" => "error", "error" => "Pagination parameters not valid"));
    exit();
}

//check if the optional filters are valid
if (!is_string($nome) || !is_string($cognome)) {
    //the optional filters are not valid
    echo json_encode(array("exit" => "error", "error" => "Optional filters not valid"));
    exit();
}

//prepare the query
$query = "SELECT * FROM utenti WHERE 1";
if ($nome !== '') {
    $query .= " AND first_name LIKE '%" . $nome . "%'";
}
if ($cognome !== '') {
    $query .= " AND last_name LIKE '%" . $cognome . "%'";
}
if ($sezione !== '') {
    $query .= " AND class_section LIKE '%" . $sezione . "%'";
}
if ($anno !== '') {
    $query .= " AND class_number LIKE '%" . $anno . "%'";
}
//convert the sorting parameters
if ($sortBy === 'nome') {
    $sortBy = 'first_name';
}
if ($sortBy === 'cognome') {
    $sortBy = 'last_name';
}
$query .= " ORDER BY " . $sortBy . " " . $sort . " LIMIT " . $startBy . ", " . $limit;

//execute the query
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    //no students found
    echo json_encode(array("exit" => "error", "error" => "No students found"));
    exit();
}
//students found
$students = array();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$results_number = count($students);

//count how many students would have satisfied the previous query without the LIMIT
$query = "SELECT COUNT(*) FROM utenti WHERE 1";
if ($nome !== '') {
    $query .= " AND first_name LIKE '%" . $nome . "%'";
}
if ($cognome !== '') {
    $query .= " AND last_name LIKE '%" . $cognome . "%'";
}
if ($sezione !== '') {
    $query .= " AND class_section LIKE '%" . $sezione . "%'";
}
if($anno !== '') {
    $query .= " AND class_number LIKE '%" . $anno . "%'";
}
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$total_students = $result->fetch_assoc()['COUNT(*)'];

//set the header so this is a json answer
echo json_encode(array("exit" => "success", "results"=> $results_number, "students" => $students, "total_students" => $total_students));
exit();

/* create a sample URL to test this API
https://amministrazione.leonapp.it/api/getStudentList.php?sortBy=nome&sort=ASC&limit=25&startBy=0&nome=&cognome=&sezione=&anno=
output: {"exit":"success","students":[{"id":1,"first_name":"Daniele","last_name":"Lin","email":"daniele.lin@studentilicei.leonexiii.it","last_login":"2023-02-26 12:46:05.128475","date_joined":"2023-02-26 12:44:12.683224","class_branch":"","class_section":"","can_buy_tickets":0,"username":"daniele","class_number":""},{"id":2,"first_name":"Niccolo","last_name":"Pagano","email":"niccolo.pagano@studentilicei.leonexiii.it","last_login":"2023-02-27 16:57:29.771402","date_joined":"2023-02-27 10:56:01.782840","class_branch":"CL","class_section":"A","can_buy_tickets":0,"username":"niccolo","class_number":"4"}]}
    */

?>