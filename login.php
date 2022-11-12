<?php
// Start the session
session_start();

$_SESSION["user"] = "";
$_SESSION["access"] = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$c_name = $_POST["user"];
	include "connect_db.php";
	$sql = "SELECT `c_pwd`,`c_access` FROM `t_user` WHERE `c_name`= '".$c_name."'";
	$result = $conn->query($sql);
	if ($result->num_rows == 0) {
		$subject = "No such user!";
		$_SESSION["user"] = "";
	}else{
		$row = $result->fetch_assoc();
		$pwd = $row["c_pwd"];
		$access = $row["c_access"];
		$userInputPwd = $_POST["password"];
		if (password_verify($userInputPwd, $pwd)) {
			$subject = "Welcome <br>".$c_name;
			$_SESSION["user"] = $c_name;
			$_SESSION["access"] = $access;
			$conn->close();
			header("Location:index.php"); //login successful. redirect to index.php
		  exit();
		}else{
			$subject = "Wrong password!";
			$_SESSION["user"] = "";
		}
	}
	$conn->close();
}else {
	$subject = "";	//no login or login fail.   display login form
}
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
</head>
<body>
	<h1 class="text-center mb-2">Login</h1>
	<div id="reminding" class="text-center mb-3 text-danger col-12 fst-italic fs-6"><?echo $subject;?></div>

	<div class="container">
		<form method="POST" action="<? echo $_SERVER["PHP_SELF"];?>">
  		<div class="row mt-3">
  			<span>
  				<select class="form-select mb-3" id="sltUser" name="user">
  				  <option selected>Who are you...</option>
  					<?
  					include "connect_db.php";
  					$sql = "SELECT `c_name`, `c_access` FROM `t_user` WHERE `c_employee` <> 'D'";
      			$result = $conn->query($sql);
      			if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
  							$c_name = $row["c_name"];
  							$c_access = $row["c_access"];
  					?>
  				  <option value="<?echo $c_name?>"><?echo $c_name?></option>
  					<?
  						}
  					}
            $conn->close();
  					?>
  				</select>
  			</span>
      </div>

      <div class="mb-3 row">
        <label for="ipbPwd" class="col-sm-2 col-form-label">Password</label>
        <div class="col-sm-10">
          <input type="password"  name="password" class="form-control" id="ipbPwd" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
 	</div> <!-- container -->

	<? include "footer.php" ?>
</body>
</html>
