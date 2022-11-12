const objGlobal = {
    c_setup: "", 
    c_subsetup: "", 
    c_value: ""
  };
  
const arrayObj = [];
  
window.addEventListener("DOMContentLoaded", function() {
  modal_Popup = new bootstrap.Modal(document.getElementById("modal_box"));
}, false);
  
//add new notice item
//para: nType - 'wa' for WhatsApp, 'mail' for Email
function f_add_notice(nType) {
    iptBox = document.getElementById("iptBox_"+nType);
    inputValue = iptBox.value;
    if (nType == 'wa') {
        pattern = /\x2b65\d{8}\x2e\d{6}$/; //start with +65 with 8 mobile number, and '.' followed by 6 digits pin code
    }else{
        pattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/; //email regex 
    }
    if (pattern.test(inputValue)) {
        noticeList = document.getElementById("ul_"+nType);
        newIdx = (noticeList.childElementCount + 1).toString();
        newListItem = document.createElement("li");
        newListItem.setAttribute('class','list-group-item list-group-item-secondary mx-auto mb-1 col-10');
        newListItem.setAttribute('id','li_'+nType+'_'+newIdx);
        newListItem.innerText = inputValue;
        newButton = document.createElement("button");
        newButton.setAttribute('class','mx-auto mb-1 btn btn-danger col-1');
        newButton.setAttribute('id','btn_'+nType+'_'+newIdx);
        newButton.setAttribute('type','button');
        newButton.setAttribute('onclick',"f_remove_notice('"+nType+"','"+newIdx+"')");
        newButton.innerText = "X";
        newRow = document.createElement("div");
        newRow.setAttribute('class','row');
        newRow.setAttribute('id','row_'+nType+'_'+newIdx);
        newRow.appendChild(newListItem);
        newRow.appendChild(newButton);
        noticeList.appendChild(newRow);
        iptBox.value = "";
    }else{
        if (nType == 'wa') {
            alert("Wrong WhatsApp format. Use +65<8 digits phone num>.<6 digits code>");
        }else{
            alert("Wrong Email format!")
        }
    }
}

//remove notice item when X button is clicked
//para: nType - 'wa' or 'mail'
function f_remove_notice(nType, idx) {
    noticeList = document.getElementById("ul_"+nType);
    rowToRemove = document.getElementById("row_"+nType+"_"+idx);
    noticeList.removeChild(rowToRemove);
}

//cancel button
function f_refresh() {
    location.reload()
}

//ok button. Read changed data and confirm to submit
function f_toConfirm() {
    class ClassToSubmit {
        constructor (c_setup, c_subsetup, c_value){
        this.c_setup = c_setup;
        this.c_subsetup = c_subsetup;
        this.c_value = c_value;
        }
    }
    idxArray = 0;
    listCount = document.getElementsByName("max_ppl_store").length;
    c_setup = "max_ppl";
    for (idxList = 0; idxList < listCount; idxList++){
        strIdx = idxList.toString();
        c_subsetup = document.getElementById("max_ppl_store"+strIdx).innerText;
        c_value = document.getElementById("max_ppl_value"+strIdx).value;
        arrayObj[idxArray] = new ClassToSubmit(c_setup,c_subsetup,c_value);
        idxArray++;
    }

    listGroup = document.getElementById("ul_wa");
    c_setup = "notice_stocking";
    c_subsetup = "WA";
    listChild = listGroup.firstElementChild; //must be ElementChild. FirstChild may be text, not list item
    while (listChild != null) {
        c_value = listChild.firstElementChild.innerText; //Each list item is a row with one list value and one X button. list value is first child of the row
        arrayObj[idxArray] = new ClassToSubmit(c_setup,c_subsetup,c_value);
        idxArray++;
        listChild = listChild.nextElementSibling;
    }

    listGroup = document.getElementById("ul_mail");
    c_subsetup = "mail";
    listChild = listGroup.firstElementChild; //must be ElementChild. FirstChild may be text, not list item
    while (listChild != null) {
        c_value = listChild.firstElementChild.innerText; //Each list item is a row with one list value and one X button. list value is first child of the row
        arrayObj[idxArray] = new ClassToSubmit(c_setup,c_subsetup,c_value);
        idxArray++;
        listChild = listChild.nextElementSibling;
    }

    strDisplay = '<table class="table"><thead><tr><th scope="col">Setup</th><th scope="col">SubSetup</th><th scope="col">Value</th></tr></thead><tbody>';
    for (idxArray=0;idxArray < arrayObj.length; idxArray++) {
        strDisplay = strDisplay + '<tr>';
        strDisplay = strDisplay + '<td>' + arrayObj[idxArray].c_setup + '</td>';
        strDisplay = strDisplay + '<td>' + arrayObj[idxArray].c_subsetup + '</td>';
        strDisplay = strDisplay + '<td>' + arrayObj[idxArray].c_value + '</td>';
        strDisplay = strDisplay + '</tr>';
    }
    strDisplay = strDisplay + '</tbody></table>';

    document.getElementById("body_modal").innerHTML = "Confirm to change system configurations?<br><br>" + strDisplay;
    document.getElementById("btn_cancel").style.visibility = "visible";
    document.getElementById("btn_ok").style.visibility = "visible";
    modal_Popup.show();
}

//submit data change
function f_submit() {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (this.responseText == "true") {
        document.getElementById("body_modal").innerHTML = "Submit successfully!<br>Press OK to return";
        document.getElementById("btn_ok").style.visibility = "visible";
        document.getElementById("btn_ok").onclick = f_refresh;
        }else{
        document.getElementById("body_modal").innerHTML = "<p class=\"text-danger\">Update failed!</p>Return code: "+ this.responseText + "<br>Press Cancel to return";
        document.getElementById("btn_cancel").style.visibility = "visible";
        // modal_Popup.hide();
        }
    }
    const strJson = JSON.stringify(arrayObj);
    xhttp.open("POST", "admin_config_update.php");
    xhttp.setRequestHeader("Accept", "application/json");
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(strJson);
    document.getElementById("lbl_modal").innerHTML = "Request submitted";
    document.getElementById("body_modal").innerHTML = "Waiting server response...";
    document.getElementById("btn_cancel").style.visibility = "hidden";
    document.getElementById("btn_ok").style.visibility = "hidden";
}//f_submit
