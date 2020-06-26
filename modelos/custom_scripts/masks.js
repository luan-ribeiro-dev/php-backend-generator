"use strict";

function changeTelMask(element) {
  if (element != null && $(element).length) {
    let masks = ["(00) 0000-00000", "(00) 00000-0000"];
    let mask = $(element).val().length > 14 ? masks[1] : masks[0];
    $(element).mask(mask, telOptions);
  }
}

let telOptions = {
  onKeyPress: function (phone, e, field, options) {
    changeTelMask(e.target);
  },
};

$(".tel").mask("(00) 0000-00000", telOptions);
$(".tel").each((index, element) => {
  changeTelMask(element);
});

$(".cnpj").mask("00.000.000/0000-00");
$(".cpf").mask("000.000.000-00");

$(".brl").mask('#.##0,00', {
  reverse: true,
  onKeyPress: function(val, e, field, options) {
    if (val.replace(/\./g, '').replace(',', '.') > 100000) {
      $(e.currentTarget).val("100.000,00");
    }
  }
});
