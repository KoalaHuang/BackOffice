const obj_stock = {
  "c": "", //category, to filter listed cards
  "r": 0 //rows to update
};

var modal_Popup;
window.addEventListener("DOMContentLoaded", function() {
   modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
}, false);

function f_whichCat() {
  elm = document.getElementsByName("btn_cat");
  for (i = 0, length = elm.length; i < length; i++) {
    if (elm[i].checked) {
      obj_stock.c = elm[i].id;
      break;
    }
  }
  //update reminding message
  if (obj_stock.c != "") {
    document.getElementById("reminding").innerHTML="Maintain stock for " + obj_stock.c;

    //display item cards
    i = 1;
    elm = document.getElementById("itemcard"+i);
    while (elm != null) {
      var rowCat = elm.getAttribute("data-stocking-cat");
      if (rowCat == obj_stock.c) {
        elm.setAttribute("class","card");
      }else {
        elm.setAttribute("class","card d-none");
      }
      i = i + 1;
      elm = document.getElementById("itemcard"+i);
    } // display cards
    //display submit and clear buttons
    document.getElementById("btn_submit").setAttribute("class","btn btn-primary");
    document.getElementById("btn_clear").setAttribute("class","btn btn-outline-primary");
    document.getElementById("link_btp").setAttribute("class","col-4 mt-3");
  } //store and cat seleted
} //function who_is_requesting

//clear button to reload the page
function f_refresh() {
  location.reload()
}

//update info with changed edit box value
function f_boxChanged(strIndex) {
  const inputBox = document.getElementById("box" + strIndex);
  var intChange = Number(inputBox.value);
  var intStock = Number(inputBox.getAttribute("data-stocking-stock"));
  var intResult;

  if (intChange == 0) {
    document.getElementById("lblResult" + strIndex).innerHTML = "";
  }else{
    intResult = intStock + intChange;

    if (intResult <  0) { //can't be less than zero
      intResult = 0;
      intChange = -intStock;
      inputBox.value = intChange;
    }
    document.getElementById("lblResult" + strIndex).innerHTML = "&nbsp&rarr;&nbsp" + intResult;
  }
}

//response to +/- buttons
function f_adjust (strIndex, isAdd) {
  const inputBox = document.getElementById("box" + strIndex);
  if (isAdd) {
    inputBox.value = Number(inputBox.value) + 1;
  }else{
    inputBox.value = Number(inputBox.value) - 1;
  }
  f_boxChanged(strIndex);
}

function f_toConfirm() {
  var i = 1;//row index
  var numRow=0; //row count of update
  var strName = "";
  var strToSubmit = "<p> Update " + obj_stock.c + " stock:</p>";
  var rowItem, rowCat; //item and store of current row
  var elmInput;
  var elm = document.getElementById("itemcard"+i);
  while (elm != null) {
    rowCat = elm.getAttribute("data-stocking-cat");
    rowItem = elm.getAttribute("data-stocking-item");
    if (rowCat == obj_stock.c) {
      var j = 1;
      elmInput = document.getElementById("box_" + i + "_" + j);
      while (elmInput != null) {
        var inputVal = Number(elmInput.value);
        var rowStock = Number(elmInput.getAttribute("data-stocking-stock"));
        var rowChangedStock = rowStock + inputVal;
        var storageLoc = document.getElementById("lblStorage_" + i + "_" + j).innerHTML;
        // console.log("item: "+ rowItem + "inputVal: "+inputVal+" storage: "+storageLoc+" stock:"+rowStock);
        if (inputVal != 0) {
          if (j == 1) {strToSubmit = strToSubmit + "<p>&diams;" + rowItem + "&diams;</p>";}
          strToSubmit = strToSubmit + "<p>" + storageLoc + ": " + rowStock + "&rarr;" + rowChangedStock + " by " + inputVal + "</p>";
          numRow++;
          var strNum = numRow.toString();
          strName = "i" + strNum;
          obj_stock[strName] = rowItem; //add item object property
          strName = "q" + strNum;
          obj_stock[strName] = inputVal; //add qty object property - changing value
          strName = "l" + strNum;
          obj_stock[strName] = storageLoc; //add  storage location property
        }// if stock is changed
        j++;
        elmInput = document.getElementById("box_" + i + "_" + j);
      } //loop stock ranges
    } // if card's cat and store  are selected
    i++;
    elm = document.getElementById("itemcard"+i);
  } // loop rows
  obj_stock.r = numRow;
  if (numRow == 0) {
    strToSubmit = "No stock allocated to fulfill.";
    document.getElementById("btn_ok").style.visibility = "hidden";
  }else{
    document.getElementById("btn_ok").style.visibility = "visible";
    document.getElementById("btn_cancel").style.visibility = "visible";
  }
  document.getElementById("body_modal").innerHTML = strToSubmit;
  // console.log(obj_stock);
  modal_Popup.show();
}// function f_toConfirm

function f_submit() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if (this.responseText == "true") {
      document.getElementById("body_modal").innerHTML = "Update successfully!<br>Press OK to return";
      document.getElementById("btn_ok").style.visibility = "visible";
      document.getElementById("btn_ok").onclick = f_refresh;
    }else{
      document.getElementById("body_modal").innerHTML = "Update failed!<br>"+ this.responseText + "<br>Press Cancel to return";
      document.getElementById("btn_cancel").style.visibility = "visible";
    }
  }
  const strJson = JSON.stringify(obj_stock);
  xhttp.open("POST", "stock_update.php");
  xhttp.setRequestHeader("Accept", "application/json");
  xhttp.setRequestHeader("Content-Type", "application/json");
  xhttp.send(strJson);
  document.getElementById("lbl_modal").innerHTML = "Stock update submitted";
  document.getElementById("body_modal").innerHTML = "Waiting server response...";
  document.getElementById("btn_cancel").style.visibility = "hidden";
  document.getElementById("btn_ok").style.visibility = "hidden";
}//f_submit
