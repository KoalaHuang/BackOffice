<?
/*
Item Admin page.  Add/change item name, storage location
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("A")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>Stocking</title>
	<script src="js/admin_item.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Admin - Item</h1>

		<div id="reminding" class="text-center mb-3 text-danger col-12 fst-italic fs-6">Danger zone. Know what you are doing...</div>
		<div class="row mt-3">
			<span>
				<select class="form-select mb-3" id="sltCat" onchange="f_catSelected()">
				  <option selected>Select Category</option>
					<?
					include "connect_db.php";
					$sql = "SELECT * FROM `t_cat`";
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
			</span>
			<span>
				<select class="form-select mb-3" disabled id="sltItem" onchange="f_itemSelected()">
				  <option selected>Select Item</option>
					<option value="addNewItem" data-stocking-storage="" data-stocking-cat="">&#9830;&nbsp;Add New Item&nbsp;&#9830;</option>
					<?
					$sql = "SELECT * FROM `t_stock`";
    			$result = $conn->query($sql);
    			if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
					?>
				  <option value="<?echo $row['c_item']?>" data-stocking-storage="<?echo $row['c_storage']?>" data-stocking-cat="<?echo $row['c_cat']?>"><?echo $row["c_item"]?></option>
					<?
						}
					}
					?>
				</select>
			</span>
		</div> <!-- cat and item selection rows-->
		<div class="col-12 text-center mb-1"><strong>Item Name</strong></div>
		<div class="input-group mb-3">
		  <input type="text" class="form-control" id="iptItem" disabled>
		</div> <!--input box-->
		<div class="col-12 text-center mb-1"><strong>Storage</strong></div>
		<div class="row mb-5">
			<div class="btn-group" role="group">
				<?
        $sql = "SELECT * FROM `t_storage`";
  			$result = $conn->query($sql);
  			if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
				?>
        <input type="radio" class="btn-check" name="btn_storage" id="<? echo $row['c_name'] ?>" autocomplete="off" disabled>
				<label class="btn btn-outline-primary" for="<? echo $row['c_name'] ?>"><? echo $row["c_name"] ?></label>
				<?
					}
    		}
				$conn->close();
				?>
			</div> <!-- btn group -->
		</div> <!-- storage row -->
		<div class="row">
			<span><button type="button" class="btn btn-primary col-3 me-5" onclick="f_toConfirm()">OK</button>
			<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Cancel</button></span>
		</div>
 	</div> <!-- container -->

	<!-- Modal Submit-->
	<div class="modal fade" id="modal_box" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="lbl_modal">Confirm to submit below request?</h5>
				</div>
				<div class="modal-body fs-6" id="body_modal">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" id="btn_cancel" onclick="f_refresh()">Cancel</button>
					<button type="button" class="btn btn-primary" id="btn_ok" onclick="f_submit()">OK</button>
				</div>
			</div>
		</div>
	</div>

	<? include "footer.php" ?>
</body>
</html>
