<?
/*
Fulfill store request
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("F")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>Stocking</title>
	<script src="js/fulfil.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Fulfill</h1>

		<div id="reminding" class="text-center mb-2 text-muted col-12 fst-italic fs-6">Select store and category</div>

		<div class="row mb-2">
			<div class="col-2 text-left align-text-bottom mt-2"><strong>Store:&nbsp</strong></div>
			<div class="btn-group col-10" role="group">
				<?
					include 'connect_db.php';
			        $sql = "SELECT * FROM `t_store`";
        			$result = $conn->query($sql);
        			if ($result->num_rows > 0) {
		            while($row = $result->fetch_assoc()) {
				?>
              <input type="radio" class="btn-check" name="btn_store" id="<? echo $row["c_name"] ?>" autocomplete="off" onclick="f_who_is_requesting()">
							<label class="btn btn-outline-primary" for="<? echo $row["c_name"] ?>"><? echo $row["c_name"] ?></label>
				<?
					    	}
			        } else {
			            	echo "ERROR! No requesting store found.";
          		}
				?>
			</div> <!-- btn group -->
		</div> <!-- row -->
		<div class="row mb-4">
			<div class="col-2 text-left align-text-bottom mt-2"><strong>CAT:&nbsp</strong></div>
			<div class="btn-group col-10" role="group">
				<?
			        $sql = "SELECT * FROM `t_cat`";
        			$result = $conn->query($sql);
        			if ($result->num_rows > 0) {
		            while($row = $result->fetch_assoc()) {
				?>
              <input type="radio" class="btn-check" name="btn_cat" id="<? echo $row["c_cat"] ?>" autocomplete="off" onclick="f_who_is_requesting()">
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
				$sql = "SELECT * FROM `t_request` WHERE `c_qty` > 0;";
				$stmt = $conn->prepare("SELECT `c_storage`, `c_qty` FROM `t_stock` WHERE `c_qty`>0 AND `c_item`=?");
				$stmt->bind_param("s", $c_item );
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$rownum = 0;
					while($row = $result->fetch_assoc()) {
						$rownum = $rownum + 1;
						$cardID = "itemcard".$rownum;
						$c_item = $row["c_item"];
						$c_qty_req = (int)$row["c_qty"];
			?>
				<div class="card d-none" id="<? echo $cardID ?>" data-stocking-item="<? echo $c_item ?>" data-stocking-cat="<? echo $row["c_cat"] ?>" data-stocking-store="<? echo $row["c_store"] ?>">
				  <div class="card-body">
				    <span class="card-title fs-5"><? echo $c_item."&nbsp"; ?></span><span class="card-subtitle fs-6 mb-2 text-muted">open request:&nbsp</span><span class="text-primary"><?echo $c_qty_req; ?></span>
						<div>
							<?
								$stockRowNum = 0;
								if ($stmt->execute()) {
			            $stmt_result = $stmt->get_result();
			            while($stk=$stmt_result->fetch_assoc())
			            {
										$stockRowNum++;
										$c_qty_stock = (int)$stk['c_qty'];
										if ($c_qty_stock >= $c_qty_req) {
											$rangeVal = $c_qty_req;
											$c_qty_req = 0;
										}else{
											$rangeVal = $c_qty_stock;
											$c_qty_req = $c_qty_req - $c_qty_stock;
										}//if allocate range value
										$rangeID = "r_".$rownum."_".$stockRowNum;
										echo '<div class="row">';
														echo '<label for="'.$rangeID.'" class="form-label col-3">'.$stk["c_storage"].'</label><span class="col-9"><span class="ms-2 text-muted">stock:'.'</span><span>'.$c_qty_stock.'</span><span class="ms-2 text-muted">&nbspallocate:</span><span class="ms-2 text-danger" id="'.$rangeID.'_val">'.$rangeVal.'</span></span>';
										echo '</div>';
			      				echo '<input type="range" class="form-range" data-stocking-storage="'.$stk['c_storage'].'" min="0" max="'.$c_qty_stock.'" step="1" value="'.$rangeVal.'" id="'.$rangeID."\" onchange=\"f_rangeVal('".$rangeID."')\">";
			            } //loop stock data
									if ($stockRowNum == 0) {
										echo '<p class="fs-5 text-danger">Out of stock!</p>';
									} //if out of stock
								}else{
									echo "Error getting stock data. item:".$c_item." store:".$row["c_store"];
									die;
								} //if_stock data found
						?>
						</div><!-- stock ranges-->
				  </div> <!-- card body-->
				</div> <!-- card -->
			<?
					}//loop request data
				} else {
			?>
					<div class="card d-none" id="itemcard1">
						<div class="card-body">
							<span class="card-title fs-5">Wah hoo, NO pending request from any store...</span>
						</div> <!--empty request table card body-->
					</div> <!--empty request table card-->
			<?
				}//if_request data found
				$stmt->close();
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
