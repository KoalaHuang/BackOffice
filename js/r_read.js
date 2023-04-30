/* Receipe - edit recipe 
*/

const arrayProduct = [];
const arrayCat = [];
const arrayRecipe = []; //all product, ver and recipe#

const objGlobal = {
	product: "",
	version: 0,
	recipe: 0,
	cat: "",
    qty: 1 //recipe quantity, default size by product
  };

window.addEventListener("DOMContentLoaded", function() {
    modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
  
    elmIptProduct = document.getElementById('iptProduct');
    elmUlProduct = document.getElementById('ulProduct');
    elmSltCat = document.getElementById('sltCat');
    elmSltVer = document.getElementById('sltVer');
	elmTxtComment = document.getElementById('txtComment');

	elmIptPlanQty = document.getElementById('iptPlanQty');//planned quantity

	objGlobal.product = elmIptProduct.value;
	objGlobal.cat = elmSltCat.value;
	objGlobal.version = elmSltVer.value;

    const totalProduct = elmUlProduct.childElementCount;
    for (idx = 0; idx < totalProduct; idx++){
        var elmLi = elmUlProduct.children[idx];
        arrayProduct[idx] = elmLi.innerText;
        arrayCat[idx] = elmLi.getAttribute("data-bo-cat");
    }

	const elmUlRecipe = document.getElementById("ulAllRecipe");
	const totalRecipe = elmUlRecipe.childElementCount;
    for (idx = 0; idx < totalRecipe; idx++){
        var elmLi = elmUlRecipe.children[idx];
		arrayRecipe[idx] = [];
        arrayRecipe[idx][0] = elmLi.getAttribute("data-bo-product"); //product
        arrayRecipe[idx][1] = elmLi.innerText; //version
        arrayRecipe[idx][2] = elmLi.getAttribute("data-bo-recipe"); //recipe num
		if ((arrayRecipe[idx][0]==objGlobal.product) && (arrayRecipe[idx][1] == objGlobal.version)) objGlobal.recipe = arrayRecipe[idx][2]; //recipe num for selected product version
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
    }else{
        elmSltCat.value = "";
        elmSltVer.value = "";
        elmSltVer.disabled = true;
		elmTxtComment.innerText = "";
	}
	document.getElementById("btnQtyUp").disabled = document.getElementById("btnQtyDown").disabled = document.getElementById("btnCook").disabled = true;
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
	objGlobal.recipe = elmVerOption.getAttribute("data-bo-recipe");
	objGlobal.version = elmVerOption.innerText;
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

/*select from dropdown list to be value in input box*/
function useSuggestedProduct(idx) {
	elmIptProduct.value = objGlobal.product = arrayProduct[idx];
	elmSltCat.value = objGlobal.cat = arrayCat[idx];
	///filter recipe version for selected product
	f_filterVersion();
	elmSltVer.disabled = false;
	elmTxtComment.value = "";//clean comment field
	elmUlProduct.innerHTML = '';
	elmUlProduct.classList.remove('listed');
	document.getElementById("btnProductList").checked = false;
	elmIptProduct.focus();
}

/* Retrive recipe for selected product */
function f_getRecipe(){
	objGlobal.product = elmIptProduct.value;
	objGlobal.cat = elmSltCat.value;
	objGlobal.version = elmSltVer.value;

	if (objGlobal.product == "") {
		alert("Please fill in product name.");
	}else{
		if (arrayProduct.includes(objGlobal.product)){
			objGlobal.product = objGlobal.product.replace(/'/g,'{'); //encoded apostrophe can't be supported by some webserver. replace it with {
			window.location.href = "r_read.php?product=" + encodeURIComponent(objGlobal.product) + "&ver=" + objGlobal.version;
		}else{
            alert("Product name doesn't exist.");
        }
    }
}
  
//Adjust recipe item quantity
function f_kg(plannedQty){
    const elmDivQty = document.getElementsByName("divQty");
    const totalReciptItem = elmDivQty.length;
	for (var idx = 0; idx < totalReciptItem; idx++){
		var elmRecipeRow = elmDivQty[idx];
        var itemQty = elmRecipeRow.innerText; //quantity
		var num = Number(itemQty)/objGlobal.qty*plannedQty;
		if (num % 1 !== 0) { // check if number has decimal
			num = num.toFixed(1); // display one decimal
		} else {
			num = num.toFixed(0); // display no decimal
		}
        elmRecipeRow.innerText = num;
	}    
    objGlobal.qty = plannedQty;
    elmIptPlanQty.value = objGlobal.qty;
}

//change planned quantity
function f_planQty(intChangVal){
    var plannedQty = Number(elmIptPlanQty.value);
    if ((Number(intChangVal) < 0) && (plannedQty == 1)){
        return;
    }else{
		plannedQty = plannedQty + Number(intChangVal);
        f_kg(plannedQty);
    }
}

//ok button to refresh the page when failed
function f_refresh() {
	window.location.href = "r_read.php";
}
  
//Cook and add into inventory
function f_cook(){
	objGlobal.qty = Number(elmIptPlanQty.value);
	if ((objGlobal.product == "") || (objGlobal.version == 0) || (objGlobal.qty == 0)){
		alert("Select product and recipe first!");
	}else{
		strUnit = (objGlobal.cat=='Batter')?'Tub':'Kg';
		strAct = "<strong>" + objGlobal.qty + " </strong> " + strUnit + "&nbspof&nbsp&nbsp<strong>" + objGlobal.product + "</strong>";	
		document.getElementById("modal_body").innerHTML = strAct;
		document.getElementById("btn_ok").disabled = false;
		document.getElementById("modal_status").innerHTML = "";
		modal_Popup.show();  
	}
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
	xhttp.open("POST", "r_read_update.php");
	xhttp.setRequestHeader("Accept", "application/json");
	xhttp.setRequestHeader("Content-Type", "application/json");
	xhttp.send(strJson);
	document.getElementById("modal_status").innerHTML = "Submitting...";
	document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = true;
  }//f_submit