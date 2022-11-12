<?
  //update product item in t_stock table
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

  if ($obj->i == 'addNewItem'){
    $sql_stock = "INSERT INTO `t_stock`(`c_storage`, `c_item`, `c_cat`, `c_qty`) VALUES (\"".$obj->ns."\",\"".$obj->ni."\",\"".$obj->c."\",0);";
  }else{
    $sql_stock = "UPDATE `t_stock` SET `c_item`=\"".$obj->ni."\",`c_storage`=\"".$obj->ns."\" WHERE `c_storage`=\"".$obj->s."\" AND `c_item`=\"".$obj->i."\";";
  }
  $result = $conn->query($sql_stock);
  $conn->close();
  echo json_encode($result);
?>
