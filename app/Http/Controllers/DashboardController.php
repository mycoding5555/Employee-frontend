<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    private string $apiBase = 'http://127.0.0.1:8000/api';

    public function index()
    {
        try {
            $employees = Http::timeout(5)->get($this->apiBase . '/employees')->json() ?? [];
            $departments = Http::timeout(5)->get($this->apiBase . '/departments')->json() ?? [];
        } catch (\Exception $e) {
            $employees = [];
            $departments = [];
        }

        $employeeCount = count($employees);
        $departmentCount = count($departments);

        return view('Dashboard.index', compact('employeeCount', 'departmentCount'));
    }
}
