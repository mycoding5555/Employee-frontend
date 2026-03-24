<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class EmployeeController extends Controller
{
    private string $apiBase = 'http://127.0.0.1:8000/api';
    private int $perPage = 9;

    // Main search page
    public function index(Request $request)
    {
        try {
            $departments = Http::timeout(5)->get($this->apiBase . '/departments')->json() ?? [];
            $employees = Http::timeout(5)->get($this->apiBase . '/employees')->json() ?? [];
        } catch (\Exception $e) {
            $departments = [];
            $employees = [];
            return view('employees.index', ['departments' => $departments, 'employees' => new LengthAwarePaginator([], 0, $this->perPage)])
                ->with('filters', [])
                ->with('error', 'Backend API is not reachable. Make sure it is running on port 8001.');
        }
        $filters = [];
        $employees = $this->paginateArray($employees, $request);
        return view('employees.index', compact('departments', 'employees', 'filters'));
    }

    // Search employees
    public function search(Request $request)
    {
        $departments = Http::get($this->apiBase . '/departments')->json();

        $query = [];
        if ($request->filled('name')) {
            $query['name'] = $request->input('name');
        }
        if ($request->filled('department_id')) {
            $query['department_id'] = $request->input('department_id');
        }

        $employees = Http::get($this->apiBase . '/employees', $query)->json();
        $filters = $request->only(['name', 'department_id']);
        $employees = $this->paginateArray($employees, $request);

        return view('employees.index', compact('departments', 'employees', 'filters'));
    }

    // AJAX search - returns JSON
    public function ajaxSearch(Request $request)
    {
        $query = [];
        if ($request->filled('name')) {
            $query['name'] = $request->input('name');
        }
        if ($request->filled('department_id')) {
            $query['department_id'] = $request->input('department_id');
        }

        try {
            $employees = Http::timeout(5)->get($this->apiBase . '/employees', $query)->json() ?? [];
        } catch (\Exception $e) {
            $employees = [];
        }

        return response()->json($employees);
    }

    // Download single employee photo
    public function downloadPhoto($id)
    {
        $response = Http::get($this->apiBase . '/employees/' . $id . '/photo');

        if ($response->failed()) {
            return back()->with('error', 'Photo not found.');
        }

        $filename = $response->header('Content-Disposition')
            ? str_replace(['attachment; filename=', '"'], '', $response->header('Content-Disposition'))
            : 'photo_' . $id . '.jpg';

        $desktopPath = $this->getDownloadFolder();
        file_put_contents($desktopPath . '/' . $filename, $response->body());

        return back()->with('success', "Photo saved to Desktop/EmployeePhotos/{$filename}");
    }

    // Download all photos in a department as zip
    public function downloadByDepartment($departmentId)
    {
        $response = Http::get($this->apiBase . '/employees/download-by-department/' . $departmentId);

        if ($response->failed()) {
            return back()->with('error', 'No photos found for this department.');
        }

        $filename = 'department_' . $departmentId . '_photos.zip';
        $desktopPath = $this->getDownloadFolder();
        file_put_contents($desktopPath . '/' . $filename, $response->body());

        return back()->with('success', "Department photos saved to Desktop/EmployeePhotos/{$filename}");
    }

    // Get or create the download folder on Desktop
    private function getDownloadFolder(): string
    {
        $home = getenv('HOME') ?: (getenv('USERPROFILE') ?: posix_getpwuid(posix_getuid())['dir']);
        $path = $home . '/Desktop/EmployeePhotos';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path;
    }

    // Paginate an array of results
    private function paginateArray(array $items, Request $request): LengthAwarePaginator
    {
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $this->perPage;
        $sliced = array_slice($items, $offset, $this->perPage);

        return new LengthAwarePaginator(
            $sliced,
            count($items),
            $this->perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}
