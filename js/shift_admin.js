/*
JS for shift change by admin
*/
const objGlobal = {
    w: "", //c_weekday
    s: "", //store
    year: "",
    mon: "",
    day: "",
    isholiday: false,
    cmon: "", //calendar month
};

arrayToSubmit = []; //arrany pass to admin_shift_save.php
var isChangeUpdated = true; //change is saved

window.addEventListener("DOMContentLoaded", function() {
    modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));

    elmCell = document.getElementById("thisCell");
    objGlobal.s = elmCell.getAttribute("data-stocking-store");
    objGlobal.year = elmCell.getAttribute("data-stocking-year");
    objGlobal.mon = elmCell.getAttribute("data-stocking-mon");    
    objGlobal.day = elmCell.getAttribute("data-stocking-day");    
    objGlobal.w = elmCell.getAttribute("data-stocking-weekday");    
    objGlobal.cmon = elmCell.getAttribute("data-stocking-calendarmon"); 
    objGlobal.isholiday = (elmCell.getAttribute("data-stocking-isholiday") == "1"); 
}, false);
  
//return button to return to month calendar displayed
function f_return() {
    strURL = "shift.php?year=" + objGlobal.year + "&mon=" + objGlobal.cmon;
    window.location.href = strURL;
}
  
//Shift data changed.
//idxTab: tab index
//intTimeChanged: 1: time change, 0: name change
function f_ShiftChanged(idxTab, intTimeChanged){
    isChangeUpdated = false;
    document.getElementById("btnUpdate").disabled = false;
    document.getElementById("btnSave").disabled = true;
    const sltTimeStart = document.getElementById('sltTimeStart'+idxTab);
    const sltTimeEnd = document.getElementById('sltTimeEnd'+idxTab);
    const checkFullDay = document.getElementById('checkFullDay'+idxTab);
    const elmSltUser =  document.getElementById('sltUser'+idxTab);
    const employeeStatus = elmSltUser.options[elmSltUser.selectedIndex].getAttribute("data-stocking-employee");
    //calculate total time
    var intTotalMins = 0;
    if (intTimeChanged == 1){ //time change
      if (checkFullDay.checked) { //full day
        sltTimeStart.value = "0:00";
        sltTimeEnd.value = "0:00";
        sltTimeStart.disabled = sltTimeEnd.disabled = true;
        checkFullDay.disabled = (employeeStatus == "F");
        if (employeeStatus == "P") {
          intTotalMins = 480; //P employee counts 8 hours
        }else{
          intTotalMins = 540  //F and S employee counts 9 hours 
        }
      }else{ //part time. 
        const startTime = sltTimeStart.value.split(":");
        const endTime = sltTimeEnd.value.split(":");
        intTotalMins = (endTime[0] - startTime[0]) * 60 + (endTime[1] - startTime[1]);
        sltTimeStart.disabled = sltTimeEnd.disabled = checkFullDay.disabled = false;
      }
    }else{ //employee change. default set to full day
      sltTimeStart.value = "0:00";
      sltTimeEnd.value = "0:00";
      checkFullDay.checked = true;
      sltTimeStart.disabled = sltTimeEnd.disabled = true;
      if (elmSltUser.value == 0){
        checkFullDay.disabled = true;
        intTotalMins = "";
      }else{
        checkFullDay.disabled = (employeeStatus == "F");
        if (employeeStatus == "P") {
          intTotalMins = 480; //P employee counts 8 hours
        }else{
          intTotalMins = 540  //F and S employee counts 9 hours 
        }
      }
      f_refreshTabUser(); //only enable selected user in one tab
    }
    document.getElementById("ipTotalMins" + idxTab).value = intTotalMins;
  }  

//Refresh Tab user list. Disable user selected in other tabs to avoid duplicate selection
function f_refreshTabUser(){
    const elmSelectedCell = document.getElementById("thisCell")
    const maxPpl = (Number)(elmSelectedCell.getAttribute("data-stocking-maxppl")); 
    var selectedUsers = [];
  
    for (idxPpl = 0, idxTab = 1; idxTab <= maxPpl; idxTab++) {
      if (document.getElementById('sltUser'+idxTab).value != 0){
        selectedUsers[idxPpl] = document.getElementById('sltUser'+idxTab).value;
        idxPpl++;
      }
    }
    for (idxTab = 1; idxTab <=maxPpl; idxTab++){
      const elmSltUser =  document.getElementById('sltUser'+idxTab);
      const optionItems = elmSltUser.options;
      for (idxOption = 1, lenOption = optionItems.length; idxOption < lenOption; idxOption++) { //start from 1 since 0 is msg not user
        if (!(optionItems[idxOption].selected)) {
        var isSelected = false;
        for (idxPpl = 0; idxPpl < selectedUsers.length; idxPpl++){
            if (optionItems[idxOption].value == selectedUsers[idxPpl]){
            isSelected = true;
            break;
            }
        }
        optionItems[idxOption].disabled = isSelected; //Disable user option if it's already selelcted by other tabs
        }
      } 
    }
  }

//Update shift to cell
function f_updateShift(){
    const elmSelectedCell = document.getElementById("thisCell")
    //get max. people required by store on the week day
    const maxPpl = (Number)(elmSelectedCell.getAttribute("data-stocking-maxppl")); 
    const minPpl = (Number)(elmSelectedCell.getAttribute("data-stocking-minppl")); 

    //post shift assignments in tabs
    for (idxPpl = 1, idxTab = 1; idxTab <= maxPpl; idxTab++) {
        const elmSltUser =  document.getElementById('sltUser'+idxTab);
        const userWorkDay = elmSltUser.options[elmSltUser.selectedIndex].getAttribute("data-stocking-workday");
        const pplLine = document.getElementById("thisCell"+idxPpl);
        const assignedPpl = elmSltUser.value;
        if (assignedPpl != 0 ){
            if (document.getElementById("checkFullDay"+idxTab).checked){
                pplLine.setAttribute("data-stocking-timestart","0:00");
                pplLine.setAttribute("data-stocking-timeend","0:00");
                pplLine.setAttribute("data-stocking-fullday","1");
            }else{
                pplLine.setAttribute("data-stocking-timestart",document.getElementById("sltTimeStart" + idxTab).value);
                pplLine.setAttribute("data-stocking-timeend",document.getElementById("sltTimeEnd" + idxTab).value);
                pplLine.setAttribute("data-stocking-fullday","0");
            }
            pplLine.setAttribute("data-stocking-totalmins",document.getElementById("ipTotalMins" + idxTab).value);

            var strClass = pplLine.getAttribute("class");
            strClass = strClass.replace(" text-warning",""); //remove off day working color
            strClass = strClass.replace(" text-danger",""); //remove vancant color
            if (!(userWorkDay.includes((String)(objGlobal.w)))) {
                strClass = strClass+" text-warning"; //add off day working color
            }
            pplLine.innerHTML = elmSltUser.value;
            pplLine.setAttribute("class",strClass);
            idxPpl++;
        }
    }
    for (;idxPpl <= maxPpl; idxPpl++){
        const pplLine = document.getElementById("thisCell"+idxPpl);
        var strClass = pplLine.getAttribute("class");
        strClass = strClass.replace(" text-warning",""); //remove off day working color
        strClass = strClass.replace(" text-danger",""); //remove vancant color
        if (idxPpl <= minPpl) {
            strClass = strClass + " text-danger"; //display vancancy as red
            pplLine.innerHTML = "*";
        }else{
            pplLine.innerHTML = "&nbsp;";
        }
        pplLine.setAttribute("class", strClass);
    }
    isChangeUpdated = true;
    document.getElementById("btnSave").disabled = false;
    document.getElementById("btnUpdate").disabled = true;
}

//ok button
function f_toConfirm() {
    class ClassToSubmit {
        constructor (store, weekday, id, year, mon, day, timestart, timeend, fullday, totalmins){
        this.store = store;
        this.weekday = weekday;
        this.id = id;
        this.year = year;
        this.mon = mon;
        this.day = day;
        this.timestart = timestart;
        this.timeend = timeend;
        this.fullday = fullday;
        this.totalmins = totalmins;
        }
    }
    var idxArray = 0;
    const strStore = objGlobal.s;
    const elmSelectedCell = document.getElementById("thisCell")
    const maxPpl = (Number)(elmSelectedCell.getAttribute("data-stocking-maxppl")); 
    for (idxPpl = 1; idxPpl <= maxPpl; idxPpl++) {
        const pplLine = document.getElementById("thisCell"+idxPpl);
        var strPpl = pplLine.innerHTML;
        if ((strPpl != '*') && (strPpl != '&nbsp;')) {
            var strTimestart = pplLine.getAttribute("data-stocking-timestart");
            var strTimeend = pplLine.getAttribute("data-stocking-timeend");
            var intFullday = pplLine.getAttribute("data-stocking-fullday");
            var intTotalMins = pplLine.getAttribute("data-stocking-totalmins");
            arrayToSubmit[idxArray] = new ClassToSubmit(strStore,objGlobal.w, strPpl, objGlobal.year, objGlobal.mon, objGlobal.day, strTimestart, strTimeend, intFullday, intTotalMins);
            idxArray++;
        }
    }
    console.log(arrayToSubmit);
    if (idxArray == 0) { //no assignment. return "" id to remove all assignments
        arrayToSubmit[idxArray] = new ClassToSubmit(strStore,objGlobal.w, "", objGlobal.year, objGlobal.mon, objGlobal.day, "", "", 0, 0);
    }
    document.getElementById("lbl_modal").innerHTML = "Confirm";
    document.getElementById("body_modal").innerHTML = "Proceed to update this shift?";
    document.getElementById("btn_ok").disabled = false;
    modal_Popup.show();
}
  
//submit data change
function f_submit() {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (this.responseText == "true") {
        document.getElementById("body_modal").innerHTML  = "Submit successfully!<br>Press OK to return";
        document.getElementById("btn_ok").setAttribute("onclick","f_return()");
        document.getElementById("btn_ok").disabled = false;
        document.getElementById("btn_cancel").disabled = true;
        document.getElementById("btnSave").disabled = false; //turn off save button
        }else{
        document.getElementById("body_modal").innerHTML  = "<p class=\"text-danger\">Update failed!</p>Return code: "+ this.responseText + "<br>Press Cancel to return";
        document.getElementById("btn_ok").disabled = true;
        document.getElementById("btn_cancel").disabled = false;
        }
    }
    const strJson = JSON.stringify(arrayToSubmit);
    xhttp.open("POST", "shift_admin_update.php");
    xhttp.setRequestHeader("Accept", "application/json");
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(strJson);
    document.getElementById("lbl_modal").innerHTML = "Request submitted";
    document.getElementById("body_modal").innerHTML = "Waiting server response...";
    document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = true;
}//f_submit