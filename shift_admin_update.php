<?
  /*update shift changed by admin into t_calendar
  Parameter:
    Array of object {store, weekday, id, year, mon, day, timestart, timeend, fullday, totalmins}
    if id = "", then remove all assignments.
  */
  session_start();

  header("Content-Type: application/json; charset=UTF-8");

  $str = file_get_contents('php://input');
  $obj = json_decode($str, false);

  if ($obj == null){
    echo json_encode("NULL JSON result from:".$str, false);
    die;
  }
  include "connect_db.php";
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  $strPostedDate = $obj[0]->year."/".$obj[0]->mon."/".$obj[0]->day;
  $currentDate = date_create_from_format("Y/n/j",$strPostedDate);
  if (($currentDate) && (date_format($currentDate,"Y/n/j") == $strPostedDate)) {

    //remove current shift arrangement
    $sql = "DELETE FROM `t_calendar` WHERE `c_date`='".date_format($currentDate,'Y-m-d')."' AND `c_store`='".$obj[0]->store."'";
    $result = $conn->query($sql);

    for ($idx = 0; $idx < count($obj); $idx++) {
      $c_id = $obj[$idx]->id;
      if ($c_id != ""){ //if id is "", it means remove all assignmens
        $c_store= $obj[$idx]->store;
        $c_weekday = $obj[$idx]->weekday;
        //get user workday
        $sql = "SELECT `c_workday` FROM `t_user` WHERE (`c_id`='".$c_id."')";
        $wdResult = $conn->query($sql);
        if ($row = $wdResult->fetch_assoc()) {
          $userWD = $row['c_workday'];
        }else{
          echo json_encode("weekday data error!".$sql);
          die;
        }
        //check if it's holiday
        $sql = "SELECT `c_holiday` FROM `t_holiday` WHERE `c_date`='".date_format($currentDate,'Y-m-d')."'";
        $holidayResult = $conn->query($sql);
        $holiday = $holidayResult->fetch_assoc();
        $isHoliday = (!(is_null($holiday)));
        if ($isHoliday) {
          $c_type = "HW";
        }else{
          if (strstr($userWD,$c_weekday)) {
            $c_type = "WW";
          }else{
            $c_type = "OW";
          }
        }// if HW
        $stmt = $conn->prepare("INSERT INTO `t_calendar`(`c_date`, `c_id`, `c_store`, `c_type`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssii",$c_date,$c_id,$c_store,$c_type,$c_timestart,$c_timeend,$c_fullday,$c_totalmins);
        $c_date = date_format($currentDate,'Y-m-d');
        $c_id = $obj[$idx]->id;
        $c_store = $obj[$idx]->store;
        $c_timestart = $obj[$idx]->timestart;
        $c_timeend = $obj[$idx]->timeend;
        $c_fullday = $obj[$idx]->fullday;
        $c_totalmins = $obj[$idx]->totalmins;
        $result = $stmt->execute();
        $stmt->close();
      }
    }
    $conn->close();
    echo json_encode($result);
  }else{
    echo json_encode("Date Error!".$str);
  }
?>
