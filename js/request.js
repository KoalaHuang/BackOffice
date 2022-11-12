const obj_request = {
  "s": "",
  "c": "",
  "r": 0
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
      obj_request.s = elm[i].id;
      break;
    }
  }
  elm = document.getElementsByName("btn_cat");
  for (i = 0, length = elm.length; i < length; i++) {
    if (elm[i].checked) {
      obj_request.c = elm[i].id;
      break;
    }
  }
  //update reminding message
  if (!((obj_request.c == "") || (obj_request.s == ""))) {
    document.getElementById("reminding").innerHTML="Request " + obj_request.c + " for " + obj_request.s + " store";

    //display item cards
    i = 1;
    elm = document.getElementById("itemcard"+i);
    while (elm != null) {
      var rowStore = elm.getAttribute("data-stocking-store");
      var rowCat = elm.getAttribute("data-stocking-cat");
      if ((rowCat == obj_request.c) && (rowStore == obj_request.s))  {
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

function f_refresh() {
  location.reload()
} //function clear()

function f_toConfirm() {
  var i = 1, numRow=0;
  var strName = "";
  var strToSubmit = "<p>Request for " + obj_request.s + ":</p>";
  var radios, rowStore, rowCat, rowItem;
  var elm = document.getElementById("itemcard"+i);
  while (elm != null) {
    rowStore = elm.getAttribute("data-stocking-store");
    rowCat = elm.getAttribute("data-stocking-cat");
    rowItem = elm.getAttribute("data-stocking-item");
    if ((rowCat == obj_request.c) && (rowStore == obj_request.s)) {
      radios = document.getElementsByName("reqQty"+i);
      for (var j=0; j<radios.length; j++){
        if (radios[j].checked) {
          strToSubmit = strToSubmit + "<p>" + rowItem + "&nbsp&nbsp[" + radios[j].value + "]</p>";
          numRow++;
          strName = "i" + numRow.toString();
          obj_request[strName]=rowItem;
          strName = "q" + numRow.toString();
          obj_request[strName]=Number(radios[j].value);
          break;
        } //if_checked qty
      } //for_search for qty
    } //if_dispolayed row
    i = i + 1;
    elm = document.getElementById("itemcard"+i);
  } // loop rows
  obj_request.r = numRow;
  document.getElementById("body_modal").innerHTML = strToSubmit + "<br>";
  modal_Popup.show();
}// function f_toConfirm

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
  const strJson = JSON.stringify(obj_request);
  xhttp.open("POST", "req_update.php");
  xhttp.setRequestHeader("Accept", "application/json");
  xhttp.setRequestHeader("Content-Type", "application/json");
  xhttp.send(strJson);
  document.getElementById("lbl_modal").innerHTML = "Request submitted";
  document.getElementById("body_modal").innerHTML = "Waiting server response...";
  document.getElementById("btn_cancel").style.visibility = "hidden";
  document.getElementById("btn_ok").style.visibility = "hidden";
}//f_submit
