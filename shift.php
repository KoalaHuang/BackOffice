<? 
/*
Shift Calendar
GET para:
year: year of calendar
mon: month of calendar
user: employee ID to filter the calendar
store: store to display. For example: Papa`Queens`VP
*/
include_once "sessioncheck.php";
if (f_shouldDie("C")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
	<script src="js/shift.js"></script>
</head>
<body>
	<h1 id="section_home" class="text-center mb-3">Shift</h1>
	<?
	$UserName = $_SESSION["user"];
	$UserID = "";
	$UserWorkday = "";
	$UserStore = "";
	$UserIsAdmin = false;
	$arrayStore = array();

	$getEmployee = $_GET['user']; //employ id sent as para
	$getStore = $_GET['store']; // store to be displayed

	include "connect_db.php";
	$sql = "SELECT `c_name`,`c_id`,`c_workday`,`c_store`, `c_access`, `c_employee` FROM `t_user` WHERE `c_name`='".$UserName."'";
	$result = $conn->query($sql);
	if ($row = $result->fetch_assoc()) {
		$UserID = $row["c_id"];
		$UserName = $row["c_name"];
		$UserWorkday = $row["c_workday"];
		$UserStore = $row["c_store"];
		$UserStatus = $row["c_employee"];
		$UserIsAdmin = strpos($row["c_access"],"M");//"M" - shift template access
	}else{
		echo "User data error!";
		die;
	}
	if ($getEmployee==NULL){
		if ($UserIsAdmin){
			$displayedUser = "All"; //admin user default display all
		}else{
			$displayedUser = $UserID; //non admin user display themself by default
		}
	}else{
		$displayedUser = $getEmployee; //if employee is sent as para, then filtered by it
	}
	$sql = "SELECT `c_name` FROM `t_store`";
	$result = $conn->query($sql);
	$idx = 0;
	while($row = $result->fetch_assoc()) {
		$rowStore = $row['c_name'];
		$arrayStore[$idx] = $rowStore;
		$idx++;
	}
	//get starting date for calendar: date of Monday of the week of first day of the month
	function f_getStartDay($theYear, $theMonth) {
		$objStartDay = date_create_from_format("Y/n/j",$theYear."/".$theMonth."/1");
		$theWeekDay = date('w', date_timestamp_get($objStartDay));
		$diffDay = $theWeekDay - 1;
		if ($diffDay < 0) $diffDay = 6;
		date_sub($objStartDay,date_interval_create_from_date_string($diffDay." days"));
		return $objStartDay;
	}

	if (($_GET['year'] != NULL) && ($_GET['mon'] != NULL)) {
			$theYear = (int)$_GET['year'];
			$theMonth = (int)$_GET['mon'];
			$objTempDay = date_create_from_format("Y/n/j",$theYear."/".$theMonth."/"."1");
			$theMonthName = date("F",date_timestamp_get($objTempDay));
	}else{
		$arrDefaultDay = getdate(); //default date in array
		$theYear = $arrDefaultDay['year'];
		$theMonth = $arrDefaultDay['mon'];
		$theMonthName = $arrDefaultDay['month'];
	}
	$objStartDay = f_getStartDay($theYear,$theMonth);
	$objStartDay->setTime(0,0,0);
	$today = new DateTime("today");
	?>

	<!--Filter using names-->
	<div class="container">
		<div id="txtUserName" class="d-none" data-stocking-userid="<?echo $UserID?>" data-stocking-userstore="<?echo $UserStore?>" data-stocking-userworkday="<?echo $UserWorkday?>" data-stocking-employee="<?echo $UserStatus?>"><?echo $UserName?></div>
		<div class="row mb-3">
			<div class="col-4"></div>
			<div class="col-4">
		<select class="form-select text-center fw-bold text-white bg-primary" id="sltName" onchange="f_NameChange()">
		<option value="All" <?echo ($displayedUser == 'All')?"selected":"";?> data-stocking-userstore="All">All</option>";
		<?
		$sql = "SELECT `c_id`, `c_name`, `c_store` FROM `t_user` WHERE NOT (`c_employee`='D')";
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) {
			if ($row['c_id'] == $displayedUser){
				$strSelected = "selected";
			}else{
				$strSelected = "";
			}
			echo "<option value=\"".$row['c_id']."\" ".$strSelected." data-stocking-userstore=\"".$row['c_store']."\">".$row['c_name']."</option>";
		}
		echo "</select>";
		?>
		</div>
		<div class="col-4"></div>
	</div>

	<div class="container">
		<div class="mb-2"><!--store switch-->
				<?
				$totalStore = count($arrayStore);
				for ($idxStore=0; $idxStore<$totalStore; $idxStore++){
					$strStoreChecked = "";
					if (($UserStore == "ALL") || ($arrayStore[$idxStore] == $UserStore) || ($UserIsAdmin)) {
						$strStoreDisplay = "";
						if (($getStore == NULL)||(strstr($getStore,$arrayStore[$idxStore]))){//if store is in GET store para, then display
							$strStoreChecked = "checked";
						}
					}else{
						$strStoreDisplay = " d-none";//hide the stores user is not assigned to
					}
				?>
				<div class="form-check form-switch form-check-inline me-5 <?echo $strStoreDisplay?>" id="<? echo "divBtnStore".$idxStore ?>">
					<input <?echo $strStoreChecked?> type="checkbox" class="form-check-input" name="btnStores" value="<? echo $arrayStore[$idxStore] ?>" id="<? echo "btnST".$idxStore ?>" onclick="f_storeSelected(<?echo $idxStore?>)">
					<label id="<? echo "lblST".$idxStore?>" class="form-check-label fw-bold" for="<? echo "btnST".$idxStore ?>"><? echo $arrayStore[$idxStore] ?></label>
				</div>
				<?
				}
				?>
		</div>
		<div class="row g-0 mb-1"><!--month switch-->
			<div class="input-group mb-1">
			  <button class="btn btn-primary" type="button" id="btnPre" onclick="f_lastMon()">&#8678;</button>
			  <input type="text" id="iptDate" data-stocking-year="<?echo $theYear?>" data-stocking-mon="<?echo $theMonth?>" class="form-control text-center fw-bold" value="<?echo $theMonthName." - ".$theYear ?>" disabled> <!--holds year and month of title. Note individual day may have diff year/mon value-->
				<button class="btn btn-primary" type="button" id="btnNext" onclick="f_nextMon()">&#8680;</button>
			</div>
		</div>
		<?
		$objDay = clone $objStartDay;//create new date object for loop
		for ($idxWeek = 1; $idxWeek < 6; $idxWeek++) { //display 5 weeks for current month
			$objWeek1stDay = clone $objDay; //create new date to store starting day of current week row
			echo "<div style=\"background:var(--bs-gray-400)\" class=\"mt-3 row row-cols-7 g-0 mb-1\">"; // row of days in the week
			for ($idxWD = 1; $idxWD < 8; $idxWD++){
				//check if it's holiday
				$sql = "SELECT `c_holiday` FROM `t_holiday` WHERE `c_date`='".date_format($objDay,'Y-m-d')."'";
				$holidayResult = $conn->query($sql);
				$holiday = $holidayResult->fetch_assoc();
				$mday = date('j',date_timestamp_get($objDay));
				if (((integer)($today->diff($objDay)->format("%R%a"))) == 0) {
					$bgToday = "bg-warning";
				}else{
					$bgToday = "";
				}
				if (is_null($holiday)) {
						$strClassHol = $mday;
					}else{
						$strClassHol = "<span class=\"text-danger\">".$mday."</span>";
				}
				$strDiv3B = "<div class=\"col ".$bgToday." text-center border-dark border-top border-start border-bottom fs-8\">".$strClassHol."<span class=\"text-muted\">";
				$strDivEnd = "</span></div>";
				switch ($idxWD) {
					case 1:
						echo $strDiv3B." M".$strDivEnd;
						break;
					case 2:
						echo $strDiv3B." T".$strDivEnd;
						break;
					case 3:
						echo $strDiv3B." W".$strDivEnd;
						break;
					case 4:
						echo $strDiv3B." T".$strDivEnd;
						break;
					case 5:
						echo $strDiv3B." F".$strDivEnd;
						break;
					case 6:
						echo $strDiv3B." S".$strDivEnd;
						break;
					case 7:
						echo "<div class=\"col ".$bgToday." text-center border border-dark fs-8\">".$strClassHol."<span class=\"text-muted\"> S".$strDivEnd;
				}
				date_add($objDay,date_interval_create_from_date_string("1 day"));
			}
			echo "</div>"; //row of days
			//display ppl for each store in each week day
			$rowStore = "";
			for ($idxStore = 0; $idxStore < $totalStore; $idxStore++) {
				$rowStore = $arrayStore[$idxStore];
				if (($UserStore == "ALL") || ($rowStore == $UserStore) || ($UserIsAdmin)) {
					if (($getStore == NULL)||(strstr($getStore,$rowStore))){
						$strStoreDisplay = ""; //if store is in GET store para, then display
					}else{
						$strStoreDisplay = " d-none";
					}
				}else{
					$strStoreDisplay = " d-none";//hide the stores user is not assigned to
				}
				//use store#_week# to name assignments in the week, to hide/show the assignments when toggling Only Me calendar
				$strStoreWeek = "Store".$idxStore."_".$idxWeek;
				//Use divStore# to name both store title and store lines to control hide/show of individual store
				echo "<div style=\"background:var(--bs-gray-200)\" class=\"row \"><span class=\"text-center".$strStoreDisplay."\" name=\"divStore".$idxStore."\"><strong>".$rowStore."</strong></span></div>";
				echo "<div class=\"row row-cols-7 g-0 mb-1".$strStoreDisplay."\" name=\"divStore".$idxStore."\">";
				$objDay = clone $objWeek1stDay; //counting day for store/ppl rows
				for ($idxWD = 1; $idxWD < 8; $idxWD++) {
					$isRowBlank = true;  //indicating no assigment is shown for current row.
					$mday = date('j',date_timestamp_get($objDay));
					$cellYear = date("Y",date_timestamp_get($objDay));
					$cellMon = date("n",date_timestamp_get($objDay));
					$cellID = $rowStore.$cellMon."_".$mday;
					if ($idxWD == 7) {
						$strBorder = "border";
					}else{
						$strBorder = "border-top border-start border-bottom";
					}
					$sqlStorePpl = "SELECT `c_minppl`,`c_maxppl` FROM `t_storeppl` WHERE `c_store`='".$rowStore."' AND `c_weekday`=".$idxWD;
					$resultPpl = $conn->query($sqlStorePpl);
					if ($rowPpl = $resultPpl->fetch_assoc()) {
						$MinPpl = $rowPpl["c_minppl"]; //min ppl required in this weekday for this store
						$MaxPpl = $rowPpl["c_maxppl"]; //max ppl required in this weekday for this store
					}else{ //in case store ppl value is not set
						$MinPpl = 2;
						$MaxPpl = 3;
					}
					$data_ppl = " data-stocking-minppl=".$MinPpl." data-stocking-maxppl=".$MaxPpl;
					if ($UserIsAdmin){//admin can edit all users and dates
						echo "<div class=\"col border-secondary ".$strBorder."\" onclick=\"f_editForAll('".$rowStore."',".$idxWD.",".$cellYear.",".$cellMon.",".$mday.")\" id=\"".$cellID."\"".$data_ppl.">";
					}else{//normal user edit future dates
						if (((integer)($today->diff($objDay)->format("%R%a"))) > 0){
							echo "<div class=\"col border-secondary ".$strBorder."\" onclick=\"f_cellSelected('".$rowStore."',".$idxWD.",".$cellYear.",".$cellMon.",".$mday.", false)\" id=\"".$cellID."\"".$data_ppl.">";
						}else{//past dates can't be edited by normal user
							echo "<div class=\"col border-secondary ".$strBorder."\" onclick=\"f_cellSelected('".$rowStore."',".$idxWD.",".$cellYear.",".$cellMon.",".$mday.", true)\" id=\"".$cellID."\"".$data_ppl.">";
						}
					}
					$sql = "SELECT `c_id`, `c_type`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins` FROM `t_calendar` WHERE `c_store`='".$rowStore."' AND `c_date`='".$cellYear."-".$cellMon."-".$mday."'";
					$result = $conn->query($sql);
					for ($idxPpl = 1; $idxPpl <= $MaxPpl; $idxPpl++) {
						$strDivClass = "<div name=\"".$strStoreWeek."\" class=\"text-center fs-6"; //name all assignme by store&week to toggle Only Me and all calendar
						$row = $result->fetch_assoc();
						if ($row){
							if ((($row['c_id']) == $displayedUser) || ($displayedUser == 'All')){
								$isRowBlank = false; //this row/week has current user assignment
							}else{
								if ((!($isRowBlank)) || ($idxPpl < $MaxPpl) || ($idxWD < 7)) {
									$strDivClass = $strDivClass . " d-none"; //hide the row if it's not the user to be displayed
								}else{
									$strDivClass = $strDivClass . " invisible"; //if whole row/week is blank, display a placehholder so that user can click to change assignment
									$isRowBlank = false;
								}
							}
							$c_type = $row['c_type'];
							$data_fullday = $row['c_fullday'];
							$data_timestart =  $row['c_timestart'];
							$data_timeend = $row['c_timeend'];
							$data_totalmins = $row['c_totalmins'];
							$strDivData = "\" data-stocking-fullday=".$data_fullday." data-stocking-timestart=\"".$data_timestart."\" data-stocking-timeend=\"".$data_timeend."\" data-stocking-totalmins=".$data_totalmins." id=\"".$cellID."_".$idxPpl."\">";
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
						}else{
							if ((!($isRowBlank)) || ($idxPpl < $MaxPpl) || ($idxWD < 7)) {
								$strDivClass = $strDivClass . " d-none"; //hide the row if it's not the user to be displayed
							}else{
								$strDivClass = $strDivClass . " invisible"; //if whole row/week is blank, display a placehholder so that user can click to change assignment
								$isRowBlank = false;
							}
							$strDivData = "\" data-stocking-fullday=\"\" data-stocking-timestart=\"\"  data-stocking-timeend=\"\" data-stocking-totalmins=\"\" id=\"".$cellID."_".$idxPpl."\">";
							if ($idxPpl <= $MinPpl) {
								echo $strDivClass." text-danger".$strDivData."*</div>";
							}else{
								echo $strDivClass.$strDivData."&nbsp;</div>";
							} //if min. ppl required is not reached
						} //if $row is not null
					}//for loop ppl in the week day
					echo "</div>";
					date_add($objDay,date_interval_create_from_date_string("1 day"));
				}//for loop weekday
				echo "</div>";
			}//for loop store
		}//for loop Weeks
		?>
</div> <!-- container -->

	<!-- Modal Submit-->
	<div class="modal fade" id="modal_box" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="lbl_modal"></h5>
				</div>
				<div class="modal-body fs-6" id="body_modal">
					<h6 class="mt-2 ms-1" id="lbl_msg"></h5>
					<div class="form-check form-switch form-check-inline ms-1 mb-3">
						<input class="form-check-input" type="checkbox" role="switch" id="checkWorking" onchange="f_ShiftChanged(0)">
						<label class="form-check-label" id="lbl_Working" for="checkFullDay"></label>
					</div>
					<div class="input-group ms-1">
						<div class="form-check form-switch form-check-inline align-self-center me-2">
							<input class="form-check-input" type="checkbox" role="switch" id="checkFullDay" onchange="f_ShiftChanged(1)">
							<label class="form-check-label" for="checkFullDay<?echo $idxTab?>">Full day</label>
						</div>
						<select class="form-select" id="sltTimeStart" onchange="f_ShiftChanged(1)">
							<?
								for ($idxTime = 0; $idxTime < 24; $idxTime++) {
									echo "<option value=\"".$idxTime.":00\">".$idxTime.":00"."</option>";
									echo "<option value=\"".$idxTime.":30\">".$idxTime.":30"."</option>";
								}
							?>
						</select>
						<span class="input-group-text">&rarr;</span>
						<select class="form-select" id="sltTimeEnd" onchange="f_ShiftChanged(1)">
							<?
								for ($idxTime = 0; $idxTime < 24; $idxTime++) {
									echo "<option value=\"".$idxTime.":00\">".$idxTime.":00"."</option>";
									echo "<option value=\"".$idxTime.":30\">".$idxTime.":30"."</option>";
								}
							?>
						</select>
					</div>
				</div><hr>
				<div class="row mb-3 ms-2">
					<div class="col-6 text-start" id="lbl_status"></div>
					<button type="button" class="col-2 btn btn-secondary me-2" id="btn_cancel" data-bs-dismiss="modal">Cancel</button>
					<button type="button" class="col-2 btn btn-primary" id="btn_ok" onclick="f_submit()">OK</button>
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
