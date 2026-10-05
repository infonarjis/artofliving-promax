// text area size increase for chat 
const textarea = document.getElementById("textareaincrease");

function setHeight(elem) {
  const style = getComputedStyle(elem, null);
  const verticalBorders = Math.round(parseFloat(style.borderTopWidth) + parseFloat(style.borderBottomWidth));
  const maxHeight = parseFloat(style.maxHeight) || 75;
  
  elem.style.height = "9px";

  const newHeight = elem.scrollHeight + verticalBorders;
  
  elem.style.overflowY = newHeight > maxHeight ? "auto" : "hidden";
  elem.style.height = Math.min(newHeight, maxHeight) + "px";
}

textarea.addEventListener("input", (e) => {
  setHeight(e.target);
});

setHeight(textarea);