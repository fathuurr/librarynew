@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Dashboard</h2>
        </div>
        <div class="card-body">
            <h5>Selamat datang, {{ auth()->user()->name }}</h5>
            <p>Role: {{ auth()->user()->roles->pluck('name')->first() }}</p>
        </div>
    </div>
@endsection
