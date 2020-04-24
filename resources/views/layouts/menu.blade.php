<nav class="navbar navbar-expand-sm navbar-dark bg-primary">
    <a class="navbar-brand" href="/">BLOODBOWL.RU</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="/">Главная</a>
            </li>
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
        </ul>
    </div>
</nav>
