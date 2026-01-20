<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="navbar-brand">
                <div class="brand-logo">
                    <img src="{{asset('/images/Alfred_bleu_tête.png')}}" height="30px" alt="" class="mr-0">
                    <img src="{{asset('/images/Alfred_bleu_texte.png')}}"  alt="">
                </div>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                        class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i
                        class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc"
                        data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content overflow-hidden">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            <li class=" nav-item ">
                <a class="d-flex align-items-center" href="/gf">
                    <i class="fas fa-file-invoice"></i>
                    <span class="menu-title text-truncate" data-i18n="dashboard">
                            Gestion des Factures
                    </span>
                </a>
            </li>



            <li class=" nav-item {{ request()->routeIs('user.budgets*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('user.budgets.index') }}">
                    <i class="fas fa-money-bill-alt"></i>
                    <span class="menu-title text-truncate" data-i18n="dashboard">
                            {{ trans_choice('labels.models.budget',2) }}
                        </span>
                </a>
            </li>
            <!-- 🔹 Fardeau de Main-d'œuvre (Pour les utilisateurs) -->
           <li class="nav-item has-sub @if (request()->is(['user/fardeauMO/contributions', 'user/fardeauMO/operations*', 'user/fardeauMO/analyse*'])) sidebar-group-active open @endif">
    <a href="#" class="d-flex align-items-center">
        <i class="fas fa-briefcase"></i>
        <span class="menu-title text-truncate">Masse salariale</span>
    </a>

    <ul class="menu-content">
        <!-- 🔹 Contributions -->
        <li class="nav-item">
            <a href="{{ route('user.fardeauMO.contributions.index') }}" 
               class="nav-link {{ request()->routeIs('user.fardeauMO.contributions.index') ? 'active' : '' }}" 
               data-link="/user/fardeauMO/contributions">
                <i class="fas fa-chart-line"></i>
                <span class="menu-title text-truncate">Contributions</span>
            </a>
        </li>

       

       <!-- 🔹 Activités (anciennement Opérations) -->
       <li class="nav-item">
    <a class="nav-link d-flex align-items-center justify-content-between" data-toggle="collapse" href="#operations">
        <div>
            <i class="fas fa-cogs"></i> Activitées
        </div>
    </a>

    <ul class="list-unstyled" >
        <li>
            <a href="{{ route('user.fardeauMO.operation_types.create') }}" class="btn btn-sm btn-primary d-block text-center">
                <i class="fas fa-plus"></i> <!-- Le bouton "+" sans flèche -->
            </a>
        </li>
        @if(isset($operationTypes) && $operationTypes->count() > 0)

            @foreach($operationTypes as $operationType)
                <li>
                    <a href="{{ route('user.fardeauMO.employees.index', ['type' => $operationType->id]) }}">
                        <i class="fas fa-cogs"></i> {{ $operationType->name }}
                    </a>
                </li>
            @endforeach

        @endif
    </ul>
</li>






        <!-- 🔹 Analyse -->
        <li class="nav-item">
            <a href="{{ route('user.fardeauMO.analyse-mo.index') }}" 
               class="nav-link {{ request()->routeIs('user.fardeauMO.analyse-mo.index') ? 'active' : '' }}" 
               data-link="/user/fardeauMO/analyse-mo">
                <i class="fas fa-chart-bar"></i>
                <span class="menu-title text-truncate">Analyse</span>
            </a>
        </li>
    </ul>
</li>
<li class="nav-item {{ request()->routeIs('user.coutscamion*') ? 'active' : '' }}">
    <a class="d-flex align-items-center" href="{{ route('user.coutscamion.index') }}">
        <i class="fas fa-truck"></i>
        <span class="menu-title text-truncate">Coût par camion</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('user.tasks*') ? 'active' : '' }}">
    <a class="d-flex align-items-center" href="{{ route('user.tasks.index') }}">
        <i class="fas fa-tasks"></i>
        <span class="menu-title text-truncate">Suivi des Tâches</span>
    </a>
</li>
<li class="nav-item {{ request()->routeIs('user.amortissements*') ? 'active' : '' }}">
    <a class="d-flex align-items-center" href="{{ route('user.amortissements.index') }}">
        <i class="fas fa-calculator"></i>
        <span class="menu-title text-truncate">Amortissements</span>
    </a>
</li>





            <li class=" nav-item {{ request()->routeIs('user.configurations*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('user.configurations.index') }}">
                    <i class="fas fa-sliders-h"></i>
                    <span class="menu-title text-truncate" data-i18n="dashboard">
                            {{ trans_choice('labels.models.configuration',2) }}
                        </span>
                </a>
            </li>
           
        </ul>
    </div>
</div>




