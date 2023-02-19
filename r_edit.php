<?
/*Edit recipe*/ 
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
				  		<input type="text" class="form-control" id="iptProduct" placeholder="search product..." <?echo ($getProduct!=NULL)?$getProduct:""?>>
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
							<option value=0 <?echo (($getVer==NULL)||($getVer==0))?"seclected":""?>>Version</option>
							<?
							for ($idx=0; $idx < $totalRows; $idx++){
								echo "<option value=".$arrayProduct[$idx][1]." data-bo-product=\"".$arrayProduct[$idx][0]."\" data-bo-recipe=".$arrayProduct[$idx][2]." ".(($getVer!=NULL)&&($getVer==$arrayProduct[$idx][1]))?"selected":"".">".$arrayProduct[$idx][1]."</option>";
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
			<div class="row gap-4 mb-3">
				<button type="button" class="btn btn-primary col-3 ms-4" onclick="f_getRecipe()">Get</button>
				<button type="button" class="btn btn-danger col-3" onclick="f_toDelete()">Delete</button>
				<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Cancel</button>
			</div>
		</div><!--product card-->

        <div class="card mb-3">
            <h5 class="card-header bg-secondary text-white">Recipe</h5>
            <div class="card-body">
				<div class="row mb-3 search-container">
					<div class="col-10"><input type="text"  class="form-control" id="iptItem"></div>
					<div class="suggestions">
						<ul id="ulItem" class="search-ul">
						<?
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
						?>
						</ul>
					</div>
					<div class="col-2">
							<input type="checkbox" class="btn-check" id="btnItemList" onchange="f_ListToggleItem()">
							<label class="btn btn-outline-primary" for="btnItemList" id="lblItemList">&nbsp;&#9776&nbsp;</label>
					</div>
				</div>
                <div class="row mb-3">
					<div class="col-4"><input type="text" class="form-control" id="iptQuantity"></div>
                    <div class="col-3"><input type="text" class="form-control" id="iptUnit" disabled></div>
                    <button class="col-4 btn btn-primary ms-3" type="button" onclick="f_add_item()">Add</button>
                </div>
                <ul class="list-group" id="ulRecipe">
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
