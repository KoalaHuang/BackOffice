<?
/*
Edit recipe
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("G")) {
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
	}
	?>
    <link rel="stylesheet" href="css/bostyles.css">    
	<script src="js/r_edit.js"></script>
	<title>Edit Recipe</title>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Edit Recipe</h1>
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
												$strSelect = " selected";
												$strComment = $row['c_comment'];
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
							if (($getVer==NULL)||($getVer==0)){//no product is selected
								echo "<option value=0 selected>New Ver</option>";
							}else{
								echo "<option value=0>New Ver</option>";
								$countVer = count($arrayVerUl);
								for ($idx=0; $idx < $countVer; $idx++){
									echo $arrayVerUl[$idx];
								}
							}
							?>
						</select>
					</div>
					<div class="col-8">
						<!--Product category is not editable via recipe function, since multiple recipe may share same product&cat-->
						<select class="form-select" id="sltCat" onchange="f_CatSelected()" <?echo (($getProduct!=NULL)&&($getVer==0))?"":"disabled"?>>
							<option value="0" <?echo ($getProduct==NULL)?"seclected":""?>>Product type...</option>
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
					<textarea class="form-control" rows="2" id="txtComment" <?echo ($getProduct==NULL)?"disabled":""?>><?echo ($getProduct!=NULL)?$strComment:"Recipe comment..."?></textarea>
				</div>
			</div> <!--2nd row-->
			<div class="row gap-2 mb-2">
				<button type="button" class="btn btn-primary col-4 ms-4" onclick="f_getRecipe()">Read / New</button>
				<button type="button" class="btn btn-primary col-3" onclick="f_saveRecipe()">Save</button>
				<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Clean</button>
			</div>
		</div><!--product card-->

        <div class="card mb-3">
            <h5 class="card-header bg-dark text-white">Recipe</h5>
            <div class="card-body">
				<div class="row mb-3 search-container">
					<div class="col-10"><input type="text"  class="form-control" id="iptItem" <?echo ($getProduct==NULL)?"disabled":""?>></div>
					<div class="suggestions">
						<ul id="ulItem" class="search-ul">
						<?
						if ($getProduct!=NULL){
							//load base from recipe table
							$sql = "SELECT `c_product`, max(c_version) FROM `t_recipe` WHERE `c_cat`='BASE' GROUP BY `c_product`";
							$result = $conn->query($sql);
							$totalRows = $result->num_rows ;
							if ($totalRows > 0) {
								while($row = $result->fetch_assoc()) {
									$c_product = $row['c_product'];
									echo "<li class=\"search-li text-danger\" data-bo-unit='g' data-bo-isbase=1>".$c_product."</li>";
								}
							}
							//load material item from material table
							$sql = "SELECT `c_name`, `c_unit` FROM `t_material`";
							$result = $conn->query($sql);
							$totalRows = $result->num_rows ;
							if ($totalRows > 0) {
								while($row = $result->fetch_assoc()) {
									$c_name = $row['c_name'];
									$c_unit = $row['c_unit'];
									echo "<li class=\"search-li\" data-bo-unit='".$c_unit."' data-bo-isbase=0>".$c_name."</li>";
								}
							}	
						}
						?>
						</ul>
					</div>
					<div class="col-2">
							<input type="checkbox" class="btn-check" id="btnItemList" onchange="f_ListToggleItem()">
							<label class="btn btn-outline-primary" for="btnItemList" id="lblItemList">&nbsp;&#9776&nbsp;</label>
					</div>
				</div>
                <div class="row mb-3">
					<div class="col-4"><input type="text" class="form-control" id="iptQuantity" <?echo ($getProduct==NULL)?"disabled":""?>></div>
                    <div class="col-2"><input type="text" class="form-control" id="iptUnit" disabled></div>
                    <button class="col-3 btn btn-primary ms-1 me-3" type="button" onclick="f_updateItem()">Update</button>
                    <button class="col-2 btn btn-danger" type="button" onclick="f_deleteItem()">DEL</button>
                </div>
				<hr><!--list recipe items-->
                <ul class="list-group" id="ulRecipe">
				<?
				$sql = "SELECT `c_material`, `c_quantity`,`c_unit`,`c_base` FROM `t_recipelib` WHERE `c_recipe`=".$recipeNum." ORDER BY `c_base` DESC";//decendent so that base can be display on top;
				$result = $conn->query($sql);
				$totalRows = $result->num_rows ;
				$idx = 0;
				if ($totalRows > 0) {
					while($row = $result->fetch_assoc()) {
						$c_material = $row['c_material'];
						$c_qty = $row['c_quantity'];
						$c_unit = $row['c_unit'];
						$c_base = ($row['c_base']>0)?"list-group-item-info":"";
						?>
						<li class="list-group-item <?echo $c_base?>" onclick="f_selectItem(this)">
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
