<? 
/*
	Shift template edit by admin
*/
include_once "sessioncheck.php";
if (f_shouldDie("M")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
	<script src="js/shift_template.js"></script>
</head>
<body>
	<h1 id="section_home" class="text-center mb-4">Shift Template</h1>
	<?
	$arrayUserName = array();
	$arrayUserID = array();
	$arrayUserWorkday = array();
	$arrayUserEmployee = array();
	$arrayStore = array();
	$arrayUserStore = array();

	include "connect_db.php";
	$sql = "SELECT `c_name`,`c_id`,`c_workday`,`c_store`, `c_employee` FROM `t_user` WHERE (NOT (`c_store`='NONE' OR `c_employee`='D'))"; //filter HP, Jerry, me and deactivated user
	$result = $conn->query($sql);
	$idx = 0;
	while($row = $result->fetch_assoc()) {
		$arrayUserID[$idx] = $row["c_id"];
		$arrayUserName[$arrayUserID[$idx]] = $row["c_name"];
		$arrayUserWorkday[$arrayUserID[$idx]] = $row["c_workday"];
		$arrayUserStore[$arrayUserID[$idx]] = $row["c_store"];
		$arrayUserEmployee[$arrayUserID[$idx]] = $row["c_employee"];
		$idx++;
	}
	$sql = "SELECT `c_name` FROM `t_store`";
	$result = $conn->query($sql);
	$idx = 0;
	while($row = $result->fetch_assoc()) {
		$arrayStore[$idx] = $row["c_name"];
		$idx++;
	}
	?>

	<div class="container">
		<div class="row row-cols-7 g-0 mb-1">
			<div class="col text-center bg-light border-top border-start border-bottom border-2 border-dark">M</div>
			<div class="col text-center bg-light border-top border-start border-bottom border-2 border-dark">T</div>
			<div class="col text-center bg-light border-top border-start border-bottom border-2 border-dark">W</div>
			<div class="col text-center bg-light border-top border-start border-bottom border-2 border-dark">T</div>
			<div class="col text-center bg-light border-top border-start border-bottom border-2 border-dark">F</div>
			<div class="col text-center bg-light border-top border-start border-bottom border-2 border-dark">S</div>
			<div class="col text-center border bg-light border-2 border-dark">S</div>
		</div> <!-- row -->
		<?
		$rowStore = "";
		$totalStore = count($arrayStore);
		for ($idxStore = 0; $idxStore < $totalStore; $idxStore++) {
			$rowStore = $arrayStore[$idxStore];
			echo "<div class=\"row bg-light\" mb-1\"><span class=\"text-center\" name=\"divStores\"><strong>".$rowStore."</strong></span></div>";
			echo "<div class=\"row row-cols-7 g-0 mb-1\">";
			for ($idxWD = 1; $idxWD < 8; $idxWD++) {
				$sqlStorePpl = "SELECT `c_minppl`,`c_maxppl` FROM `t_storeppl` WHERE `c_store`='".$rowStore."' AND `c_weekday`=".$idxWD;
				$resultPpl = $conn->query($sqlStorePpl);
				if ($rowPpl = $resultPpl->fetch_assoc()) {
					$MinPpl = $rowPpl["c_minppl"]; //min ppl required in this weekday for this store
					$MaxPpl = $rowPpl["c_maxppl"]; //max ppl required in this weekday for this store
				}else{ //in case store ppl value is not set
					$MinPpl = 2;
					$MaxPpl = 3;
				}
				if ($idxWD == 7) {
					$strBorder = "border";
				}else{
					$strBorder = "border-top border-start border-bottom";
				}
				echo "<div class=\"col border-secondary ".$strBorder."\" data-stocking-minppl=\"".$MinPpl."\" data-stocking-maxppl=\"".$MaxPpl."\" onclick=\"f_clickCell('".$rowStore."',".$idxWD.")\" id=\"".$rowStore.$idxWD."\">";
				$sql = "SELECT `c_id`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins` FROM `t_shifttemp` WHERE `c_store`='".$rowStore."' AND `c_weekday`=".$idxWD;
				$result = $conn->query($sql);
				for ($idxPpl = 1; $idxPpl <= $MaxPpl ; $idxPpl++) { 
					$row = $result->fetch_assoc();
					$strDivClass = "<div class=\"text-center fs-6";
					$strDivData = "\" id=\"".$rowStore.$idxWD.$idxPpl."\"";
					if ($row){
						//read and store assignment data in the cell
						$data_fullday = $row['c_fullday'];
						if ($data_fullday == 1){
							$data_timestart = $data_timeend = "";
						}else{
							$data_timestart =  $row['c_timestart'];
							$data_timeend = $row['c_timeend'];
						}
						$data_totalmins = $row['c_totalmins'];
						$strDivData = $strDivData." data-stocking-timestart=\"".$data_timestart."\" data-stocking-timeend=\"".$data_timeend."\" data-stocking-fullday=".$data_fullday." data-stocking-totalmins=".$data_totalmins.">";
						//mark working on day off
						if (strstr($arrayUserWorkday[$row['c_id']],(string)$idxWD)) {
							echo $strDivClass.$strDivData.$row['c_id']."</div>";
						}else{
							echo $strDivClass." text-warning".$strDivData.$row['c_id']."</div>";
						}//if is off day working
					}else{
						$strDivData = $strDivData.">";
						if ($idxPpl <= $MinPpl) {
							echo $strDivClass." text-danger".$strDivData."*</div>"; //showing vancancy as red star
						}else{
							echo $strDivClass.$strDivData."&nbsp;</div>";
						} //if min. ppl required is not reached
					} //if $row is not null
				}//for loop ppl in the week day
				echo "</div>";
			}//for loop weekday
			echo "</div>";
		}//for loop store
		?>

		<!--Cell Edit-->
		<div class="border bg-light mt-4 mb-3">
			<div class="row mb-1">
				<div class="fs-6 mb-3" id="txtSelection"><strong>Click on calendar to select and change...</strong></div>
			</div>
			<nav><div class="nav nav-tabs mb-3" id="shiftTab" role="tablist">
  				<li class="nav-item" role="presentation">
				    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" id="tabButton1" disabled>1</button>
				</li>
				<li class="nav-item" role="presentation">
				    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" id="tabButton2" disabled>2</button>
				</li>
				<li class="nav-item" role="presentation">
				    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab" id="tabButton3" disabled>3</button>
				</li>
				<li class="nav-item" role="presentation">
				    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab4" type="button" role="tab" id="tabButton4" disabled>4</button>
				</li>
			</div></nav>
			<div class="tab-content" id="shiftTabContent">
				<?
					for ($idxTab = 1; $idxTab < 5; $idxTab++){
				?>
					<div class="tab-pane fade show <?if ($idxTab==1) echo "active"?>" id="tab<?echo $idxTab?>" role="tabpanel" tabindex="<?echo $idxTab?>">
						<div class="input-group mb-3">
							<select class="form-select" id="sltUser<?echo $idxTab?>" onchange="f_ShiftChanged(<?echo $idxTab?>,0)">
								<option value="0">Select...</option>
							<?
								for ($idxUser = 0; $idxUser < count($arrayUserID); $idxUser++) {
									$userID = $arrayUserID[$idxUser];
									echo "<option value=\"".$userID."\" data-stocking-workday=\"".$arrayUserWorkday[$userID]."\" data-stocking-userstore=\"".$arrayUserStore[$userID]."\" data-stocking-employee=\"".$arrayUserEmployee[$userID]."\">".$arrayUserName[$userID]."&nbsp;(".$arrayUserWorkday[$userID].")"."</option>";
								}
							?>
							</select>
							<span class="input-group-text">Total mins:</span><input type="text" class="form-control text-" id="ipTotalMins<?echo $idxTab?>" disabled>
						</div>
						<div class="input-group mb-3">
							<div class="form-check form-switch form-check-inline me-2 align-self-center">
								<input class="form-check-input" type="checkbox" role="switch" id="checkFullDay<?echo $idxTab?>" onchange="f_ShiftChanged(<?echo $idxTab?>,1)">
								<label class="form-check-label" for="checkFullDay<?echo $idxTab?>">Full day</label>
							</div>
							<select class="form-select" id="sltTimeStart<?echo $idxTab?>" onchange="f_ShiftChanged(<?echo $idxTab?>,1)">
								<?
									for ($idxTime = 0; $idxTime < 24; $idxTime++) {
										echo "<option value=\"".$idxTime.":00\">".$idxTime.":00"."</option>";
										echo "<option value=\"".$idxTime.":30\">".$idxTime.":30"."</option>";
									}
								?>
							</select>
							<span class="input-group-text">&rarr;</span>
							<select class="form-select" id="sltTimeEnd<?echo $idxTab?>" onchange="f_ShiftChanged(<?echo $idxTab?>,1)">
								<?
									for ($idxTime = 0; $idxTime < 24; $idxTime++) {
										echo "<option value=\"".$idxTime.":00\">".$idxTime.":00"."</option>";
										echo "<option value=\"".$idxTime.":30\">".$idxTime.":30"."</option>";
									}
								?>
							</select>
						</div>
					</div>
				<?} //tab loop?>
			</div> <!-- tab content -->

			<div class="row mb-3"><span>
				<button type="button" class="btn btn-primary col me-3" onclick="f_updateSelectedWD()" id="btnUpdateShift" disabled>Post Shift</button>
				<button type="button" class="btn btn-primary col" onclick="f_toConfirmSaveTemplate()" id="btnSave" disabled>Save Template</button>
			</span></div>
		</div>
		
		<!--Apply template to calendar-->
		<?
			$mydate=getdate(date("U"));
			$thisYear = $mydate['year'];
			$thisMonth = $mydate['mon'];
			$thisDate = $mydate['mday'];
		?>
		<div class="mb-3 border bg-light">
			<div class="fs-6 mb-3"><strong>Apply Shift to date range</strong></div>
			<div class="input-group">
				<select class="form-select" id="sltFromYear">
				<?
					for ($idxTime = $thisYear; $idxTime < $thisYear+10; $idxTime++) {
						echo "<option value=".$idxTime.">".$idxTime."</option>";
					}
				?>
				</select>
				<select class="form-select" id="sltFromMon">
				<?
					for ($idxTime = 1; $idxTime < 13; $idxTime++) {
						if ($idxTime == $thisMonth){
							echo "<option value=".$idxTime." selected>".$idxTime."</option>";
						}else{
							echo "<option value=".$idxTime.">".$idxTime."</option>";
						}
					}
				?>
				</select>
				<select class="form-select" id="sltFromDay">
				<?
					for ($idxTime = 1; $idxTime < 32; $idxTime++) {
						if ($idxTime == $thisDate){
							echo "<option value=".$idxTime." selected>".$idxTime."</option>";
						}else{
							echo "<option value=".$idxTime.">".$idxTime."</option>";
						}
					}
				?>
				</select>
			</div>
			<div class="text-center">&darr;</div>
			<div class="input-group mb-3">
				<select class="form-select" id="sltToYear">
				<?
					for ($idxTime = $thisYear; $idxTime < $thisYear + 10; $idxTime++) {
						echo "<option value=".$idxTime.">".$idxTime."</option>";
					}
				?>
				</select>
				<select class="form-select" id="sltToMon">
				<?
					for ($idxTime = 1; $idxTime < 13; $idxTime++) {
						if ($idxTime == $thisMonth){
							echo "<option value=".$idxTime." selected>".$idxTime."</option>";
						}else{
							echo "<option value=".$idxTime.">".$idxTime."</option>";
						}
					}
				?>
				</select>
				<select class="form-select" id="sltToDay">
				<?
					for ($idxTime = 1; $idxTime < 32; $idxTime++) {
						if ($idxTime == $thisDate){
							echo "<option value=".$idxTime." selected>".$idxTime."</option>";
						}else{
							echo "<option value=".$idxTime.">".$idxTime."</option>";
						}
					}
				?>
				</select>
			</div>
			<div class="mb-3">
				<button type="button" class="btn btn-primary col-3" onclick="f_applyShift()">Apply</button>
			</div>
		</div> <!-- Apply -->
	</div> <!-- container -->

	<!-- Toast -->
	<div style="position: relative; left: 50%; transform: translate(-50%, 0px);">
		<div class="toast fade bg-light" role="alert" id="myToast">
			<div class="toast-body text-center">
				<h1 class="text-warning">&#9888</h1>Change the weekday not saved yet! <br>Drop changes and move to another day?
				<div class="mt-2 pt-2 border-top">
					<button type="button" class="btn btn-primary  me-3" data-bs-dismiss="toast" onclick="f_DropChanges()">Yes</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="toast">No</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Submit-->
	<div class="modal fade" id="modal_box" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="lbl_modal"></h5>
				</div>
				<div class="modal-body fs-6" id="body_modal">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" id="btn_cancel" data-bs-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" id="btn_ok" onclick="f_saveTemplate()">OK</button>
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
