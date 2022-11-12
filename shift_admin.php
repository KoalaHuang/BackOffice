<?
/*
Shift changed by admin
Called from shift screen when user is admin
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

	<title>BackOffice</title>
	<script src="js/shift_admin.js"></script>
</head>
<body>
	<h1 id="section_home" class="text-center mb-4">Shift Adjustment</h1>
	<?
	$arrayUserName = array();
	$arrayUserID = array();
	$arrayUserWorkday = array();
	$arrayStore = array();
	$arrayUserStore = array();
	$arrayUserEmployee = array();
    $arrayAssigned = array();
	$arrayFullday = array();
	$arrayTimestart = array();
	$arrayTimeend = array();
	$arrayTotalmins = array();

	include "connect_db.php";
	$sql = "SELECT `c_name`,`c_id`,`c_workday`,`c_store`, `c_employee` FROM `t_user` WHERE (NOT (`c_store`='NONE' OR `c_employee`='D'))"; //filter HP, Jerry and me, and deactivated user
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
    $thisYear = $_GET['year'];
    $thisMon = $_GET['mon'];
    $thisDay = $_GET['day'];
    $thisWeekDay = $_GET['WD'];
    $thisStore = $_GET['store'];
	$calendarMon = $_GET['cmon'];  //return to month of calendar displayed
    $objDay = date_create_from_format("Y/n/j",$thisYear."/".$thisMon."/".$thisDay);
	$thisDate = date_format($objDay,'Y-m-d');
	$sql = "SELECT `c_holiday` FROM `t_holiday` WHERE `c_date`='".$thisDate."'";
	$holidayResult = $conn->query($sql);
	$holiday = $holidayResult->fetch_assoc();
	$sqlStorePpl = "SELECT `c_minppl`,`c_maxppl` FROM `t_storeppl` WHERE `c_store`='".$thisStore."' AND `c_weekday`=".$thisWeekDay;
	$resultPpl = $conn->query($sqlStorePpl);
	if ($rowPpl = $resultPpl->fetch_assoc()) {
		$MinPpl = $rowPpl["c_minppl"]; //min ppl required in this weekday for this store
		$MaxPpl = $rowPpl["c_maxppl"]; //max ppl required in this weekday for this store
	}else{ //in case store ppl value is not set
		$MinPpl = 2;
		$MaxPpl = 3;
	}
	?>

	<div class="container">
    <div class="mb-3" id="thisCell" data-stocking-year="<?echo $thisYear?>" data-stocking-mon="<?echo $thisMon?>" data-stocking-day="<?echo $thisDay?>" data-stocking-store="<?echo $thisStore?>" data-stocking-maxppl="<?echo $MaxPpl?>" data-stocking-minppl="<?echo $MinPpl?>" data-stocking-weekday="<?echo $thisWeekDay?>" data-stocking-isholiday="<?if (is_null($holiday)) {echo "0";}else{echo "1";}?>" data-stocking-calendarmon="<?echo $calendarMon?>">
		<span class="bg-light" >Date:&nbsp;</span>
		<span class="bg-white fw-bold"><?echo date_format($objDay,"M j Y  D")?></span>
		<span class="bg-light ms-3" >Store:&nbsp;</span>
		<span class="bg-white fw-bold"><?echo $thisStore?></span>
    </div>
    <?
    $sql = "SELECT `c_id`, `c_type`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins` FROM `t_calendar` WHERE `c_store`='".$thisStore."' AND `c_date`='".$thisDate."'";
    $result = $conn->query($sql);
	echo "<div class=\"card mb-3\">";
    echo "<div class=\"card body\">";
	echo "<div class=\"card-title\" text-muted>Current shift:</div>";
    echo "<div class=\"row mb-1\">";
    echo "<div class=\"col\">";
    for ($intAssigned = 1, $idxPpl = 1; $idxPpl <= $MaxPpl; $idxPpl++) { //start from 1, to match with tab index
        $row = $result->fetch_assoc();
        $strDivClass = "<div class=\"text-center fs-6";
		if ($row){
			$arrayAssigned[$intAssigned] = $row['c_id'];
			$arrayFullday[$intAssigned] = $row['c_fullday'];
			$arrayTimestart[$intAssigned] = $row['c_timestart'];
			$arrayTimeend[$intAssigned] = $row['c_timeend'];
			$arrayTotalmins[$intAssigned] = $row['c_totalmins'];
			$c_type = $row['c_type'];
			$strDivData = "\" data-stocking-fullday=".$arrayFullday[$intAssigned]." data-stocking-timestart=\"".$arrayTimestart[$intAssigned]."\" data-stocking-timeend=\"".$arrayTimeend[$intAssigned]."\" data-stocking-totalmins=".$arrayTotalmins[$intAssigned]." id=\"thisCell".$idxPpl."\">";
			switch ($c_type) {
				case "WW":
					echo $strDivClass.$strDivData.$row['c_id']."</div>";
					break;
				case "OW":
					echo $strDivClass." text-warning".$strDivData.$row['c_id']."</div>";
					break;
				case "HW":
					echo $strDivClass." text-danger".$strDivData.$row['c_id']."</div>";
					break;
			}
			$intAssigned++;
		}else{
			$strDivData = "\" data-stocking-fullday=\"\" data-stocking-timestart=\"\"  data-stocking-timeend=\"\" data-stocking-totalmins=\"\" id=\"thisCell".$idxPpl."\">";
			if ($idxPpl <= $MinPpl) {
				echo $strDivClass." text-danger".$strDivData."*</div>";
			}else{
				echo $strDivClass.$strDivData."&nbsp;</div>";
			} //if min. ppl required is not reached
		} //if $row is not null
	}//for loop ppl in the week day
    echo "</div>"; //col
    echo "</div>"; //row
    echo "</div>"; //card body
    echo "</div>"; //card
    ?>
	<div class="border bg-light mt-4 mb-4">
		<nav><div class="nav nav-tabs mb-3" id="shiftTab" role="tablist">
			<?
				for ($idxTab = 1; $idxTab <= $MaxPpl; $idxTab++){
			?>
				<li class="nav-item" role="presentation">
					<button class="nav-link <?if ($idxTab==1) echo "active"?>" data-bs-toggle="tab" data-bs-target="#tab<?echo $idxTab?>" type="button" role="tab" id="tabButton<?echo $idxTab?>"><?echo $idxTab?></button>
				</li>
			<?
				}
			?>
		</div></nav>
		<div class="tab-content" id="shiftTabContent">
			<?
				for ($idxTab = 1; $idxTab <= $MaxPpl; $idxTab++){
			?>
				<div class="tab-pane fade show <?if ($idxTab==1) echo "active"?>" id="tab<?echo $idxTab?>" role="tabpanel" tabindex="<?echo $idxTab?>">
					<div class="input-group mb-3 ms-1">
						<select class="form-select" id="sltUser<?echo $idxTab?>" onchange="f_ShiftChanged(<?echo $idxTab?>,0)">
							<option value="0">Select...</option>
						<?
							for ($idxUser = 0; $idxUser < count($arrayUserID); $idxUser++) {
								$userID = $arrayUserID[$idxUser];
								if (($arrayUserStore[$userID] == $thisStore) || ($arrayUserStore[$userID] == "ALL")){//only user works in the store is listed
									$userSeq = array_search($userID,$arrayAssigned,true); //return user sequence in assigned list. false if not found
									if ($userSeq != false){//currently assigned
										if ($userSeq == $idxTab){
											$optionStatus = " selected"; //display in this tab
										}else{
											$optionStatus = " disabled"; //display in other tab, disabled in this tab
										}
									}else{
										$optionStatus = ""; //not assigned
									}
									echo "<option value=\"".$userID."\" data-stocking-workday=\"".$arrayUserWorkday[$userID]."\" data-stocking-employee=\"".$arrayUserEmployee[$userID]."\"".$optionStatus.">".$arrayUserName[$userID]."&nbsp;(".$arrayUserWorkday[$userID].")"."</option>";
								}
							}
						?>
						</select>
						<span class="input-group-text">Total mins:</span><input type="text" class="form-control text-" id="ipTotalMins<?echo $idxTab?>"
						<?
							if ($idxTab <= $intAssigned){
								echo "value=".$arrayTotalmins[$idxTab];
							}
							echo " disabled";
						?>>
					</div>
					<div class="input-group mb-3 ms-1">
						<div class="form-check form-switch form-check-inline me-2 align-self-center">
							<input class="form-check-input" type="checkbox" role="switch" id="checkFullDay<?echo $idxTab?>" onchange="f_ShiftChanged(<?echo $idxTab?>,1)"
							<?
							if ($idxTab <= $intAssigned){
								if ($arrayFullday[$idxTab]==1){
									echo "checked";									
								}
							}else{
								echo "disabled";
							}
							?>>
							<label class="form-check-label" for="checkFullDay<?echo $idxTab?>">Full day</label>
						</div>
						<select class="form-select" id="sltTimeStart<?echo $idxTab?>" onchange="f_ShiftChanged(<?echo $idxTab?>,1)"
						<?
							if (($idxTab > $intAssigned) ||($arrayFullday[$idxTab]==1)){
								echo "disabled";
								$timeRec = "0:00";
							}else{
								$timeRec = $arrayTimestart[$idxTab];
							}
						?>>
						<?
								for ($idxTime = 0; $idxTime < 24; $idxTime++) {
									if ($timeRec == ($idxTime.":00")) {$strDisabled = " selected";}else{$strDisabled = "";}
									echo "<option value=\"".$idxTime.":00\"".$strDisabled.">".$idxTime.":00"."</option>";
									if ($timeRec == ($idxTime.":30")) {$strDisabled = " selected";}else{$strDisabled = "";}
									echo "<option value=\"".$idxTime.":30\"".$strDisabled.">".$idxTime.":30"."</option>";
								}
						?>
						</select>
						<span class="input-group-text">&rarr;</span>
						<select class="form-select" id="sltTimeEnd<?echo $idxTab?>" onchange="f_ShiftChanged(<?echo $idxTab?>,1)"
						<?
							if (($idxTab > $intAssigned) ||($arrayFullday[$idxTab]==1)){
								echo "disabled";
								$timeRec = "0:00";
							}else{
								$timeRec = $arrayTimeend[$idxTab];
							}
						?>>
						<?
								for ($idxTime = 0; $idxTime < 24; $idxTime++) {
									if ($timeRec == ($idxTime.":00")) {$strDisabled = " selected";}else{$strDisabled = "";}
									echo "<option value=\"".$idxTime.":00\"".$strDisabled.">".$idxTime.":00"."</option>";
									if ($timeRec == ($idxTime.":30")) {$strDisabled = " selected";}else{$strDisabled = "";}
									echo "<option value=\"".$idxTime.":30\"".$strDisabled.">".$idxTime.":30"."</option>";
								}
						?>
						</select>
					</div>
				</div>
			<?} //tab loop?>
		</div> <!-- tab content -->
		<div class="row mb-3 ms-1">
			<span><button type="button" class="btn btn-primary col-4 me-1" onclick="f_updateShift()" id="btnUpdate" disabled>Post Shift</button>
			<button type="button" class="btn btn-primary col-3 me-1" onclick="f_toConfirm()" id="btnSave" disabled>Save</button>
			<button type="button" class="btn btn-secondary col-3" onclick="f_return()">Return</button></span>
		</div>
    </div> <!-- container -->

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
					<button type="button" class="btn btn-primary" id="btn_ok" onclick="f_submit()">OK</button>
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
