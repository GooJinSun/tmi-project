<?php
# Insert new comments to DB & Get all data from DB.

include 'login.php';
//Contains $myservername, $myusername, $mypassword, $mydbname
$servername = $myservername;
$username = $myusername;
$password = $mypassword;
$dbname = $mydbname;

header("Content-Type:application/json; charset=utf-8");


// Create connection
$link = mysql_connect($servername, $username, $password)
    OR die(mysql_error());
$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$comment = $_POST['comment'];
if ($comment !=Null ){
	$ip = "";
	if ($_POST['ip'] != Null){
		$ip = $_POST['ip'];
	}
    $need_block = False;
    if (strpos($comment, '<') !== false) {
        $need_block = True;
    }
    if (strpos($comment, '>') !== false) {
        $need_block = True;
    }
    if (strpos($comment, 'http://') !== false) {
        $need_block = True;
    }
    if (strpos($comment, 'https://') !== false) {
        $need_block = True;
    }
    if (!$need_block){
        $comment = iconv("UTF-8","EUC-KR", $comment);
	   $sql = sprintf("INSERT INTO Comments (`comment`,`ip`) VALUES ('%s','%s')",
                    mysql_real_escape_string($comment,$link),
                    mysql_real_escape_string($ip,$link));
	   $conn->query($sql);
    }
}

#
$sql = "SELECT * FROM `Comments` ORDER BY `id` ASC";
$result =  $conn->query($sql);

//euc-kr to utf-8
function e2u($n)
{
    return iconv("EUC-KR","UTF-8", $n);
}


if ($result->num_rows > 0) {
	$rows = array();
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	// $t = new stdClass();
     //    $t->id = $row["id"];
     //    $t->comment = $row["comment"];
     //    $t->timestamp = $row["timestamp"];
     //    $t->user = $row["user"];
     //    $o[] = $t;
    	$rows[] = array_map('e2u',$row);
        // unset($t);
    }
    #print_r($rows);
    echo json_encode($rows);//,JSON_UNESCAPED_UNICODE);
} else {
    echo "[]";
}
$conn->close();
?>