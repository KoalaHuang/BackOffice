<?
/*
Check out current inventory and place production request
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("C")) {
	header("Location:login.php");
	exit();
  }

 if ($_SERVER["REQUEST_METHOD"] == "POST") {  
	$selectedCat = $_POST["cat"];
 }else{
	$selectedCat = "Gelato";  //default display 
 }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
	<script src="js/r_report.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center">Inventory</h1>
        <!--Category selection-->
		<form class="row g-3 align-items-center my-3 border bg-light" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
            <div class="mb-3">
				<div class="form-check form-check-inline">
					<input <?echo ($selectedCat=="Gelato")?"checked":""?> type="radio" class="form-check-input" name="reportBy" value="Gelato" id="radioByGelato">
					<label class="form-check-label" for="radioByGelato">Gelato</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="radio" <?echo ($selectedCat=="Sorbet")?"checked":""?> class="form-check-input" name="reportBy" value="Sorbet" id="radioBySorbet">
					<label class="form-check-label" for="radioBySorbet">Sorbet</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="radio" <?echo ($selectedCat=="Batter")?"checked":""?> class="form-check-input" name="reportBy" value="Batter" id="radioByBatter">
					<label class="form-check-label" for="radioByBatter">Batter</label>
				</div>
            </div>        
        </form>
        <?
			$arrayProductCurrent = array(array()); //product currently having inventory, (product, qty)
			$arrayProductRequest = array(); //product to be requested, current inventory is zero

			//read current inventory - inventory that has not been consumed
			include "connect_db.php";
			$sql = "SELECT t_recipe.c_product, COUNT(c_indate) FROM `t_production` INNER JOIN t_recipe ON t_production.c_recipe = t_recipe.c_recipe WHERE c_outdate IS NULL AND t_recipe.c_cat = '".$selectedCat."' GROUP BY t_recipe.c_product";
			$result = $conn->query($sql);
			$idxProd = 0; //product index
			if ($result){
				while($row = $result->fetch_assoc()) {
					myLOG($row);
					$arrayProductCurrent[$idxProd][0] = $row['c_product'];  //product
					$arrayProductCurrent[$idxProd++][1] = $row['COUNT(c_indate)'];  //inventory
				}
			}
		?>

		<!--Current Inventory-->
        <div class="card mb-3">
            <h5 class="card-header bg-dark text-white">Current</h5>
            <div class="card-body">
				<div class="d-grid gap-2">
					<?
						for ($idx = 0; $idx < $idxProd; $idx++){
							echo "<button class=\"btn btn-primary\" type=\"button\">".$arrayProductCurrent[$idx][0]." ( ".$arrayProductCurrent[$idx][1]." )</button>";
						}
					?>
  				</div>
			</div><!--card body-->
		</div><!--Inventory card-->


	<?
	$conn->close();
	include "footer.php";
	?>
</body>
</html>
