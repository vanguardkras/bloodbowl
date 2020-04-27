@if(session()->has($name))
    <div class="alert alert-{{ $importance }} alert-dismissible">
        {{ session()->get($name) }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif
