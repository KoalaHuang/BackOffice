/* Receipt - Material javacript */

const arrayM = [];
const arrayCost = [];
const arrayUnit = [];
const arraySupplier = [];

window.addEventListener("DOMContentLoaded", function() {
    modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
  
    elmIptMaterial = document.getElementById('iptMaterial');
    elmUlMaterial = document.getElementById('ulMaterial');
    totalMaterial = elmUlMaterial.childElementCount;
    for (idx = 0; idx < totalMaterial; idx++){
        var elmLi = elmUlMaterial.children[idx];
        arrayM[idx] = elmLi.innerText;
        arrayCost[idx] = elmLi.getAttribute("data-bo-cost");
        arrayUnit[idx] = elmLi.getAttribute("data-bo-unit");
        arraySupplier[idx] = elmLi.getAttribute("data-bo-supplier");
    }
    elmIptMaterial.addEventListener('keyup', searchHandler);
    elmUlMaterial.addEventListener('click', useSuggestion);  
  }, false);

function search(str) {
	let results = [];
	const val = str.toLowerCase();

	for (i = 0; i < arrayM.length; i++) {
		if (arrayM[i].toLowerCase().indexOf(val) > -1) {
			results.push(arrayM[i]);
		}
	}

	return results;
}

function searchHandler(e) {
	const inputVal = e.currentTarget.value;
	let results = [];
	if (inputVal.length > 0) {
		results = search(inputVal);
	}
	showSuggestions(results, inputVal);
}

function showSuggestions(results, inputVal) {
    
    elmUlMaterial.innerHTML = '';

	if (results.length > 0) {
		for (i = 0; i < results.length; i++) {
			let item = results[i];
			// Highlights only the first match
			// TODO: highlight all matches
			const match = item.match(new RegExp(inputVal, 'i'));
			item = item.replace(match[0], `<strong>${match[0]}</strong>`);
			elmUlMaterial.innerHTML += `<li>${item}</li>`;
		}
		elmUlMaterial.classList.add('listed');
	} else {
		results = [];
		elmUlMaterial.innerHTML = '';
		elmUlMaterial.classList.remove('listed');
	}
}

function useSuggestion(e) {
	elmIptMaterial.value = e.target.innerText;
	elmIptMaterial.focus();
	elmUlMaterial.innerHTML = '';
	elmUlMaterial.classList.remove('listed');
}

