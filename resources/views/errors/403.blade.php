@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-danger">Access Denied</div>
                <div class="card-body">
                    <h4 class="alert alert-danger">
                        <i class="fas fa-ban"></i> Admin Access Required
                    </h4>
                    <p>You must be logged in as the admin user to perform this action.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        <i class="fas fa-home"></i> Return Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection