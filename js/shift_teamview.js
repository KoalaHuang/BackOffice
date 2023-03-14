const objGlobal = {
  name: "",
  id: "",
  year: 0, //current year
  mon: 0, //current month
  mday: 0, //current day of month
  wd: 0 //week day
};

window.addEventListener("DOMContentLoaded", function() {
  const elmIptDate = document.getElementById("iptDate");
  objGlobal.year = Number(elmIptDate.getAttribute("data-stocking-year"));
  objGlobal.mon = Number(elmIptDate.getAttribute("data-stocking-mon"));
  
  elmUser =  document.getElementById('txtUserName');
  objGlobal.name = elmUser.innerText;
  objGlobal.id = elmUser.getAttribute("data-stocking-userid");
}, false);

//cancel button
function f_refresh() {
  location.reload();
}

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

function f_nextMon() {
  if (objGlobal.mon == 12) {
    newYear = objGlobal.year + 1;
    newMon = 1;
  }else{
    newYear = objGlobal.year;
    newMon = objGlobal.mon + 1;
  }
  window.location.href = "shift_teamview.php?year=" + newYear.toString() + "&mon=" + newMon.toString();
}

function f_lastMon() {
  if (objGlobal.mon == 1) {
    newYear = objGlobal.year - 1;
    newMon = 12;
  }else{
    newYear = objGlobal.year;
    newMon = objGlobal.mon - 1;
  }
  window.location.href = "shift_teamview.php?year=" + newYear.toString() + "&mon=" + newMon.toString();
}