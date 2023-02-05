
// demo-2
new Lightpick({
  field: document.getElementById('demo-1'),
  secondField: document.getElementById('demo-2'),
  onSelect: function(start, end){
      document.getElementById('result-2').innerHTML = start.format('Do MMMM YYYY') + ' to ' + end.format('Do MMMM YYYY') + " for " + end.diff(start,'days') + " days";
  }
});