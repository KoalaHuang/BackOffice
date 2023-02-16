/* Receipt - Material javacript */

const arrayM = [];
const arrayCost = [];
const arrayUnit = [];
const arrayMoq = [];
const arraySupplier = [];

const objGlobal = {
	name: "",
	unit: "",
	cost: 0,
	supplier: "",
	moq: 0,
	act: 0 //1: update, 2: insert, 3: delete, 0: no change
  };

window.addEventListener("DOMContentLoaded", function() {
    modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
  
	elmUnit = document.getElementById("iptUnit");
	elmCost = document.getElementById("iptCost");
	elmMoq = document.getElementById("iptMoq");
	elmSupplier = document.getElementById("sltSupplier");

    elmIptMaterial = document.getElementById('iptMaterial');
    elmUlMaterial = document.getElementById('ulMaterial');
    totalMaterial = elmUlMaterial.childElementCount;
    for (idx = 0; idx < totalMaterial; idx++){
        var elmLi = elmUlMaterial.children[idx];
        arrayM[idx] = elmLi.innerText;
        arrayCost[idx] = elmLi.getAttribute("data-bo-cost");
        arrayUnit[idx] = elmLi.getAttribute("data-bo-unit");
        arraySupplier[idx] = elmLi.getAttribute("data-bo-supplier");
        arrayMoq[idx] = elmLi.getAttribute("data-bo-moq");
    }
    elmIptMaterial.addEventListener('keyup', searchHandler);
  }, false);

 /*filter values for input box drop down list*/
function search(str) {
	let results = [];
	const val = str.toLowerCase();

	for (i = 0; i < arrayM.length; i++) {
		if (arrayM[i].toLowerCase().indexOf(val) > -1) {
			results.push(i);
		}
	}

	return results;
}

/*trigger input box filter drop downlist when key in the box*/
function searchHandler(e) {
	const inputVal = e.currentTarget.value;
	let results = [];
	if (inputVal.length > 0) {
		results = search(inputVal);
	}else{
		elmCost.value = "";
		elmUnit.value = "";
		elmMoq.value = "";
		elmSupplier.value = "";
		elmCost.disabled = elmUnit.disabled = elmMoq.disabled = elmSupplier.disabled = true;
	}
	showSuggestions(results, inputVal);
}

/*show input box drop down with filtered result*/
function showSuggestions(results, inputVal) {
    elmUlMaterial.innerHTML = '';
	if (results.length > 0) {
		for (i = 0; i < results.length; i++) {
			let itemIdx = results[i];
			let item = arrayM[itemIdx];

            const match = item.match(new RegExp(inputVal, 'i'));
			item = item.replace(match[0], `<strong>${match[0]}</strong>`);
			elmUlMaterial.innerHTML += `<li class="search-li" onclick="useSuggestion(${itemIdx})">${item}</li>`;
		}
		elmUlMaterial.classList.add('listed');
		f_checkBtnList(true);
	} else {
		results = [];
		elmUlMaterial.innerHTML = '';
		elmUlMaterial.classList.remove('listed');
		f_checkBtnList(false);
	}
}

/*check/uncheck dropdown button*/
function f_checkBtnList(isToCheck){
	if (isToCheck){
		document.getElementById("btnList").checked = true;
		document.getElementById("lblBtnList").innerHTML = "&nbsp;-&nbsp;";
	}else{
		document.getElementById("btnList").checked = false;
		document.getElementById("lblBtnList").innerHTML = "&nbsp;+&nbsp;";
	}
}

/*Toggle dropdown list when button is clicked*/
function f_ListToggle(){
	if (document.getElementById("btnList").checked){
		const inputVal = elmIptMaterial.value;
		if (inputVal.length > 0) {
			let results = [];
			results = search(inputVal);
			showSuggestions(results, inputVal);
		}else{
			elmUlMaterial.innerHTML = '';
			for (var i = 0; i < arrayM.length; i++) {
				elmUlMaterial.innerHTML += `<li class="search-li" onclick="useSuggestion(${i})">${arrayM[i]}</li>`;
			}
			elmUlMaterial.classList.add('listed');
			f_checkBtnList(true);
		}
	}else{
		elmUlMaterial.innerHTML = '';
		elmUlMaterial.classList.remove('listed');
		f_checkBtnList(false);
	}
}

/*select from dropdown list to be value in input box*/
function useSuggestion(idx) {
	elmIptMaterial.value = arrayM[idx];
	elmCost.value = arrayCost[idx];
	elmUnit.value = arrayUnit[idx];
	elmMoq.value = arrayMoq[idx];
	elmSupplier.value = arraySupplier[idx];
	elmUlMaterial.innerHTML = '';
	elmUlMaterial.classList.remove('listed');
	elmCost.disabled = elmUnit.disabled = elmMoq.disabled = elmSupplier.disabled = false;
	f_checkBtnList(false);
	elmIptMaterial.focus();
}

/*Confirm to submit change*/
function f_toConfirm(){
	objGlobal.name = elmIptMaterial.value;
	objGlobal.act = 2; //insert new record
	strAct = "Add new material";
	for (i=0; i < arrayM.length; i++){
		if (objGlobal.name == arrayM[i]){
			objGlobal.act = 1; //update existing record
			strAct = "Update material";
			break;
		}
	}
	objGlobal.cost = elmCost.value;
	objGlobal.unit = elmUnit.value;
	objGlobal.moq = elmMoq.value;
	objGlobal.supplier = elmSupplier.value;
	document.getElementById("modal_body").innerHTML = "<strong>" + strAct + "</strong><br>" + "Item: " + objGlobal.name + "<br>Cost: " + objGlobal.cost + "<br>Unit: " + objGlobal.unit + "<br>MOQ: " + objGlobal.moq + "<br>Supplier: " + objGlobal.supplier;
	document.getElementById("btn_ok").disabled = false;
	document.getElementById("modal_status").innerHTML = "";
	modal_Popup.show();  
}

/*Confirm to submit change*/
function f_toDelete(){
	objGlobal.name = elmIptMaterial.value;
	objGlobal.act = 0; 
	for (i=0; i < arrayM.length; i++){
		if (objGlobal.name == arrayM[i]){
			objGlobal.act = 3; //Delete record
			break;
		}
	}
	if (objGlobal.act == 3){
		objGlobal.cost = elmCost.value;
		objGlobal.unit = elmUnit.value;
		objGlobal.moq = elmMoq.value;
		objGlobal.supplier = elmSupplier.value;
		document.getElementById("modal_body").innerHTML = "<strong>Delete Material</strong><br>" + "Item: " + objGlobal.name + "<br>Cost: " + objGlobal.cost + "<br>Unit: " + objGlobal.unit + "<br>MOQ: " + objGlobal.moq + "<br>Supplier: " + objGlobal.supplier;
		document.getElementById("btn_ok").disabled = false;
	}else{
		document.getElementById("modal_body").innerHTML = "<strong>Material doesn't exist! Can't delete.</strong><br>" + "Item: " + objGlobal.name + "<br>Cost: " + objGlobal.cost + "<br>Unit: " + objGlobal.unit + "<br>MOQ: " + objGlobal.moq + "<br>Supplier: " + objGlobal.supplier;
		document.getElementById("btn_ok").disabled = true;
		document.getElementById("btn_cancel").disabled = false;
	}
	document.getElementById("modal_status").innerHTML = "";
	modal_Popup.show();  
}

//submit data change
function f_submit() {
	const xhttp = new XMLHttpRequest();
	xhttp.onload = function() {
	  document.getElementById("modal_status").innerHTML = "";
	  if (this.responseText == "true") {
		document.getElementById("modal_body").innerHTML  = "Submit successfully!<br>Press OK to return";
		document.getElementById("btn_ok").setAttribute("onclick","f_refresh()");
		document.getElementById("btn_ok").disabled = false;
		document.getElementById("btn_cancel").disabled = true;
	  }else{
		document.getElementById("modal_body").innerHTML  = "<p class=\"text-danger\">Update failed!</p>Return code: "+ this.responseText + "<br>Press Cancel to return";
		document.getElementById("btn_ok").disabled = true;
		document.getElementById("btn_cancel").disabled = false;
	  }
	}
	const strJson = JSON.stringify(objGlobal);
	xhttp.open("POST", "r_material_update.php");
	xhttp.setRequestHeader("Accept", "application/json");
	xhttp.setRequestHeader("Content-Type", "application/json");
	xhttp.send(strJson);
	document.getElementById("modal_status").innerHTML = "Submitting...";
	document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = true;
  }//f_submit
  
//ok button to refresh the page when failed
function f_refresh() {
	location.reload();
  }
  