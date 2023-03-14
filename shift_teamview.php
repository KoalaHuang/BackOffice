<? 
/*
Shift calendar view by team
GET para:
year: year of calendar
mon: month of calendar
user: employee ID to filter the calendar
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
	<script src="js/shift_teamview.js"></script>
</head>
<body>
	<h1 id="section_home" class="text-center mb-3">Team Shift</h1>
	<?
	$UserName = $_SESSION["user"];
	$UserID = "";
	$arrayStore = array();
    $arrayEmployee = array();
    $arrayEmployeeID = array();
    $arrayBkColor = ["--bs-success-bg-subtle","--bs-danger-bg-subtle","--bs-warning-bg-subtle","--bs-info-bg-subtle","--bs-success","--bs-danger","--bs-warning","--bs-info"];

	$getEmployee = $_GET['user']; //employ id sent as para

	include "connect_db.php";
	if ($getEmployee==NULL){
		$displayedUser = "All"; 
	}else{
		$displayedUser = $getEmployee; 
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

	<div class="container">
		<div id="txtUserName" class="d-none" data-stocking-userid="<?echo $UserID?>"><?echo $UserName?></div>
		<?
		$sql = "SELECT `c_id`, `c_name` FROM `t_user` WHERE NOT (`c_employee`='D')";
		$result = $conn->query($sql);
        $idx = 0;
		while($row = $result->fetch_assoc()) {
            $arrayEmployee[$idx] = $row['c_name'];
            $arrayEmployeeID[$idx++] = $row['c_id'];
        }
        $totalEmployee = $idx;
		?>
	</div>

	<div class="container">
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
			echo "<div style=\"background:var(--bs-gray-400)\" class=\"mt-3 row row-cols-8 g-0 mb-1\">"; // row of days in the week
            echo "<div class=\"col text-center border-dark border-top border-start border-bottom fs-8\"></div>";
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
			//display rows of employees for this week
			for ($idxEmployee = 0; $idxEmployee < $totalEmployee; $idxEmployee++) {
                echo "<div class=\"row row-cols-8 g-0 mb-1\">";
				$rowEmployee = $arrayEmployee[$idxEmployee];
                $rowEmployeeID = $arrayEmployeeID[$idxEmployee];
				echo "<div class=\"col border-top border-start border-bottom\">".$rowEmployee."</div>";
				$objDay = clone $objWeek1stDay; //counting day for store/ppl rows
				for ($idxWD = 1; $idxWD < 8; $idxWD++) {
					$mday = date('j',date_timestamp_get($objDay));
					$cellYear = date("Y",date_timestamp_get($objDay));
					$cellMon = date("n",date_timestamp_get($objDay));
					$cellID = $rowStore.$cellMon."_".$mday;
					if ($idxWD == 7) {
						$strBorder = " border";
					}else{
						$strBorder = " border-top border-start border-bottom";
					}
                    $sql = "SELECT `c_store`, `c_timestart`, `c_timeend` FROM `t_calendar` WHERE `c_id`='".$rowEmployeeID."' AND `c_date`='".$cellYear."-".$cellMon."-".$mday."'";
                    $result = $conn->query($sql);
                    if ($row = $result->fetch_assoc()) {
                        $strBkColor = $arrayBkColor[array_search($row['c_store'],$arrayStore)];
                        echo "<div class=\"col text-center".$strBorder."\" style=\"background:var(".$strBkColor.")\">".$row['c_store']."</div>";
                    }else{
                        echo "<div class=\"col text-center text-secondary".$strBorder."\">&nbsp;</div>";
                    }
					date_add($objDay,date_interval_create_from_date_string("1 day"));
				}//for loop weekday
				echo "</div>";
			}//for loop employee
		}//for loop Weeks
		?>
    </div> <!-- container -->
<?
$conn->close();
include "footer.php"
?>
</body>
</html>
