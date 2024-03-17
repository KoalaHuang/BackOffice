<?
/*
Shift repot by date range for team (all employees)
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("L")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
	<script src="js/shift_report.js"></script>
</head>
<body>
	<?
	$hideResult = true;
	$inputError = false;
	$isReportByDay = true;
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$fromDate = date_create_from_format("Y/n/j",$_POST["sltFromYear"]."/".$_POST["sltFromMon"]."/".$_POST["sltFromDay"]);
		$toDate = date_create_from_format("Y/n/j",$_POST["sltToYear"]."/".$_POST["sltToMon"]."/".$_POST["sltToDay"]);
		if ((is_null($fromDate)) || is_null($toDate)) {
			$inputError = true;
		}else{
			$dateCompare = date_diff($fromDate,$toDate)->format("%R");
			if (($dateCompare == NULL) || ($dateCompare != "+")) {
				$inputError = true;
			}else{
				$hideResult = false;
			}
		}
	}
	?>

	<div class="container">
		<h1 id="section_home" class="text-center mb-3">Shift Report</h1>
		<!--date form-->
		<form class="row g-0 mb-4 border bg-light" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
			<?
			$mydate=getdate(date("U"));
			$thisYear = $mydate['year'];
			$thisMonth = $mydate['mon'];
			$thisDate = $mydate['mday'];
			if ($inputError) {
				echo "<div class=\"text-danger fst-italic fs-6 ms-1 mb-1\">Date value error!</div>";
			}else{
				echo "<div class=\"text-muted fst-italic fs-6 ms-1 mb-1\">Report date range:</div>";
			}
			?>
			<div class="input-group ps-1 pe-1">
				<select class="form-select" name="sltFromYear" id="sltFromYear">
				<?
					if ($hideResult){
						$selectedTime = $thisYear; 
					}else{
						$selectedTime = date_format($fromDate,"Y");
					}
					for ($idxTime = $thisYear-1; $idxTime < $thisYear+2; $idxTime++) {
						$strDisplay = ">";
						if ($idxTime == $selectedTime){
							$strDisplay = " selected>";
						}
						echo "<option value=".$idxTime.$strDisplay.$idxTime."</option>";
					}
				?>
				</select>
				<select class="form-select" name="sltFromMon" id="sltFromMon">
				<?
					if ($hideResult){
						$selectedTime = $thisMonth; 
					}else{
						$selectedTime = date_format($fromDate,"n");
					}
					for ($idxTime = 1; $idxTime < 13; $idxTime++) {
						$strDisplay = ">";
						if ($idxTime == $selectedTime){
							$strDisplay = " selected>";
						}
						echo "<option value=".$idxTime.$strDisplay.$idxTime."</option>";
					}
				?>
				</select>
				<select class="form-select" name="sltFromDay" id="sltFromDay">
				<?
					if ($hideResult){
						$selectedTime = 1; 
					}else{
						$selectedTime = date_format($fromDate,"j");
					}
					for ($idxTime = 1; $idxTime < 32; $idxTime++) {
						$strDisplay = ">";
						if ($idxTime == $selectedTime){
							$strDisplay = " selected>";
						}
						echo "<option value=".$idxTime.$strDisplay.$idxTime."</option>";
					}
				?>
				</select>
			</div>
			<div class="text-center">&darr;</div>
			<div class="input-group mb-3  ps-1 pe-1">
				<select class="form-select" name="sltToYear" id="sltToYear">
				<?
					if ($hideResult){
						$selectedTime = $thisYear; 
					}else{
						$selectedTime = date_format($toDate,"Y");
					}
					for ($idxTime = $thisYear-1; $idxTime < $thisYear + 2; $idxTime++) {
						$strDisplay = ">";
						if ($idxTime == $selectedTime){
							$strDisplay = " selected>";
						}
						echo "<option value=".$idxTime.$strDisplay.$idxTime."</option>";
					}
				?>
				</select>
				<select class="form-select" name="sltToMon" id="sltToMon">
				<?
					if ($hideResult){
						$selectedTime = $thisMonth; 
					}else{
						$selectedTime = date_format($toDate,"n");
					}
					for ($idxTime = 1; $idxTime < 13; $idxTime++) {
						$strDisplay = ">";
						if ($idxTime == $selectedTime){
							$strDisplay = " selected>";
						}
						echo "<option value=".$idxTime.$strDisplay.$idxTime."</option>";
					}
				?>
				</select>
				<select class="form-select" name="sltToDay" id="sltToDay">
				<?
					if ($hideResult){
						$selectedTime = cal_days_in_month(CAL_GREGORIAN, $thisMonth, $thisYear); 
					}else{
						$selectedTime = date_format($toDate,"j");
					}
					for ($idxTime = 1; $idxTime < 32; $idxTime++) {
						$strDisplay = ">";
						if ($idxTime == $selectedTime){
							$strDisplay = " selected>";
						}
						echo "<option value=".$idxTime.$strDisplay.$idxTime."</option>";
					}
				?>
				</select>
			</div>
			<div class="mb-3 ps-1">
				<button type="button" class="btn btn-outline-primary me-2" onclick="f_changeMonth(-1)"> << </button>
				<button type="button" class="btn btn-outline-primary me-5" onclick="f_changeMonth(1)"> >> </button>
				<button type="submit" class="btn btn-primary me-2">Submit</button>
				<button type="reset" class="btn btn-secondary">Reset</button>
			</div><!-- Apply -->
		</form>
		<div class="card text-bg-light mb-2">
			<div class="card-body">
				<p class="card-text">Full time by <strong>days</strong>, Part time by <strong>hours</strong><br><strong>WW</strong>: normal working day, <strong>HW</strong>: working in holiday.<br><strong>B5</strong> hours before 5pm, <strong>A5</strong> hours after 5pm</p>
			</div>
		</div>		
		<?
		//retrive date into Arrays
		if (!($inputError || $hideResult)){
			$arrayUserName = array();
			$arrayUserType = array();
			$arrayUserID = array();
			$arrayStore = array();
			$arrayPeople = array(array(),array(),array());

			include "connect_db.php";
			//employee data
			$sql = "SELECT `c_name`,`c_id`,`c_employee` FROM `t_user` WHERE (`c_employee`='F' OR `c_employee`='P') AND (NOT (`c_store`='NONE'))";
			$result = $conn->query($sql);
			$idx = 0;
			while($row = $result->fetch_assoc()) {
				$arrayUserID[$idx] = $row["c_id"];
				$arrayUserName[$arrayUserID[$idx]] = $row["c_name"];
				$arrayUserType[$arrayUserID[$idx]] = $row["c_employee"];
				$idx++;
			}
			//store data
			$sql = "SELECT `c_name` FROM `t_store`";
			$result = $conn->query($sql);
			$idx = 0;
			while($row = $result->fetch_assoc()) {
				$arrayStore[$idx] = $row["c_name"];
				$idx++;
			}

			//Count shift data.  Full time by days, Part time by hours
			$idx = 0;
			//Count by days for full time
			$sql = "SELECT `c_id`, count(`c_date`) AS c_COUNT,`c_store`,`c_type` FROM `t_calendar` WHERE `c_date`>='".date_format($fromDate,"Y-m-d")."' AND `c_date`<='".date_format($toDate,"Y-m-d")."' GROUP BY `c_id`, `c_store`, `c_type`;";
			$result = $conn->query($sql);
			while($row = $result->fetch_assoc()) {
				$c_id = $row['c_id'];
				$c_count = $row['c_COUNT'];
				$c_store = $row['c_store'];
				$c_type = $row['c_type'];
				$arrayPeople[$c_id][$c_store][$c_type] = $c_count; //count the days by Id by store, by HW and WW
				$idx++;
			}
			//count by hours for part time, split by WW or HW
			//count hours by before 5pm and after 5pm due to different pay rate
			$sql = "SELECT `c_id`,`c_store`,`c_type`,SUM(`c_before5`) AS c_BEFORE5PM, SUM(`c_after5`) AS c_AFTER5PM FROM `t_calendar`
			WHERE `c_date`>='".date_format($fromDate,"Y-m-d")."' AND `c_date`<='".date_format($toDate,"Y-m-d")."' GROUP BY `c_id`, `c_store`, `c_type`";
			$result = $conn->query($sql);
			while($row = $result->fetch_assoc()) {
				$c_id = $row['c_id'];
				$before5pm = $row['c_BEFORE5PM'];
				$after5pm = $row['c_AFTER5PM'];
				$c_store = $row['c_store'];
				$c_type = $row['c_type'];
				$arrayPeople[$c_id][$c_store][$c_type.' B5'] = $before5pm/60; //store before 5pm hours count by ID by store by working type (WW or HW)
				$arrayPeople[$c_id][$c_store][$c_type.' A5'] = $after5pm/60; //store after 5pm hours count by ID by store by working type (WW or HW)
				$idx++;
			}

			$conn->close();
			//OFF Day working count
			//count working days in given period
			$stampFromDate = $fromDate->getTimestamp();
			date_add($toDate,date_interval_create_from_date_string("1 day"));//move out 1 day to include toDate in counting
			$stampToDate = $toDate->getTimestamp();
			$days_difference = floor(($stampToDate - $stampFromDate)/86400);
			$weeks_difference = floor($days_difference / 7); // Complete weeks
			$first_day = date("w", $stampFromDate);
			$days_remainder = floor($days_difference % 7);
			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
			if ($odd_days > 7) { // Sunday
				$days_remainder--;
			}
			if ($odd_days > 6) { // Saturday
				$days_remainder--;
			}
			$intWorkingDays = ($weeks_difference * 5) + $days_remainder;			
		}
		?>

		<!--Result Row-->
		<div class="row px-3 col mb-2 <?if ($hideResult) echo "d-none"?>">
		<?
		if (is_iterable($arrayUserID)) {
		    $countUserID = count($arrayUserID,1);
		}else{
		    die;
		}
		$xSum = array(); //sum for stores
		$ySum = array(); //sum for working types
		for ($idxPpl = 0; $idxPpl<$countUserID; $idxPpl++) {
			$c_id = $arrayUserID[$idxPpl];
			$c_name = $arrayUserName[$c_id];
			$ySum = array_fill(0,count($ySum),0);
			$xSum = array_fill(0,count($xSum),0);
			if ($arrayUserType[$c_id]=="F") {
				$arrayWorkType = ["WW","HW"];//full time display by days of working in working (WW) or holiday (HW)
			}else{
				$arrayWorkType = ["WW B5","WW A5", "HW B5", "HW A5"];//part time display by before 5pm and after 5pm hours by working day
			}

			echo "<a class=\"btn btn-outline-dark mb-1\" data-bs-toggle=\"collapse\" href=\"#rpt".$c_id."\" role=\"button\">".$c_name."</a>";
			echo "<div class=\"collapse\" id=\"rpt".$c_id."\">";
			echo "<div class=\"card card-body\"><table class=\"table\"><thead>";
			echo "<tr>";
			if ($arrayUserType[$c_id]=="F"){
				echo "<th scope=\"col\">In Day</th>";//counting full time by days
			}else{
				echo "<th scope=\"col\">In Hours</th>";//counting part time by hours
			}
			for ($idxType = 0; $idxType<count($arrayWorkType); $idxType++) {
				echo "<th scope=\"col\">".$arrayWorkType[$idxType]."</th>";
				$ySum[$arrayWorkType[$idxType]] = 0;
			}
			echo "<th class=\"table-secondary\" scope=\"col\">SUM</th>"; //column of store sum
			echo "</tr></thead><tbody>";
			for ($idxStore = 0; $idxStore<count($arrayStore); $idxStore++) {
				$c_store = $arrayStore[$idxStore];
				$xSum[$idxStore]=0;
				echo "<tr>";
				echo "<th scope=\"col\">".$c_store."</th>";
				for ($idxType = 0; $idxType<count($arrayWorkType); $idxType++) {
					$c_type = $arrayWorkType[$idxType];
					if (is_null($arrayPeople[$c_id][$c_store][$c_type])) {
						$c_count = 0;
					}else{
						$c_count = $arrayPeople[$c_id][$c_store][$c_type];
					} //if count is null
					echo "<td scope=\"col\">".$c_count."</th>";
					$ySum[$c_type] = $ySum[$c_type] + $c_count;
					$xSum[$idxStore] = $xSum[$idxStore] + $c_count;
				} //for loop type
				echo "<td class=\"table-secondary\" scope=\"col\">".$xSum[$idxStore]."</th>"; //sum of this store
				echo "</tr>";
			} // for loop store
	
			//Working type SUM
			echo "<tr>";
			echo "<th class=\"table-secondary\" scope=\"col\">SUM</th>";
			for ($totalWork = 0, $idxType = 0; $idxType<count($arrayWorkType); $idxType++) {
				$c_type = $arrayWorkType[$idxType];
				$totalWork = $totalWork + $ySum[$c_type]; //sum total working days/hours
				echo "<td class=\"table-secondary\" scope=\"col\">".$ySum[$c_type]."</th>";
			}
			echo "<td class=\"table-dark text-white\" scope=\"col\">".$totalWork."</th>"; //TOTAL
			echo "</tr>";

			//For full time, to count OFF day working by comparing with calendar working days
			if (($arrayUserType[$c_id]=="F") AND ($ySum["WW"] - $intWorkingDays) > 0){
				echo "<tr>";
				echo "<td class=\"table-secondary fst-italic\" scope=\"col\" colspan=\"".(count($arrayWorkType)+1)."\">Besides ".$intWorkingDays." weekday, OFF time working</td>";
				echo "<td class=\"table-secondary\" scope=\"col\">".($ySum["WW"] - $intWorkingDays)."</td>";
				echo "</tr>";
			}
	
			echo "</tbody></table>";
			echo "</div>";
			echo "</div>";
		} // for loop ppl
		?>
		</div> <!-- result row-->

	<?
	include "footer.php";
	?>
</body>
</html>
