"use strict";
let menu, animate;
(function () {
    let e = document.querySelectorAll("#layout-menu");
    e.forEach(function (e) {
        menu = new Menu(e, { orientation: "vertical", closeChildren: false });
        animate = false;
        window.Helpers.scrollToActive(animate);
        window.Helpers.mainMenu = menu;
    });
    let t = document.querySelectorAll(".layout-menu-toggle");
    t.forEach((e) => {
        e.addEventListener("click", (e) => {
            e.preventDefault();
            window.Helpers.toggleCollapsed();
        });
    });
    let l = function (e, t) {
        let l = null;
        e.onmouseenter = function () {
            if (!Helpers.isSmallScreen()) {
                l = setTimeout(t, 300);
            } else {
                l = setTimeout(t, 0);
            }
        };
        e.onmouseleave = function () {
            document.querySelector(".layout-menu-toggle").classList.remove("d-block");
            clearTimeout(l);
        };
    };
    if (document.getElementById("layout-menu")) {
        l(document.getElementById("layout-menu"), function () {
            if (!Helpers.isSmallScreen()) {
                document.querySelector(".layout-menu-toggle").classList.add("d-block");
            }
        });
    }
    let n = document.getElementsByClassName("menu-inner"),
        o = document.getElementsByClassName("menu-inner-shadow")[0];
    if (n.length > 0 && o) {
        n[0].addEventListener("ps-scroll-y", function () {
            if (this.querySelector(".ps__thumb-y").offsetTop) {
                o.style.display = "block";
            } else {
                o.style.display = "none";
            }
        });
    }
    const s = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    s.map(function (e) {
        return new bootstrap.Tooltip(e);
    });
    window.Helpers.setAutoUpdate(true);
    window.Helpers.initPasswordToggle();
    window.Helpers.initSpeechToText();
    if (window.Helpers.isSmallScreen()) {
        return;
    }
    window.Helpers.setCollapsed(true, false);
})();

$(document).ready(function(){ 
	$("#addAdmindetails").click(function () {
		var username = $('#userName').attr('data-id')
		var password = $('#passwords').attr('data-id')
		$('#email').val(username);
		$('#password').val(password);
	});
});
