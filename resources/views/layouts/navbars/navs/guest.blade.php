<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between w-100 flex-nowrap">
            <div class="d-flex align-items-center flex-nowrap">
                <div class="icon-logo">
                    <img src="{{ asset('light-bootstrap/img/Apex-Logo.png') }}" alt="Apex Developers icon">
                </div>
                <a class="navbar-brand mb-0 ms-2" href="#pablo" style="color: rgba(230, 136, 136, 0.996); white-space:nowrap;">{{ __('Apex Developers') }}</a>
            </div>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-bar burger-lines"></span>
                <span class="navbar-toggler-bar burger-lines"></span>
                <span class="navbar-toggler-bar burger-lines"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav ms-auto d-flex flex-row justify-content-end w-100" style="gap: 1rem;">
                <li class="nav-item @if(isset($activePage) && $activePage == 'register') active @endif">
                    <a href="{{ route('register') }}" class="nav-link">
                        <i class="nc-icon nc-badge"></i> {{ __('Register') }}
                    </a>
                </li>
                <li class="nav-item @if(isset($activePage) && $activePage == 'login') active @endif">
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="nc-icon nc-mobile"></i> {{ __('Login') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
