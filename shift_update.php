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
      //get user workday
      $sql = "SELECT `c_workday` FROM `t_user` WHERE (`c_id`='".$obj->id."')";
    	$wdResult = $conn->query($sql);
      if ($row = $wdResult->fetch_assoc()) {
        $userWD = $row['c_workday'];
      }else{
        echo json_encode("Employee weekday data error!");
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
        if (strstr($userWD,(string)$obj->wd)) {
          $c_type = "WW";
        }else{
          $c_type = "OW";
        }
      }// if HW
      $stmt = $conn->prepare("INSERT INTO `t_calendar`(`c_date`, `c_id`, `c_store`, `c_type`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins`) VALUES (?,?,?,?,?,?,?,?)");
      $stmt->bind_param("ssssssii",$c_date,$c_id,$c_store,$c_type,$c_timestart,$c_timeend,$c_fullday,$c_totalmins);
      $result = true;
      $c_date = date_format($currentDate,'Y-m-d');
      $c_id = $obj->id;
      $c_store = $obj->store;
      $c_timestart = $obj->timestart;
      $c_timeend = $obj->timeend;
      $c_fullday = $obj->fullday;
      $c_totalmins = $obj->totalmins;
      $result = $stmt->execute();
      $stmt->close();
      break;
    case 2:  //update existing assignment's working time
      array_push($noticeMsg,"Change: change shift timing as ".$c_timestart." to ".$c_timeend);
      $stmt = $conn->prepare("UPDATE `t_calendar` SET `c_timestart`=?,`c_timeend`=?,`c_fullday`=?,`c_totalmins`=? WHERE `c_date`=? AND `c_id`=? AND `c_store`=?");
      $stmt->bind_param("ssiisss",$c_timestart,$c_timeend,$c_fullday,$c_totalmins,$c_date,$c_id,$c_store);
      $result = true;
      $c_date = date_format($currentDate,'Y-m-d');
      $c_id = $obj->id;
      $c_store = $obj->store;
      $c_timestart = $obj->timestart;
      $c_timeend = $obj->timeend;
      $c_fullday = $obj->fullday;
      $c_totalmins = $obj->totalmins;
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
?>
