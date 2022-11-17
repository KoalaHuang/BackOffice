<?
  //apply shift template to date range in t_calendar
  //also apply full time employee leave data to t_leave with user working day setup
  session_start();

  header("Content-Type: application/json; charset=UTF-8");

  $str = file_get_contents('php://input');
  $obj = json_decode($str, false);

  if ($obj == null){
    echo "NULL JSON result from:".$str;
    die;
  }
  include "connect_db.php";
  include "mylog.php";

  $currentDate = date_create_from_format("Y/n/j",$obj->from);
  $endDate = date_create_from_format("Y/n/j",$obj->to);

  if ((!($currentDate)) || (!($endDate))) {
    echo json_encode("Dates Error!");
  }else{
    //clean t_calendar for affected days
    $sql = "DELETE FROM `t_calendar` WHERE `c_date` >= '".date_format($currentDate,'Y-m-d')."' AND `c_date` <= '".date_format($endDate,'Y-m-d')."'";
    $conn->query($sql);
    //clean t_leave for selected days
    $sql = "DELETE FROM `t_leave` WHERE `c_date` >= '".date_format($currentDate,'Y-m-d')."' AND `c_date` <= '".date_format($endDate,'Y-m-d')."'";
    $conn->query($sql);

    //get user data
    $arrayUserID = array();
    $arrayUserWorkday = array();
    $sql = "SELECT `c_name`,`c_id`,`c_employee` FROM `t_user` WHERE (NOT `c_store`='NONE')";
  	$userResult = $conn->query($sql);
  	$idx = 0;
  	while($row = $userResult->fetch_assoc()) {
  		$arrayUserID[$idx] = $row["c_id"];
      $arrayUserEmployee[$idx] = $row["c_employee"];//full time or part time employee
  		$idx++;
  	}
    $totalUser = $idx;
    //prepare t_calendar INSERT
    $stmt = $conn->prepare("INSERT INTO `t_calendar`(`c_date`, `c_id`, `c_store`, `c_type`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins`) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssii",$c_date,$c_id,$c_store,$c_type,$c_timestart,$c_timeend,$c_fullday,$c_totalmins);
    //prepare t_leave INSERT
    $stmt_leave = $conn->prepare("INSERT INTO `t_leave`(`c_date`, `c_id`, `c_type`) VALUES (?,?,'LO')");
    $stmt_leave->bind_param("ss",$c_date,$c_id);

    $result = true;
    //update t_calendar day by day
    while ($currentDate <= $endDate) {
      $currentWD = date("w",date_format($currentDate,"U"));
      if ($currentWD == 0) {$currentWD = 7;} //my calendar sunday is 7
      
      //apply shift to current date
      //check if current date is holiday
      $sql = "SELECT `c_holiday` FROM `t_holiday` WHERE `c_date`='".date_format($currentDate,'Y-m-d')."'";
      $holidayResult = $conn->query($sql);
      $holiday = $holidayResult->fetch_assoc();
      $isHoliday = (!(is_null($holiday)));
      //read shift template
      $sql = "SELECT `c_id`,`c_store`,`c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins` FROM `t_shifttemp` WHERE (`c_weekday`=".$currentWD.")";
    	$shiftTempResult = $conn->query($sql);
      while ($shiftTemp = $shiftTempResult->fetch_assoc()){
        $c_date = date_format($currentDate,'Y-m-d');
        $c_id = $shiftTemp['c_id'];
        $c_store = $shiftTemp['c_store'];
        $c_timestart = $shiftTemp['c_timestart'];
        $c_timeend = $shiftTemp['c_timeend'];
        $c_fullday = $shiftTemp['c_fullday'];
        $c_totalmins = $shiftTemp['c_totalmins'];
        if ($isHoliday) {
          $c_type = "HW";
        }else{
          $c_type = "WW";
        }// if HW
        $result = ($result && $stmt->execute());
      }//while loop shiftTemp result

      date_add($currentDate,date_interval_create_from_date_string("1 day"));
    }//while loop dates
    $stmt->close();
    $conn->close();
    echo json_encode($result);
  }
?>
