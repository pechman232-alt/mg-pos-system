<style>
    
    .alert-success {
        background-color: #98e5aa;
        color: white;
        border-color: green;
    }

    .alert-primary {
        background-color: #90D26D;
        color: white;
        border-color: #416D19;
    }

    .alert-danger {
        background-color: red;
        color: white;
        border-color: red;
    }

</style>


@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="success" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (Session::has('update'))
    <div class="alert alert-primary alert-dismissible" role="alert">
        {{ session('update') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (Session::has('error'))
    <div class="alert alert-warning alert-dismissible" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (Session::has('delete'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        {{ session('delete') }}
        <button type="delete" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif