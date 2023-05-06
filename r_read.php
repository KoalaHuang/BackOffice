<?
/*
Read recipe
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("P")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? 	
	include "header.php"; 
	include "connect_db.php";
	$getProduct = $_GET['product'];
	if ($getProduct != NULL) {//retrieve recipe for product
		$getProduct = str_replace('{',"'",$getProduct); //apostrophe is transfered as { 
		(int)$getVer = $_GET['ver'];
		$strCat = "";
	}
	$userID = $_SESSION["id"];
	?>
    <link rel="stylesheet" href="css/bostyles.css">    
	<script src="js/r_read.js"></script>
	<title>Recipe</title>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Production</h1>
        <div class="card mb-3">
            <h5 class="card-header bg-dark text-white">Product</h5>
            <div class="card-body">
				<div class="row mb-3">
					<div class="col-10 search-container">
				  		<input type="text" class="form-control" id="iptProduct" placeholder="search product..." value="<?echo ($getProduct!=NULL)?$getProduct:""?>">
						<div class="suggestions">
							<ul id="ulProduct" class="search-ul">
							<?
							$arrayProduct = [[]];
							$arrayVerUl = []; //Version option HTML for selected products
							$sql = "SELECT * FROM `t_recipe` WHERE `c_cat` IN (SELECT `c_cat` FROM `t_cataccess` WHERE `c_id`='".$userID."') ORDER BY `c_product`,`c_recipe`";
							$result = $conn->query($sql);
							$totalRows = $result->num_rows ;
							if ($totalRows > 0) {
								$idx = $idxVerUl = 0;
								$lastProduct = "";
								while($row = $result->fetch_assoc()) {
									$c_product = $row['c_product'];
									$c_cat = $row['c_cat'];
									$c_ver = $row['c_version'];
									$c_recipe = $row['c_recipe'];
									if ($c_product != $lastProduct){
										echo "<li class=\"search-li\" data-bo-cat=\"".$c_cat."\" onclick=\"useSuggestion('".$c_product."')\">".$c_product."</li>";
										$lastProduct = $c_product;
									}
									$arrayProduct[$idx][0] = $c_product;
									$arrayProduct[$idx][1] = $c_ver;
									$arrayProduct[$idx][2] = $c_recipe;
									if ($getProduct!=NULL){
										if ($getProduct == $c_product){
											$strSelect = "";
											$arrayVerUl[$idxVerUl] = "<option value=".$arrayProduct[$idx][1]." data-bo-product=\"".$arrayProduct[$idx][0]."\" data-bo-recipe=".$arrayProduct[$idx][2];
											if ($getVer == $c_ver){
												$recipeNum = $c_recipe;//every verion of product has unique recipe number
												$strComment = $row['c_comment'];
												$strSelect = " selected";
												$strCat = $c_cat;
											}
											$arrayVerUl[$idxVerUl] = $arrayVerUl[$idxVerUl].$strSelect.">".$arrayProduct[$idx][1]."</option>";
											$idxVerUl++;
										} 
									}
									$idx++;	
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
				</div> <!--1st row-->
				<div class="row mb-3">
					<div class="col-4">
						<ul class="d-none" id="ulAllRecipe">
						<?
						for ($idx=0; $idx < $totalRows; $idx++){
							echo "<li data-bo-product=\"".$arrayProduct[$idx][0]."\" data-bo-recipe=".$arrayProduct[$idx][2].">".$arrayProduct[$idx][1]."</li>";
						}
						?>
						</ul>
						<select class="form-select" id="sltVer">
							<?
                            $countVer = count($arrayVerUl);
                            for ($idx=0; $idx < $countVer; $idx++){
                                echo $arrayVerUl[$idx];
                            }
							?>
						</select>
					</div>
					<div class="col-8">
						<select class="form-select" id="sltCat" disabled>
							<option value="0"  <?echo ($getProduct==NULL)?"seclected":""?>>Product type...</option>
							<?
							$sql = "SELECT c_cat FROM `t_cat`";
							$result = $conn->query($sql);
							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$c_cat = $row["c_cat"];
							?>
									<option value="<?echo $c_cat?>" <?echo (($getProduct!=NULL)&&($strCat==$c_cat))?"selected":""?>><?echo $c_cat?></option>
							<?
								}
							}
							?>
						</select>
					</div>
				</div>
				<div>
					<textarea class="form-control" rows="2" id="txtComment" disabled><?echo ($getProduct!=NULL)?$strComment:"Recipe comment..."?></textarea>
				</div>
				<div class="row my-3">
					<?
					if ($getProduct!=NULL){
						$sql = "SELECT `c_default` FROM `t_product` WHERE `c_product`='".$getProduct."'";
						$result = $conn->query($sql);
						if ($result) {
							$row = $result->fetch_assoc();
							$defaultSize = $row['c_default'];
						}else{
							echo "Error reading product default size!";
							die;
						}
					}
					?>
					<div class="col-3">
						<input type="text" class="form-control text-center" id="iptPlanQty" value="<?echo $defaultSize?>">
					</div>
					<div class="col-1 d-flex align-items-center">
						<span class="form-text"><?echo ($strCat=='Batter')?"Tub":"Kg"?></span>
					</div>
					<div class="col-2">
						<button type="button" id="btnQtyUp" class="btn btn-primary" onclick="f_planQty(0.5)" <?echo ($getProduct!=NULL)?"":"disabled"?>>&nbsp;+&nbsp;</button>					
					</div>
					<div class="col-2">
						<button type="button" id="btnQtyDown" class="btn btn-primary" onclick="f_planQty(-0.5)" <?echo ($getProduct!=NULL)?"":"disabled"?>>&nbsp;-&nbsp;</button>					
					</div>
					<div class="col-4 text- d-flex align-items-center">
						<span class="form-text"><abbr title="<Gelato> Full: 3.5kg Haf: 2kg  <Base> 6kg  <Batter> by tub">by stock qty</abbr></span>
					</div>
				</div><!--Cook quantity-->
				<div class="row mb-2 mt-2">
					<button type="button" class="btn btn-primary col-3 ms-3" onclick="f_getRecipe()">Load</button>
					<button type="button" class="btn btn-primary col-3 mx-2" id="btnCook" onclick="f_cook()" <?echo ($getProduct!=NULL)?"":"disabled"?>>Cook!</button>
					<button type="button" class="btn btn-secondary col-3 ms-5" onclick="f_refresh()">Clean</button>
				</div>
			</div> <!--card body-->
		</div><!--product card-->

        <div class="card mb-3">
            <h5 class="card-header bg-dark text-white">Recipe</h5>
            <div class="card-body">

				<!--list recipe items-->
                <ul class="list-group" id="ulRecipe">
				<?
				if ($getProduct!=NULL){				
					$sql = "SELECT `c_material`, `c_quantity`,`c_unit`,`c_base` FROM `t_recipelib` WHERE `c_recipe`=".$recipeNum." ORDER BY `c_base` DESC";//decendent so that base can be display on top
					$result = $conn->query($sql);
					$totalRows = $result->num_rows ;
					$idx = 0;
					if ($totalRows > 0) {
						while($row = $result->fetch_assoc()) {
							$c_material = $row['c_material'];
							$c_qty = $row['c_quantity'];
							$c_unit = $row['c_unit'];
							$c_base = $row['c_base'];
							if ($c_base>0){//raw materail is 0. otherwise c_base is recipe number of Base
							?>
							<button class="btn btn-primary text-start" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBase">							
								<div class="row">
									<div class="col-7"><?echo $c_material?></div>
									<div class="col-3 text-end" name="divQty"><?echo $c_qty * $defaultSize?></div>
									<div class="col-2"><?echo $c_unit?></div>
								</div>							
							</button>
							<div class="collapse my-3" id="collapseBase">
								<div class="card card-body">
								<?
									$sqlBase = "SELECT `c_material`, `c_quantity`,`c_unit` FROM `t_recipelib` WHERE `c_recipe`=".$c_base." ORDER BY `c_base` DESC";
									$resultBase = $conn->query($sqlBase);
									$totalRowsBase = $resultBase->num_rows ;
									if ($totalRowsBase > 0) {
										while($rowBase = $resultBase->fetch_assoc()) {
											$c_materialBase = $rowBase['c_material'];
											$c_qtyBase = $rowBase['c_quantity'];
											$c_unitBase = $rowBase['c_unit'];
								?>
											<li class="list-group-item">
												<div class="row">
													<div class="col-7"><?echo $c_materialBase?></div>
													<div class="col-3 text-end" name="divQty"><?echo $c_qtyBase * $defaultSize?></div>
													<div class="col-2"><?echo $c_unitBase?></div>
												</div>
											</li>
								<?
										}
									}
								?>
								</div>
							</div><!--collapse-->
							<?
							}else{
							?>
								<li class="list-group-item">
									<div class="row">
										<div class="col-7"><?echo $c_material?></div>
										<div class="col-3 text-end" name="divQty"><?echo $c_qty * $defaultSize?></div>
										<div class="col-2"><?echo $c_unit?></div>
									</div>
								</li>
							<?							
							}//base or non-base
						}//recipe itemloop
					}//when reading recipe sucessfully
				}//when product is selected
				?>
                </ul>
            </div>
        </div><!--Recipe card-->
 	</div> <!-- container -->

	<!-- Modal Submit-->
	<div class="modal fade" id="modal_box" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_title">Add below production record?</h5>
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
		include "footer.php" 
	?>
</body>
</html>
