$('.cep').mask('99999-999');
function checkStates(element) {
	return new Promise((aceitar, rejeitar) => {
		$.getJSON("https://viacep.com.br/ws/" + $(element).val() + "/json/?callback=?", function (data) {
      let form = $(element).parents('form').first();
      let estado = $(form).find('.estado');
      let cidade = $(form).find('.cidade');
      let bairro = $(form).find('.bairro');
      let rua = $(form).find('.rua');
			if (!("erro" in data)) {
        console.log(data);
				$(estado).val(data.uf).change();
				$(cidade).val(data.localidade).change();
				$(bairro).val(data.bairro);
				$(rua).val(data.logradouro);
				aceitar(true);
			} else {
				alert("CEP não encontrado.");
				$(estado).val("SP").change();
				$(cidade).val(null).change();
				$(bairro).val(null);
				$(rua).val(null);
				aceitar(false);
			}
		});
	})
}

$('.cep').keyup((event) => {
	let element = event.currentTarget;
	if ($(element).val().length == 9 && (event.keyCode < 37 || event.keyCode > 40) && event.keyCode != 27 && event.keyCode != 13) {
		$("#rua").attr('readonly', true);
		$("#bairro").attr('readonly', true);
		checkStates(element).then(isCepValid => {
			if (isCepValid) {
				$("#numero_casa").focus();
			} else {
				$("#cep").focus();
			}
		});
		$("#rua").attr('readonly', false);
		$("#bairro").attr('readonly', false);
	}
});
if ($('.cep').length > 0 && $('.cep').val() != '') checkStates();
