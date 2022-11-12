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
		<h1 id="section_home" class="text-center mb-3">Admin - Configuration</h1>
        <div class="card mb-3">
            <h5 class="card-header">Max people required by store</h5>
            <div class="card-body">
            <?
                include "connect_db.php";
                $sql = "SELECT `c_subsetup`, `c_value` FROM `t_config` WHERE `c_setup`='max_ppl'";
    			$result = $conn->query($sql);
                $idx = 0;
    			if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
						$store = $row["c_subsetup"];
                        $c_value = $row["c_value"];
			?>
                <div class="input-group mb-1">
                    <span class="input-group-text col-3" id="max_ppl_store<?echo $idx?>" name="max_ppl_store"><?echo $store?></span>
                    <input type="text" class="form-control" value="<?echo $c_value?>" id="max_ppl_value<?echo $idx?>">
                </div>
            <?
                        $idx++;
                    }
                }
            ?>            
            </div>
        </div>

        <div class="card mb-3">
            <h5 class="card-header">Notice when stocking data changed</h5>
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
