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
		$getCat = $_GET['cat'];
		(int)$getVer = $_GET['ver'];
	}
	?>
    <link rel="stylesheet" href="css/styles.css">    
	<script src="js/r_read.js"></script>
	<title>Recipe</title>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Recipe</h1>
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
							$sql = "SELECT * FROM `t_recipe` ORDER BY `c_product`,`c_recipe`";
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
			</div> <!--2nd row-->
			<div class="row gap-2 mb-2">
				<button type="button" class="btn btn-primary col-4 mx-4" onclick="f_getRecipe()">Read</button>
				<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Clean</button>
			</div>
		</div><!--product card-->

        <div class="card mb-3">
            <h5 class="card-header bg-dark text-white">Recipe</h5>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-2">
						<input type="radio" class="btn-check" id="rdoQty1" name="rdoQty" onclick="f_kg(1)" checked>
						<label class="btn btn-outline-primary" for="rdoQty1">&nbsp;1&nbsp;</label>
					</div>
                    <div class="col-2">
						<input type="radio" class="btn-check" id="rdoQty2" name="rdoQty" onclick="f_kg(2)">
						<label class="btn btn-outline-primary" for="rdoQty2">&nbsp;2&nbsp;</label>
					</div>
                    <div class="col-2">
						<input type="radio" class="btn-check" id="rdoQty35" name="rdoQty" onclick="f_kg(3.5)">
						<label class="btn btn-outline-primary" for="rdoQty35">3.5</label>
					</div>
                    <div class="col-2">
						<input type="radio" class="btn-check" id="rdoQty4" name="rdoQty" onclick="f_kg(4)">
						<label class="btn btn-outline-primary" for="rdoQty4">&nbsp;4&nbsp;</label>
					</div>
					<div class="col-3"><input type="text" placeholder="Kg" class="form-control" id="iptPlanQty" onchange="f_planQty()"></div>
                </div>
				<hr><!--list recipe items-->
                <ul class="list-group" id="ulRecipe">
				<?
				$sql = "SELECT `c_material`, `c_quantity`,`c_unit`,`c_base` FROM `t_recipelib` WHERE `c_recipe`=".$recipeNum." ORDER BY `c_base`";
				$result = $conn->query($sql);
				$totalRows = $result->num_rows ;
				$idx = 0;
				if ($totalRows > 0) {
					while($row = $result->fetch_assoc()) {
						$c_material = $row['c_material'];
						$c_qty = $row['c_quantity'];
						$c_unit = $row['c_unit'];
						$c_base = ($row['c_base']==1)?"list-group-item-info":"";
						?>
						<li class="list-group-item <?echo $c_base?>">
							<div class="row">
								<div class="col-7"><?echo $c_material?></div>
								<div class="col-3 text-end"><?echo $c_qty?></div>
								<div class="col-2"><?echo $c_unit?></div>
							</div>
						</li>
						<?
					}
				}
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
		include "footer.php" 
	?>
</body>
</html>
