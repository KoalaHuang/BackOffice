<?
/*Edit recipe

TODO: fix Select can't hide options in Safari. Need to store value in list, and filter options

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
		$getCat = $_GET['cat'];
		(int)$getVer = $_GET['ver'];
	}
	?>
    <link rel="stylesheet" href="css/styles.css">    
	<script src="js/r_edit.js"></script>
	<title>Edit Recipe</title>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Edit Recipe</h1>
        <div class="card mb-3">
            <h5 class="card-header bg-secondary text-white">Product</h5>
            <div class="card-body">
				<div class="row mb-3">
					<div class="col-10 search-container">
				  		<input type="text" class="form-control" id="iptProduct" placeholder="search product..." value="<?echo ($getProduct!=NULL)?$getProduct:""?>">
						<div class="suggestions">
							<ul id="ulProduct" class="search-ul">
							<?
							$arrayProduct = [[]];
							$sql = "SELECT * FROM `t_recipe`";
							$result = $conn->query($sql);
							$totalRows = $result->num_rows ;
							if ($totalRows > 0) {
								$idx = 0;
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
										if (($getProduct == $c_product) && ($getVer == $c_ver)){
											$recipeNum = $c_recipe;//every verion of product has unique recipe number
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
						<select class="form-select" id="sltVer" onchange="f_VerSelected()">
							<option value=0 <?echo (($getVer==NULL)||($getVer==0))?"seclected":""?>>New Ver</option>
							<?
							for ($idx=0; $idx < $totalRows; $idx++){
								$strSelect = (($getVer!=NULL)&&($recipeNum==$arrayProduct[$idx][2]))?" selected":"";
								$strDisplay = (($getVer==NULL)||($getProduct!=$arrayProduct[$idx][0]))?" d-none":"";
								echo "<option class=\"".$strDisplay."\" value=".$arrayProduct[$idx][1]." data-bo-product=\"".$arrayProduct[$idx][0]."\" data-bo-recipe=".$arrayProduct[$idx][2].$strSelect.">".$arrayProduct[$idx][1]."</option>";
							}
							?>
						</select>
					</div>
					<div class="col-8">
						<select class="form-select" id="sltCat" onchange="f_CatSelected()">
							<option <?echo ($getProduct==NULL)?"seclected":""?>>Product type...</option>
							<?
							$sql = "SELECT c_cat FROM `t_cat`";
							$result = $conn->query($sql);
							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$c_cat = $row["c_cat"];
							?>
									<option value="<?echo $c_cat?>" <?echo (($getProduct!=NULL)&&($getCat==$c_cat))?"selected":""?>><?echo $c_cat?></option>
							<?
								}
							}
							?>
						</select>
					</div>
				</div> <!--2nd row-->
			</div>
			<div class="row gap-3 mb-2">
				<button type="button" class="btn btn-primary col-3 ms-4" onclick="f_getRecipe()">Read</button>
				<button type="button" class="btn btn-primary col-3" onclick="f_toDelete()">Save</button>
				<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Clean</button>
			</div>
		</div><!--product card-->

        <div class="card mb-3">
            <h5 class="card-header bg-secondary text-white">Recipe</h5>
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
                    <div class="col-3"><input type="text" class="form-control" id="iptUnit" disabled></div>
                    <button class="col-4 btn btn-primary ms-3" type="button" onclick="f_add_item()">Add</button>
                </div>
				<hr><!--list recipe items-->
                <ul class="list-group" id="ulRecipe">
				<?
				$sql = "SELECT `c_material`, `c_quantity`,`c_unit`,`c_base` FROM `t_recipelib` WHERE `c_recipe`=".$recipeNum;
				$result = $conn->query($sql);
				$totalRows = $result->num_rows ;
				if ($totalRows > 0) {
					while($row = $result->fetch_assoc()) {
						$c_material = $row['c_material'];
						$c_qty = $row['c_quantity'];
						$c_unit = $row['c_unit'];
						$c_base = ($row['c_base']==1)?"list-group-item-info":"";
						?>
						<li class="list-group-item <?echo $c_base?>">
							<div class="row">
								<div class="col-8"><?echo $c_material?></div>
								<div class="col-3 text-end"><?echo $c_qty?></div>
								<div class="col-1"><?echo $c_unit?></div>
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
