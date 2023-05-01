<?
/*
Show and consume current inventory
*/ 
include_once "sessioncheck.php";
if (f_shouldDie("Q")) {
	header("Location:login.php");
	exit();
  }

 if ($_SERVER["REQUEST_METHOD"] == "POST") {  
	$selectedCat = $_POST["cat"];
 }else{
	$selectedCat = "Gelato";
 }
?>
<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
	<script src="js/r_report.js"></script>
</head>
<body>
	<div class="container">
		<h1 id="section_home" class="text-center mb-3">Inventory</h1>
        <!--Category selection-->
		<form class="row g-0 mb-4 border bg-light" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
            <div class="ps-1 mb-3">
				<div class="form-check form-check-inline me-5">
					<input <?echo ($selectedCat=="Gelato")?"checked":""?> type="radio" class="form-check-input" name="reportBy" value="Gelato" id="radioByGelato">
					<label class="form-check-label" for="radioByGelato">Gelato</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="radio" <?echo ($selectedCat=="Sorbet")?"checked":""?> class="form-check-input" name="reportBy" value="Sorbet" id="radioBySorbet">
					<label class="form-check-label" for="radioBySorbet">Sorbet</label>
				</div>
				<div class="form-check form-check-inline">
					<input type="radio" <?echo ($selectedCat=="Batter")?"checked":""?> class="form-check-input" name="reportBy" value="Batter" id="radioByBatter">
					<label class="form-check-label" for="radioByBatter">Batter</label>
				</div>
            </div>        
        </form>
        <?
			$arrayProduct = array(); //product array. Read t_production order by product to create this array
			$arrayInUser = array();//array for production user, use product as index
			$arrayInDate = array(); //array for productioin date, use product as index
			$arraySize = array(); //product size, use product as index

			//read inventory that has not been consumed
			include "connect_db.php";
			$sql = "SELECT t_recipe.c_product, c_size, c_indate, c_inuser FROM `t_production` INNER JOIN t_recipe ON t_production.c_recipe = t_recipe.c_recipe WHERE c_outdate IS  NULL AND t_recipe.c_cat = \'".$selectedCat."\' ORDER BY t_recipe.c_product";
			$sql = "SELECT t_recipe.c_product, c_size, c_indate, c_inuser FROM `t_production` INNER JOIN t_recipe ON t_production.c_recipe = t_recipe.c_recipe WHERE c_outdate IS  NULL AND t_recipe.c_cat = \'Gelato\'  UNION SELECT c_product, 0, NULL, NULL FROM t_product WHERE c_plan = 1 AND c_cat = \'Gelato\'";
			$result = $conn->query($sql);
			$idx = 0;
			if ($row = $result->fetch_assoc()){
				$arrayProduct[$idx] = $row["t_recipe.c_product"];
				$arrayInUser[$arrayProduct[$idx]] = $row["c_inuser"];
				$arrayInDate[$arrayProduct[$idx]] = $row["c_indate"];
				$arraySize[$arrayProduct[$idx]] = $row["c_size"];
			}
			while($row = $result->fetch_assoc()) {
				if ($row["t_recipe.c_product"] != $arrayProduct[$idx]){
					//next product
					$idx++;
					$arrayProduct[$idx] = $row["t_recipe.c_product"];
				}
				$arrayInUser[$arrayProduct[$idx]] = $row["c_inuser"];
				$arrayInDate[$arrayProduct[$idx]] = $row["c_indate"];
				$arraySize[$arrayProduct[$idx]] = $row["c_size"];
			}
			$conn->close();
		?>

		<!--Result Row-->
		<div class="row px-3 col mb-2">
		<?
		$ySum = array(); //store sum of working days for each store
		for ($idxProd = 0; $idxProd < ; $idxPpl++) {
			$c_id = $arrayUserID[$idxPpl];
			$c_name = $arrayUserName[$c_id];
			$ySum = array_fill(0,count($ySum),0);
			$xSum = array_fill(0,count($xSum),0);

			echo "<a class=\"btn btn-outline-dark mb-1\" data-bs-toggle=\"collapse\" href=\"#rpt".$c_id."\" role=\"button\">".$c_name."</a>";
			echo "<div class=\"collapse\" id=\"rpt".$c_id."\">";
			echo "<div class=\"card card-body\"><table class=\"table\"><thead>";
			echo "<tr>";
			echo "<th scope=\"col\">Type</th>";
			for ($idxStore = 0; $idxStore<count($arrayStore); $idxStore++) {
				echo "<th scope=\"col\">".$arrayStore[$idxStore]."</th>";
				$ySum[$idxStore] = 0;
			}
			echo "<th class=\"table-secondary\" scope=\"col\">SUM</th>"; //work type sum
			echo "</tr></thead><tbody>";
			for ($idxType = 0; $idxType<count($arrayWorkType); $idxType++) {
				$c_type = $arrayWorkType[$idxType];
				echo "<tr>";
				echo "<th scope=\"col\">".$c_type."</th>";
				for ($idxStore = 0; $idxStore<count($arrayStore); $idxStore++) {
					$c_store = $arrayStore[$idxStore];
					if (is_null($arrayPeople[$c_id][$c_store][$c_type])) {
						$c_count = 0;
					}else{
						$c_count = $arrayPeople[$c_id][$c_store][$c_type];
					} //if count is null
					echo "<td scope=\"col\">".$c_count."</th>";
					$xSum[$c_type] = $xSum[$c_type] + $c_count;
					$ySum[$idxStore] = $ySum[$idxStore] + $c_count;
				} //for loop store
				echo "<td class=\"table-secondary\" scope=\"col\">".$xSum[$c_type]."</th>"; //sum of this work type from all stores.
				echo "</tr>";
			} // for loop type
			//Store SUM
			echo "<tr>";
			echo "<th class=\"table-secondary\" scope=\"col\">SUM</th>";
			for ($idxStore = 0, $totalWork=0; $idxStore<count($arrayStore); $idxStore++) {
				$totalWork = $totalWork + $ySum[$idxStore];
				echo "<td class=\"table-secondary\" scope=\"col\">".$ySum[$idxStore]."</th>";
			}
			echo "<td class=\"table-dark text-white\" scope=\"col\">".$totalWork."</th>"; //store sum
			echo "</tr>";
			//OFF day working count for FULL TIME employee
			if (($arrayUserType[$c_id]=="F") AND ($xSum["WW"] - $intWorkingDays) > 0){
				if ($isReportByDay){$strWorking = "days";}else{$strWorking = "hours";}
				echo "<tr>";
				echo "<td class=\"table-secondary fst-italic\" scope=\"col\" colspan=\"".(count($arrayStore)+1)."\">Besides ".$intWorkingDays." week ".$strWorking.", OFF time working</td>";
				echo "<td class=\"table-secondary\" scope=\"col\">".($xSum["WW"] - $intWorkingDays)."</td>";
				echo "</tr>";
			}

			echo "</tbody></table>";
			echo "</div>";
			echo "</div>";
		} // for loop ppl
		?>
		</div> <!-- result row-->

	<?
	include "footer.php";
	?>
</body>
</html>
