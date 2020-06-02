<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// get database connection

// instantiate product object


$emblem = '{"r":"';
$emblem2 = '","e":"http:\/\/democracyforhk.net\/emblems\/emblems\/';
$retobj = '{';
// get posted data
$json = file_get_contents('php://input');
$data = json_decode($json);
// make sure data is not empty
if (!empty($data->players)) {


    // create the product
   $servername = "localhost";
    $username = "usernamehere";
    $password = "passwordhere";
    $dbname = "databasenamehere";

    //require_once "config.php";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    foreach ($data->players as $item) { //foreach element in $arr

        $sql = "SELECT pfp,exp FROM players WHERE uid='$item->uid'";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $rows = array();
            while ($r = mysqli_fetch_assoc($result)) {
                $rows[] = $r;
            }
            $new = str_replace(' ', '%20', $rows[0]['pfp']);
            $rank = intdiv(sqrt($rows[0]['exp']), 4);
            if ($rank > 42) {
                $rank = 42;
            }
            $retobj = $retobj . '"' . $item->playerIndex . '":' . $emblem . $rank . $emblem2  . $new . '"}';
            if ($item->playerIndex + 1 != count($data->players)) {
                $retobj = $retobj . ',';
            }
        } 
        else {
            $pfp = "default.png";
            $retobj = $retobj . '"' . $item->playerIndex . '":' . $emblem . '0' . $emblem2 . 'default.png" }';
            if ($item->playerIndex + 1 != count($data->players)) {
                $retobj = $retobj . ',';
            }
        }
    }
    $conn->close();
    $retobj = $retobj . '}';
    if (count($data->players) > 0) {

        // set response code - 201 created
        http_response_code(200);

        // tell the user
        echo $retobj;
        //echo '{"0":{"r":"0","e":"http:\/\/democracyforhk.net\/img\/panda.png"}}';
    }

    // if unable to create the product, tell the user
    else {

        // set response code - 503 service unavailable
        http_response_code(503);

        // tell the user
        echo json_encode(array("message" => "Unable to create product."));
    }
}
// tell the user data is incomplete
else {
    // set response code - 400 bad request
    http_response_code(400);
    // tell the user
    echo json_encode(array("message" => "API designed for ElDewrito. Does nothing in a browser."));
}
