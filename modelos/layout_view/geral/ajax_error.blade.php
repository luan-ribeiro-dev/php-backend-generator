if (error.code != undefined && error.code === {{\Controle\Geral::VALIDATION_ERROR_CODE}}) {
  let first_field = null;
  @if(isset($validation_message) && $validation_message != null)
    alert("{!!$validation_message!!}");
  @endif
  $(form).find("input, select").addClass("is-valid");
  $(form).find("input, select").removeClass("is-invalid");
  Object.keys(error.errors).forEach((key) => {
    let li = "";
    error.errors[key].forEach((e) => {
      li += "<li>" + e + "</li>";
    });
    $(form)
    .find('input[name="' + key + '"]')
    .siblings(".invalid-feedback")
    .html(li);
    $(form)
      .find('input[name="' + key + '"]')
      .addClass("is-invalid");
    $(form)
      .find('input[name="' + key + '"]')
      .removeClass("is-valid");
    if (first_field == null) first_field = 'input[name="' + key + '"]';
  });
  $(form).find(first_field).focus();
} else if (error.code === {{\Controle\Geral::UNKNOWN_ERROR_CODE}}) {
  alert(error.message);
  $(form).find("input, select").removeClass("is-valid");
  $(form).find("input, select").removeClass("is-invalid");
}
