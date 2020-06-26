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
  if(!value) return 0;
  if (value.includes("R$") || value.includes(","))
    return parseFloat(
      value
        .replace(/\./g, "")
        .replace(",", ".")
        .replace(/ /g, "")
        .replace("R$", "")
    );
  else return "R$ " + parseFloat(value).toLocaleString("pt-BR", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}
