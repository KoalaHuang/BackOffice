function f_add_notice_wa() {
  iptBox_wa = document.getElementById("input_notice_wa");
  input_wa = iptBox_wa.value;
  const pattern = /\x2b65\d{8}\x2e\d{6}$/; //start with +65 with 8 mobile number, and '.' followed by 6 digits pin code
  if (pattern.test(input_wa)) {
    wa_list = document.getElementById("ul_wa");
    newIdx = (wa_list.childElementCount + 1).toString();
    newListItem = document.createElement("li");
    newListItem.setAttribute('class','list-group-item list-group-item-secondary me-1 mb-1 col-10');
    newListItem.setAttribute('id','li_wa_'+newIdx);
    newListItem.innerText = input_wa;
    newButton = document.createElement("button");
    newButton.setAttribute('class','mb-1 btn btn-danger col-1');
    newButton.setAttribute('id','btn_wa_'+newIdx);
    newButton.setAttribute('type','button');
    newButton.setAttribute('onclick','f_remove_notice_wa('+newIdx+')');
    newButton.innerText = "X";
    newRow = document.createElement("div");
    newRow.setAttribute('class','row');
    newRow.setAttribute('id','wa_row_'+newIdx);
    newRow.appendChild(newListItem);
    newRow.appendChild(newButton);
    document.getElementById("ul_wa").appendChild(newRow);
    iptBox_wa.value = "";
  }else{
    alert("WhatsApp notice number format: +65<8 digits phone num>.<6 digits code>");
  }
}

function f_remove_notice_wa(idx) {
  list_wa = document.getElementById("ul_wa");
  //listItemToRemove = document.getElementById("li_wa_"+idx.toString());
  //buttonToRemove = document.getElementById("btn_wa_"+idx.toString());
  rowToRemove = document.getElementById("wa_row_"+idx.toString());
  list_wa.removeChild(rowToRemove);
}