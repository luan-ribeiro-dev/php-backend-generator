<!DOCTYPE html>
<html lang="pt-br">

<head>
  @include('layout.template.head')
  
  @yield('head-section')
</head>

<body>
  @include('layout.template.sidenav')

  @include('layout.template.content')

  @include('layout.template.foot')

  @yield('script-end')
</body>

</html>