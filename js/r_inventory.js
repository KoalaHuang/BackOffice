/* Prdouction - Inventory: check out inventory and put up request
*/

const objGlobal = {
	product: "",
    act: 1 //1 check out inventory, 2 add request, 3 remove request
  };

const arrayProduct = [];

window.addEventListener("DOMContentLoaded", function() {
    modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));

    elmIptProduct = document.getElementById('iptProduct');
    elmUlProduct = document.getElementById('ulProduct');

    const totalProduct = elmUlProduct.childElementCount;
    for (idx = 0; idx < totalProduct; idx++){
        var elmLi = elmUlProduct.children[idx];
        arrayProduct[idx] = elmLi.innerText;
    }
	elmIptProduct.addEventListener('keyup', searchHandler);
  }, false);

 /*filter values for input box drop down list*/
function search(str,arr) {
	let results = [];
	const valueLower = str.toLowerCase();
	for (i = 0; i < arr.length; i++) {
		if (arr[i].toLowerCase().indexOf(valueLower) > -1) {
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
        results = search(inputVal,arrayProduct);
    }
	document.getElementById("btnAddRequest").disabled = true; 
	showSuggestedProduct(results, inputVal);
}

/*show input box drop down with filtered product*/
function showSuggestedProduct(results, inputVal) {
    elmUlProduct.innerHTML = '';
	const elmBtn = document.getElementById("btnProductList");
	if (results.length > 0) {
		for (i = 0; i < results.length; i++) {
			let itemIdx = results[i];
			let item = arrayProduct[itemIdx];

            const match = item.match(new RegExp(inputVal, 'i'));
			item = item.replace(match[0], `<strong>${match[0]}</strong>`);
			elmUlProduct.innerHTML += `<li class="search-li" onclick="useSuggestedProduct(${itemIdx})">${item}</li>`;
		}
		elmUlProduct.classList.add('listed');
		elmBtn.checked = true;
	} else {
		results = [];
		elmUlProduct.innerHTML = '';
		elmUlProduct.classList.remove('listed');
		elmBtn.checked = false;
	}
}

/*Toggle dropdown list when Product button is clicked*/
function f_ListToggleProduct(){
	const elmBtn = document.getElementById("btnProductList");
	if (elmBtn.checked){
		const inputVal = elmIptProduct.value;
		if (inputVal.length > 0) {
			let results = [];
			results = search(inputVal,arrayProduct);
			showSuggestedProduct(results, inputVal);
		}else{
			elmUlProduct.innerHTML = '';
			for (var i = 0; i < arrayProduct.length; i++) {
				elmUlProduct.innerHTML += `<li class="search-li" onclick="useSuggestedProduct(${i})">${arrayProduct[i]}</li>`;
			}
			elmUlProduct.classList.add('listed');
			elmBtn.checked = true;
		}
	}else{
		elmUlProduct.innerHTML = '';
		elmUlProduct.classList.remove('listed');
		elmBtn.checked = false;
	}
}

/*select from dropdown list to be value in input box*/
function useSuggestedProduct(idx) {
	elmIptProduct.value = objGlobal.product = arrayProduct[idx];
	document.getElementById("btnAddRequest").disabled = false; 
	elmUlProduct.innerHTML = '';
	elmUlProduct.classList.remove('listed');
	document.getElementById("btnProductList").checked = false;
	elmIptProduct.focus();
}

/* Select product category */
function f_selectCat(strCat){
    window.location.href = "r_inventory.php?cat=" + encodeURIComponent(strCat);
}

//ok button to refresh the page when failed
function f_refresh() {
    const elmCats = document.getElementsByName("reportBy");
    const totalItem = elmCats.length;
	for (var idx = 0; idx < totalItem; idx++){
        if (elmCats[idx].checked) strCat =  elmCats[idx].innerText;
    }
	f_selectCat(strCat);
}

//check out inventory
function f_checkOut(strProd){
	objGlobal.product = strProd;
    objGlobal.act = 1; //check out inventory
    strAct = "Confirm to check out one pcs of <strong>" + objGlobal.product + "</strong> ?";	
    document.getElementById("modal_body").innerHTML = strAct;
    document.getElementById("btn_ok").disabled = false;
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
	xhttp.open("POST", "r_inventory_update.php");
	xhttp.setRequestHeader("Accept", "application/json");
	xhttp.setRequestHeader("Content-Type", "application/json");
	xhttp.send(strJson);
	document.getElementById("modal_status").innerHTML = "Submitting...";
	document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = true;
  }//f_submit