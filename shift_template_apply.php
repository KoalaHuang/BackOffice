<?
  //apply shift template to date range in t_calendar
  session_start();

  header("Content-Type: application/json; charset=UTF-8");

  $str = file_get_contents('php://input');
  $obj = json_decode($str, false);

  if ($obj == null){
    echo "NULL JSON result from:".$str;
    die;
  }
  include "connect_db.php";

  $currentDate = date_create_from_format("Y/n/j",$obj->from);
  $endDate = date_create_from_format("Y/n/j",$obj->to);

  if ((!($currentDate)) || (!($endDate))) {
    echo json_encode("Dates Error!");
  }else{
    //clean t_calendar for affected days
    $sql = "DELETE FROM `t_calendar` WHERE `c_date` >= '".date_format($currentDate,'Y-m-d')."' AND `c_date` <= '".date_format($endDate,'Y-m-d')."'";
    $conn->query($sql);

    //get user data
    $arrayUserEmployee = array();
    $sql = "SELECT `c_name`,`c_id`,`c_employee` FROM `t_user` WHERE (NOT `c_store`='NONE') AND (`c_employee`='F' OR `c_employee`='P')";
  	$userResult = $conn->query($sql);
  	while($row = $userResult->fetch_assoc()) {
      $arrayUserEmployee[$row["c_id"]] = $row["c_employee"];//full time or part time employee
  	}
    //prepare t_calendar INSERT
    $stmt = $conn->prepare("INSERT INTO `t_calendar`(`c_date`, `c_id`, `c_store`, `c_type`, `c_timestart`, `c_timeend`, `c_fullday`, `c_totalmins`, `c_employeetype`, `c_before5`, `c_after5`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssiisii",$c_date,$c_id,$c_store,$c_type,$c_timestart,$c_timeend,$c_fullday,$c_totalmins,$c_employeetype,$c_before5,$c_after5);

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
        //calculate before and after 5pm working hours for part time
        $c_after5 = 0;
        $c_before5 = 0;
        $c_employeetype = $arrayUserEmployee[$c_id];

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

        $result = ($result && $stmt->execute());
      }//while loop shiftTemp result

      date_add($currentDate,date_interval_create_from_date_string("1 day"));
    }//while loop dates
    $stmt->close();
    $conn->close();
    echo json_encode($result);
  }
?>
