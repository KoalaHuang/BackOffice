<?
/*
Manage material data
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("I")) {
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
    <link rel="stylesheet" href="css/bostyles.css">    
	<script src="js/r_material.js"></script>
	<title>Recipe</title>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Material</h1>
        <div class="card mb-3">
            <h5 class="card-header bg-secondary text-white">Edit Material</h5>
            <div class="card-body">
				<div class="row mb-3">
					<div class="col-10 search-container">
				  		<input type="text" class="form-control" id="iptMaterial" placeholder="search material...">
						<div class="suggestions">
							<ul id="ulMaterial" class="search-ul">
							<?
							$sql = "SELECT * FROM `t_material`";
							$result = $conn->query($sql);
							if ($result->num_rows > 0) {
								$idx = 0;
								while($row = $result->fetch_assoc()) {
							?>
								<li class="search-li" data-bo-supplier="<?echo $row['c_supplier']?>" data-bo-unit="<?echo $row['c_unit']?>" data-bo-cost="<?echo $row['c_cost']?>" data-bo-moq="<?echo $row['c_moq']?>" onclick="useSuggestion(<?echo $idx?>)"><?echo $row["c_name"]?></li>
							<?
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
					<div class="col-2 text-end">Unit</div>
					<div class="col-4">
						<input type="text" class="form-control" id="iptUnit" disabled>
					</div>
					<div class="col-2 text-end">MOQ&nbsp;</div>
					<div class="col-4">
						<input type="text" class="form-control" id="iptMoq" disabled>
					</div>
				</div><!--2nd row-->
				<div class="row mb-3">
					<div class="col-2 text-end">Cost</div>
					<div class="col-4">
						<input type="text" class="form-control" id="iptCost" disabled>
					</div>
				</div> <!--3rd row-->
				<div class="my-3">
					<select class="form-select" id="sltSupplier" onchange="f_supplierSelected()" disabled>
						<option selected>Select Supplier</option>
						<?
						$sql = "SELECT c_name FROM `t_supplier`";
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()) {
								$c_name = $row["c_name"];
						?>
								<option value="<?echo $c_name?>"><?echo $c_name?></option>
						<?
							}
						}
						?>
					</select>
				</div>
			</div>
			<div class="row gap-3 mb-3">
				<button type="button" class="btn btn-primary col-3 ms-4" onclick="f_toConfirm()">OK</button>
				<button type="button" class="btn btn-danger col-3" onclick="f_toDelete()">Delete</button>
				<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Cancel</button>
			</div>
		</div>

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
