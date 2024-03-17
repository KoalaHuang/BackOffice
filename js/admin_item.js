const objGlobal = {
  "c": "", //category of item
  "i": "", //itemSelected. if it's inserting new item, 'i' will be 'addNewItem'
  "s": "", //storage of selected item
  "ni": "", //new item name
  "ns": "", //new storage location
};

const arrayObjItem = [];

window.addEventListener("DOMContentLoaded", function() {
  modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));

   //read item options into array
   elmSltItem =  document.getElementById('sltItem');
   var optionItems = elmSltItem.options;
   //0 is 'Select Item', 1 is 'addNewItem'
   for (idxOption = 2, lenOption = optionItems.length; idxOption < lenOption; idxOption++) {
     const objItem = {
       "c": "", //category of item
       "i": "", //item name
       "s": "" //storage
     };
     objItem.c = optionItems[2].getAttribute("data-stocking-cat");
     objItem.s = optionItems[2].getAttribute("data-stocking-storage");
     objItem.i = optionItems[2].value;
     arrayObjItem.push(objItem);
     elmSltItem.remove(2);
   }

}, false);

//update display with latst objGlobal value
function f_updateDisplay () {
  //item select
  if (objGlobal.i == 'Select Item') { //reset item list
    const elmSltItem =  document.getElementById('sltItem');
    for (idxOption = elmSltItem.options.length - 1; idxOption > 1; idxOption--) {
      elmSltItem.remove(idxOption);
    }
    elmSltItem.options[0].selected = true;
    if (objGlobal.c == 'Select Category') { //no cat is selected, disbled item selection
      elmSltItem.disabled = true;
    }else{ //change category
      elmSltItem.disabled = false;
      for (idxOption = 0, lenOption = arrayObjItem.length; idxOption < lenOption; idxOption++) {
        var catOfOption = arrayObjItem[idxOption].c;
        if (catOfOption == objGlobal.c) {
          var optionItem = document.createElement("option");
          optionItem.text = arrayObjItem[idxOption].i;
          optionItem.setAttribute("data-stocking-storage",arrayObjItem[idxOption].s);
          elmSltItem.add(optionItem);
        }
      } // for
    }//if cat is selected or not
  } //if item list need to reset

  //item edit box
  var itemBox = document.getElementById("iptItem");
  if (objGlobal.i == 'Select Item') {
    itemBox.value = "";
    itemBox.disabled = true;
  }else{
    if (objGlobal.i == 'addNewItem') {
      itemBox.placeholder = "name of new item";
      itemBox.value = "";
    }else{
      itemBox.value = objGlobal.i;
    }
    itemBox.disabled = false;
  }
  //storage radio buttons
  elmBtnStorage = document.getElementsByName("btn_storage");
  for (idxStorage = 0, length = elmBtnStorage.length; idxStorage < length; idxStorage++) {
    elmBtnStorage[idxStorage].disabled = (objGlobal.i == 'Select Item');
    elmBtnStorage[idxStorage].checked = (elmBtnStorage[idxStorage].id == objGlobal.s);
  }//for
}

//Category selection under item tab changed. Clean item selectin and inputs
function f_catSelected() {
  objGlobal.c = document.getElementById('sltCat').value;
  objGlobal.i = "Select Item";
  objGlobal.s = "";
  f_updateDisplay();
}

//item selected
function f_itemSelected() {
  var optionItems = document.getElementById('sltItem').children;
  for (idxOption = 0, lenOption = optionItems.length; idxOption < lenOption; idxOption++) {
    if (optionItems[idxOption].selected) {
      objGlobal.i = optionItems[idxOption].value;
      objGlobal.s = optionItems[idxOption].getAttribute("data-stocking-storage");
      break;
    }
  }//for
  f_updateDisplay();
}

//cancel button
function f_refresh() {
  location.reload()
}

//ok button
function f_toConfirm() {
  objGlobal.ni = document.getElementById("iptItem").value;
  elmBtnStorage = document.getElementsByName("btn_storage");
  for (idxStorage = 0, length = elmBtnStorage.length; idxStorage < length; idxStorage++) {
    if (elmBtnStorage[idxStorage].checked) {
      objGlobal.ns = elmBtnStorage[idxStorage].id;
    }//if
  }//for_storage value
  if ((objGlobal.ni == "") || (objGlobal.ns == "")) {
    document.getElementById("lbl_modal").innerHTML = "Create new item";
    document.getElementById("body_modal").innerHTML = "<p class=\"text-danger\">Item name and Storage can not be blank!</p>Press Cancel to return";
    document.getElementById("btn_cancel").style.visibility = "visible";
    document.getElementById("btn_ok").style.visibility = "hidden";
  }else{
    var strToSubmit = "";
    if (objGlobal.i == 'addNewItem') {
      document.getElementById("lbl_modal").innerHTML = "Confirm to create new item?";
      strToSubmit = "Item Category: " + objGlobal.c + "<br>" + "Item name: " + objGlobal.ni + "<br>" + "Item storage location: " + objGlobal.ns;
    }else{
      document.getElementById("lbl_modal").innerHTML = "Confirm to change current item?";
      strToSubmit = "Item Category: " + objGlobal.c + "<br>" + "Item name: " + objGlobal.i + "<br>" + "Item storage location: " + objGlobal.s;
      strToSubmit = strToSubmit + "<br><strong> change to </strong><br>"
      strToSubmit = strToSubmit + "Item Category: " + objGlobal.c + "<br>" + "Item name: " + objGlobal.ni + "<br>" + "Item storage location: " + objGlobal.ns;
    }
    document.getElementById("body_modal").innerHTML = strToSubmit;
    document.getElementById("btn_cancel").style.visibility = "visible";
    document.getElementById("btn_ok").style.visibility = "visible";
  }
  modal_Popup.show();
}

//submit data change
function f_submit() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if (this.responseText == "true") {
      document.getElementById("body_modal").innerHTML = "Submit successfully!<br>Press OK to return";
      document.getElementById("btn_ok").style.visibility = "visible";
      document.getElementById("btn_ok").onclick = f_refresh;
    }else{
      document.getElementById("body_modal").innerHTML = "<p class=\"text-danger\">Update failed!</p>Return code: "+ this.responseText + "<br>Press Cancel to return";
      document.getElementById("btn_cancel").style.visibility = "visible";
      // modal_Popup.hide();
    }
  }
  const strJson = JSON.stringify(objGlobal);
  xhttp.open("POST", "admin_item_update.php");
  xhttp.setRequestHeader("Accept", "application/json");
  xhttp.setRequestHeader("Content-Type", "application/json");
  xhttp.send(strJson);
  document.getElementById("lbl_modal").innerHTML = "Request submitted";
  document.getElementById("body_modal").innerHTML = "Waiting server response...";
  document.getElementById("btn_cancel").style.visibility = "hidden";
  document.getElementById("btn_ok").style.visibility = "hidden";
}//f_submit
