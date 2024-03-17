<?
/*
Request/Fulfill/Stocking Report
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("T")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>Stocking</title>
	<script src="js/report.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Report</h1>
		<div id="reminding" class="text-center mb-2 text-muted col-12 fst-italic fs-6">Select reprot type</div>
		<div class="row mb-4">
			<div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="btn_ordertype" id="Request" autocomplete="off" onclick="f_whichType()">
				<label class="btn btn-outline-primary" for="Request">Request</label>
				<input type="radio" class="btn-check" name="btn_ordertype" id="Fulfill" autocomplete="off" onclick="f_whichType()">
				<label class="btn btn-outline-primary" for="Fulfill">Fulfill</label>
				<input type="radio" class="btn-check" name="btn_ordertype" id="Stock" autocomplete="off" onclick="f_whichType()">
				<label class="btn btn-outline-primary" for="Stock">Stock</label>
			</div> <!-- btn group -->
		</div> <!-- row. selection area -->

		<div class="row col mb-2">
			<?
				include 'connect_db.php';
				$sql = "SELECT * FROM `t_report` WHERE DATEDIFF(CURDATE(),`c_date`)<60;"; //limit to 1 mth data
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$rownum = 0;
					$c_ordernum_current = ""; //one order can have multiple rows
					while($row = $result->fetch_assoc()) {
						$c_ordernum = $row["c_ordernum"];
						$orderType = $c_ordernum[0];
						if ($c_ordernum != $c_ordernum_current) { //creating item card
							if ($c_ordernum_current != "") {
			?>
													</tbody>
												</table>
											</div> <!--order info-->
										</div> <!--collapse body-->
									</div> <!-- cardbody with button-->
								</div> <!--card of one order-->
			<?
							} // close previous card
							$rownum = $rownum + 1;
							$orderRow = 1;
							$c_ordernum_current = $c_ordernum;
							$cardID = "cardType".$rownum;
			?>
				<div class="card d-none" id="<? echo $cardID ?>" data-stocking-type="<? echo $orderType ?>">
				  <div class="card-body">
						<p>
						  <a class="btn btn-outline-dark col-12" data-bs-toggle="collapse" href="#<?echo "order".$rownum?>" role="button">
						    <?
								$date = date_create($row["c_date"]);
								$strBtnText = date_format($date,"M.d");
								if ($orderType != 's') {
									$strBtnText = $strBtnText." ~ ".$row["c_store"]; //store name for fulfill and reqeust orders
								}
								$strBtnText = $strBtnText." ~ ".$row["c_cat"]." ~ ".$row["c_user"];
								echo $strBtnText;
								?>
						  </a>
						</p>
						<div class="collapse" id="<?echo "order".$rownum?>">
						  <div class="card card-body">
								<table class="table">
								  <thead>
								    <tr>
      								<th scope="col">#</th>
										<?
										if ($orderType == 'r') {
										?>
											<th scope="col">Item</th>
								      <th scope="col">Quantity</th>
										<?
										}else{
										?>
											<th scope="col">Item</th>
											<th scope="col">Storage</th>
											<th scope="col">Quantity</th>
										<?
										}
										?>
								    </tr>
								  </thead>
								  <tbody>
										<?
									} // if this is first row of the order
										?>
      						<tr>
										<th scope="row"><?echo $orderRow?></th>
										<td><?echo $row["c_item"]?></td>
									<?
									if ($orderType != 'r') { //fulfill or stock order
									?>
											<td><?echo $row["c_storage"]?></td>
									<?
									} //if this is storage column
									?>
										<td><?echo $row["c_qty"]?></td>
									</tr>
								<?
								$orderRow++;
								} //while loop
								?>
									</tbody>
								</table>
							</div> <!--order info-->
						</div> <!--collapse body-->
					</div> <!-- cardbody with button-->
				</div> <!--card of one order-->
			<?
				} else {
			?>
			<div class="card d-none" id="cardType1">
				<div class="card-body">
					<span class="card-title fs-5 text-danger">No reprot data!</span>
				</div> <!--empty request table card body-->
			</div> <!--empty request table card-->
			<?
			}//if_request data found
			$conn->close();
			?>
		</div> <!-- card list -->

	<? include "footer.php" ?>
</body>
</html>
