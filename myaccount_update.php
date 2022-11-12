<?
  //Receive profile update into t_user table
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

  $c_name = $obj->n;
  $c_pwd = password_hash($obj->p,PASSWORD_DEFAULT);
  $stmt = $conn->prepare("UPDATE `t_user` SET `c_pwd`=? WHERE `c_name`=?");
  $stmt->bind_param("ss",$c_pwd,$c_name);
  $result = $stmt->execute();
  $stmt->close();
  $conn->close();
  echo json_encode($result);
?>
