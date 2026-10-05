$(document).ready(function(){
    $("#resetDynamicColor").click(function (e) {
        e.preventDefault();
        if ($("#addEditForm").valid()) {
            $("#reset_default_value").val("Yes");
            $("#addEditForm").trigger("submit");
        } else {
            return false;
        }
    });
    
    

    $('input[name="background_type"]').change(function () {
        let selectedValue = $(this).val();
        if (selectedValue === "Color") {
            $('.background_pattern').hide();
            $('.background_color').show();
        }else{
            $('.background_pattern').show();
            $('.background_color').hide();
        }
    });
    $('input[name="background_type"]:checked').change();

});


// Background Color Changes :
const gradientBox = document.querySelector('.gradient-color-box'),
      selectMenu = document.querySelector('.select-box'),
      colorInputs = document.querySelectorAll(".colors input[type='color']"),
      opacityInputs = document.querySelectorAll(".colors input[type='range']"),
      refreshBtn = document.querySelector('.refresh'),
      saveBtn = document.querySelector('.save'),
      gradientInput = document.getElementById('background_color'),
      gradientModal = new bootstrap.Modal(document.getElementById('gradientModal'));
const getRandomColor = () => `#${Math.floor(Math.random() * 0xffffff).toString(16).padStart(6, '0')}`;
const hexToRgba = (hex, opacity) => `rgba(${parseInt(hex.slice(1, 3), 16)}, ${parseInt(hex.slice(3, 5), 16)}, ${parseInt(hex.slice(5, 7), 16)}, ${opacity})`;
const generateBackground = (isRandom = false) => {
    if (isRandom) {
        [colorInputs[0].value, colorInputs[1].value] = [getRandomColor(), getRandomColor()];
        [opacityInputs[0].value, opacityInputs[1].value] = [Math.random().toFixed(2), Math.random().toFixed(2)];
    }
    const [color1, color2] = [hexToRgba(colorInputs[0].value, opacityInputs[0].value), hexToRgba(colorInputs[1].value, opacityInputs[1].value)];
    gradientBox.style.background = `linear-gradient(${selectMenu.value}, ${color1} 0%, ${color2} 100%)`;
};
gradientInput.addEventListener('click', () => gradientModal.show());
refreshBtn.addEventListener('click', () => generateBackground(true));
colorInputs.forEach(input => input.addEventListener('input', () => generateBackground(false)));
opacityInputs.forEach(input => input.addEventListener('input', () => generateBackground(false)));
selectMenu.addEventListener('change', () => generateBackground(false));
saveBtn.addEventListener('click', () => {
    gradientInput.value = `background: ${gradientBox.style.background}`;
    gradientModal.hide();
});