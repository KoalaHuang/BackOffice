const objGlobal = {
  "n": "", //name
  "p": "", //p
  "w": "", //old working day
  "nw": "" // new working day
};

window.addEventListener("DOMContentLoaded", function() {
  modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
  objGlobal.w = "";
  elmBtn = document.getElementsByName("btn_workday");
  for (idx = 0, length = elmBtn.length; idx < length; idx++) {
    if (elmBtn[idx].checked) {
      objGlobal.w = objGlobal.w + elmBtn[idx].value;
    }
  }//workday value
}, false);

//cancel button
function f_refresh() {
  location.reload()
}

//password f_pwdChanged
function f_pwdChanged() {
  document.getElementById("btn_ok").disabled = (document.getElementById("iptPwd").value == "");
}

//ok button
function f_toConfirm() {
  objGlobal.n = document.getElementById("iptName").value;
  objGlobal.p = document.getElementById("iptPwd").value;
  const strTitle = "Confirm to update your info?";
  var strBody = "Name: " + objGlobal.n;
  var needToCancel = true;

  //get working day
  strWorkDay = "";
  elmBtn = document.getElementsByName("btn_workday");
  for (idx = 0, length = elmBtn.length; idx < length; idx++) {
    if (elmBtn[idx].checked) {
      strWorkDay = strWorkDay + elmBtn[idx].value;
    }
  }
  if (strWorkDay != objGlobal.w){
    strBody = strBody + "<br>Working day</span>: " + strWorkDay;
    objGlobal.nw = strWorkDay;
    needToCancel = false;
  }else{
    objGlobal.nw = "";//indicating no change on working day
  }

  if (objGlobal.p != '') {
    strBody = strBody + "<br><span class=\"text-danger\">Password</span>: " + objGlobal.p;
    needToCancel = false;
  }else{
    if (objGlobal.nw == ""){
      strBody = strBody + "<br>Nothing is changed."
      needToCancel = true;
    }
  }

  document.getElementById("btn_ok").disabled = needToCancel;
  document.getElementById("lbl_modal").innerHTML = strTitle;
  document.getElementById("body_modal").innerHTML = strBody;
  modal_Popup.show();
}

//submit data change
function f_submit() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if (this.responseText == "true") {
      document.getElementById("body_modal").innerHTML  = "Submit successfully!<br>Press OK to return";
      document.getElementById("btn_ok").setAttribute("onclick","f_refresh()");
      document.getElementById("btn_ok").disabled = false;
      document.getElementById("btn_cancel").disabled = true;
    }else{
      document.getElementById("body_modal").innerHTML  = "<p class=\"text-danger\">Update failed!</p>Return code: "+ this.responseText + "<br>Press Cancel to return";
      document.getElementById("btn_ok").disabled = true;
      document.getElementById("btn_cancel").disabled = false;
    }
  }
  const strJson = JSON.stringify(objGlobal);
  xhttp.open("POST", "myaccount_update.php");
  xhttp.setRequestHeader("Accept", "application/json");
  xhttp.setRequestHeader("Content-Type", "application/json");
  xhttp.send(strJson);
  document.getElementById("lbl_modal").innerHTML = "Request submitted";
  document.getElementById("body_modal").innerHTML = "Waiting server response...";
  document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = true;
}//f_submit
