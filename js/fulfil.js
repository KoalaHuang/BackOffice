const obj_fulfil = {
  "s": "", //request store
  "c": "", //request category, to filter listed cards
  "r": 0 //rows to update
};

var modal_Popup;
window.addEventListener("DOMContentLoaded", function() {
   modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
}, false);

function f_who_is_requesting() {
  var elm = document.getElementsByName("btn_store");
  var i = 0;
  //check selected store and category
  for (i = 0, length = elm.length; i < length; i++) {
    if (elm[i].checked) {
      obj_fulfil.s = elm[i].id;
      break;
    }
  }
  elm = document.getElementsByName("btn_cat");
  for (i = 0, length = elm.length; i < length; i++) {
    if (elm[i].checked) {
      obj_fulfil.c = elm[i].id;
      break;
    }
  }
  //update reminding message
  if (!((obj_fulfil.c == "") || (obj_fulfil.s == ""))) {
    document.getElementById("reminding").innerHTML="Fulfill " + obj_fulfil.c + " for " + obj_fulfil.s + " store";

    //display item cards
    i = 1;
    elm = document.getElementById("itemcard"+i);
    while (elm != null) {
      var rowStore = elm.getAttribute("data-stocking-store");
      var rowCat = elm.getAttribute("data-stocking-cat");
      if ((rowCat == obj_fulfil.c) && (rowStore == obj_fulfil.s))  {
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

//real time display Range value
function f_rangeVal (rangeID) {
  var intVal = document.getElementById(rangeID).value;
  document.getElementById(rangeID+"_val").innerHTML =  intVal;
}

function f_toConfirm() {
  var i = 1;//row index
  var numRow=0; //row count of update
  var strName = "";
  var strToSubmit = "<p>" + obj_fulfil.s + ":</p>";
  var rowStore, rowItem, rowCat; //item and store of current row
  var elm = document.getElementById("itemcard"+i);
  var rangeStk; //stock range element
  while (elm != null) {
    rowStore = elm.getAttribute("data-stocking-store");
    rowCat = elm.getAttribute("data-stocking-cat");
    rowItem = elm.getAttribute("data-stocking-item");
    if ((rowCat == obj_fulfil.c) && (rowStore == obj_fulfil.s)) {
      var j = 1;
      rangeStk = document.getElementById("r_" + i + "_" + j);
      while (rangeStk != null) {
        var rangeVal = rangeStk.value;
        var storageLoc = rangeStk.getAttribute("data-stocking-storage");
        if (rangeVal > 0) {
          strToSubmit = strToSubmit + "<p>" + rowItem + "&nbsp&nbsp[" + rangeVal + "] from " + storageLoc + "</p>";
          numRow++;
          var strNum = numRow.toString();
          strName = "i" + strNum;
          obj_fulfil[strName] = rowItem; //add item object property
          strName = "q" + strNum;
          obj_fulfil[strName] = Number(rangeVal); //add qty object property
          strName = "l" + strNum;
          obj_fulfil[strName] = storageLoc; //add  storage location property
        }// if allocate stock
        j++;
        rangeStk = document.getElementById("r_" + i + "_" + j);
      } //loop stock ranges
    } // if card's cat and store  are selected
    i++;
    elm = document.getElementById("itemcard"+i);
  } // loop rows
  obj_fulfil.r = numRow;
  if (numRow == 0) {
    strToSubmit = "No stock allocated to fulfill.";
    document.getElementById("btn_ok").style.visibility = "hidden";
  }else{
    document.getElementById("btn_ok").style.visibility = "visible";
    document.getElementById("btn_cancel").style.visibility = "visible";
  }
  document.getElementById("body_modal").innerHTML = strToSubmit;
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
      // modal_Popup.hide();
    }
  }
  const strJson = JSON.stringify(obj_fulfil);
  xhttp.open("POST", "fulfil_update.php");
  xhttp.setRequestHeader("Accept", "application/json");
  xhttp.setRequestHeader("Content-Type", "application/json");
  xhttp.send(strJson);
  document.getElementById("lbl_modal").innerHTML = "Request submitted";
  document.getElementById("body_modal").innerHTML = "Waiting server response...";
  document.getElementById("btn_cancel").style.visibility = "hidden";
  document.getElementById("btn_ok").style.visibility = "hidden";
}//f_submit
