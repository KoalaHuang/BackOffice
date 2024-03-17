<?
/*
 Admin - System Configuration
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("A")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
    <? include "header.php"; ?>
	<title>Stocking</title>
	<script src="js/admin_config.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-3">Admin - Preference</h1>
        <div class="card mb-3">
            <h5 class="card-header bg-info">Notice when shift is changed</h5>
            <div class="card-body">
                <div class="card-title">WhatsApp Notice</div>
                <div class="input-group mb-1">
                    <input type="text" class="col-9 form-control" placeholder="+65PHONE-NUM.PIN" id = "iptBox_shiftwa">
                    <button class="col-3 btn btn-primary" type="button" onclick="f_add_notice('shiftwa')">Add</button>                    
                </div>
                <ul class="list-group" id="ul_shiftwa">
            <?
                include "connect_db.php";
                $sql = "SELECT `c_value` FROM `t_config` WHERE `c_setup`='notice_shift' AND `c_subsetup`='WA'";
    			$result = $conn->query($sql);
                $idx = 0;
    			if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $c_value = $row["c_value"];
			?>
                    <div class="row" id="row_shiftwa_<?echo $idx?>">
                        <li class="list-group-item list-group-item-secondary mx-auto mb-1 col-10" id="li_shiftwa_<?echo $idx?>"><?echo $c_value?></li>
                        <button type="button" class="mx-auto mb-1 btn btn-danger col-1" id="btn_shiftwa_<?echo $idx?>"  onclick="f_remove_notice('shiftwa','<?echo $idx?>')">X</button>
                    </div>
            <?
                        $idx++;
                    }
                }
            ?>            
                </ul>
                <div class="card-title mt-3">Email Notice</div>
                <div class="input-group mb-1">
                    <input type="text" class="col-9 form-control" placeholder="Email address" id = "iptBox_shiftmail">
                    <button class="col-3 btn btn-primary" type="button" onclick="f_add_notice('shiftmail')">Add</button>                    
                </div>
                <ul class="list-group" id="ul_shiftmail">
            <?
                include "connect_db.php";
                $sql = "SELECT `c_value` FROM `t_config` WHERE `c_setup`='notice_shift' AND `c_subsetup`='mail'";
    			$result = $conn->query($sql);
                $idx = 0;
    			if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $c_value = $row["c_value"];
			?>
                    <div class="row" id="row_shiftmail_<?echo $idx?>">
                        <li class="list-group-item list-group-item-secondary mx-auto mb-1 col-10" id="li_shiftmail_<?echo $idx?>"><?echo $c_value?></li>
                        <button type="button" class="mx-auto mb-1 btn btn-danger col-1" id="btn_shiftmail_<?echo $idx?>"  onclick="f_remove_notice('shiftmail','<?echo $idx?>')">X</button>
                    </div>
            <?
                        $idx++;
                    }
                }
                $conn->close();
            ?>            
                </ul>
            </div>
        </div>

        <div class="card mb-3">
            <h5 class="card-header bg-info">Notice when stocking data changed</h5>
            <div class="card-body">
                <div class="card-title">WhatsApp Notice</div>
                <div class="input-group mb-1">
                    <input type="text" class="col-9 form-control" placeholder="+65PHONE-NUM.PIN" id = "iptBox_wa">
                    <button class="col-3 btn btn-primary" type="button" onclick="f_add_notice('wa')">Add</button>                    
                </div>
                <ul class="list-group" id="ul_wa">
            <?
                include "connect_db.php";
                $sql = "SELECT `c_value` FROM `t_config` WHERE `c_setup`='notice_stocking' AND `c_subsetup`='WA'";
    			$result = $conn->query($sql);
                $idx = 0;
    			if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $c_value = $row["c_value"];
			?>
                    <div class="row" id="row_wa_<?echo $idx?>">
                        <li class="list-group-item list-group-item-secondary mx-auto mb-1 col-10" id="li_wa_<?echo $idx?>"><?echo $c_value?></li>
                        <button type="button" class="mx-auto mb-1 btn btn-danger col-1" id="btn_wa_<?echo $idx?>"  onclick="f_remove_notice('wa','<?echo $idx?>')">X</button>
                    </div>
            <?
                        $idx++;
                    }
                }
            ?>            
                </ul>
                <div class="card-title mt-3">Email Notice</div>
                <div class="input-group mb-1">
                    <input type="text" class="col-9 form-control" placeholder="Email address" id = "iptBox_mail">
                    <button class="col-3 btn btn-primary" type="button" onclick="f_add_notice('mail')">Add</button>                    
                </div>
                <ul class="list-group" id="ul_mail">
            <?
                include "connect_db.php";
                $sql = "SELECT `c_value` FROM `t_config` WHERE `c_setup`='notice_stocking' AND `c_subsetup`='mail'";
    			$result = $conn->query($sql);
                $idx = 0;
    			if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $c_value = $row["c_value"];
			?>
                    <div class="row" id="row_mail_<?echo $idx?>">
                        <li class="list-group-item list-group-item-secondary mx-auto mb-1 col-10" id="li_mail_<?echo $idx?>"><?echo $c_value?></li>
                        <button type="button" class="mx-auto mb-1 btn btn-danger col-1" id="btn_mail_<?echo $idx?>"  onclick="f_remove_notice('mail','<?echo $idx?>')">X</button>
                    </div>
            <?
                        $idx++;
                    }
                }
                $conn->close();
            ?>            
                </ul>
            </div>
        </div>

		<div class="row">
			<span><button type="button" class="btn btn-primary col-3 me-5" onclick="f_toConfirm()">OK</button>
			<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Cancel</button></span>
		</div>
 	</div> <!-- container -->

	<!-- Modal Submit-->
	<div class="modal fade" id="modal_box" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="lbl_modal">Confirm to submit below request?</h5>
				</div>
				<div class="modal-body fs-6" id="body_modal">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" id="btn_cancel" onclick="f_refresh()">Cancel</button>
					<button type="button" class="btn btn-primary" id="btn_ok" onclick="f_submit()">OK</button>
				</div>
			</div>
		</div>
	</div>

	<? include "footer.php" ?>
</body>
</html>
