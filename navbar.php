<nav class="navbar navbar-expand-sm navbar-light bg-white">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><img class="img-fluid align-top" src="img/CoffeePlus_wordlogo.jpeg"><span>&nbspBack Office</span></a>
    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" id="btn-nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav text-center me-2 mb-auto mb-lg-0">
          <?
          $access = $_SESSION["access"];
          if (strstr($access,"C")){ 
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"shift.php\">Shift</a>";
            echo "</li>";
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"shift_teamview.php\">View by team</a>";
            echo "</li>";
          }
          if (strstr($access,"E")) {
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"shift_myreport.php\">My Shift Report</a>";
            echo "</li>";
          }
          if (strstr($access,"M")) {
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"shift_template.php\">Shift Template</a>";
            echo "</li>";
          }
          if (strstr($access,"L")) {
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"shift_report.php\">Team Shift Report</a>";
            echo "</li>";
          }
          if  ((strstr($access,"Q")) || (strstr($access,"I")) || (strstr($access,"G")) || (strstr($access,"P"))) {
            echo "<li class=\"nav-item dropdown\">";
            echo "<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"recipedropdown\" role=\"button\" data-bs-toggle=\"dropdown\">Production</a>";
            echo "<ul class=\"dropdown-menu\">";
            if (strstr($access,"P")) {
              echo "<li><a class=\"dropdown-item\" href=\"r_read.php\">Cook</a></li>";
            }
            if (strstr($access,"Q")) {
              echo "<li><a class=\"dropdown-item\" href=\"r_inventory.php\">Inventory</a></li>";
            }
            if (strstr($access,"G")) {
              echo "<li><a class=\"dropdown-item\" href=\"r_edit.php\">Recipe</a></li>";
            }
            if (strstr($access,"I")) {
              echo "<li><a class=\"dropdown-item\" href=\"r_material.php\">Material</a></li>";
            }
            echo "</ul>";
          }
          if (strstr($access,"V")) {
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"leave.php\">Leave</a>";
            echo "</li>";
          }
          if (strstr($access,"O")) {
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"myaccount.php\">Profile</a>";
            echo "</li>";
          }
          if (strstr($access,"A")) {
            echo "<li class=\"nav-item dropdown\">";
            echo "<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"admindropdown\" role=\"button\" data-bs-toggle=\"dropdown\">Admin</a>";
            echo "<ul class=\"dropdown-menu\">";
            echo "<li><a class=\"dropdown-item\" href=\"admin_item.php\">Item</a></li>";
            echo "<li><a class=\"dropdown-item\" href=\"admin_user.php\">User</a></li>";
            echo "<li><a class=\"dropdown-item\" href=\"admin_config.php\">Preference</a></li>";
            echo "</ul>";
            echo "</li>";
          }
          ?>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
      </ul>
    </div> <!-- navbar-collapse -->
  </div>  <!-- container fluid -->
</nav>
