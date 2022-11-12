<?
/*
Stock data maintainance
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("S")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>Stocking</title>
	<script src="js/stock.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Stock</h1>
		<div id="reminding" class="text-center mb-2 text-muted col-12 fst-italic fs-6">Select item category</div>
		<div class="row mb-4">
			<div class="btn-group col-12" role="group">
				<?
					include 'connect_db.php';
	        $sql = "SELECT * FROM `t_cat`";
    			$result = $conn->query($sql);
    			if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
				?>
              <input type="radio" class="btn-check" name="btn_cat" id="<? echo $row["c_cat"] ?>" autocomplete="off" onclick="f_whichCat()">
							<label class="btn btn-outline-primary" for="<? echo $row["c_cat"] ?>"><? echo $row["c_cat"] ?></label>
				<?
					    	}
			        } else {
			            echo "ERROR! No category found.";
          		}
				?>
			</div> <!-- btn group -->
		</div> <!-- row. selection area -->

		<div class="row col mb-2">
			<?
				$sql = "SELECT * FROM `t_stock`;";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$rownum = 0;
					$storageRowNum = 1;
					$c_item_current = ""; //one item can have mutiple storage. which will be put under card of same item
					while($row = $result->fetch_assoc()) {
						$c_item = $row["c_item"];
						if ($c_item != $c_item_current) { //creating item card
							if ($c_item_current != "") { //close last card
			?>
								</div><!-- stock ranges-->
							</div> <!-- card body-->
						</div> <!-- card -->

			<?			}
							$rownum = $rownum + 1;
							$storageRowNum = 1;
							$strIndex = "_".$rownum."_".$storageRowNum;
							$c_item_current = $c_item;
							$c_qty = $row["c_qty"];
							$cardID = "itemcard".$rownum;
				?>
			<div class="card d-none" id="<? echo $cardID ?>" data-stocking-item="<? echo $c_item ?>" data-stocking-cat="<? echo $row["c_cat"] ?>">
			  <div class="card-body">
			    <span class="card-title fs-5"><? echo $c_item."&nbsp"; ?></span><span class="card-subtitle fs-6 mb-2 text-muted"></span><span class="text-primary"></span>
					<div>
						<?
						}else{ //same item differet stoarge location
							$storageRowNum = $storageRowNum + 1;
							$strIndex = "_".$rownum."_".$storageRowNum;
							$c_qty = $row["c_qty"];
						} //if _ found another storage for current item
						?>
						<div class="input-group mb-3">
  						<span class="input-group-text col-3" id="<? echo "lblStorage".$strIndex ?>"><? echo $row["c_storage"] ?></span>
							<span class="input-group-text col-3">
								<span><? echo $c_qty ?></span>
								<span class="fw-bold" id="<? echo "lblResult".$strIndex ?>"></span>
							</span>
						  <input type="text" class="form-control text-center col-2 text-primary" data-stocking-stock="<? echo $c_qty ?>" value="0" onchange="<? echo "f_boxChanged('".$strIndex."')"; ?>" id="<? echo "box".$strIndex ?>">
						  <button class="btn btn-outline-secondary col-2" type="button" onclick="<? echo "f_adjust('".$strIndex."',true)"; ?>">+</button>
						  <button class="btn btn-outline-secondary col-2" type="button" onclick="<? echo "f_adjust('".$strIndex."',false)"; ?>">-</button>
						</div>
				<?
					}//loop request data
				?>
				<!-- close last card -->
					</div><!-- stock ranges-->
				</div> <!-- card body-->
			</div> <!-- card -->
				<?
				} else {
			?>
			<div class="card d-none" id="itemcard1">
				<div class="card-body">
					<span class="card-title fs-5 text-danger">No stock data!</span>
				</div> <!--empty request table card body-->
			</div> <!--empty request table card-->
			<?
			}//if_request data found
			$conn->close();
			?>
		</div> <!-- card list -->
		<div class="row mt-3">
	    <div class="col-4">
      	<button type="button" class="btn btn-primary d-none" id="btn_submit" onclick="f_toConfirm()">Submit</button>
	    </div>
	    <div class="col-4">
	      <button type="button" class="btn btn-outline-primary d-none" id="btn_clear" onclick="f_refresh()">Clear</button>
	    </div>
			<div class="col-4 mt-3 d-none" id="link_btp">
	      <a href="#section_home">Back to top</a>
	    </div>
		</div> <!-- buttons -->
		<!-- Modal Submit-->
		<div class="modal fade" id="modal_box" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="lbl_modal">Confirm to fulfill?</h5>
					</div>
					<div class="modal-body fs-6" id="body_modal">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" id="btn_cancel" data-bs-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-primary" id="btn_ok" onclick="f_submit()">OK</button>
					</div>
				</div>
			</div>
		</div>
 	</div> <!-- container -->

	<? include "footer.php" ?>
</body>
</html>
