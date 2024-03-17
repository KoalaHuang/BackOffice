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
          //if employee is part time, need to calculate before and after 5pm hours
          $sql = "SELECT `c_employee` FROM `t_user` WHERE `c_id`='".$c_id."'";
          $EmployeeResult = $conn->query($sql);
          $row = $EmployeeResult->fetch_assoc();
          $c_employeetype = $row['c_employee'];//get the employeetype F or P

          $stmt = $conn->prepare("INSERT INTO `t_calendar`(`c_date`, `c_id`, `c_store`, `c_type`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins`, `c_employeetype`, `c_before5`, `c_after5`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
          $stmt->bind_param("ssssssiisii",$c_date,$c_id,$c_store,$c_type,$c_timestart,$c_timeend,$c_fullday,$c_totalmins,$c_employeetype,$c_before5,$c_after5);
          $c_date = date_format($currentDate,'Y-m-d');
          $c_store = $obj[$idx]->store;
          $c_timestart = $obj[$idx]->timestart;
          $c_timeend = $obj[$idx]->timeend;
          $c_fullday = $obj[$idx]->fullday;
          $c_totalmins = $obj[$idx]->totalmins;
          $c_after5 = 0;
          $c_before5 = 0;
          if ($c_employeetype == 'P') {
            if ($c_fullday == 1){
              $c_after5 = 240;  //if full day is selected, counted as 4 hours after 5pm
              $c_before5 = 300; //5 hours before 5pm
            }else{
              //count working hours before 5pm and after 5pm respectively
              $StartTo5 = date_diff(date_create($c_timestart),date_create("17:00"));
              $EndTo5 = date_diff(date_create($c_timeend),date_create("17:00"));
              $Diff = date_diff(date_create($c_timestart),date_create($c_timeend));
              if ($StartTo5->format("%R") == '-'){ 
                $c_before5 = 0; //start after 5pm
              }else{
                if ($EndTo5->format("%R") == '-'){
                  $c_before5 = (int)$StartTo5->format("%h")*60 + (int)$StartTo5->format("%i"); //end after 5pm
                  }else{
                      $c_before5 = (int)$Diff->format("%h")*60 + (int)$Diff->format("%i"); //end before 5pm
                  }
              }
              
              if ($EndTo5->format("%R") == '+'){ 
                $c_after5 = 0; //End before 5pm
              }else{
                if ($StartTo5->format("%R") == '+'){
                  $c_after5 = (int)$EndTo5->format("%h")*60 + (int)$EndTo5->format("%i"); //start before 5pm
                  }else{
                      $c_after5 = (int)$Diff->format("%h")*60 + (int)$Diff->format("%i"); //start after 5pm
                  }
              }
              
              if ($c_after5 + $c_before5 >= 600){
                $c_before5 = $c_before5 - 60;  //when total working hour is more than 10, deduct 1 hour from before 5pm as resting hour
              }
            }
          }// if part time
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
