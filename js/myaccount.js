const objGlobal = {
  "n": "", //name
  "p": "", //p
};

window.addEventListener("DOMContentLoaded", function() {
  modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
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
  var strBody = strTitle = "";
  var needToCancel = true;
  strTitle = "Confirm to update user?";
  strBody = "Name: " + objGlobal.n;
  if (objGlobal.p != '') {
    strBody = strBody + "<br><span class=\"text-danger\">Password</span>: " + objGlobal.p;
    needToCancel = false;
  }else{
    strBody = strBody + "Nothing is changed."
    needToCancel = true;
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
