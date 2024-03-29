<?
/*
 Leave - leave report and application. Team lead (M access) can access team leave
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("V")) {
	header("Location:login.php");
	exit();
}

$UserID = $_SESSION["id"];
$UserName = $_SESSION["user"];

$arrLeaveType = array();
$arrLeaveQuota = array();
include "connect_db.php";

//check user access. Store leads can edit leave for others
$sql = "SELECT `c_name`,`c_id`,`c_access` FROM `t_user` WHERE `c_name`='".$UserName."'";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    $UserID = $row["c_id"];
    $UserName = $row["c_name"];
    $isTeamLead = strpos($row["c_access"],"M");//"M" - team lead access (same access code as shift template)
}else{
    echo "User data error!";
    die;
}

if (($isTeamLead) && ($_GET['user'] != NULL)){
    $selectedUser = $_GET['user'];
}else{
    $selectedUser = $UserID;
}

//read leave quota for the user
$sql = "SELECT `c_leavetype`,`c_quota` FROM `t_leavequota` WHERE `c_id`='".$selectedUser."'";
$result = $conn->query($sql);
$idxType = 0;
while($row = $result->fetch_assoc()) {
    $arrLeaveType[$idxType] = $row['c_leavetype'];
    $arrLeaveQuota[$idxType] = $row['c_quota'];//leave quota counted in half day
    $idxType++;
}

?>
<!DOCTYPE html>
<html>
<head>
    <? include "header.php"; ?>
	<title>Leave</title>
    <!--Lightpick Snippet-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <link rel="stylesheet" type="text/css" href="vendor/Lightpick/css/lightpick.css">
    <script src="vendor/Lightpick/js/lightpick.js"></script>
    <script src="js/leave.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-3">Leave</h1>
		<div class="row mb-3">
			<div class="col-4"></div>
			<div class="col-4">
                <?
                if ($isTeamLead){//display user selection for team lead to edit others' leave
                    echo "<select class=\"form-select text-center text-white bg-primary\" id=\"sltName\" onchange=\"f_NameChange()\">";
                    $sql = "SELECT `c_id`, `c_name` FROM `t_user` WHERE (`c_employee`='F')";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()) {
                        if ($row['c_id'] == $selectedUser){
                            $strSelected = "selected";
                            $selectedUserName = $row['c_name'];
                            $displayTxtUserName = " d-none"; //hide txtUserName element for team lead
                        }else{
                            $strSelected = "";
                        }
                        echo "<option value=\"".$row['c_id']."\" ".$strSelected.">".$row['c_name']."</option>";
                    }
                    echo "</select>";
                }else{
                    $displayTxtUserName = "";
                    $selectedUserName = $UserName;
                }
                ?>
                <h5 id="txtUserName" class="text-center<?echo $displayTxtUserName?>" data-stocking-userid="<?echo $selectedUser?>"><?echo $selectedUserName?></h5>
		    </div>
		    <div class="col-4"></div>
    	</div>

        <div class="card mb-3">
            <h5 class="card-header bg-secondary text-white">Leave summary</h5>
            <div class="card-body">
                <table class="table" id="tableLeave">
                    <thead>
                        <tr>
                            <td>Leave</th>
                            <td>Used (day)</th>
                            <td>Left (day)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                            $totalType = count($arrLeaveType);
                            for ($idxType = 0; $idxType < $totalType; $idxType++){
                                $sql = "SELECT SUM(`c_count`) FROM `t_leave` WHERE `c_id`=\"".$selectedUser."\" AND `c_leavetype`=\"".$arrLeaveType[$idxType]."\"";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $c_count = $row["SUM(`c_count`)"];
                                }else{
                                    $c_count = 0;
                                }
                                echo "<tr>";
                                echo "<th scope=\"row\">".$arrLeaveType[$idxType]."</th>";
                                echo "<td>".($c_count/2)."</td>";//leave counts saved as counting by half days. display in days
                                echo "<td>".(($arrLeaveQuota[$idxType] - $c_count)/2)."</td>";
                                echo "</tr>";
                            }
                        ?>            
                    </tbody>
                </table>
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeave">Show leave history</button>
                <div class="collapse" id="collapseLeave">
                    <table class="table">
                        <thead>
                            <tr>
                                <td>Date (y/m/d)</td>
                                <td>Kind</td>
                                <td>Day</td>
                                <td>Type</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?
                            $sql = "SELECT c_from, c_to, c_leavetime, c_leavetype, c_count FROM `t_leave` WHERE c_id='".$selectedUser."'";
                            $result = $conn->query($sql);
                            $idx = 0;
                            $today = new DateTime("today");
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $dateFrom = date_create_from_format("Y-m-d",$row["c_from"]);
                                    $dateTo = date_create_from_format("Y-m-d",$row["c_to"]);
                                    $c_from = date_format($dateFrom,"y/n/j");
                                    $c_to = date_format($dateTo,"y/n/j");
                                    $strKind = $row["c_leavetime"];
                                    $c_leavetype = $row["c_leavetype"];
                                    $c_count = $row["c_count"];
                                    $dateFrom->setTime(0,0,0);
                                    $diffFromToday = $today->diff($dateFrom);
                                    //if ($diffFromToday->invert == 1){ //Leave has started. Can't cancell
                                    //    $strRowAttribute = "class=\"text-dark\"";
                                    //}else{
                                        $strRowAttribute = "class=\"text-primary\" onclick=\"f_LeaveSelected('".$c_from."')\"";
                                    //}
                        ?>
                                    <tr <?echo $strRowAttribute?>>
                                        <td><?echo $c_from."-".$c_to?></td>
                                        <td><?echo $strKind?></td>
                                        <td><?echo $c_count/2?></td>
                                        <td><?echo $c_leavetype?></td>
                                    </tr>
                        <?
                                    $idx++;
                                }
                            }
                        ?>            
                        </tbody>
                    </table>
                </div> <!--collapse-->            
            </div> <!--card body-->
        </div><!--card-->

        <!--Pick date using Lightpick date snippet-->
        <div class="card mb-3">
            <h5 class="card-header bg-secondary text-white">Apply leave</h5>
            <div class="card-body">
                <div class="card-title" id="txtTitle">Pick type of leave and dates</div>
                <div class="row mb-2">
                    <div class="col-4">
                        <select class="form-select text-white bg-primary" id="sltLeaveType" onchange="f_refreshTitle()">
                        <?
                        for ($idxType = 0; $idxType < $totalType; $idxType++){
                            echo "<option value=\"".$arrLeaveType[$idxType]."\">".$arrLeaveType[$idxType]."</option>";
                        }
                        ?>
                        </select>
                    </div>
                    <div class="col-8">
                        <div class="form-check form-check-inline mb-2">
                            <input class="form-check-input" type="radio" name="groupLeaveKind" id="rdoFull" checked onchange="f_refreshTitle()">
                            <label class="col form-check-label me-2" for="rdoFull">FULL DAY</label>
                        </div>
                        <div class="form-check form-check-inline mb-2">
                            <input class="form-check-input me-2" type="radio" name="groupLeaveKind" id="rdoMorning" onchange="f_refreshTitle()">
                            <label class="form-check-label me-2" for="rdoMorning">AM</label>
                        </div>
                        <div class="form-check form-check-inline mb-2">
                            <input class="form-check-input" type="radio" name="groupLeaveKind" id="rdoAfternoon" onchange="f_refreshTitle()">
                            <label class="form-check-label" for="rdoAfternoon">PM</label>
                        </div>
                    </div>
                </div><!--Leave type-->
                <!--lightpick element placeholder. Detail setup by leave.js-->
                <div class="mb-2"><input type="text" class="form-control d-none" id = "iptDate"></input></div>
            </div>
		<div class="row ms-3 mb-3">
            <button type="button" class="btn btn-primary col-3 me-3" onclick="f_OK()">OK</button>
			<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Cancel</button>
        </div>
 	</div> <!-- container -->

	<!-- Modal Submit-->
	<div class="modal fade" id="modal_box" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_title">Confirm to submit below request?</h5>
				</div>
				<div class="modal-body fs-6" id="modal_body"></div>
				<div class="row modal-footer">
                    <div class="row mb-3">
                        <div class="col-4 text-start text-secondary me-2" id="modal_status"></div>
                        <button type="button" class="col-3 btn btn-primary me-2" id="btn_ok" onclick="f_submit()">&nbsp;&nbsp;OK&nbsp;&nbsp;</button>
                        <button type="button" class="col-3 btn btn-secondary" id="btn_cancel" data-bs-dismiss="modal">Cancel</button>
                    </div>
				</div>
			</div>
		</div>
	</div>

	<? 
        $conn->close();
        include "footer.php" 
    ?>
</body>
</html>
