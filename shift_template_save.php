<?
  //save shift template to t_shittemp

  header("Content-Type: application/json; charset=UTF-8");

  $str = file_get_contents('php://input');
  $obj = json_decode($str, false);

  if ($obj == null){
    echo "NULL JSON result from:".$str;
    die;
  }
  include "connect_db.php";
  $result = true;
  $sql = "DELETE FROM `t_shifttemp` WHERE 1;";
  $conn->query($sql);

  $stmt = $conn->prepare("INSERT INTO `t_shifttemp`(`c_weekday`, `c_id`, `c_store`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins`) VALUES (?,?,?,?,?,?,?)");
  $stmt->bind_param("issssii",$c_weekday,$c_id,$c_store,$c_timestart,$c_timeend,$c_fullday,$c_totalmins);
  $length = count($obj);
  for ($idx = 0; $idx < $length; $idx++) {
    $c_weekday = $obj[$idx]->w;
    $c_id = $obj[$idx]->p;
    $c_store = $obj[$idx]->s;
    $c_timestart = $obj[$idx]->st;
    $c_timeend = $obj[$idx]->et;
    $c_fullday = $obj[$idx]->fd;
    $c_totalmins = $obj[$idx]->tm;

    $result = $stmt->execute();
  }

  $stmt->close();
  $conn->close();
  echo json_encode($result);
?>
