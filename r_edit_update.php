<?
  /*Update t_recipe and t_recipelib table by 
    obj = {
	product: "",
	version: 0,
	recipe: 0,
	cat: "",
  comment: "",
	arrayItemM: [], //recipe item
	arrayItemU: [], //recipe unit
	arrayItemQ: [], //recipe qty
	arrayItemB: [], //0: raw material, 1: base, need to get recipe num of base to save in c_base
	act: 0//1: update, 2: insert
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
  $c_comment = $obj->comment;

  try{
    $conn->autocommit(false);
    $conn->begin_transaction();
    if ($obj->act == 2){ //insert new recipe
      $sql = "SELECT `c_value` FROM `t_config` WHERE `c_setup`='recipe_num'";
      $result = $conn->query($sql);
      if (!$result) throw new Exception('Error getting new recipe number!');
      $row = $result->fetch_assoc();
      $c_recipe = $row['c_value'];
      $sql = "UPDATE `t_config` SET `c_value`=".($c_recipe+1)." WHERE `c_setup`='recipe_num'";
      $result = $conn->query($sql);
      if (!$result) throw new Exception('Error updating new recipe number!');
      $sql = "INSERT INTO `t_recipe` (`c_recipe`,`c_product`,`c_cat`,`c_version`,`c_comment`) VALUES (".$c_recipe.",\"".$c_product."\",'".$c_cat."',".$c_version.",'".$c_comment."')";
      $result = $conn->query($sql);
      if (!$result) throw new Exception('Error creating new recipe!');
      $totalRow = count($obj->arrayItemM);
      for ($i = 0; $i < $totalRow; $i++){
        $c_material = $obj->arrayItemM[$i];
        $c_unit = $obj->arrayItemU[$i];
        $c_quantity = $obj->arrayItemQ[$i];
        $c_base = $obj->arrayItemB[$i];
        if ($c_base!=0){
          $sqlBase = "SELECT `c_recipe` FROM `t_recipe` WHERE `c_product`=\"".$c_material."\"";
          $resultBase = $conn->query($sqlBase);
          if (!$resultBase) throw new Exception('Error getting recipe number for'.$c_material.'!');
          $row = $resultBase->fetch_assoc();
          $c_base = $row['c_recipe'];
        }
        $sql = "INSERT INTO `t_recipelib` (`c_recipe`,`c_material`,`c_quantity`,`c_unit`,`c_base`) VALUES (".$c_recipe.",\"".$c_material."\",".$c_quantity.",'".$c_unit."',".$c_base.")";
        $result = $conn->query($sql);
        if (!$result) throw new Exception('Error creating new recipe item!');
      }
      if ($c_version == 1) { //new product. insert new product in t_product table
        switch ($c_cat){
          case 'Gelato':
          case 'Sorbet':
            $c_default = 2;
            break;
          case 'Batter':
            $c_default = 1;
            break;
          case 'BASE':
            $c_default = 6;
            break;
          default:
            $c_default = 1;
        }
        $sql = "INSERT INTO `t_product` (`c_product`, `c_default`, `c_plan`,`c_warn`,`c_cat`) VALUES (\"".$c_product."\",".$c_default.",0,0,'".$c_cat."')";
        $result = $conn->query($sql);
        if (!$result) throw new Exception('Error creating new product!');
      }
    }else{//update existing recipe
      //update comment field
      $sql = "UPDATE `t_recipe` SET `c_comment`=\"".$c_comment."\" WHERE `c_recipe`=".$c_recipe;
      $result = $conn->query($sql);
      if (!$result){
        throw new Exception('Error updating recipe comment!');
      }
      $sql = "DELETE FROM `t_recipelib` WHERE `c_recipe`=".$c_recipe;
      $result = $conn->query($sql);
      if ($result){
        $totalRow = count($obj->arrayItemM);
        for ($i = 0; $i < $totalRow; $i++){
          $c_material = $obj->arrayItemM[$i];
          $c_unit = $obj->arrayItemU[$i];
          $c_quantity = $obj->arrayItemQ[$i];
          $c_base = $obj->arrayItemB[$i];
          if ($c_base!=0){
            $sqlBase = "SELECT `c_recipe` FROM `t_recipe` WHERE `c_product`=\"".$c_material."\"";
            $resultBase = $conn->query($sqlBase);
            if (!$resultBase) throw new Exception('Error getting recipe number for'.$c_material.'!');
            $row = $resultBase->fetch_assoc();
            $c_base = $row['c_recipe'];
          }
          $sql = "INSERT INTO `t_recipelib` (`c_recipe`,`c_material`,`c_quantity`,`c_unit`,`c_base`) VALUES (".$c_recipe.",\"".$c_material."\",".$c_quantity.",'".$c_unit."',".$c_base.")";
          $result = $conn->query($sql);
          if (!$result) throw new Exception('Error creating new recipe when updating recipe with item:'.$c_material."!");
        }
      }else{
        throw new Exception('Error deleting old recipe when updating recipe!');
      }
    }
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
