"use strict";

$(".cep").mask("99999-999");

function search_cep(element) {
  let $form = $(element).parents("form").first();
  let $cep = $form.find('input[name="cep"]');
  let $rua = $form.find('input[name="rua"]');
  let $bairro = $form.find('input[name="bairro"]');
  let $numero = $form.find('input[name="numero"]');
  $rua.attr("readonly", true);
  $bairro.attr("readonly", true);
  checkStates(element)
    .then((isCepValid) => {
      $numero.focus();
      $cep.removeClass("is-invalid");
    })
    .catch((error) => {
      $cep.focus();
      $cep.addClass("is-invalid");
      $cep.siblings(".invalid-feedback").html("<li>CEP inválido</li>");
    });
  $rua.attr("readonly", false);
  $bairro.attr("readonly", false);
}

function checkStates(element) {
  return new Promise((aceitar, rejeitar) => {
    let form = $(element).parents("form").first();
    let estado = $(form).find(".estado");
    let cidade = $(form).find(".cidade");
    let bairro = $(form).find(".bairro");
    let rua = $(form).find(".rua");
    $.ajax({
      url: "https://viacep.com.br/ws/" + $(element).val() + "/json/?callback=?",
      dataType: "json",
      success: (data) => {
        if (!("erro" in data)) {
          $(estado).val(data.uf).change();
          $(cidade).val(data.localidade).change();
          $(bairro).val(data.bairro);
          $(rua).val(data.logradouro);
          aceitar(true);
        } else {
          $(estado).val("SP").change();
          $(cidade).val(null).change();
          $(bairro).val(null);
          $(rua).val(null);
          rejeitar("CEP não encontrado");
        }
      },
      error: (error) => {
        $(estado).val("SP").change();
        $(cidade).val(null).change();
        $(bairro).val(null);
        $(rua).val(null);
        rejeitar("CEP não encontrado");
      },
    });
  });
}

$(".reload-cep").click((event) => {
  search_cep($(event.currentTarget).siblings(".cep"));
});

$(".cep").keyup((event) => {
  let element = event.currentTarget;
  if (
    $(element).val().length == 9 &&
    (event.keyCode < 37 || event.keyCode > 40) &&
    event.keyCode != 27 &&
    event.keyCode != 13
  ) {
    search_cep(element);
  }
});
if ($(".cep").length > 0 && $(".cep").val() != "") checkStates();
