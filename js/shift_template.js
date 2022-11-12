const objGlobal = {
  "n": "", //name
  "i": "", //id
  "w": 0, //c_weekday
  "s": "", //store
  "st": "", //time start
  "et": "", //time end
  "fd": "", //full day
  "tm": 0, //total minutes
  "pw": 0,
  "ps": "",
};

arrayToSubmit = []; //arrany pass to admin_shift_save.php
const objDateRange = { //date range object pass to admin_shift_apply.php
  from: "",
  to: ""
}

var isChangeUpdated = true; //if shift assignment change is updated
var ToStore = ""; //store clicked before shift change is saved
var ToWeekDay = 0; //weekday clicked before shift change is saved

window.addEventListener("DOMContentLoaded", function() {
  modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
}, false);

//return weekday name for Number
function f_weekday(intWD) {
  switch (intWD) {
    case 1:
      return 'Monday';
    case 2:
      return 'Tuesday';
    case 3:
      return 'Wednesday';
    case 4:
      return 'Thursday';
    case 5:
      return 'Friday';
    case 6:
      return 'Saturday';
    case 7:
      return 'Sunday';
  }
}

//click on cell
function f_clickCell(strStore, intWD) {
  if (isChangeUpdated) {
    f_cellSelected(strStore, intWD);
  }else{
    ToStore = strStore;
    ToWeekDay = intWD;
    const myToast = new bootstrap.Toast(document.getElementById("myToast"));
    myToast.show();
  }
}

//drop changes on shift arrangement - Yes button on toast msg box clicked
function f_DropChanges() {
  f_cellSelected(ToStore, ToWeekDay);
}

//Shift data changed.
//idxTab: tab index
//intTimeChanged: 1: time change, 0: name change
function f_ShiftChanged(idxTab, intTimeChanged){
  isChangeUpdated = false;
  document.getElementById("btnUpdateShift").disabled = false;
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

//highlight selected cell and update user selection
function f_cellSelected(strStore, intWD) {
  objGlobal.ps = objGlobal.s;
  objGlobal.pw = objGlobal.w;
  objGlobal.s = strStore;
  objGlobal.w = intWD;
  const cellName = objGlobal.s + objGlobal.w;
  const cellNamePre = objGlobal.ps + objGlobal.pw;
  const elmSelectedCell = document.getElementById(cellName)
  //highlight cell with border
  var strClass = "";
  if ((objGlobal.ps != "") && (objGlobal.pw != 0)) {
    strClass = document.getElementById(cellNamePre).getAttribute("class");
    strClass = strClass.replace(" text-bg-secondary",""); //remove background from previous selection
    //strClass = strClass.replace("danger","primary"); //remove background from previous selection
    document.getElementById(cellNamePre).setAttribute("class",strClass);
  }
  strClass = elmSelectedCell.getAttribute("class");
  strClass = strClass + " text-bg-secondary"; //highlight selected cell
  elmSelectedCell.setAttribute("class",strClass);
  document.getElementById("txtSelection").innerHTML = "<strong>" + objGlobal.s + "&nbsp;" + f_weekday(objGlobal.w) + ":</strong>";

  //get max. people required by store on the week day
  var maxPpl = (Number)(elmSelectedCell.getAttribute("data-stocking-maxppl")); 

  //post shift assignments in tabs
  for (idxTab = 1; idxTab <= maxPpl; idxTab++) {
    const elmSltUser =  document.getElementById('sltUser'+idxTab);
    const optionItems = elmSltUser.options;
    const pplLine = document.getElementById(cellName+idxTab);
    var assignedPpl = pplLine.innerHTML;
    const sltTimeStart = document.getElementById('sltTimeStart'+idxTab);
    const sltTimeEnd = document.getElementById('sltTimeEnd'+idxTab);
    const checkFullDay = document.getElementById('checkFullDay'+idxTab);
    document.getElementById("tabButton"+idxTab).disabled = false;
    //employee name
    for (idxOption = 1, lenOption = optionItems.length; idxOption < lenOption; idxOption++) { //start from 1 since 0 is msg not user
      var userStore = optionItems[idxOption].getAttribute("data-stocking-userstore");
      if ((objGlobal.s == userStore) || (userStore == "ALL")) {
        optionItems[idxOption].disabled = false; //turn on user option if store matches
      }else{
        optionItems[idxOption].disabled = true; //disable user option if UserStore is not matched
      }
    } 
    //start/end time and full day check
    if (assignedPpl == "*" || assignedPpl == "&nbsp;"){
      elmSltUser.value = "0";  //reset user drop down
      sltTimeStart.disabled = sltTimeEnd.disabled = checkFullDay.disabled = true;
      sltTimeStart.value = sltTimeEnd.value = "0:00";
      checkFullDay.checked = false;
      document.getElementById('ipTotalMins'+idxTab).value = "";
    }else{
      elmSltUser.value = assignedPpl;
      const intFullday = pplLine.getAttribute("data-stocking-fullday");
      if (intFullday == 1){
        sltTimeStart.disabled = sltTimeEnd.disabled = true;
        checkFullDay.checked = true;
        checkFullDay.disabled = (elmSltUser.options[elmSltUser.selectedIndex].getAttribute("data-stocking-employee")=="F"); //F employee must be full day
      }else{
        sltTimeStart.disabled = sltTimeEnd.disabled = checkFullDay.disabled = false;
        sltTimeStart.value = pplLine.getAttribute("data-stocking-timestart");
        sltTimeEnd.value = pplLine.getAttribute("data-stocking-timeend");
        checkFullDay.checked = checkFullDay.disabled = false;
      }
      document.getElementById('ipTotalMins'+idxTab).value = pplLine.getAttribute("data-stocking-totalmins");
    }
  }
  for (;idxTab < 5;idxTab++){ //TAB number FIXED at 5!!!!
    document.getElementById("tabButton"+idxTab).disabled = true;
  }
  f_refreshTabUser(); //only enable selected user in one tab
  isChangeUpdated = true;
  document.getElementById("btnUpdateShift").disabled = true;
  const triggerFirstTabEl = document.querySelector('#shiftTab li:first-child button')
  bootstrap.Tab.getInstance(triggerFirstTabEl).show() // Select first tab
}

//Refresh Tab user list. Disable user selected in other tabs to avoid duplicate selection
function f_refreshTabUser(){
  const cellName = objGlobal.s + objGlobal.w;
  const elmSelectedCell = document.getElementById(cellName)
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
      var userStore = optionItems[idxOption].getAttribute("data-stocking-userstore");
      if ((objGlobal.s == userStore) || (userStore == "ALL")) {
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
      }else{
        optionItems[idxOption].disabled = true; //disable user option if UserStore is not matched
      }
    } 
  }
}

//Update selected weeday's shift arrangement
function f_updateSelectedWD(){
  const cellName = objGlobal.s + objGlobal.w;
  const elmSelectedCell = document.getElementById(cellName)
  //get max. people required by store on the week day
  const maxPpl = (Number)(elmSelectedCell.getAttribute("data-stocking-maxppl")); 
  const minPpl = (Number)(elmSelectedCell.getAttribute("data-stocking-minppl")); 

  //post shift assignments in tabs
  for (idxPpl = 1, idxTab = 1; idxTab <= maxPpl; idxTab++) {
    const elmSltUser =  document.getElementById('sltUser'+idxTab);
    const userWorkDay = elmSltUser.options[elmSltUser.selectedIndex].getAttribute("data-stocking-workday");
    const assignedPpl = elmSltUser.value;
    if (assignedPpl != 0 ){
      const pplLine = document.getElementById(cellName+idxPpl);
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
    const pplLine = document.getElementById(cellName+idxPpl);
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
}

//Msgbox to confirm saving the template
function f_toConfirmSaveTemplate() {
  class ClassToSubmit {
    constructor (store, weeday, people, starttime, endtime, fullday, totalmins){
      this.s = store;
      this.w = weeday;
      this.p = people;
      this.st = starttime;
      this.et = endtime;
      this.fd = fullday;
      this.tm = totalmins;
    }
  }
  idxArray = 0;
  var strStore = "";
  elmStores = document.getElementsByName("divStores");
  for (idxStore = 0; idxStore<elmStores.length; idxStore++){
    strStore = elmStores[idxStore].innerText;
    for (idxWD = 1; idxWD < 8; idxWD++) {
      //get max. people required by store on the week day
      const maxPpl = (Number)(document.getElementById(strStore+idxWD).getAttribute("data-stocking-maxppl")); 
      for (idxPpl = 1; idxPpl <= maxPpl; idxPpl++) {
        const elmPpl = document.getElementById(strStore+idxWD+idxPpl);
        strPpl = elmPpl.innerHTML;
        if ((strPpl != '*') && (strPpl != '&nbsp;')) {
          arrayToSubmit[idxArray] = new ClassToSubmit(strStore,idxWD,strPpl, elmPpl.getAttribute("data-stocking-timestart"), elmPpl.getAttribute("data-stocking-timeend"), (Number)(elmPpl.getAttribute("data-stocking-fullday")), (Number)(elmPpl.getAttribute("data-stocking-totalmins")));
          idxArray++;
        }
      }
    }
  }
  document.getElementById("lbl_modal").innerHTML = "Confirm";
  document.getElementById("body_modal").innerHTML = "Save shift tempaltes?";
  console.log(arrayToSubmit);
  document.getElementById("btn_ok").disabled = false;
  modal_Popup.show();
}

//Save template
function f_saveTemplate() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if (this.responseText == "true") {
      document.getElementById("body_modal").innerHTML  = "Submit successfully! Press OK to return";
      document.getElementById("btn_ok").setAttribute("onclick","f_refresh()");
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
  xhttp.open("POST", "shift_template_save.php");
  xhttp.setRequestHeader("Accept", "application/json");
  xhttp.setRequestHeader("Content-Type", "application/json");
  xhttp.send(strJson);
  document.getElementById("lbl_modal").innerHTML = "Request submitted";
  document.getElementById("body_modal").innerHTML = "Waiting server response...";
  document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = true;
}//f_submit

//confirm to apply shift template to dater range
function f_applyShift() {
  if (document.getElementById("btnSave").disabled) {
    const fromYear = document.getElementById("sltFromYear").value;
    const fromMon = document.getElementById("sltFromMon").value;
    const fromDay = document.getElementById("sltFromDay").value;
    const toYear = document.getElementById("sltToYear").value;
    const toMon = document.getElementById("sltToMon").value;
    const toDay = document.getElementById("sltToDay").value;
    dateFrom = new Date(fromYear, Number(fromMon)-1, fromDay,"23","59");
    dateTo = new Date(toYear, Number(toMon)-1, toDay,"23","59"); 
    const today = new Date();
    if ((dateFrom > dateTo) || (dateFrom < today) || (dateFrom == "Invalid Date") || (dateTo == "Invalid Date")) {
      alert ("Date range error! From: " + dateFrom + "  To: " + dateTo);
    }else{
      objDateRange.from = fromYear.toString() + "/" + fromMon + "/" + fromDay;
      objDateRange.to = toYear.toString() + "/" + toMon + "/" + toDay;
      document.getElementById("lbl_modal").innerHTML = "Apply Shift Tempalte";
      document.getElementById("body_modal").innerHTML = "Update shift from " + objDateRange.from + " to " + objDateRange.to + " ?"
      document.getElementById("btn_ok").setAttribute("onclick","f_submitShift()");
      document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = false;
      modal_Popup.show();
    }
  }else{
    alert ("Shift template change is not saved. Save first.")
  }
}

function f_submitShift() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if (this.responseText == "true") {
      document.getElementById("body_modal").innerHTML  = "Submit successfully!<br>Press OK to return";
      document.getElementById("btn_ok").setAttribute("onclick","f_refresh()");
      document.getElementById("btn_ok").disabled = false;
      document.getElementById("btn_cancel").disabled = true;
      document.getElementById("btnSave").disabled = false; //turn off save button
    }else{
      document.getElementById("body_modal").innerHTML  = "<p class=\"text-danger\">Update failed!</p>Return code: "+ this.responseText + "<br>Press Cancel to return";
      document.getElementById("btn_ok").disabled = true;
      document.getElementById("btn_cancel").disabled = false;
    }
  }

  const strJson = JSON.stringify(objDateRange);
  xhttp.open("POST", "shift_template_apply.php");
  xhttp.setRequestHeader("Accept", "application/json");
  xhttp.setRequestHeader("Content-Type", "application/json");
  xhttp.send(strJson);
  document.getElementById("lbl_modal").innerHTML = "Request submitted";
  document.getElementById("body_modal").innerHTML = "Waiting server response...";
  document.getElementById("btn_cancel").disabled =  document.getElementById("btn_ok").disabled = true;
}

//reload
function f_refresh(){
  location.reload();
}