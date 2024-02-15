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
		//$isReportByDay = ($_POST["reportBy"] == "day"); FT by day, PT by hours. no para required
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
			<div class="text-secondary fw-semibold fs-6 ms-1 mb-1">Full time by days, Part time by hours</div>			
		</form>
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
			$sql = "SELECT `c_name`,`c_id`,`c_employee` FROM `t_user` WHERE (NOT ((`c_store`='NONE') OR (`c_employee`='D')))";
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

			//count shift data
			$idx = 0;
			//Count by days for full time
			$sql = "SELECT count(`t_calendar`.`c_date`) AS c_COUNT,`t_calendar`.`c_id`,`t_calendar`.`c_store`,`t_calendar`.`c_type` FROM `t_calendar` JOIN `t_user` WHERE `t_user`.`c_employee` = 'F' AND `t_user`.`c_id`=`t_calendar`.`c_id` AND `t_calendar`.`c_date`>='".date_format($fromDate,"Y-m-d")."' AND `t_calendar`.`c_date`<='".date_format($toDate,"Y-m-d")."' GROUP BY `t_calendar`.`c_id`, `t_calendar`.`c_store`, `t_calendar`.`c_type`;";
			$result = $conn->query($sql);

			while($row = $result->fetch_assoc()) {
				$c_id = $row['c_id'];
				$c_name = $arrayUserName[$c_id];
				$c_count = $row['c_COUNT'];
				$c_store = $row['c_store'];
				$c_type = $row['c_type'];
				$arrayPeople[$c_id][$c_store][$c_type] = $c_count; //count the days by Id by store, by HW and WW
				$idx++;
			}

			//count by hours for part time, split by before 5pm and after 5pm
			//count hours by before 5pm and after 5pm due to different pay rate
			//need to cosider hours may fall all into before or after 5pm scenarios
			//if it's full day, before5pm counted as 5 hours, after5pm counted as 4 hours (total 9 hours)

			$sql = "SELECT `t_calendar`.`c_id`, `t_calendar`.`c_store`,
			SUM(CASE
				WHEN `t_calendar`.`c_fullday` = 1 THEN 5
				WHEN timediff(`t_calendar`.`c_timeend`,'17:00')<0 THEN hour(timediff(`t_calendar`.`c_timeend`,`t_calendar`.`c_timestart`))
				WHEN timediff(`t_calendar`.`c_timeend`,'17:00')>=0 AND timediff(`t_calendar`.`c_timestart`,'17:00')>=0 THEN 0
				ELSE hour(timediff('17:00',`t_calendar`.`c_timestart`))
			END) AS c_BEFORE5PM,
			
			SUM(CASE
				WHEN `t_calendar`.`c_fullday` = 1 THEN 4
				WHEN timediff(`t_calendar`.`c_timestart`,'17:00')>0 THEN hour(timediff(`t_calendar`.`c_timeend`,`t_calendar`.`c_timestart`))
				WHEN timediff(`t_calendar`.`c_timestart`,'17:00')<=0 AND timediff(`t_calendar`.`c_timeend`,'17:00')<=0 THEN 0
				ELSE hour(timediff(`t_calendar`.`c_timeend`,'17:00'))
			END) AS c_AFTER5PM
			FROM `t_calendar`
			JOIN `t_user` WHERE `t_user`.`c_employee` = 'P' AND `t_user`.`c_id`=`t_calendar`.`c_id` AND
			`t_calendar`.`c_date`>='".date_format($fromDate,"Y-m-d")."' AND `t_calendar`.`c_date`<='".date_format($toDate,"Y-m-d")."' GROUP BY `t_calendar`.`c_id`,`t_calendar`.`c_store`";

			$result = $conn->query($sql);

			while($row = $result->fetch_assoc()) {
				$c_id = $row['c_id'];
				$c_name = $arrayUserName[$c_id];
				$before5pm = $row['c_BEFORE5PM'];
				$after5pm = $row['c_AFTER5PM'];
				$c_store = $row['c_store'];
				$arrayPeople[$c_id][$c_store]['B5'] = $before5pm;//store before 5pm hours count by ID by store
				$arrayPeople[$c_id][$c_store]['A5'] = $after5pm; //store after 5pm hours count by ID by store
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
			if ($isReportByDay){
				$intWorkingDays = ($weeks_difference * 5) + $days_remainder;			
			}else{
				$intWorkingDays = (($weeks_difference * 5) + $days_remainder) * 10;		
			}
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
		$xSum = array(); //store sum of working days for each type
		$ySum = array(); //store sum of working days for each store
		for ($idxPpl = 0; $idxPpl<$countUserID; $idxPpl++) {
			$c_id = $arrayUserID[$idxPpl];
			$c_name = $arrayUserName[$c_id];
			$ySum = array_fill(0,count($ySum),0);
			$xSum = array_fill(0,count($xSum),0);

			echo "<a class=\"btn btn-outline-dark mb-1\" data-bs-toggle=\"collapse\" href=\"#rpt".$c_id."\" role=\"button\">".$c_name."</a>";
			echo "<div class=\"collapse\" id=\"rpt".$c_id."\">";
			echo "<div class=\"card card-body\"><table class=\"table\"><thead>";
			echo "<tr>";
			echo "<th scope=\"col\">Type</th>";
			for ($idxStore = 0; $idxStore<count($arrayStore); $idxStore++) {
				echo "<th scope=\"col\">".$arrayStore[$idxStore]."</th>";
				$ySum[$idxStore] = 0;
			}
			echo "<th class=\"table-secondary\" scope=\"col\">SUM</th>"; //work type sum
			echo "</tr></thead><tbody>";
			if ($arrayUserType[$c_id]=="F") {
				$arrayWorkType = ["WW","HW"];//full time display by days of working in working (WW) or holiday (HW)
			}else{
				$arrayWorkType = ["B5","A5"];//part time display by before 5pm and after 5pm hours counting
			}

			for ($idxType = 0; $idxType<count($arrayWorkType); $idxType++) {
				$c_type = $arrayWorkType[$idxType];
				echo "<tr>";
				echo "<th scope=\"col\">".$c_type."</th>";
				for ($idxStore = 0; $idxStore<count($arrayStore); $idxStore++) {
					$c_store = $arrayStore[$idxStore];
					if (is_null($arrayPeople[$c_id][$c_store][$c_type])) {
						$c_count = 0;
					}else{
						$c_count = $arrayPeople[$c_id][$c_store][$c_type];
					} //if count is null
					echo "<td scope=\"col\">".$c_count."</th>";
					$xSum[$c_type] = $xSum[$c_type] + $c_count;
					$ySum[$idxStore] = $ySum[$idxStore] + $c_count;
				} //for loop store
				echo "<td class=\"table-secondary\" scope=\"col\">".$xSum[$c_type]."</th>"; //sum of this work type from all stores.
				echo "</tr>";
			} // for loop type
			//Store SUM
			echo "<tr>";
			echo "<th class=\"table-secondary\" scope=\"col\">SUM</th>";
			for ($idxStore = 0, $totalWork=0; $idxStore<count($arrayStore); $idxStore++) {
				$totalWork = $totalWork + $ySum[$idxStore];
				echo "<td class=\"table-secondary\" scope=\"col\">".$ySum[$idxStore]."</th>";
			}
			echo "<td class=\"table-dark text-white\" scope=\"col\">".$totalWork."</th>"; //store sum
			echo "</tr>";
			//OFF day working count for FULL TIME employee
			if (($arrayUserType[$c_id]=="F") AND ($xSum["WW"] - $intWorkingDays) > 0){
				if ($isReportByDay){$strWorking = "days";}else{$strWorking = "hours";}
				echo "<tr>";
				echo "<td class=\"table-secondary fst-italic\" scope=\"col\" colspan=\"".(count($arrayStore)+1)."\">Besides ".$intWorkingDays." week ".$strWorking.", OFF time working</td>";
				echo "<td class=\"table-secondary\" scope=\"col\">".($xSum["WW"] - $intWorkingDays)."</td>";
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
