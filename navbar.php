<nav class="navbar navbar-expand-sm navbar-light bg-white">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><img class="img-fluid align-top" src="img/CoffeePlus_wordlogo.jpeg"><span>&nbspBack Office</span></a>
    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" id="btn-nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav text-center me-2 mb-auto mb-lg-0">
          <?
          if (strstr($_SESSION["access"],"C")) {
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"shift.php\">Shift</a>";
            echo "</li>";
          }
          if (strstr($_SESSION["access"],"E")) {
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"shift_report.php\">Shift Report</a>";
            echo "</li>";
          }
          if (strstr($_SESSION["access"],"M")) {
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"shift_template.php\">Shift Template</a>";
            echo "</li>";
          }
          if  (strstr($access,"R") || (strstr($access,"F")) || (strstr($access,"S")) || (strstr($access,"T"))) {
            echo "<li class=\"nav-item dropdown\">";
            echo "<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"stockdropdown\" role=\"button\" data-bs-toggle=\"dropdown\">Stock</a>";
            echo "<ul class=\"dropdown-menu\">";
            if (strstr($access,"R")) {
              echo "<li><a class=\"dropdown-item\" href=\"request.php\">Request</a></li>";
            }
            if (strstr($access,"F")) {
              echo "<li><a class=\"dropdown-item\" href=\"fulfil.php\">Fufill</a></li>";
            }
            if (strstr($access,"S")) {
              echo "<li><a class=\"dropdown-item\" href=\"stock.php\">Stock</a></li>";
            }
            if (strstr($access,"T")) {
              echo "<li><a class=\"dropdown-item\" href=\"report.php\">Report</a></li>";
            }
            echo "</ul>";
          }
          if (strstr($_SESSION["access"],"O")) {
            echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"myaccount.php\">Profile</a>";
            echo "</li>";
          }
          if (strstr($_SESSION["access"],"A")) {
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
