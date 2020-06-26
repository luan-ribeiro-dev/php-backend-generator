<?php 

use \Modelo\Usuario;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  @include('layout.adm.head')
  
  @yield('head-section')
</head>

<body>
  @include('layout.adm.sidenav')

  @include('layout.adm.content')

  @include('layout.adm.foot')

  @yield('script-end')
</body>

</html>