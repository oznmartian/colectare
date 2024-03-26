function SelectByCod() {
  // cautare cod in formular ABG
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SelectByCod");
  filter = input.value.toUpperCase();
  table = document.getElementById("TableABG");
  tr = table.getElementsByTagName("tr");

  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[3];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }

}



function SelectByCategory() {
  //selectare categorie in Formular ABG

  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SelectByCategory");
  filter = input.value.toUpperCase();
  table = document.getElementById("TableABG");
  tr = table.getElementsByTagName("tr");

  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];
    if (td) {
      txtValue = td.innerHTML || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function SelectByBrand() {
  //selectare  brand in Formular ABG

  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("SelectByBrand");
  filter = input.value.toUpperCase();
  table = document.getElementById("TableABG");
  tr = table.getElementsByTagName("tr");

  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function selectBrand() {

  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Brand");
  filter = input.value.toUpperCase();
  table = document.getElementById("finalTable");
  tr = table.getElementsByTagName("tr");

  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[3];
    if (td) {

      txtValue = td.textContent || td.innerText; 
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function selectCategorie() {
  
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("Categorie");
  filter = input.value.toUpperCase();
  table = document.getElementById("finalTable");
  tr = table.getElementsByTagName("tr");
  console.log(tr);

  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function selectUser() {
  
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("User");
  filter = input.value.toUpperCase();
  table = document.getElementById("finalTable");
  tr = table.getElementsByTagName("tr");
  console.log(tr);

  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[9];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

function selectMagazin() {
  
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("selectMagazin");
  filter = input.value.toUpperCase();
  table = document.getElementById("finalTable");
  tr = table.getElementsByTagName("tr");

  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    console.log(td);
    if (td) {
     
      txtValue = td.innerHTML || td.innerText;

      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}


//export in Excel
function ExportToExcel(type, fn, dl) {
  var elt = document.getElementById("finalTable");
  var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
  return dl
    ? XLSX.write(wb, { bookType: type, bookSST: true, type: "base64" })
    : XLSX.writeFile(wb, fn || "MySheetName." + (type || "xlsx"));
}

//stores items in the localStorage
function store() {
  var cantitate;
  cantitate = document.getElementsByClassName("classCant");
 

  for (let i = 0; i < cantitate.length; i++) {
    
    cantitate[i].value = localStorage.getItem("cantitate" + i);


    cantitate[i].addEventListener("change", (event) => {
      localStorage.setItem("cantitate" + i, cantitate[i].value);
    });
  }
}
//Butoane +/- cu salvare in local storage
function clickCounter() {
  let btnAdd = document.getElementsByClassName("btnAdd");
  let btnSub = document.getElementsByClassName("btnSub");
  let cantInit = document.getElementsByClassName("classCant");

  for (let i = 0; i < cantInit.length; i++) {
   

    cantInit[i].value = localStorage.getItem("cantitate" + i);

    btnAdd[i].addEventListener("click", (event) => {
      cantInit[i].value = Number(cantInit[i].value) + 1;
     
      document.getElementsByClassName("classCant").innerHTML =
        cantInit[i].value;
      localStorage.setItem("cantitate" + i, cantInit[i].value);
    });
    btnSub[i].addEventListener("click", (event) => {
      cantInit[i].value = Number(cantInit[i].value) - 1;
      if(cantInit[i].value <=0){ cantInit[i].value = 0};
    
      document.getElementsByClassName("classCant").innerHTML =
        cantInit[i].value;
      localStorage.setItem("cantitate" + i, cantInit[i].value);
    });
  }
}

// activarea functiiolor de salvare in local storage dupa ce s-a incarcat pagina
window.onload = function () {
  document.querySelectorAll.onchange = store();

  document.querySelectorAll.onclick = clickCounter();
};


function submitForm() {
  return confirm("Esti sigur ca vrei sa trimiti datele?");
}

function stergereLocalStorage() {
if(!confirm("Esti sigur ca vrei stergi datele?")){
  return false;
}
  window.localStorage.clear();
}

//dezactivare tasta enter
window.addEventListener(
  "keydown",
  function (e) {
    if (
      e.keyIdentifier == "U+000A" ||
      e.keyIdentifier == "Enter" ||
      e.keyCode == 13
    ) {
      if (
        (e.target.nodeName == "INPUT" && e.target.type == "number") ||
        e.target.type == "text"
      ) {
        e.preventDefault();

        return false;
      }
    }
  },
  true
);
       






