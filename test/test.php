<!DOCTYPE html>
<html>
<body>

<?php
$start=date_create("9:00");
$end=date_create("20:30");
$pm5 = date_create("17:00");

$StartTo5 = date_diff($start,$pm5);
$EndTo5 = date_diff($end,$pm5);
$Diff = date_diff($start,$end);

if ($StartTo5->format("%R") == '-'){ 
	$before5 = 0; //start after 5pm
}else{
	if ($EndTo5->format("%R") == '-'){
		$before5 = (int)$StartTo5->format("%h")*60 + (int)$StartTo5->format("%i"); //end after 5pm
    }else{
        $before5 = (int)$Diff->format("%h")*60 + (int)$Diff->format("%i"); //end before 5pm
    }
}

if ($EndTo5->format("%R") == '+'){ 
	$after5 = 0; //End before 5pm
}else{
	if ($StartTo5->format("%R") == '+'){
		$after5 = (int)$EndTo5->format("%h")*60 + (int)$EndTo5->format("%i"); //start before 5pm
    }else{
        $after5 = (int)$Diff->format("%h")*60 + (int)$Diff->format("%i"); //start after 5pm
    }
}

echo "start: ".$start->format("%h:%i");
echo "<br>";
echo "end: ".$end->format("%h:%i");
echo "<br>";
echo "before5: ".$before5;
echo "<br>";
echo "after5: ".$after5;
?>


</body>
</html>
