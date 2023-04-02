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
    try{
      $conn->autocommit(false);
      $conn->begin_transaction();

      //remove current shift arrangement
      $sql = "DELETE FROM `t_calendar` WHERE `c_date`='".date_format($currentDate,'Y-m-d')."' AND `c_store`='".$obj[0]->store."'";
      $result = $conn->query($sql);
      if (!$result) throw new Exception('Error deleting existing assignments!');

      for ($idx = 0; $idx < count($obj); $idx++) {
        $c_id = $obj[$idx]->id;
        if ($c_id != ""){ //if id is "", it means remove all assignmens
          //check if user is used assigned on same day
          $sql = "SELECT `c_store` FROM `t_calendar` WHERE (`c_id`='".$c_id."' AND `c_date`='".date_format($currentDate,'Y-m-d')."')";
          $chkResult = $conn->query($sql);
          if ($row = $chkResult->fetch_assoc()) {
            $chkStore = $row['c_store'];
            throw new Exception($c_id." already working in ".$chkStore." on ".date_format($currentDate,'Y-m-d'));
          }
          $c_store= $obj[$idx]->store;
          //check if it's holiday
          $sql = "SELECT `c_holiday` FROM `t_holiday` WHERE `c_date`='".date_format($currentDate,'Y-m-d')."'";
          $holidayResult = $conn->query($sql);
          $holiday = $holidayResult->fetch_assoc();
          $isHoliday = (!(is_null($holiday)));
          if ($isHoliday) {
            $c_type = "HW";
          }else{
              $c_type = "WW";
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
          if (!$result) throw new Exception('Error updating assignment for '.$c_id.'!');
          $stmt->close();
        }
      }
      if ($conn->commit()){
        $result = true;
      }else{
        throw new Exception('Error commit database changes!');
      }
    } catch (Exception $e){
      $conn->rollback();
      $result = $e->getMessage();//when database error happens, return error code
    }
    $conn->close();
    echo json_encode($result);
  }else{
    echo json_encode("Date Error!".$str);
  }
?>
