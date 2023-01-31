//js for leave.php
new Lightpick({
  field: document.getElementById('iptDates'),
  singleDate: false,
  onSelect: function(start, end){
    strStart = start ? start.format('Do MMMM YYYY') : "...";
    strEnd = end ? end.format('Do MMMM YYYY') : "..."
      document.getElementById('section_home').innerHTML =  strStart + ' to ' + strEnd;
  }
});