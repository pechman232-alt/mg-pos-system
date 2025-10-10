@extends('layouts.homePages')
@section('content')

<style>
    .form-card {
        background-color: #fff;
        border: 1px solid #DEAA5E;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(222, 170, 94, 0.15);
        padding: 25px;
        transition: all 0.3s ease;
        animation: slideInRight 0.6s ease forwards;
    }

    @keyframes slideInRight {
        0% {
            opacity: 0;
            transform: translateX(40px);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .form-label {
        font-weight: 600;
        color: #444;
    }

    .form-control:focus {
        border-color: #DEAA5E;
        box-shadow: 0 0 5px rgba(222, 170, 94, 0.4);
    }

    .btn-save {
        background-color: #DEAA5E;
        color: #fff;
        border: none;
        transition: 0.3s ease;
    }

    .btn-save:hover {
        background-color: #c99646;
        transform: scale(1.05);
    }

    .btn-back {
        border: 1px solid #DEAA5E;
        color: #DEAA5E;
        transition: 0.3s ease;
    }

    .btn-back:hover {
        background-color: #DEAA5E;
        color: white;
        transform: scale(1.05);
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="form-card">
                <h4 class="mb-3"><i class="bx bx-edit"></i> Edit Branch</h4>
                <hr>

                <form action="{{ url('/branch/update', encrypt($branch->id)) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="branch_name" class="form-label">Branch Name</label>
                        <input type="text" name="branch_name" id="branch_name" 
                               class="form-control" placeholder="Enter branch name" 
                               value="{{ $branch->branch_name }}" required>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ url('/branch') }}" class="btn btn-back">
                            <i class="bx bx-arrow-back"></i> Back
                        </a>
                        <button type="submit" class="btn btn-save">
                            <i class="bx bx-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
