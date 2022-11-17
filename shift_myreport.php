<?
/*
Shift repot by date range for current user
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("E")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
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
		$isReportByDay = ($_POST["reportBy"] == "day");
	}
	?>

	<div class="container">
		<h1 id="section_home" class="text-center mb-3">My Shift Report</h1>
		<!--date form-->
		<form class="row g-0 mb-4 border bg-light" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
			<?
			$mydate=getdate(date("U"));
			$thisYear = $mydate['year'];
			$thisMonth = $mydate['mon'];
			$thisDate = $mydate['mday'];
			if ($inputError) {
				echo "<div class=\"text-danger fst-italic fs-6 mb-1\">Date value error!</div>";
			}else{
				echo "<div class=\"text-muted fst-italic fs-6 mb-1\">Report date range:</div>";
			}
			?>
			<div class="input-group ps-1 pe-1">
				<select class="form-select" name="sltFromYear">
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
				<select class="form-select" name="sltFromMon">
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
				<select class="form-select" name="sltFromDay">
				<?
					if ($hideResult){
						$selectedTime = $thisDate; 
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
				<select class="form-select" name="sltToYear">
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
				<select class="form-select" name="sltToMon">
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
				<select class="form-select" name="sltToDay">
				<?
					if ($hideResult){
						$selectedTime = $thisDate; 
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
			<div class="ps-1 mb-3">
				<div class="form-check form-check-inline me-5">
					<input <?if ($isReportByDay){echo "checked";}?> type="radio" class="form-check-input" name="reportBy" value="day" id="radioByDay">
					<label class="form-check-label" for="radioByDay">Count by day</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="radio" <?if (!$isReportByDay){echo "checked";}?> class="form-check-input" name="reportBy" value="hour" id="radioByMins">
					<label class="form-check-label" for="radioByMins">Count by hour</label>
				</div>
			</div>
			<div class="mb-3 ps-1">
				<button type="submit" class="btn btn-primary me-5">Submit</button>
				<button type="reset" class="btn btn-secondary">Reset</button>
			</div><!-- Apply -->
		</form>

		<?
		//retrive date into Arrays
		if (!($inputError || $hideResult)){
			$arrayUserName = array();
			$arrayUserID = array();
			$arrayStore = array();
			$arrayWorkType = ["WW","HW"];
			$arrayPeople = array(array(),array(),array());

			include "connect_db.php";
			$sql = "SELECT `c_name`,`c_id` FROM `t_user` WHERE `c_id`='".$_SESSION["id"]."'";
			$result = $conn->query($sql);
			$idx = 0;
			while($row = $result->fetch_assoc()) {
				$arrayUserID[$idx] = $row["c_id"];
				$arrayUserName[$arrayUserID[$idx]] = $row["c_name"];
				$idx++;
			}
			$sql = "SELECT `c_name` FROM `t_store`";
			$result = $conn->query($sql);
			$idx = 0;
			while($row = $result->fetch_assoc()) {
				$arrayStore[$idx] = $row["c_name"];
				$idx++;
			}
			if ($isReportByDay){
				$fieldTotal = "count(`c_date`)";
			}else{
				$fieldTotal = "sum(`c_totalmins`)";
			}
			$sql = "SELECT ".$fieldTotal.",`c_id`,`c_store`,`c_type` FROM `t_calendar` WHERE `c_id`='".$_SESSION["id"]."'  AND `c_date`>='".date_format($fromDate,"Y-m-d")."' AND `c_date`<='".date_format($toDate,"Y-m-d")."' GROUP BY `c_id`,`c_store`, `c_type`;";
			$result = $conn->query($sql);
			if ($result) {
                $idx = 0;
                while($row = $result->fetch_assoc()) {
                    $c_id = $row['c_id'];
                    $c_name = $arrayUserName[$c_id];
                    $c_count = $row[$fieldTotal];
                    $c_store = $row['c_store'];
                    $c_type = $row['c_type'];
                    if ($isReportByDay){
                        $arrayPeople[$c_id][$c_store][$c_type] = $c_count; //count the days
                    }else{
                        $arrayPeople[$c_id][$c_store][$c_type] = round($c_count/60,1); //count the hours
                    }
                    $idx++;
                }
                $conn->close();
            }
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
		$ySum = array(); //store sum of working days for each store
		$xSum = array(); //store sum of working days for each type
		for ($idxPpl = 0; $idxPpl<$countUserID; $idxPpl++) {
			$c_id = $arrayUserID[$idxPpl];
			$c_name = $arrayUserName[$c_id];
			array_fill(0,count($ySum),0);
			array_fill(0,count($xSum),0);

			echo "<a class=\"btn btn-outline-dark mb-1\" data-bs-toggle=\"collapse\" href=\"#rpt".$c_id."\" role=\"button\">".$c_name."</a>";
			echo "<div class=\"collapse\" id=\"rpt".$c_id."\">";
			echo "<div class=\"card card-body\"><table class=\"table\"><thead>";
			echo "<tr>";
			echo "<th scope=\"col\">Type</th>";
			for ($idxStore = 0; $idxStore<count($arrayStore); $idxStore++) {
				echo "<th scope=\"col\">".$arrayStore[$idxStore]."</th>";
			}
			echo "<th class=\"table-secondary\" scope=\"col\">SUM</th>"; //work type sum
			echo "</tr></thead><tbody>";
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
			//OFF day working count
			if (($xSum["WW"] - $intWorkingDays) > 0){
				echo "<tr>";
				echo "<td class=\"table-secondary\" scope=\"col\" colspan=\"".(count($arrayStore)+1)."\">Besides ".$intWorkingDays." weekday, OFF time works</td>";
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
