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
            $employees = new LengthAwarePaginator([], 0, $this->perPage);
            $filters = [];
            $error = 'Backend API is not reachable. Make sure it is running on port 8001.';
            return view('employees.index', compact('departments', 'employees', 'filters', 'error'));
        }
        $filters = [];
        $employees = $this->paginateArray($employees, $request);
        return view('employees.index', compact('departments', 'employees', 'filters'));
    }

    // Search employees by department 
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

        usort($employees, function ($a, $b) {
            $titleA = $a['title']['id'] ?? PHP_INT_MAX;
            $titleB = $b['title']['id'] ?? PHP_INT_MAX;
            if ($titleA !== $titleB) {
                return $titleA <=> $titleB;
            }
            return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
        });

        return response()->json($employees);
    }

    // Show employee photo inline (for <img> tags)
    public function showPhoto($id)
    {
        $response = Http::timeout(5)->get($this->apiBase . '/employees/' . (int) $id . '/photo');

        if ($response->failed()) {
            abort(404);
        }

        $contentType = $response->header('Content-Type', 'image/jpeg');

        return response($response->body(), 200)
            ->header('Content-Type', $contentType)
            ->header('Cache-Control', 'public, max-age=86400');
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

    // Download all photos in a department, organized into folders by title (ranked from top to bottom)
    public function downloadByDepartment($departmentId)
    {
        // Fetch all employees in this department
        $employees = Http::timeout(10)->get($this->apiBase . '/employees', [
            'department_id' => $departmentId,
        ])->json() ?? [];

        if (empty($employees)) {
            return back()->with('error', 'No employees found for this department.');
        }

        // Sort employees by title rank (ascending id = top rank first)
        usort($employees, function ($a, $b) {
            $titleA = $a['title']['id'] ?? PHP_INT_MAX;
            $titleB = $b['title']['id'] ?? PHP_INT_MAX;
            if ($titleA !== $titleB) {
                return $titleA <=> $titleB;
            }
            return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
        });

        // Group employees by title
        $grouped = [];
        foreach ($employees as $emp) {
            $titleName = $emp['title']['name'] ?? 'Unknown';
            $grouped[$titleName][] = $emp;
        }

        $desktopPath = $this->getDownloadFolder();
        $deptName = $employees[0]['department']['name'] ?? 'department_' . $departmentId;
        // Sanitize folder name
        $deptFolder = preg_replace('/[\/\\\\:*?"<>|]/', '_', $deptName);
        $deptPath = $desktopPath . '/' . $deptFolder;

        $downloadCount = 0;
        $titleIndex = 1;

        foreach ($grouped as $titleName => $emps) {
            // Create subfolder for each title, numbered by rank
            $safeTitleName = preg_replace('/[\/\\\\:*?"<>|]/', '_', $titleName);
            $titleFolder = $deptPath . '/' . $titleIndex . '_' . $safeTitleName;
            if (!is_dir($titleFolder)) {
                mkdir($titleFolder, 0755, true);
            }

            foreach ($emps as $emp) {
                if (empty($emp['photo'])) {
                    continue;
                }

                try {
                    $photoResponse = Http::timeout(10)->get($this->apiBase . '/employees/' . $emp['id'] . '/photo');
                    if ($photoResponse->successful()) {
                        $filename = $photoResponse->header('Content-Disposition')
                            ? str_replace(['attachment; filename=', '"'], '', $photoResponse->header('Content-Disposition'))
                            : 'photo_' . $emp['id'] . '.jpg';

                        file_put_contents($titleFolder . '/' . $filename, $photoResponse->body());
                        $downloadCount++;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            $titleIndex++;
        }

        if ($downloadCount === 0) {
            return back()->with('error', 'No photos found for this department.');
        }

        return back()->with('success', "បានរក្សាទុករូបថត {$downloadCount} សន្លឹក ក្នុង Desktop/EmployeePhotos/{$deptFolder}/ (ចែកតាមតំណែង)");
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
        usort($items, function ($a, $b) {
            $titleA = $a['title']['id'] ?? PHP_INT_MAX;
            $titleB = $b['title']['id'] ?? PHP_INT_MAX;
            if ($titleA !== $titleB) {
                return $titleA <=> $titleB;
            }
            return ($a['id'] ?? 0) <=> ($b['id'] ?? 0);
        });

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
