<?
session_start();

$user = $_SESSION["user"];
if (is_null($user) || ($user == "")) {
  header("Location:login.php");
  exit();
}
$access = $_SESSION["access"];
?>
<!DOCTYPE html>
<html>
<head>
  <? include "header.php"; ?>
	<title>BackOffice</title>
</head>
<body>
	<div class="container">
		<h1 class="text-center text-secondary mb-2">Welcome, <strong><?echo $user;?></strong></h1>
    <?
      if ((strstr($access,"C") || (strstr($access,"E")) || (strstr($access,"M")) || (strstr($access,"L")))) {//shift
    ?>
    <div class="d-grid gap-2 col-6 border border-secondary mx-auto p-2 mb-3">
    <h5 class="text-center text-muted">Shift</h5>
      <?
      if (strstr($access,"C")) {
        echo "<a href=\"shift.php\" class=\"btn btn-primary mb-1\" role=\"button\">Shift</a>";
      }
      if (strstr($access,"E")) {
        echo "<a href=\"shift_myreport.php\" class=\"btn btn-primary mb-1\" role=\"button\">My Report</a>";
      }
      if (strstr($access,"M")) {
        echo "<a href=\"shift_template.php\" class=\"btn btn-primary mb-1\" role=\"button\">Template</a>";
      }
      if (strstr($access,"L")) {
        echo "<a href=\"shift_report.php\" class=\"btn btn-primary mb-1\" role=\"button\">Team Report</a>";
      }
      ?>
    </div>
    <?}?>
     <?
      if (strstr($access,"R") || (strstr($access,"F")) || (strstr($access,"S")) || (strstr($access,"T"))) {//stock
    ?>
    <div class="d-grid gap-2 col-6 border border-primary mx-auto p-2 mb-3">
      <h5 class="text-center text-muted">Stock</h5>
      <?
      if (strstr($access,"R")) {
        echo "<a href=\"request.php\" class=\"btn btn-primary mb-1\" role=\"button\">Request</a>";
      }
      if (strstr($access,"F")) {
        echo "<a href=\"fulfil.php\" class=\"btn btn-primary mb-1\" role=\"button\">Fulfill</a>";
      }
      if (strstr($access,"S")) {
        echo "<a href=\"stock.php\" class=\"btn btn-primary mb-1\" role=\"button\">Stock</a>";
      }
      if (strstr($access,"T")) {
        echo "<a href=\"report.php\" class=\"btn btn-primary mb-1\" role=\"button\">Report</a>";
      }
      ?>
    </div>
    <?}?>
    <?
      if (strstr($access,"P") || (strstr($access,"D")) || (strstr($access,"I")) || (strstr($access,"G"))) {//stock
    ?>
    <div class="d-grid gap-2 col-6 border border-primary mx-auto p-2 mb-3">
      <h5 class="text-center text-muted">Recipe</h5>
      <?
      if (strstr($access,"P")) {
        echo "<a href=\"r_read.php\" class=\"btn btn-primary mb-1\" role=\"button\">Read Recipe</a>";
      }
      if (strstr($access,"G")) {
        echo "<a href=\"r_edit.php\" class=\"btn btn-primary mb-1\" role=\"button\">Edit Recipe</a>";
      }
      if (strstr($access,"I")) {
        echo "<a href=\"r_material.php\" class=\"btn btn-primary mb-1\" role=\"button\">Material</a>";
      }
      ?>
    </div>
    <?}?>
    <?
      if (strstr($access,"V")) {//Leave
    ?>
    <div class="d-grid gap-2 col-6 border border-secondary mx-auto p-2 mb-3">
    <h5 class="text-center text-muted">Leave</h5>
    <?
      if (strstr($access,"V")) {
        echo "<a href=\"leave.php\" class=\"btn btn-primary\" role=\"button\">Leave</a>";
      }
    ?>
    </div>
    <?}?>
    <?
      if (strstr($access,"O")) {//profile
    ?>
    <div class="d-grid gap-2 col-6 border border-secondary mx-auto p-2 mb-3">
    <h5 class="text-center text-muted">Profile</h5>
    <?
      if (strstr($access,"O")) {
        echo "<a href=\"myaccount.php\" class=\"btn btn-primary\" role=\"button\">Profile</a>";
      }
    ?>
    </div>
    <?}?>
    <?
      if (strstr($access,"A")) {//admin
        echo "<div class=\"d-grid gap-2 col-6 border border-secondary mx-auto p-2 mb-3\">";
        echo "<h5 class=\"text-center text-muted\">Admin</h5>";
        echo "<a href=\"admin_item.php\" class=\"btn btn-primary mb-1\" role=\"button\">Item</a>";
        echo "<a href=\"admin_user.php\" class=\"btn btn-primary mb-1\" role=\"button\">User</a>";
        echo "<a href=\"admin_config.php\" class=\"btn btn-primary mb-1\" role=\"button\">Preference</a>";
        echo "</div>";
      }
    ?>
  </div> <!-- container -->

	<? include "footer.php" ?>
</body>
</html>
