<div class="main-content" style="height: 100vh;" id="panel">
  <!-- Topnav -->
  <nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom">
    <div class="container-fluid">
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Navbar links -->
        <ul class="navbar-nav align-items-center  ml-md-auto ">
          <li class="nav-item d-xl-none">
            <!-- Sidenav toggler -->
            <div class="pr-3 sidenav-toggler sidenav-toggler-dark" data-action="sidenav-pin"
              data-target="#sidenav-main">
              <div class="sidenav-toggler-inner">
                <i class="sidenav-toggler-line"></i>
                <i class="sidenav-toggler-line"></i>
                <i class="sidenav-toggler-line"></i>
              </div>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
              aria-expanded="false">
              <i class="ni ni-bell-55"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-xl  dropdown-menu-right  py-0 overflow-hidden">
              <!-- Dropdown header -->
              <div class="px-3 py-3">
                <h6 class="text-sm text-muted m-0">Você não tem <strong class="text-primary"><?php ?></strong> notificações.
                </h6>
              </div>
              <!-- List group
              <div class="list-group list-group-flush">
                <a href="#!" class="list-group-item list-group-item-action">
                  <div class="row align-items-center">
                    <div class="col ml--2">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h4 class="mb-0 text-sm">John Snow</h4>
                        </div>
                        <div class="text-right text-muted">
                          <small>2 hrs ago</small>
                        </div>
                      </div>
                      <p class="text-sm mb-0">Let's meet at Starbucks at 11:30. Wdyt?</p>
                    </div>
                  </div>
                </a>
              </div>
              View all -->
              <a href="#!" class="dropdown-item text-center text-primary font-weight-bold py-3">Ver tudo</a>
            </div>
          </li>
        </ul>
        <ul class="navbar-nav align-items-center  ml-auto ml-md-0 ">
          <li class="nav-item dropdown">
            <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
              aria-expanded="false">
              <div class="media align-items-center">
                <div class="media-body  ml-2  d-none d-lg-block">
                  <span class="mb-0 text-sm font-weight-bold">
                    {{$logged_user->getNome()}}
                    <i class="fas fa-sort-down"></i></span>
                </div>
              </div>
            </a>
            <div class="dropdown-menu  dropdown-menu-right ">
              <a href="profile" class="dropdown-item">
                <i class="ni ni-single-02"></i>
                <span>Perfil</span>
              </a>
              <a href="alterar_senha" class="dropdown-item">
                <i class="ni ni-settings-gear-65"></i>
                <span>Alterar senha</span>
              </a>
              <div class="dropdown-divider"></div>
              <a href="../../function/sair" class="dropdown-item">
                <i class="ni ni-user-run"></i>
                <span>Sair</span>
              </a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Header -->
  <div class="header bg-primary pb-6">
    <div class="container-fluid">
      <div class="header-body">
        <div class="row align-items-center py-4">
          <div class="col-lg-6 col-7">
            <h6 class="h2 text-white d-inline-block mb-0">@yield('dashboard-title')</h6>
          </div>
          @yield('header-after')
        </div>
      </div>
    </div>
  </div>

  <!-- Page content -->
  <div class="container-fluid mt--6">
    @if(!isset($no_content))
    <div class="row">
      <div class="col-xl-12">
        <div class="card bg-neutral">
          <div class="card-header bg-transparent">
            <div class="row align-items-center">
              <div class="col">
                <h3 class="h3 mb-0">@yield('card-header-title')</h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            @if(isset($_SESSION['cadastro_erro']))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <span class="alert-inner--icon"><i class="ni ni-support-16"></i></span>
              <span class="alert-inner--text"><?php echo $_SESSION['cadastro_erro']; ?></span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php unset($_SESSION['cadastro_erro']); ?>
            @endif
            @if (isset($_SESSION['cadastro_ok']))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <span class="alert-inner--icon"><i class="ni ni-like-2"></i></span>
              <span class="alert-inner--text"><?php echo $_SESSION['cadastro_ok']; ?></span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php unset($_SESSION['cadastro_ok']); ?>
            @endif
            @yield('content')
          </div>
        </div>
      </div>
    </div>
    @endif

    @yield('foot-content')

    <!-- Footer -->
    @include('layout.adm.footer')
  </div>

</div>