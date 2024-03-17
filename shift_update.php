<?
  //update shift for individual in t_calendar
  session_start();

  header("Content-Type: application/json; charset=UTF-8");

  $str = file_get_contents('php://input');
  $obj = json_decode($str, false);
  if ($obj == null){
    echo json_encode("NULL JSON result from:".$str);
    die;
  }
  include "whatsapp.php"; //send notification

  include "connect_db.php";
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  $strPostedDate = $obj->year."/".$obj->mon."/".$obj->mday;
  $currentDate = date_create_from_format("Y/n/j",$strPostedDate);
  //Validate Date value
  if (!(($currentDate) && (date_format($currentDate,"Y/n/j") == $strPostedDate))) {
    echo json_encode("Dates Error!");
    die;
  }
  $noticeMsg = array("Shift Updated");
  array_push($noticeMsg,"Name: ".$obj->id);
  array_push($noticeMsg,"Date: ".date_format($currentDate,'Y-m-d'));
  array_push($noticeMsg,"Store: ".$obj->store);

  switch ($obj->status) {
    case 0://remove working assignment
      $sql = "DELETE FROM `t_calendar` WHERE `c_date`='".date_format($currentDate,'Y-m-d')."' AND `c_id`='".$obj->id."' AND `c_store`='".$obj->store."'";
      $result = $conn->query($sql);
      array_push($noticeMsg,"Change: removed from shift");
      break;
    case 1: //add working assignment
      //check if user is used assigned on same day
      $sql = "SELECT `c_store` FROM `t_calendar` WHERE (`c_id`='".$obj->id."' AND `c_date`='".date_format($currentDate,'Y-m-d')."')";
    	$chkResult = $conn->query($sql);
      if ($row = $chkResult->fetch_assoc()) {
        $chkStore = $row['c_store'];
        echo json_encode($obj->id." already working in ".$chkStore." on ".date_format($currentDate,'Y-m-d'));
        die;
      }
      array_push($noticeMsg,"Change: added to shift");
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
      $stmt = $conn->prepare("INSERT INTO `t_calendar`(`c_date`, `c_id`, `c_store`, `c_type`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins`, `c_employeetype`, `c_before5`, `c_after5`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
      $stmt->bind_param("ssssssiisii",$c_date,$c_id,$c_store,$c_type,$c_timestart,$c_timeend,$c_fullday,$c_totalmins,$c_employeetype,$c_before5,$c_after5);
      $result = true;
      $c_date = date_format($currentDate,'Y-m-d');
      $c_id = $obj->id;
      $c_store = $obj->store;
      $c_timestart = $obj->timestart;
      $c_timeend = $obj->timeend;
      $c_fullday = $obj->fullday;
      $c_totalmins = $obj->totalmins;
      //if employee is part time, need to calculate before and after 5pm hours
      $sql = "SELECT `c_employee` FROM `t_user` WHERE `c_id`='".$c_id."'";
      $EmployeeResult = $conn->query($sql);
      $row = $EmployeeResult->fetch_assoc();
      $c_employeetype = $row['c_employee'];//get the employeetype F or P
      $c_after5 = 0;
      $c_before5 = 0;
      f_count5pm($c_fullday,$c_timestart,$c_timeend,$c_after5,$c_before5);
      $result = $stmt->execute();
      $stmt->close();
      break;
    case 2:  //update existing assignment's working time
      $stmt = $conn->prepare("UPDATE `t_calendar` SET `c_timestart`=?,`c_timeend`=?,`c_fullday`=?,`c_totalmins`=?, `c_before5`=?, `c_after5`=? WHERE `c_date`=? AND `c_id`=? AND `c_store`=?");
      $stmt->bind_param("ssiiiisss",$c_timestart,$c_timeend,$c_fullday,$c_totalmins,$c_before5,$c_after5,$c_date,$c_id,$c_store);
      $result = true;
      $c_date = date_format($currentDate,'Y-m-d');
      $c_id = $obj->id;
      $c_store = $obj->store;
      $c_timestart = $obj->timestart;
      $c_timeend = $obj->timeend;
      $c_fullday = $obj->fullday;
      $c_totalmins = $obj->totalmins;
      //if employee is part time, need to calculate before and after 5pm hours
      $sql = "SELECT `c_employee` FROM `t_user` WHERE `c_id`='".$c_id."'";
      $EmployeeResult = $conn->query($sql);
      $row = $EmployeeResult->fetch_assoc();
      $c_employeetype = $row['c_employee'];//get the employeetype F or P
      $c_after5 = 0;
      $c_before5 = 0;
      f_count5pm($c_fullday,$c_timestart,$c_timeend,$c_after5,$c_before5);
      array_push($noticeMsg,"Change: shift time ".$c_timestart." to ".$c_timeend);
      $result = $stmt->execute();
      $stmt->close();
      break;
    default:
      echo json_encode("Error request: ".$obj->status);
  }
  //send notification
  send_notice("H",$noticeMsg); //send email and whatsapp notice

  $conn->close();
  echo json_encode($result);

  //function to count part time work hours before and after 5pm
  function f_count5pm ($c_fullday, $c_timestart, $c_timeend, &$c_after5, &$c_before5) {
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
  }

?>
