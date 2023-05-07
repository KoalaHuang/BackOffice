<?
/*
Check out current inventory and place production request
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("C")) {
	header("Location:login.php");
	exit();
  }

$selectedCat = $_GET['cat'];
if ($selectedCat == NULL){
	$selectedCat = "Gelato";  //default display 
}
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
    <link rel="stylesheet" href="css/bostyles.css">    
	<script src="js/r_inventory.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center">Inventory</h1>
        <!--Category selection-->
		<form class="row g-3 align-items-center my-3 border bg-light" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
            <div class="mb-3">
				<div class="form-check form-check-inline">
					<input <?echo ($selectedCat=="Gelato")?"checked":""?> type="radio" class="form-check-input" name="reportBy" onclick="f_selectCat('Gelato')" id="radioByGelato">
					<label class="form-check-label" for="radioByGelato">Gelato</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="radio" <?echo ($selectedCat=="Sorbet")?"checked":""?> class="form-check-input" name="reportBy" onclick="f_selectCat('Sorbet')"  id="radioBySorbet">
					<label class="form-check-label" for="radioBySorbet">Sorbet</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="radio" <?echo ($selectedCat=="Batter")?"checked":""?> class="form-check-input" name="reportBy" onclick="f_selectCat('Batter')" id="radioByBatter">
					<label class="form-check-label" for="radioByBatter">Batter</label>
				</div>
            </div>        
        </form>
        <?
			$arrayProductCurrent = array(array()); //product currently having inventory, (product, qty)

			//read current inventory - inventory that has not been consumed
			include "connect_db.php";
			$sql = "SELECT t_recipe.c_product, COUNT(c_indate) FROM `t_production` INNER JOIN t_recipe ON t_production.c_recipe = t_recipe.c_recipe WHERE c_outdate IS NULL AND t_recipe.c_cat = '".$selectedCat."' GROUP BY t_recipe.c_product";
			$result = $conn->query($sql);
			$idxProd = 0; //product index
			if ($result){
				while($row = $result->fetch_assoc()) {
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
							$c_product = $arrayProductCurrent[$idx][0];
							echo "<button class=\"btn btn-primary\" type=\"button\" onclick=\"f_checkOut('".$c_product."')>".$c_product." ( ".$arrayProductCurrent[$idx][1]." )</button>";
						}
					?>
  				</div>
			</div><!--card body-->
		</div><!--Inventory card-->
        <?
			//request list
			$arrayProductRequest = array(array()); //product to be requested. (product, c_plan (1 or 0))

			//get product that does't have stock, and their plan flag
			$sql = "SELECT c_product, c_plan from t_product WHERE c_cat = '".$selectedCat."' AND c_product NOT IN (SELECT t_recipe.c_product FROM `t_production` INNER JOIN t_recipe ON t_production.c_recipe = t_recipe.c_recipe WHERE c_outdate IS NULL GROUP BY t_recipe.c_product)";
			$result = $conn->query($sql);
			$idxProd = 0; //product index
			if ($result){
				while($row = $result->fetch_assoc()) {
					$arrayProductRequest[$idxProd][0] = $row['c_product'];  //product
					$arrayProductRequest[$idxProd++][1] = $row['c_plan'];  //product plan falg. 1: shown as request when no stock
				}
			}
		?>
		<!--Request card-->
        <div class="card mb-3">
            <h5 class="card-header bg-dark text-white">Request</h5>
            <div class="card-body">
				<div class="row mb-3">
					<div class="col-8 search-container">
				  		<input type="text" class="form-control" id="iptProduct" placeholder="search product..." value="">
						<div class="suggestions">
							<ul id="ulProduct" class="search-ul">
							<?
								for ($idx=0; $idx < $idxProd; $idx++){
									if ($arrayProductRequest[$idx][1]==0){
										$c_product = $arrayProductRequest[$idx][0];
										echo "<li class=\"search-li\" onclick=\"useSuggestion('".$c_product."')\">".$c_product."</li>";
									}
								}
							?>
							</ul>
						</div>						
					</div>
					<div class="col-2">
						<input type="checkbox" class="btn-check" id="btnProductList" onchange="f_ListToggleProduct()">
						<label class="btn btn-outline-primary" for="btnProductList" id="lblProductList">&nbsp;&#9776&nbsp;</label>
					</div>
					<div class="col-2">
						<button class="btn btn-primary" type="button" id="btnAddRequest" onclick="f_addRequest()">Add</button>
					</div>
				</div> <!--1st row-->
				<div class="d-grid gap-2">
					<?
						for ($idx = 0; $idx < $idxProd; $idx++){
							if ($arrayProductRequest[$idx][1] == 1){ //display planned product
								echo "<button class=\"btn btn-outline-primary\" type=\"button\">".$arrayProductRequest[$idx][0]."</button>";
							}
						}
					?>
  				</div><!--planned list-->
			</div><!--card body-->
		</div><!--Inventory card-->
	</div> <!--Container-->

	<!-- Modal Submit-->
	<div class="modal fade" id="modal_box" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_title">Confirm to submit below request?</h5>
				</div>
				<div class="modal-body fs-6" id="modal_body"></div>
				<div class="row modal-footer">
                    <div class="row mb-3">
                        <div class="col-4 text-start text-secondary me-2" id="modal_status"></div>
                        <button type="button" class="col-3 btn btn-primary me-2" id="btn_ok" onclick="f_submit()">&nbsp;&nbsp;OK&nbsp;&nbsp;</button>
                        <button type="button" class="col-3 btn btn-secondary" id="btn_cancel" data-bs-dismiss="modal">Cancel</button>
                    </div>
				</div>
			</div>
		</div>
	</div>

	<?
	$conn->close();
	include "footer.php";
	?>
</body>
</html>
