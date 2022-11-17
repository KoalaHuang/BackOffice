function f_changeMonth(intStep){
    fromYear = Number(document.getElementById("sltFromYear").value);
    fromMon = Number(document.getElementById("sltFromMon").value);
    fromMon = fromMon + intStep;
    if (fromMon == 0){
        fromMon = 12
        fromYear--;
    }else{
        if (fromMon == 13){
            fromMon = 1;
            fromYear++;
        }
    }
    if (fromMon == 12){
        toDay = 31;
    }else{
        objtoDay = new Date(fromYear, fromMon, 0); //get last day of the month. JS count month from 0.
        toDay = objtoDay.getDate();
    }
    document.getElementById("sltFromYear").value = fromYear;
    document.getElementById("sltFromMon").value = fromMon;
    document.getElementById("sltFromDay").value = 1;
    document.getElementById("sltToYear").value = fromYear;
    document.getElementById("sltToMon").value = fromMon;
    document.getElementById("sltToDay").value = toDay;
}