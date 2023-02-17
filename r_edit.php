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
	<? 	include "header.php"; 
	include "connect_db.php";
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
				  		<input type="text" class="form-control" id="iptProduct" placeholder="search product...">
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
									if ($c_product != $lastProduct){
										echo "<li class=\"search-li\" data-bo-cat=\"".$c_cat."\" onclick=useSuggestion(\"".$c_product."\")>".$c_product."</li";
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
						<input type="checkbox" class="btn-check" id="btnList" onchange="f_ListToggle()">
						<label class="btn btn-outline-primary" for="btnList" id="lblBtnList">&nbsp;+&nbsp;</label>
					</div>
				</div> <!--1st row-->
				<div class="row mb-3">
					<div class="col-4">
						<select class="form-select" id="sltVer" onchange="f_VerSelected()">
							<option selected>Verion...</option>
							<?
							for ($idx=0; $idx < $totalRows; $idx++){
								echo "<option value=".$arrayProduct[$idx][1]." data-bo-product=\"".$arrayProduct[$idx][0]."\" data-bo-recipe=".$arrayProduct[$idx][2].">".$arrayProduct[$idx][1]."</option>";
							}
							?>
						</select>
					</div>
					<div class="col-8">
						<select class="form-select" id="sltCat" onchange="f_CatSelected()">
							<option selected>Product type...</option>
							<?
							$sql = "SELECT c_cat FROM `t_cat`";
							$result = $conn->query($sql);
							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									$c_cat = $row["c_cat"];
							?>
									<option value="<?echo $c_cat?>"><?echo $c_cat?></option>
							<?
								}
							}
							?>
						</select>
					</div>
				</div> <!--2nd row-->
			</div>
			<div class="row gap-3 mb-3">
				<button type="button" class="btn btn-primary col-3 ms-4" onclick="f_toConfirm()">OK</button>
				<button type="button" class="btn btn-danger col-3" onclick="f_toDelete()">Delete</button>
				<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Cancel</button>
			</div>
		</div><!--product card-->

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
