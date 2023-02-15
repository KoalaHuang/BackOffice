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
	console.log("keyup");
	console.log(e);
	const inputVal = e.currentTarget.value;
	let results = [];
	if (inputVal.length > 0) {
		results = search(inputVal);
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
			elmUlMaterial.innerHTML += `<li data-bo-cost="${arrayCost[itemIdx]}" data-bo-moq="${arrayMoq[i]}" data-bo-unit="${arrayUnit[itemIdx]}" data-bo-supplier="${arraySupplier[itemIdx]}" onclick="useSuggestion(${itemIdx})">${item}</li>`;
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
	console.log("toggle");
	if (document.getElementById("btnList").checked){
		const inputVal = elmIptMaterial.value;
		if (inputVal.length > 0) {
			let results = [];
			results = search(inputVal);
			showSuggestions(results, inputVal);
		}else{
			elmUlMaterial.innerHTML = '';
			for (var i = 0; i < arrayM.length; i++) {
				elmUlMaterial.innerHTML += `<li data-bo-cost="${arrayCost[i]}" data-bo-moq="${arrayMoq[i]}" data-bo-unit="${arrayUnit[i]}" data-bo-supplier="${arraySupplier[i]}" onclick="useSuggestion(${i})">${arrayM[i]}</li>`;
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
	console.log("mouse");
	console.log(idx);
	elmIptMaterial.focus();
//	elmCost.value = e.target.getAttribute("data-bo-cost");
//	elmUnit.value = e.target.getAttribute("data-bo-unit");
//	elmMoq.value = e.target.getAttribute("data-bo-moq");
//	elmCost.value = e.target.getAttribute("data-bo-supplier");
	elmUlMaterial.innerHTML = '';
	elmUlMaterial.classList.remove('listed');
	f_checkBtnList(false);
}

function f_inputDone(e){
	console.log(e);
	/*collapse dropdown list*/
	f_checkBtnList(false);
	f_ListToggle();
	const InputVal = elmIptMaterial.value;
	if (InputVal.length == 0){
		elmUnit.disabled = elmCost.disabled = elmMoq.disabled = elmSupplier.disabled = true;
		elmUnit.value = elmCost.value = elmMoq.value = "";
	}else{
		elmUnit.disabled = elmCost.disabled = elmMoq.disabled = elmSupplier.disabled = false;

	}
}