window.addEventListener("DOMContentLoaded", function() {
  //when clicking on nav bar or ADMIN drop down, don't collapse
   const $navbarNav = document.querySelector("#navbarToggler");
   const $admindropdown = document.querySelector("#admindropdown");
   const $stockdropdown = document.querySelector("#stockdropdown");
   if ($navbarNav) {
     const navbarNavCollapse = (event) => {
       if (!(($navbarNav == event.target) || ($admindropdown == event.target) || ($stockdropdown == event.target))) {
         $navbarNav.setAttribute("class","collapse navbar-collapse");
         document.removeEventListener("mouseup", navbarNavCollapse);
       }
     }

     $navbarNav.addEventListener("shown.bs.collapse", () => {
       document.addEventListener("mouseup", navbarNavCollapse);
     });
   }
}, false);
