let estado = $('body').find(".estado");
let cidade = $('body').find(".cidade");

if ($(estado).length > 0 && $(cidade).length > 0) {
  for (let i = 0; i < $(estado).length; i++) {
    let esta = estado[i];
    let cid = cidade[i];
    $.getJSON('/assets/js/custom_script/estados_cidades.json', function (data) {
      var items = [];
      var options = '<option value="SP">São Paulo</option>';

      $.each(data, function (key, val) {
        options += '<option value="' + val.sigla + '">' + val.nome + '</option>';
      });
      $(esta).html(options);

      $(esta).change(function () {
        var options_cidades = '';
        var str = "";

        $(esta).find('option:selected').each(function () {
          str += $(this).text();
        });

        $.each(data, function (key, val) {
          if (val.nome == str) {
            $.each(val.cidades, function (key_city, val_city) {
              options_cidades += '<option value="' + val_city + '">' + val_city + '</option>';
            });
          }
        });

        $(cid).html(options_cidades);

      }).change();

    });
  }
}