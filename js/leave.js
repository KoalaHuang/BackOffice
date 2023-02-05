//js for leave.php
const objGlobal = {
  name: "",
  id: "",
  from: "",
  to: "",
  type: "",
  kind: "",
  count: 0
};


window.addEventListener("DOMContentLoaded", function() {
  objDate = new Lightpick({
    field: document.getElementById('iptDate'),
    inline: true,
    singleDate: false,
    selectForward: true,
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
    objGlobal.type = document.getElementById("sltLeaveType").value;
    document.getElementById('txtTitle').innerHTML = "<span class=\"text-danger\">" + intCountOfDays + " " + objGlobal.kind + " " + objGlobal.type + "</span> from <span class=\"text-danger\">" + objGlobal.from + '</span> to <span class=\"text-danger\">' + objGlobal.to + "</span>";
  }
}

//select leave to cancel
function f_LeaveSelected(strFrom){
  alert(strFrom);
}

//OK to submit?
function f_OK(){
  if (objGlobal.from == ""){
    alert ("Pick the date to take leave.");
  }else{
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