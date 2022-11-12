<?
  //update sysetm configuration in t_config table
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
  $sql = "DELETE FROM `t_config` WHERE 1;";
  $conn->query($sql);

  $stmt = $conn->prepare("INSERT INTO `t_config`(`c_setup`, `c_subsetup`, `c_value`) VALUES (?,?,?)");
  $stmt->bind_param("sss",$c_setup,$c_subsetup,$c_value);
  $length = count($obj);
  for ($idx = 0; $idx < $length; $idx++) {
    $c_setup = $obj[$idx]->c_setup;
    $c_subsetup = $obj[$idx]->c_subsetup;
    $c_value = $obj[$idx]->c_value;
    $result = $stmt->execute();
  }

  $stmt->close();
  $conn->close();
  echo json_encode($result);
?>
