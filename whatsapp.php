<?
  //send notice via maill or whatsapp
  //$type: "S", send notice for stocking data change
  //$type: "H", shift change notice
  //$msg: array of string. each element is one line. First string is mail subject
  include "mylog.php";

  function send_mailNote($to,$subject,$msg){
    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    mail($to,$subject,$msg,$headers);
  }

  function send_whatsapp($phone, $apikey, $message){
    $url='https://api.callmebot.com/whatsapp.php?source=php&phone='.$phone.'&text='.urlencode($message).'&apikey='.$apikey;
    $html=file_get_contents($url);
  }

  function send_notice($type,$msg){
    include "connect_db.php";
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }     
    switch ($type) {
    case "S":
      $c_setup = "notice_stocking";
      break;
    case "H":
      $c_setup = "notice_shift";
      break;
    default:
      exit;
    }
    myLOG($msg);
    //send mail notice
    $sqlNotice = "SELECT `c_value` FROM `t_config` WHERE `c_setup`= '".$c_setup."' AND `c_subsetup`='mail'";
    $result = $conn->query($sqlNotice);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $to = $row["c_value"];
      while($row = $result->fetch_assoc()) {
        $to = $to.", ".$row["c_value"];
      }
      $subject = $msg[0];
      $message = "
      <html>
      <head>
      <title>".$subject."</title>
      </head>
      <body>";
      for ($idx=1; $idx < count($msg); $idx++){
        $message = $message."<p>".$msg[$idx]."</p>";          
      }
      $message = $message."<p>Go to <a href=\"http://coffeeplus.sg/bo\">CoffeePlus BackOffice</a> for detail.</p>
      </body>
      </html>
      ";
      send_mailNote($to,$subject,$message);
    }
    //send WhatsApp notice
    $sqlNotice = "SELECT `c_value` FROM `t_config` WHERE `c_setup`= '".$c_setup."' AND `c_subsetup`='WA'";
    $result = $conn->query($sqlNotice);
    myLOG($result);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $token = strtok($row["c_value"],".");
        $phone = $token;
        $token = strtok(".");
        $apikey = $token;
        $message = "";
        for ($idx=0; $idx < count($msg); $idx++){
          $message = $message.$msg[$idx]."\n";          
        }
          $message = $message."Go to http://coffeeplus.sg/bo for detail.";
        send_whatsapp($phone,$apikey,$message);
      }
    }
    $conn->close();
  }

?>