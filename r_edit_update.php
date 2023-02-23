<?
  /*Update t_recipe and t_recipelib table by 
    obj = {
	product: "",
	version: 0,
	recipe: 0,
	cat: "",
	arrayItemM: [], //recipe item
	arrayItemU: [], //recipe unit
	arrayItemQ: [], //recipe qty
	arrayItemB: [], //1: base, 2: raw material
	act: 0 //1: update, 2: insert
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

  if ($obj->act < 3){ 
    $c_name = $obj->name;
    $c_unit = $obj->unit;
    $c_supplier = $obj->supplier;
    $c_cost = $obj->cost;
    $c_moq = $obj->moq;
    if ($obj->act == 2){//Insert new material
        $stmt = $conn->prepare("INSERT INTO `t_material`(`c_name`, `c_unit`, `c_supplier`, `c_cost`, `c_moq`) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssdd", $c_name,$c_unit,$c_supplier,$c_cost,$c_moq);
    }else{//update material
        $stmt = $conn->prepare("UPDATE `t_material` SET `c_unit`=?, `c_supplier`=?, `c_cost`=?, `c_moq`=? WHERE `c_name`=?");
        $stmt->bind_param("ssdds", $c_unit,$c_supplier,$c_cost,$c_moq,$c_name);
    }
  }else{//delete material
    $c_name = $obj->name;
    $stmt = $conn->prepare("DELETE FROM `t_material` WHERE `c_name`=?");
    $stmt->bind_param("s", $c_name);
  }
  $result = $stmt->execute();
  $stmt->close();
  $conn->close();
  echo json_encode($result);
?>
