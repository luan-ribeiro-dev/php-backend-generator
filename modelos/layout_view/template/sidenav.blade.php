<?php
use Modelo\Selecao;
$selecao = Selecao::select()
  ->selectColumns(["count(id) as quant"])
  ->where("status_selecao = ?", 0)
  ->get(true, true); 
?>
<nav class="sidenav navbar navbar-vertical  fixed-left  navbar-expand-xs navbar-light bg-white" id="sidenav-main">
  <div class="scrollbar-inner">
    <!-- Brand -->
    <div class="sidenav-header  align-items-center">
      <a class="navbar-brand" href="/pages/adm/index">
        <img src="../../assets/img/brand/blue.png" class="navbar-brand-img" alt="...">
      </a>
    </div>
    <div class="navbar-inner">
      <!-- Collapse -->
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <!-- Nav items -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="/pages/adm/administrador">
              <i class="fas fa-user text-primary"></i>
              <span class="nav-link-text">Administrador</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/pages/adm/regional">
              <i class="fas fa-user text-primary"></i>
              <span class="nav-link-text">Regional</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/pages/adm/franqueado">
              <i class="fas fa-user text-primary"></i>
              <span class="nav-link-text">Franqueado</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/pages/adm/cliente">
              <i class="fas fa-user text-primary"></i>
              <span class="nav-link-text">Cliente</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/pages/adm/prestadores_analise">
              <i class="fas fa-people-carry text-primary"></i>
              <span class="nav-link-text">Prestadores em análise</span>
              @if ($selecao['quant'] > '0')
              <span class="align-top badge badge-pill badge-primary badge-xs"
                style="font-size: 10px">{{$selecao['quant']}}</span>
              @endif
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/pages/adm/contrato">
              <i class="fas fa-file text-primary"></i>
              <span class="nav-link-text">Contrato</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>