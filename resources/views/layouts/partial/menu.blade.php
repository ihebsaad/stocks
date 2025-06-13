  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo
    <a href="{{route('home')}}" class="brand-link">
      <img src="{{ asset('img/logo.png')}}" alt="Logo" class="brand-image img-circle elevation-3 bg-white" style="opacity: .8">
      <span class="brand-text font-weight-light">{{env('APP_NAME')}}</span>
    </a>
-->
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          @if(Auth::user()->thumb!='')
          <a href="{{route('home')}}"><img src="<?php echo URL::asset('img/users/' . Auth::user()->thumb); ?>" width="160" class="img-circle elevation-2" alt="User Image"></a>
          @else
          <a href="{{route('home')}}"><img src="{{ asset('img/users/user.png')}}" width="160" class="img-circle elevation-2" alt="User Image"></a>
          @endif
        </div>
        <div class="info ml-3">
          <a href="{{route('profile')}}" class="d-block"> {{ Auth::user()->name}} {{ Auth::user()->lastname }}</a>
        </div>
      </div>

      <!-- SidebarSearch Form
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
-->
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- 
          <li class="nav-item menu-open">
            <a href="#" class="nav-link {{ request()->is('invoices') || request()->is('quotes') || request()->is('invoices/*') || request()->is('quotes/*') || request()->is('livraisons/*')   ? 'active' : '' }}">
              <i class="nav-icon fas fa-file text-white"></i>
              <p>
                Commandes
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="#" class="nav-link {{ request()->is('quotes.index') ||  request()->is('quotes')  ? 'active' : '' }}">
                  <i class="fas fa-file-invoice nav-icon text-secondary"></i>
                  <p>Devis</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link {{ request()->is('invoices') ||  request()->is('invoices/*') ? 'active' : '' }}">
                  <i class="fas fa-file-invoice-dollar nav-icon text-secondary"></i>
                  <p>Factures</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link {{ request()->is('livraisons') ||  request()->is('livraisons/*')  ? 'active' : '' }}">
                  <i class="fas fa-file-alt nav-icon text-secondary"></i>
                  <p>Bons de livraison</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link {{ request()->is('calendar')   ? 'active' : '' }}" >
                  <i class="fas fa-calendar-alt  nav-icon text-secondary"></i>
                  <p>Calendrier</p>
                </a>
              </li>
            </ul>
          </li>
-->
          <li class="nav-item menu-open {{ request()->is('products') ||   request()->is('products/*') || request()->is('orders') ||   request()->is('orders/*') || request()->is('parcels') ||   request()->is('parcels/*') || request()->is('clients') ||   request()->is('clients/*')   ? 'menu-open' : '' }} ">
            <a href="#" class="nav-link {{    request()->is('products') ||   request()->is('products/*') || request()->is('orders') ||   request()->is('orders/*') || request()->is('parcels') ||   request()->is('parcels/*') || request()->is('clients') ||   request()->is('clients/*')   ? 'active' : '' }}">
              <i class="nav-icon fas fa-store text-white"></i>
              <p>
                Gestions
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('orders.create')}}" class="nav-link {{ request()->is('orders/create')  ? 'active' : '' }}">
                  <i class="fas fa-cart-plus nav-icon text-secondary"></i>
                  <p>Nouvelle commande</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('orders.index')}}" class="nav-link {{ request()->is('orders')  ? 'active' : '' }}">
                  <i class="fas fa-shopping-cart nav-icon text-secondary"></i>
                  <p>Commandes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('orders.current')}}" class="nav-link {{ request()->is('orders-current')  ? 'active' : '' }}">
                  <i class="fas fa-store nav-icon text-secondary"></i>
                  <p>Archive de commandes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('parcels.index')}}" class="nav-link {{ request()->is('parcels')  ? 'active' : '' }}">
                  <i class="fas fa-box nav-icon text-secondary"></i>
                  <p>Liste des colis</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('products.index')}}" class="nav-link {{ request()->is('products')  ? 'active' : '' }}">
                  <i class="fas fa-couch nav-icon text-secondary"></i>
                  <p>Produits</p>
                </a>
              </li>
              @can('isAdmin')
              <li class="nav-item">
                <a href="{{ route('stock.entries.index') }}" class="nav-link {{ request()->is('stock/*')  ? 'active' : '' }}">
                  <i class="fas fa-cubes nav-icon text-secondary"></i>
                  <p>Stocks</p>
                </a>
              </li>
              @endcan
               <li class="nav-item">
                <a href="{{ route('clients.index') }}" class="nav-link {{ request()->is('clients/*')  ? 'active' : '' }}">
                  <i class="fas fa-users nav-icon text-secondary"></i>
                  <p>Clients</p>
                </a>
              </li>
            </ul>
          </li>
          @can('isAdmin')
          <li class="nav-item  {{  request()->is('categories/*') || request()->is('providers/*') || request()->is('categories') ||  request()->is('providers') || request()->is('users') || request()->is('delivery-companies') ||  request()->is('delivery-companies.index')    ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{  request()->is('categories/*') || request()->is('providers/*') || request()->is('categories') || request()->is('providers') || request()->is('users') || request()->is('delivery-companies.index') ||  request()->is('delivery-companies')    ? 'active' : '' }}">
              <i class="nav-icon fas fa-cog text-white"></i>
              <p>
                Paramètres
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <li class="nav-item">
                <a href="{{route('users.index')}}" class="nav-link {{ request()->is('users/*') ||  request()->is('users')  ? 'active' : '' }}">
                  <i class="fas fa-users nav-icon text-secondary"></i>
                  <p>Utilisateurs</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('delivery-companies.index')}}" class="nav-link {{ request()->is('delivery-companies/*') ||  request()->is('delivery-companies')  ? 'active' : '' }}">
                  <i class="fas fa-truck nav-icon text-secondary"></i>
                  <p>Sociétés de livraison</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('providers.index')}}" class="nav-link {{ request()->is('providers') || request()->is('providers/*') ? 'active' : '' }}">
                  <i class="fas fa-building nav-icon text-secondary"></i>
                  <p>Fournisseurs</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('categories.index')}}" class="nav-link {{ request()->is('categories') || request()->is('categories/*') ? 'active' : '' }}">
                  <i class="fas fa-tags nav-icon text-secondary"></i>
                  <p>Catégories</p>
                </a>
              </li>
            </ul>
          </li>
          @endcan



        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>