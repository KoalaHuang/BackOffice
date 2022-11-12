<?
/*
Store to raise Request
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("R")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>Stocking</title>
	<script src="js/request.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Request</h1>
		<div class="text-center mb-2 text-secondary col-12 fw-bold fs-6"><?echo $_SESSION["user"]?></div>

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
				$sql = "SELECT * FROM `t_request`;";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$rownum = 0;
					while($row = $result->fetch_assoc()) {
						$rownum = $rownum + 1;
						$cardID = "itemcard".$rownum;
						$radioID = "reqQty".$rownum;
			?>
				<div class="card d-none" id="<? echo $cardID ?>" data-stocking-item="<? echo $row["c_item"] ?>" data-stocking-cat="<? echo $row["c_cat"] ?>" data-stocking-store="<? echo $row["c_store"] ?>">
				  <div class="card-body">
				    <span class="card-title fs-5"><? echo $row["c_item"] ?></span>&nbsp<span class="card-subtitle fs-6 mb-2 text-muted"><? if ($row["c_qty"]>0) {echo "open request: ".$row["c_qty"];} ?></span>
						<div class="btn-group col-12" role="group">
						  <input type="radio" class="btn-check" name="<? echo $radioID ?>" id="<? echo $radioID ?>1" value="1" autocomplete="off">
						  <label class="btn btn-outline-primary" for="<? echo $radioID ?>1">1</label>
						  <input type="radio" class="btn-check" name="<? echo $radioID ?>" id="<? echo $radioID ?>2" value="2" autocomplete="off">
						  <label class="btn btn-outline-primary" for="<? echo $radioID ?>2">2</label>
						  <input type="radio" class="btn-check" name="<? echo $radioID ?>" id="<? echo $radioID ?>3" value="3" autocomplete="off">
						  <label class="btn btn-outline-primary" for="<? echo $radioID ?>3">3</label>
							<input type="radio" class="btn-check" name="<? echo $radioID ?>" id="<? echo $radioID ?>4" value="4" autocomplete="off">
						  <label class="btn btn-outline-primary" for="<? echo $radioID ?>4">4</label>
							<input type="radio" class="btn-check" name="<? echo $radioID ?>" id="<? echo $radioID ?>5" value="5" autocomplete="off">
						  <label class="btn btn-outline-primary" for="<? echo $radioID ?>5">5</label>
						</div> <!-- qty button -->
				  </div> <!-- card body-->
				</div> <!-- card -->
			<?
						}
					} else {
								echo "ERROR! No category found.";
					}
					$conn->close();
			?>
		</div> <!-- item list -->
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
						<h5 class="modal-title" id="lbl_modal">Confirm to submit below request?</h5>
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
