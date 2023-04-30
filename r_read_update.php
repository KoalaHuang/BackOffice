<?
  /*add production record in t_production by 
    obj = {
	product: ""  //product
	version: 0, //recipe version
	recipe: 0, //recipe
	cat: "", //product category
    qty: 0 //quantity
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
  $c_version = $obj->version;
  $c_recipe = $obj->recipe;
  $c_cat = $obj->cat;
  $c_qty = $obj->qty;
  $c_user = $_SESSION["id"];
  $c_date = date('Y-m-d');

  try{
    $conn->autocommit(false);
    $conn->begin_transaction();
    $sql = "INSERT INTO `t_production` (`c_recipe`,`c_size`,`c_indate`,`c_inuser`) VALUES (".$c_recipe.",".$c_qty.",\"".$c_date."\",\"".$c_user."\")";
    $result = $conn->query($sql);
    if (!$result) throw new Exception('Error insert production record!');
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
