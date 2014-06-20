sfHover = function() {
    var sfEls = document.getElementById("nav").getElementsByTagName("LI");
    for (var i=0; i<sfEls.length; i++) {
        addEvent(sfEls[i], "mouseover", function() {
            this.className+=" sfhover";
        });
        addEvent(sfEls[i], "mouseout", function() {
            this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
        });
    }
}

function addEvent(el, name, func) {
    if (el.attachEvent)
        el.attachEvent("on" + name, func);
    else
        el.addEventListener(name, func, false);
}

addEvent(window, "load", sfHover);