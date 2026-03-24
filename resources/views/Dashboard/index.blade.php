@extends('layout.main')

@section('title', 'ផ្ទាំងគ្រប់គ្រង')
@section('page-title', 'ផ្ទាំងគ្រប់គ្រង')
@section('page-subtitle', 'ទិដ្ឋភាពទូទៅនៃប្រព័ន្ធគ្រប់គ្រងបុគ្គលិក')

@section('content')
    <div class="dashboard-welcome">
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="app-card">
                    <div class="card-body-custom text-center" style="padding: 2rem 1.5rem;">
                        <div class="empty-state-icon" style="margin: 0 auto 1rem; background: rgba(13,110,253,.1); color: #0d6efd;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h2 style="font-weight: 700; color: var(--text);">{{ $employeeCount }}</h2>
                        <p style="color: var(--text-muted); font-size: .875rem; margin: 0;">បុគ្គលិកសរុប</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="app-card">
                    <div class="card-body-custom text-center" style="padding: 2rem 1.5rem;">
                        <div class="empty-state-icon" style="margin: 0 auto 1rem; background: rgba(25,135,84,.1); color: #198754;">
                            <i class="bi bi-building"></i>
                        </div>
                        <h2 style="font-weight: 700; color: var(--text);">{{ $departmentCount }}</h2>
                        <p style="color: var(--text-muted); font-size: .875rem; margin: 0;">នាយកដ្ឋានសរុប</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
