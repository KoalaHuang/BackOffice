<?
/*
User Profile view and update. Can change password
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("O")) {
	header("Location:login.php");
	exit();
  }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
	<script src="js/myaccount.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-2">Profile</h1>
		<?
		include "connect_db.php";
		$sql = "SELECT * FROM `t_user` WHERE `c_name`='".$_SESSION["user"]."'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$c_name = $row["c_name"];
			$c_access = $row["c_access"];
			$c_id = $row["c_id"];
			$c_workday = $row["c_workday"];
			$c_store = $row["c_store"];
		}else{
			echo "Data error!";
			die;
		}
		?>
		<div class="row mt-3">
		<div class="input-group mb-3"> <!--name-->
			<span class="input-group-text">Name</span>
		  <input type="text" class="form-control" id="iptName" value="<?echo $c_name?>" disabled>
		</div>
		<div class="input-group mb-3"> <!--ID-->
			<span class="input-group-text">ID</span>
		  <input type="text" class="form-control" id="iptID" value="<?echo $c_id?>" disabled>
		</div>
		<div class="input-group mb-3"> <!--pwd-->
			<span class="input-group-text">password</span>
		  <input type="password" placeholder="key in new password" class="form-control" id="iptPwd">
		</div>
		<div class="col-12 bg-light text-center mb-1"><strong>Working Day</strong></div>
		<div class="row mx-auto"> <!--working day-->
		<div class="col"><div class="form-check">
			<input disabled type="checkbox" class="form-check-input" name="btn_workday" id="wd1" <?if (strstr($c_workday,"1")) echo "checked"?>>
		</div></div>
		<div class="col"><div class="form-check">
			<input disabled type="checkbox" class="form-check-input" name="btn_workday" id="wd2" <?if (strstr($c_workday,"2")) echo "checked"?>>
		</div></div>
		<div class="col"><div class="form-check">
			<input disabled type="checkbox" class="form-check-input" name="btn_workday" id="wd3" <?if (strstr($c_workday,"3")) echo "checked"?>>
		</div></div>
		<div class="col"><div class="form-check">
			<input disabled type="checkbox" class="form-check-input" name="btn_workday" id="wd4" <?if (strstr($c_workday,"4")) echo "checked"?>>
		</div></div>
		<div class="col"><div class="form-check">
			<input disabled type="checkbox" class="form-check-input" name="btn_workday" id="wd5" <?if (strstr($c_workday,"5")) echo "checked"?>>
		</div></div>
		<div class="col"><div class="form-check">
			<input disabled type="checkbox" class="form-check-input" name="btn_workday" id="wd6" <?if (strstr($c_workday,"6")) echo "checked"?>>
		</div></div>
		<div class="col"><div class="form-check">
			<input disabled type="checkbox" class="form-check-input" name="btn_workday" id="wd7" <?if (strstr($c_workday,"7")) echo "checked"?>">
		</div></div>
		</div>
		<div class="row mx-auto mb-3">
		<span class="col text-primary">Mon</span>
		<span class="col text-primary">Tue</span>
		<span class="col text-primary">Wed</span>
		<span class="col text-primary">Thu</span>
		<span class="col text-primary">Fri</span>
		<span class="col text-primary">Sat</span>
		<span class="col text-primary">Sun</span>
		</div>
		<div class="col-12 bg-light text-center mb-1"><strong>Store</strong></div>
		<div class="row mx-auto">
		<?
		$sql = "SELECT `c_name` FROM `t_store`";
		$result = $conn->query($sql);
		$idx = 0;
		while($row = $result->fetch_assoc()) {
			$arrayStore[$idx] = $row["c_name"];
		?>
		<div class="col"><div class="form-check">
						<input type="radio" class="form-check-input" name="btn_store" <?if ($c_store == $arrayStore[$idx]) echo "checked"; ?> disabled>
		</div></div>
		<?
		$idx++;
		}
		$conn->close();
		?>
			<div class="col"><div class="form-check">
							<input type="radio" class="form-check-input" name="btn_store" <?if ($c_store == "ALL") echo "checked"; ?> disabled>
			</div></div>
			<div class="col"><div class="form-check">
							<input type="radio" class="form-check-input" name="btn_store" <?if ($c_store == "NONE") echo "checked"; ?> disabled>
			</div></div>
		</div>
		<div class="row mx-auto mb-3">
		<?
		for ($idx=0;$idx<count($arrayStore);$idx++) {
				echo "<span class=\"col text-primary\">".$arrayStore[$idx]."</span>";
		}
		?>
			<span class="col text-primary">ALL</span>
			<span class="col text-primary">NONE</span>
		</div>
		<div class="row">
			<span><button type="button" id="btn_toConfirm" class="btn btn-primary col-3 me-5" onclick="f_toConfirm()">OK</button>
			<button type="button" class="btn btn-secondary col-3" onclick="f_refresh()">Cancel</button></span>
		</div>
	</div> <!-- container -->

	<!-- Modal Submit-->
	<div class="modal fade" id="modal_box" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="lbl_modal"></h5>
				</div>
				<div class="modal-body fs-6" id="body_modal">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" id="btn_cancel" data-bs-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" id="btn_ok" onclick="f_submit()">OK</button>
				</div>
			</div>
		</div>
	</div>

	<? include "footer.php" ?>
</body>
</html>
