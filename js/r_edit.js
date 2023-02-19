/* Receipt - Material javacript */

const arrayProduct = [];
const arrayCat = [];
const arrayItem = [];
const arrayItemUnit = [];
const arrayItemIsBase = [];

const objGlobal = {
	product: "",
	version: 0,
	recipe: 0,
	cat: "",
	act: 0 //1: update, 2: insert, 3: delete, 0: no change
  };

window.addEventListener("DOMContentLoaded", function() {
    modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
  
    elmIptProduct = document.getElementById('iptProduct');
    elmUlProduct = document.getElementById('ulProduct');
    elmSltCat = document.getElementById('sltCat');
    elmSltVer = document.getElementById('sltVer');

	elmIptItem = document.getElementById('iptItem');
	elmIptUnit = document.getElementById('iptUnit');
	elmIptQty = document.getElementById('iptQuantity');
	elmUlItem = this.document.getElementById('ulItem');

    const totalProduct = elmUlProduct.childElementCount;
    for (idx = 0; idx < totalProduct; idx++){
        var elmLi = elmUlProduct.children[idx];
        arrayProduct[idx] = elmLi.innerText;
        arrayCat[idx] = elmLi.getAttribute("data-bo-cat");
    }
    const totalItem = elmUlItem.childElementCount;
    for (idx = 0; idx < totalItem; idx++){
        var elmLi = elmUlItem.children[idx];
        arrayItem[idx] = elmLi.innerText;
        arrayItemUnit[idx] = elmLi.getAttribute("data-bo-unit");
        arrayItemIsBase[idx] = (elmLi.getAttribute("data-bo-isbase")==1)?true:false;
    }

	elmIptProduct.addEventListener('keyup', searchHandler);
    elmIptItem.addEventListener('keyup', searchHandler);
  }, false);

 /*filter values for input box drop down list*/
function search(str,arr) {
	let results = [];
	const val = str.toLowerCase();
	for (i = 0; i < arr.length; i++) {
		if (arr[i].toLowerCase().indexOf(val) > -1) {
			results.push(i);
		}
	}
	return results;
}

/*trigger input box filter drop downlist when key in the box*/
function searchHandler(e) {
	const inputVal = e.currentTarget.value;
	let results = [];
	if (e.currentTarget == elmIptProduct){
		if (inputVal.length > 0) {
			results = search(inputVal,arrayProduct);
		}else{
			elmSltCat.value = "Product type...";
			elmSltVer.value = "Recipe";
			elmSltCat.disabled = elmSltVer.disabled = true;
			elmIptUnit.disabled = elmIptQty.disabled = true;
		}
		showSuggestedProduct(results, inputVal);
	}else{
		if (inputVal.length > 0) {
			results = search(inputVal,arrayItem);
		}else{
			elmIptUnit.value = "";
			elmIptQty.value = "";
			elmIptUnit.disabled = elmIptQty.disabled = true;
		}
		showSuggestedItem(results,inputVal);
	}
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

/*show input box drop down with filtered material item*/
function showSuggestedItem(results, inputVal) {
    elmUlItem.innerHTML = '';
	const elmBtn = document.getElementById("btnItemList");
	if (results.length > 0) {
		for (i = 0; i < results.length; i++) {
			let itemIdx = results[i];
			let item = arrayItem[itemIdx];

            const match = item.match(new RegExp(inputVal, 'i'));
			item = item.replace(match[0], `<strong>${match[0]}</strong>`);
			var strTextColor = (arrayItemIsBase[itemIdx])?" text-danger":"";
			elmUlItem.innerHTML += `<li class="search-li${strTextColor}" onclick="useSuggestedItem(${itemIdx})">${item}</li>`;
		}
		elmUlItem.classList.add('listed');
		elmBtn.checked = true;
	} else {
		results = [];
		elmUlItem.innerHTML = '';
		elmUlItem.classList.remove('listed');
		elmBtn.checked = false;
	}
}
/*Toggle dropdown list when Product button is clicked*/
function f_ListToggleProduct(){
	const elmBtn = document.getElementById("btnProductList");
	const elmLabel = document.getElementById("lblProductList");
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

/*Toggle dropdown list when Material Item button is clicked*/
function f_ListToggleItem(){
	const elmBtn = document.getElementById("btnItemList");
	if (elmBtn.checked){
		const inputVal = elmIptItem.value;
		if (inputVal.length > 0) {
			let results = [];
			results = search(inputVal,arrayItem);
			showSuggestedItem(results, inputVal);
		}else{
			elmUlItem.innerHTML = '';
			for (var i = 0; i < arrayItem.length; i++) {
				var strTextColor = '';
				if (arrayItemIsBase[i]){
					strTextColor = ' text-danger';
				}
				elmUlItem.innerHTML += `<li class="search-li${strTextColor}" onclick="useSuggestedItem(${i})">${arrayItem[i]}</li>`;
			}
			elmUlItem.classList.add('listed');
			elmBtn.checked = true;
		}
	}else{
		elmUlItem.innerHTML = '';
		elmUlItem.classList.remove('listed');
		elmBtn.checked = false;
	}
}

/*select from dropdown list to be value in input box*/
function useSuggestedProduct(idx) {
	elmIptProduct.value = objGlobal.product = arrayProduct[idx];
	elmSltCat.value = objGlobal.cat = arrayCat[idx];
	///filter recipe version for selected product
	const countVer = elmSltVer.childElementCount;
	objGlobal.ver = 0;
	for (i = 1;i < countVer; i++){//first option (0) is 'Recipe'
		var elmVerOption = elmSltVer.children[i];
		if (elmVerOption.getAttribute('data-bo-product') == objGlobal.product){
			elmVerOption.setAttribute('class',"");
			objGlobal.ver = elmVerOption.value;
		}else{
			elmVerOption.setAttribute('class',"d-none");
		}
	}
	if(objGlobal.ver > 0){
		elmSltVer.value = objGlobal.ver;
	}
	elmSltCat.disabled = elmSltVer.disabled = false;
	elmUlProduct.innerHTML = '';
	elmUlProduct.classList.remove('listed');
	document.getElementById("btnProductList").checked = false;
	elmIptProduct.focus();
}

/*select from dropdown list to be value in input box*/
function useSuggestedItem(idx) {
	elmIptItem.value = arrayItem[idx];
	elmIptQty.value = "";
	elmIptUnit.value = arrayItemUnit[idx];
	elmIptQty.disabled = false;
	elmUlItem.innerHTML = '';
	elmUlItem.classList.remove('listed');
	document.getElementById("btnItemList").checked = false;
	elmIptQty.focus();
}

/* Retrive recipe for selected product */
function f_getRecipe(){
	objGlobal.product = elmIptProduct.value;
	objGlobal.cat = elmSltCat.value;
	objGlobal.ver = elmSltVer;
	objGlobal.act = 2; //insert new record

	if (arrayProduct.includes(objGlobal.product)){
		window.location.href = "r_edit.php?product=" + objGlobal.product + "&cat=" + objGlobal.cat + "&ver=" + objGlobal.ver;
	}
	document.getElementById("modal_body").innerHTML = "<strong>Create NEW recipe?</strong><br>" + "Product: " + objGlobal.product + "<br>Type: " + objGlobal.cat;
	document.getElementById("btn_ok").setAttribute('onclick','f_newRecipe()');
	document.getElementById("btn_ok").disabled = false;
	document.getElementById("modal_status").innerHTML = "";
	modal_Popup.show();  
}

/* Create new recipe */
function f_newRecipe(){
	window.location.href = "r_edit.php?product=" + objGlobal.product + "&cat=" + objGlobal.cat + "&ver=0";
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
	xhttp.open("POST", "r_edit_update.php");
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
  