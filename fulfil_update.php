<?
  //receive fulfill data and update t_request and t_stock table
  //receive data in JSON format
  //return true if sucess otherwise return false
  session_start();

  header("Content-Type: application/json; charset=UTF-8");

  $str = file_get_contents('php://input');
  $obj = json_decode($str, false);
  // myLOG(__FILE__."\n"."str: ".print_r($str,true)." obj: ".print_r($obj,true)."  input: ".file_get_contents('php://input')." _POST:".print_r($_POST,true));

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
  $c_ordernum = "f".date_timestamp_get(date_create()).rand(0,9);
  $stmt_req = $conn->prepare("UPDATE `t_request` SET `c_qty`=IF(`c_qty`>?,`c_qty`-?,0) WHERE `c_store`=? AND `c_item`=?");
  $stmt_stk = $conn->prepare("UPDATE `t_stock` SET `c_qty`=`c_qty`-? WHERE `c_storage`=? AND `c_item`=?");
  $stmt_req->bind_param("iiss", $c_qty,$c_qty,$c_store,$c_item);
  $stmt_stk->bind_param("iss", $c_qty,$c_storage,$c_item);
  $stmt_report = $conn->prepare("INSERT INTO `t_report`(`c_date`, `c_ordernum`, `c_item`, `c_cat`, `c_store`, `c_storage`,`c_qty`,`c_user`) VALUES (?,?,?,?,?,?,?,?)");
  $stmt_report->bind_param("ssssssis",$c_date,$c_ordernum,$c_item,$c_cat,$c_store,$c_storage,$c_qty,$c_user);
  $numRow = (int)($obj->r);
  $numRow = (int)($obj->r);
  if (!$numRow) {
    $errDB = "JSON Para error".$obj->r;
    die;
  }else{
    $c_store = $obj->s;
    for ($i = 1; $i <= $numRow; $i++) {
      $nameItem = "i".$i;
      $nameQty = "q".$i;
      $nameStorage = "l".$i;
      $c_item = $obj->$nameItem;
      $c_qty = $obj->$nameQty;
      $c_storage = $obj->$nameStorage;
      // myLOG("store: ".$c_store." item: ".$c_item." storage: ".$c_storage." qty: ".$c_qty."date: ".$c_date."ordernum: ".$c_ordernum);
      $result = ($result && $stmt_req->execute());
      $result = ($result && $stmt_stk->execute());
      $result = ($result && $stmt_report->execute());
      // myLOG("stmt_report ".print_r($stmt_report,TRUE));
    } // for
    echo json_encode($result);
  } //if $numRow correct
  $stmt_req->close();
  $stmt_stk->close();
  $conn->close();
?>
