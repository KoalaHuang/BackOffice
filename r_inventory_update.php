<?
  /*Check out inventory or add/remove inventory request
    obj = {
	    product: ""  //product
      act: 1 //1 check out inventory, 2 add request, 3 remove request
    }
  */
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
  $c_product = $obj->product;
  $c_user = $_SESSION["id"];
  $c_date = date('Y-m-d');

  try{
    $conn->autocommit(false);
    $conn->begin_transaction();
    switch ($obj->act) {
      case 1:
        $sql = "UPDATE `t_production` SET `c_outdate`='".$c_date."',`c_outuser`='".$c_user."' WHERE c_recipe IN (SELECT c_recipe FROM t_recipe WHERE t_recipe.c_product = '".$c_product."') AND c_outdate IS NULL LIMIT 1";
        $err = "Error check out inventory for ".$c_product."!";
        break;
      case 2:
        $sql = "UPDATE `t_product` SET `c_plan`=1 WHERE `c_product`='".$c_product."'";
        $err = "Error adding request for ".$c_product."!";
        break;
      case 3:
        $sql = "UPDATE `t_product` SET `c_plan`=0 WHERE `c_product`='".$c_product."'";
        $err = "Error removing request for ".$c_product."!";
        break;
    }
    $result = $conn->query($sql);
    if (!$result) throw new Exception($err);
    if ($conn->commit()){
      $result = true;
    }else{
      throw new Exception('Error commit database changes!');
    }
  } catch (Exception $e){
    $conn->rollback();
    echo json_encode($e->getMessage());
  }
  $conn->close();  
  echo json_encode($result);
?>
