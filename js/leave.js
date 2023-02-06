//js for leave.php
const objGlobal = {
  name: "",
  id: "",
  from: "",
  to: "",
  type: "",
  kind: "",
  count: 0,
  act: 1 //1: apply leave. 0: cancel leave
};


window.addEventListener("DOMContentLoaded", function() {
  const today = new Date();
  objDate = new Lightpick({ // Lightpick snippet manual in vendor\Lightpick\README.md
    field: document.getElementById('iptDate'),
    inline: true,//show calendar
    singleDate: false,//from and to dates
    selectForward: true,//only select forward date
    minDate: today, //only select future dates
    onSelect: function(start, end){
      f_refreshTitle();
    }
  });
  
  modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));

  elmUser =  document.getElementById('txtUserName');
  objGlobal.name = elmUser.innerText;
  objGlobal.id = elmUser.getAttribute("data-stocking-userid");

}, false);

//refresh title message with latest selection
function f_refreshTitle(){
  start = objDate.getStartDate();
  end = objDate.getEndDate();
  objGlobal.from = start ? start.format('YYYY-M-D') : "...";//date format from http://momentjs.com
  objGlobal.to = end ? end.format('YYYY-M-D') : "...";
  if ((objGlobal.from != "...") && (objGlobal.to != "...")) {
    intCountOfDays = end.diff(start,'days') + 1;
    if (document.getElementById("rdoFull").checked) {
      objGlobal.kind = "FULL DAY";
      objGlobal.count = intCountOfDays * 2; //leave is calculated by number of half days
    }else{
      objGlobal.kind = ((document.getElementById("rdoAfternoon")).checked)?"PM":"AM";
      objGlobal.count = intCountOfDays; 
    }
    objGlobal.act = 1; //apply leave
    objGlobal.type = document.getElementById("sltLeaveType").value;
    document.getElementById('txtTitle').innerHTML = "<span class=\"text-danger\">" + intCountOfDays + " " + objGlobal.kind + " " + objGlobal.type + "</span> from <span class=\"text-danger\">" + objGlobal.from + '</span> to <span class=\"text-danger\">' + objGlobal.to + "</span>";
  }
}

//select leave to cancel
function f_LeaveSelected(strFrom){
  objGlobal.from = strFrom;
  objGlobal.act = 0; //cancel leave
  document.getElementById("modal_title").innerHTML = "Cancel Leave"
  document.getElementById("modal_body").innerHTML = "Cancel leave starting from <span class=\"text-danger\">" + strFrom + "</span>?";
  document.getElementById("btn_ok").disabled = false;
  document.getElementById("modal_status").innerHTML = "";
  modal_Popup.show();  
}

//OK to submit?
function f_OK(){
  if ((objGlobal.act == 1) && ((objGlobal.from == "") || (objGlobal.to == ""))){
    alert ("Pick the date to take leave.");
  }else{
    document.getElementById("modal_title").innerHTML = "Apply Leave"
    document.getElementById("modal_body").innerHTML = document.getElementById('txtTitle').innerHTML;
    document.getElementById("btn_ok").disabled = false;
    document.getElementById("modal_status").innerHTML = "";
    modal_Popup.show();  
  }
}

//cancel button
function f_refresh() {
  location.reload();
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
  xhttp.open("POST", "leave_update.php");
  xhttp.setRequestHeader("Accept", "application/json");
  xhttp.setRequestHeader("Content-Type", "application/json");
  xhttp.send(strJson);
  document.getElementById("modal_status").innerHTML = "Submitting...";
  document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = true;
}//f_submit
