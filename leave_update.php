<?
  //Update t_leave table with leave application or cancellation
  //receive data in JSON format
  //return true if sucess otherwise return false
  session_start();

  header("Content-Type: application/json; charset=UTF-8");

  $str = file_get_contents('php://input');
  $obj = json_decode($str, false);

  if ($obj == null){
    echo "NULL JSON result from:".$str;
    die;
  }

  include "connect_db.php";
  $result = true;

  if ($obj->act == 1){ //apply leave
    $stmt = $conn->prepare("INSERT INTO `t_leave`(`c_id`, `c_leavetype`, `c_count`, `c_from`, `c_to`, `c_leavetime`) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssdsss", $c_id,$c_leavetype,$c_count,$c_from,$c_to,$c_leavetime);
    $c_id = $obj->id;
    $c_leavetype = $obj->type;
    $c_count = $obj->count;
    $c_from = $obj->from;
    $c_to = $obj->to;
    $c_leavetime = $obj->kind;
  }else{//cancel leave
    $c_id = $obj->id;
    $c_from = $obj->from;
    $stmt = $conn->prepare("DELETE FROM `t_leave` WHERE `c_id`=? AND `c_from`=?");
    $stmt->bind_param("ss", $c_id,$c_from);
  }
  $result = $stmt->execute();
  $stmt->close();
  $conn->close();
  echo json_encode($result);
?>
