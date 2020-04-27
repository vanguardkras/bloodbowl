<nav class="navbar navbar-expand-sm navbar-dark bg-primary">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <a class="navbar-brand" href="/">BLOODBOWL.RU</a>
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="/">{{ __('pages.menu.main') }}</a>
            </li>
            @auth
                @if(auth()->user()->isSuper())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown">
                            Super
                        </a>
                        <div class="dropdown-menu bg-dark">
                            <a class="dropdown-item text-light bg-dark" href="/races">Races</a>
                        </div>
                    </li>
                @endif
            @endauth
        </ul>
    </div>
    <ul class="navbar-nav ml-auto">
        @auth
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle small" href="#" role="button" data-toggle="dropdown">
                    {{ Auth::user()->username }} <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item small" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link small" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link small" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            @endif
        @endauth
    </ul>
</nav>
