/* Receipe - edit recipe 
*/

const arrayProduct = [];
const arrayCat = [];
const arrayItem = [];
const arrayItemUnit = [];
const arrayItemIsBase = [];
const arrayRecipe = []; //all product, ver and recipe#

const objGlobal = {
	product: "",
	version: 0,
	recipe: 0,
	cat: "",
	arrayItemM: [], //recipe item
	arrayItemU: [], //recipe unit
	arrayItemQ: [], //recipe qty
	arrayItemB: [], //1: base, 2: raw material
	act: 0 //1: update, 2: insert
  };

window.addEventListener("DOMContentLoaded", function() {
    modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
  
    elmIptProduct = document.getElementById('iptProduct');
    elmUlProduct = document.getElementById('ulProduct');
    elmSltCat = document.getElementById('sltCat');
    elmSltVer = document.getElementById('sltVer');

	elmIptItem = document.getElementById('iptItem');//recipe edit input box
	elmIptUnit = document.getElementById('iptUnit');
	elmIptQty = document.getElementById('iptQuantity');
	elmUlItem = document.getElementById('ulItem'); //all material list group
	elmUlRecipeItem = document.getElementById('ulRecipe'); //recipe item list group

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

	const elmUlRecipe = document.getElementById("ulAllRecipe");
	const totalRecipe = elmUlRecipe.childElementCount;
    for (idx = 0; idx < totalRecipe; idx++){
        var elmLi = elmUlRecipe.children[idx];
		arrayRecipe[idx] = [];
        arrayRecipe[idx][0] = elmLi.getAttribute("data-bo-product"); //product
        arrayRecipe[idx][1] = elmLi.innerText; //version
        arrayRecipe[idx][2] = elmLi.getAttribute("data-bo-recipe"); //recipe num
    }

	elmIptProduct.addEventListener('keyup', searchHandler);
    elmIptItem.addEventListener('keyup', searchHandler);
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
	if (e.currentTarget == elmIptProduct){
		if (inputVal.length > 0) {
			results = search(inputVal,arrayProduct);
		}else{
			elmSltCat.value = "0";
			elmSltVer.value = 0;
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

/*Filter version/recipe for selected product*/
function f_filterVersion(){
	objGlobal.product = elmIptProduct.value;
	for (var i = (elmSltVer.length - 1); i > 0; i--){
		elmSltVer.remove(i); //remove verison selection. 0 is 'New Ver'
	}
	for (var i = 0; i < arrayRecipe.length; i++){
		if (arrayRecipe[i][0] == objGlobal.product){
			var elmVerOption = document.createElement("option");
			elmVerOption.setAttribute("data-bo-product",arrayRecipe[i][0]);
			elmVerOption.innerText = elmVerOption.value = arrayRecipe[i][1];
			elmVerOption.setAttribute("data-bo-recipe",arrayRecipe[i][2]);
			elmSltVer.add(elmVerOption);
		}
	}
	elmSltVer.selectedIndex = elmSltVer.length - 1;
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
	f_filterVersion();
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
	objGlobal.version = elmSltVer.value;
	objGlobal.act = 2; //insert new record

	if ((objGlobal.product == "") || (objGlobal.cat == 0)) {
		alert("Please fill in product name and type.");
	}else{
		if (arrayProduct.includes(objGlobal.product)){
			window.location.href = "r_edit.php?product=" + encodeURIComponent(objGlobal.product) + "&cat=" + encodeURIComponent(objGlobal.cat) + "&ver=" + objGlobal.version;
		}else{
			document.getElementById("modal_body").innerHTML = "<strong>Create NEW recipe?</strong><br><br>" + "Product: " + objGlobal.product + "<br>Type: " + objGlobal.cat;
			document.getElementById("btn_ok").setAttribute('onclick','f_newRecipe()');
			document.getElementById("btn_ok").disabled = false;
			document.getElementById("modal_status").innerHTML = "";
			modal_Popup.show();  
		}
	}
}

/* Create new recipe */
function f_newRecipe(){
	window.location.href = "r_edit.php?product=" + encodeURIComponent(objGlobal.product) + "&cat=" + encodeURIComponent(objGlobal.cat) + "&ver=0";
}

/* Select recipe item */
function f_selectItem(elmSelected){
	const totalReciptItem = elmUlRecipeItem.childElementCount;
	for (var idx = 0; idx < totalReciptItem; idx++){
		var elmLi = elmUlRecipeItem.children[idx];//list item <li>
		var strLiClass = elmLi.getAttribute('class');
		strLiClass = strLiClass.replace(' active','');
		if (elmLi == elmSelected){
			strLiClass = strLiClass + ' active';
			var elmRecipeRow = elmUlRecipeItem.children[idx].children[0];//<div row>
			elmIptItem.value = elmRecipeRow.children[0].innerText; //material
			elmIptQty.value = elmRecipeRow.children[1].innerText; //quantity
			elmIptUnit.value = elmRecipeRow.children[2].innerText; //unit
		}
		elmLi.setAttribute('class',strLiClass);
	}
}

/* return 1 if material is BASE, 0 if material can't be found */
function f_checkMaterial(str){
	var isBase = 0;
	for (var idx=0; idx < arrayItem.length; idx++){
		if (arrayItem[idx] == str){
			isBase = (arrayItemIsBase[idx])?1:2;
			break;
		}
	}
	return isBase;
}

/*Update recipe item by changing its quantity or adding new item in the recipe*/
function f_updateItem(){
	const totalReciptItem = elmUlRecipeItem.childElementCount;
	const strItem = elmIptItem.value;
	var isNewItem = true;
	for (var idx = 0; idx < totalReciptItem; idx++){
		var elmRecipeRow = elmUlRecipeItem.children[idx].children[0];//<div row>
		console.log(idx);
		console.log(elmRecipeRow);
		if (strItem == elmRecipeRow.children[0].innerText){
			isNewItem = false;
			elmRecipeRow.children[1].innerText = elmIptQty.value; //quantity
			f_selectItem(elmUlRecipeItem.children[idx]);
			break;
		}
	}
	if (isNewItem){ //new recipe item
		const elmLi = document.createElement("li"); //append new list item
		const isBase = f_checkMaterial(elmIptItem.value);
		console.log(isBase);
		if ( isBase == 0){
			alert('Wrong material name! Please select from list.');
		}else{
			if (isBase == 1){
				elmLi.setAttribute('class',"list-group-item list-group-item-info active");
			}else{
				elmLi.setAttribute('class',"list-group-item active");
			}
			elmLi.setAttribute('onclick','f_selectItem(this)');
			const elmRow = document.createElement("div");
			elmRow.setAttribute('class','row');
			var elmCol = document.createElement("div");
			elmCol.setAttribute('class','col-7');
			elmCol.innerText = elmIptItem.value;
			elmRow.appendChild(elmCol);
			elmCol = document.createElement("div");
			elmCol.setAttribute('class','col-3 text-end');
			elmCol.innerText = elmIptQty.value;
			elmRow.appendChild(elmCol);
			elmCol = document.createElement("div");
			elmCol.setAttribute('class','col-2');
			elmCol.innerText = elmIptUnit.value;
			elmRow.appendChild(elmCol);
			elmLi.appendChild(elmRow);
			elmUlRecipeItem.appendChild(elmLi);
			f_selectItem(elmLi);
		}

	}
}

/* delete selected item */
function f_deleteItem(){
	const totalReciptItem = elmUlRecipeItem.childElementCount;
	for (var idx = 0; idx < totalReciptItem; idx++){
		var elmLiRecipe = elmUlRecipeItem.children[idx];
		if (elmLiRecipe.getAttribute('class').indexOf('active') >= 0){
			elmLiRecipe.remove();
			break;
		}
	}
}

/* to save the recipe */
function f_saveRecipe(){
	objGlobal.product = elmIptProduct.value;
	objGlobal.cat = elmSltCat.value;
	objGlobal.version = elmSltVer.value;
	objGlobal.recipe = elmSltVer.children[elmSltVer.selectedIndex].getAttribute('data-bo-recipe');
	var strAct = "";
	var isExitingProduct = false;
	const totalReciptItem = elmUlRecipeItem.childElementCount;


	if ((objGlobal.product == "") || (objGlobal.cat == 0) || (totalReciptItem == 0)){
		if (objGlobal.product == ""){
			alert("Produt name is required!");
		}else{
			if (objGlobal.cat == 0){
				alert("Produt type is required!");
			}else{
				alert("No recipe is defined. Try to add something...");
			}
		}
		return;
	}//check data integrity

	isExitingProduct = arrayProduct.includes(objGlobal.product);
	if (isExitingProduct){
		if (objGlobal.version == 0){
			strAct = "Create <span class=\"text-danger\">NEW</span> recipe for <span class=\"text-danger\">EXISTING</span> product?";
			objGlobal.act = 2; //new recipe - new recipe version for current product
			//get current max version of the product
			var strVer = elmSltVer.lastElementChild.innerText;
			objGlobal.version = Number(strVer) + 1; //new version
		}else{
			strAct = "Update <span class=\"text-danger\">EXISTING</span> recipe?";
			objGlobal.act = 1; //update recipe
		}
	}else{
		if (objGlobal.version == 0){
			strAct = "Create <span class=\"text-danger\">NEW</span> recipe with <span class=\"text-danger\">NEW</span> product?";
			objGlobal.act = 2; //new recipe - new product recipe
			objGlobal.version = 1; //new product starts with recipe version 1
		}else{
			alert("Product can't be found. Select 'New Ver' to create new recipe if product is correct.");
			return;
		}
	}

	for (var idx = 0; idx < totalReciptItem; idx++){
		var elmRecipeRow = elmUlRecipeItem.children[idx].children[0];//<div row>
		objGlobal.arrayItemM[idx] = elmRecipeRow.children[0].innerText; //material
		objGlobal.arrayItemQ[idx] = elmRecipeRow.children[1].innerText; //quantity
		objGlobal.arrayItemU[idx] = elmRecipeRow.children[2].innerText; //unit
		objGlobal.arrayItemB[idx] = (elmUlRecipeItem.children[idx].getAttribute('class').indexOf('list-group-item-info') < 0)?2:1;//isbase
	}
	document.getElementById("modal_body").innerHTML = "<strong>" + strAct + "</strong><br><br>" + "Product: " + objGlobal.product + "<br>Recipe version: " + objGlobal.version + "<br>Product type: " + objGlobal.cat;
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
	xhttp.open("POST", "r_edit_update.php");
	xhttp.setRequestHeader("Accept", "application/json");
	xhttp.setRequestHeader("Content-Type", "application/json");
	xhttp.send(strJson);
	document.getElementById("modal_status").innerHTML = "Submitting...";
	document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = true;
  }//f_submit
  
//ok button to refresh the page when failed
function f_refresh() {
	window.location.href = "r_edit.php";
}
  