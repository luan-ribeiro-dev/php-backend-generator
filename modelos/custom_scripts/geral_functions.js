function format_date(date) {
  let dia, mes, ano;
  if (date.includes("-")) {
    let d = date.split("-");
    dia = d[2];
    mes = d[1];
    ano = d[0];
    return [dia, mes, ano].join("/");
  } else if (date.includes("/")) {
    let d = date.split("-");
    dia = d[0];
    mes = d[1];
    ano = d[2];
    return [ano, mes, dia].join("-");
  } else {
    return date;
  }
}

function is_valid_date(d) {
  try {
    d = new Date(d);
    return (
      d instanceof Date &&
      !isNaN(d) &&
      d.getFullYear() > 1940 &&
      d.getFullYear() < 2040
    );
  } catch (error) {
    return false;
  }
}

function validarCPF(cpf) {
  cpf = cpf.replace(/[^\d]+/g, "");
  if (cpf == "") return false;
  // Elimina CPFs invalidos conhecidos
  if (
    cpf.length != 11 ||
    cpf == "00000000000" ||
    cpf == "11111111111" ||
    cpf == "22222222222" ||
    cpf == "33333333333" ||
    cpf == "44444444444" ||
    cpf == "55555555555" ||
    cpf == "66666666666" ||
    cpf == "77777777777" ||
    cpf == "88888888888" ||
    cpf == "99999999999"
  )
    return false;
  // Valida 1o digito
  add = 0;
  for (i = 0; i < 9; i++) add += parseInt(cpf.charAt(i)) * (10 - i);
  rev = 11 - (add % 11);
  if (rev == 10 || rev == 11) rev = 0;
  if (rev != parseInt(cpf.charAt(9))) return false;
  // Valida 2o digito
  add = 0;
  for (i = 0; i < 10; i++) add += parseInt(cpf.charAt(i)) * (11 - i);
  rev = 11 - (add % 11);
  if (rev == 10 || rev == 11) rev = 0;
  if (rev != parseInt(cpf.charAt(10))) return false;
  return true;
}

function format_value(value) {
  value = value.toString();
  if (!value) return 0;
  if (value.includes("R$") || value.includes(","))
    return parseFloat(
      value
        .replace(/\./g, "")
        .replace(",", ".")
        .replace(/ /g, "")
        .replace("R$", "")
    );
  else
    return (
      "R$" +
      parseFloat(value).toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
}

function brl_to_float(value) {
  if (!value) return 0;
  value = value.toString();
  return parseFloat(
    value
      .replace(/\./g, "")
      .replace(",", ".")
      .replace(/ /g, "")
      .replace("R$", "")
  );
}

function float_to_brl(value, is_cifrao) {
  if (!value) return (is_cifrao ? "R$" : "") + "0,00";
  return (
    (is_cifrao ? "R$" : "") +
    value.toLocaleString("pt-BR", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })
  );
}

function addZero(value) {
  value = parseInt(value);
  if (value < 10) return "0" + value;
  else return value;
}

function set_url_args(args) {
  // Construct URLSearchParams object instance from current URL querystring.
  var queryParams = new URLSearchParams(window.location.search);

  args.forEach((arg) => {
    queryParams.set(arg.name, arg.value);
  });

  // Replace current querystring with the new one.
  history.replaceState(null, null, "?" + queryParams.toString());
}

function get_location() {
	return new Promise((res, rej)=>{
		navigator.geolocation.getCurrentPosition(pos=>{
			res(pos)
		},() => rej("Não foi possível pegar a posição do usuário"));
	})
}

String.prototype.capitalize = function () {
  return this.charAt(0).toUpperCase() + this.slice(1);
};

Date.prototype.getDateString = function () {
  let month = this.getUTCMonth() + 1; //months from 1-12
  let day = this.getUTCDate();
  let year = this.getUTCFullYear();
  return year + "-" + addZero(month) + "-" + addZero(day);
};

Date.prototype.getTimeString = function () {
  return addZero(this.getHours()) + ":" + addZero(this.getMinutes());
};

Date.prototype.format = function (format) {
  if (format.includes("Y")) format = format.replace("Y", this.getUTCFullYear());
  if (format.includes("m"))
    format = format.replace("m", addZero(this.getUTCMonth() + 1));
  if (format.includes("d"))
    format = format.replace("d", addZero(this.getUTCDate()));

  if (format.includes("H"))
    format = format.replace("H", addZero(this.getHours()));
  if (format.includes("i"))
    format = format.replace("i", addZero(this.getMinutes()));
  if (format.includes("s"))
    format = format.replace("i", addZero(this.getSeconds()));
  return format;
};

Number.prototype.to_brl = function (is_cifrao = true) {
  return float_to_brl(this, is_cifrao);
};

String.prototype.to_float = function () {
  return brl_to_float(this);
};
