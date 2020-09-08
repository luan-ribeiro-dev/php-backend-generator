$(".datatable").each((index, element) => {
  let $element = $(element);
  if ($element.data("filter")) {
    $element.DataTable({
      order: $element.data("filter"),
      language: {
        url:
          "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json",
      },
    });
  } else {
    $element.DataTable({
      language: {
        url:
          "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json",
      },
    });
  }
});
$(".datatable");
