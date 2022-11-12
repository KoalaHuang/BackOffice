<?
  //receive update data into t_stock
  //receive data in JSON format
  //return true if sucess otherwise return false
  session_start();

  header("Content-Type: application/json; charset=UTF-8");
  include "whatsapp.php";

  $str = file_get_contents('php://input');
  $obj = json_decode($str, false);
  // myLOG(__FILE__."\n"."str: ".print_r($str,true)." obj: ".print_r($obj,true)."  input: ".file_get_contents('php://input')." $_POST:".print_r($_POST,true));

  if ($obj == null){
    echo "NULL JSON result from:".$str;
    die;
  }

  include "connect_db.php";
  $errDB = "";
  $result = true;
  $c_date = date('Y-m-d');
  $c_user = $_SESSION["user"];
  $c_ordernum = "s".date_timestamp_get(date_create()).rand(0,9); //create ordernum with timestamp and 1 digit rand
  $c_cat = $obj->c;
  $stmt = $conn->prepare("UPDATE `t_stock` SET `c_qty`=`c_qty`+? WHERE `c_storage`=? AND `c_item`=?");
  $stmt->bind_param("iss", $c_qty,$c_storage,$c_item);
  $stmt_report = $conn->prepare("INSERT INTO `t_report`(`c_date`, `c_ordernum`, `c_item`, `c_cat`, `c_storage`, `c_qty`, `c_user`) VALUES (?,?,?,?,?,?,?)");
  $stmt_report->bind_param("sssssis",$c_date,$c_ordernum,$c_item,$c_cat,$c_storage,$c_qty,$c_user);
  $numRow = (int)($obj->r);
  // myLOG("obj: ".print_r($obj,TRUE)." numRow: ".$numRow);
  if (!$numRow) {
    $errDB = "JSON Para error".$obj->r;
    die;
  }else{
    $noticeMsg = array("Stock Updated");
    array_push($noticeMsg,$c_cat." stock updated.");
    for ($i = 1; $i <= $numRow; $i++) {
      $nameItem = "i".$i;
      $nameQty = "q".$i;
      $nameStorage = "l".$i;
      $c_item = $obj->$nameItem;
      $c_qty = $obj->$nameQty;
      $c_storage = $obj->$nameStorage;
      // myLOG("store: ".$c_store." item: ".$c_item." qty: ".$c_qty);
      array_push($noticeMsg,"( ".$c_item." )  ".sprintf("%+d",$c_qty)." @ ".$c_storage);
      $result = ($result && $stmt->execute());
      $result = ($result && $stmt_report->execute());
      // myLOG("stmt after ".print_r($stmt,TRUE));
    } // for
    // myLOG("result: ".print_r($result,TRUE));
    send_notice("S",$noticeMsg); //send email and whatsapp notice
    echo json_encode($result);
  } //if $numRow correct
  $stmt->close();
  $conn->close();
?>
