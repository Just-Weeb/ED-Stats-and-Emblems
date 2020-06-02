<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// get posted data
$json = file_get_contents('php://input');
$data = json_decode($json);

if (!empty($data->players)) {


     // create the product
    $servername = "localhost";
    $username = "usernamehere";
    $password = "passwordhere";
    $dbname = "databasenamehere";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    foreach ($data->players as $item) { //foreach element in $arr
        $sql = "SELECT kills, deaths, assists, exp, totalgames FROM players WHERE uid='$item->uid'";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $rows = mysqli_fetch_assoc($result);
            $kills = $rows['kills'];
            $newkills = $kills + $item->playerGameStats->kills;
            $deaths = $rows['deaths'] + $item->playerGameStats->deaths;
            $assists = $rows['assists'] + $item->playerGameStats->assists;
            $killbonus = intdiv($newkills, 2);
            $exp = $rows['exp'] + 10 + $killbonus;
            $totalgames = $rows['totalgames']+1;

            $sql1 = "UPDATE players SET username = '$item->name', tag = '$item->serviceTag', kills = $newkills, deaths = $deaths, assists = $assists, exp = $exp, totalgames = $totalgames WHERE uid = '$item->uid'";

            if ($conn->query($sql1) === TRUE) {
                //echo "New record created successfully";
            } else {
                echo "Error: " . $sql1 . "<br>" . $conn->error;
            }
        } else {
            $pfp = "default.png";

		$pfp = "default.png";
            $sql = "INSERT INTO players (username, tag, ip, uid, pfp, kills, deaths, assists, exp, kdratio, totalgames)
    VALUES ('$item->name', '$item->serviceTag', '$item->ip', '$item->uid', '$pfp', 0,0,0,0,0,1 )";

            if ($conn->query($sql) === TRUE) {
                //echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
    $conn->close();
    if (count($data->players) > 0) {

        // set response code - 201 created
        http_response_code(200);

        // tell the user
        echo "SUBMITTED";
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
