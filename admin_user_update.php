<?
  //receive request data and update t_user table
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

  if ($obj->n == 'addNewUser'){
    $stmt = $conn->prepare("INSERT INTO `t_user`(`c_name`, `c_pwd`, `c_id`, `c_workday`, `c_access`, `c_store`, `c_employee`) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssss", $c_name,$c_pwd,$c_id,$c_workday,$c_access,$c_store,$c_employee);
    $c_name = $obj->nn;
    $c_id = $obj->ni;
    $c_workday = $obj->nw;
    $c_access = $obj->na;
    $c_store = $obj->ns;
    $c_pwd = password_hash($obj->p,PASSWORD_DEFAULT);
    $c_employee = $obj->ne;
  }else{
    $c_newname = $obj->nn;
    $c_name = $obj->n;
    $c_id = $obj->ni;
    $c_workday = $obj->nw;
    $c_access = $obj->na;
    $c_store = $obj->ns;
    $c_employee = $obj->ne;
    if ($obj->p == "") {
      $stmt = $conn->prepare("UPDATE `t_user` SET `c_name`=?,`c_id`=?,`c_workday`=?,`c_access`=?, `c_store`=?, `c_employee`=? WHERE `c_name`=?");
      $stmt->bind_param("sssssss", $c_newname,$c_id,$c_workday,$c_access,$c_store,$c_employee,$c_name);
    }else{
      $c_pwd = password_hash($obj->p,PASSWORD_DEFAULT);
      $stmt = $conn->prepare("UPDATE `t_user` SET `c_name`=?,`c_id`=?,`c_workday`=?,`c_access`=?, `c_pwd`=?, `c_store`=?, `c_employee`=? WHERE `c_name`=?");
      $stmt->bind_param("ssssssss", $c_newname,$c_id,$c_workday,$c_access,$c_pwd,$c_store,$c_employee,$c_name);
    }
  }
  $result = $stmt->execute();
  $stmt->close();
  $conn->close();
  echo json_encode($result);
?>
