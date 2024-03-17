<?
  //receive request data and update t_request table
  //receive data in JSON format
  //return true if sucess otherwise return false

  session_start();
  header("Content-Type: application/json; charset=UTF-8");
  include "whatsapp.php";

  $str = file_get_contents('php://input');
  $obj = json_decode($str, false);

  if ($obj == null){
    echo "NULL JSON result from:".$str;
    die;
  }

  include "connect_db.php";
  $errDB = "";
  $result = true;
  $c_date = date('Y-m-d');
  $c_user = $_SESSION["user"];
  $c_cat = $obj->c;
  $c_ordernum = "r".date_timestamp_get(date_create()).rand(0,9);
  $stmt = $conn->prepare("UPDATE `t_request` SET `c_qty`=`c_qty`+? WHERE `c_store`=? AND `c_item`=?");
  $stmt->bind_param("iss", $c_qty,$c_store,$c_item);
  $stmt_report = $conn->prepare("INSERT INTO `t_report`(`c_date`, `c_ordernum`, `c_item`, `c_cat`, `c_store`, `c_qty`, `c_user`) VALUES (?,?,?,?,?,?,?)");
  $stmt_report->bind_param("sssssis",$c_date,$c_ordernum,$c_item,$c_cat,$c_store,$c_qty,$c_user);
  $numRow = (int)($obj->r);
  if (!$numRow) {
    $errDB = "JSON Para error".$obj->r;
    die;
  }else{
    $c_store = $obj->s;
    $noticeMsg = array("New Request");
    array_push($noticeMsg,$c_cat." request from store ".$c_store);
    for ($i = 1; $i <= $numRow; $i++) {
      $nameItem = "i".$i;
      $nameQty = "q".$i;
      $c_item = $obj->$nameItem;
      $c_qty = $obj->$nameQty;
      array_push($noticeMsg,"( ".$c_item." )  ".$c_qty);
      $result = ($result && $stmt->execute());
      $result = ($result && $stmt_report->execute());
    } // for
    send_notice("S",$noticeMsg); //send email and whatsapp notice
    echo json_encode($result);
  } //if $numRow correct
  $stmt->close();
  $stmt_report->close();
  $conn->close();
?>
