<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeController extends Controller
{
    /** Maps 'Admin', 'HR Staff', 'Employee' → user.role values */
    private const DEPT_ROLE_MAP = [
        'Admin'    => 'admin',
        'HR Staff' => 'hr_staff',
        'Employee' => 'employee',
    ];

    private const IMPORT_COLUMNS = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'role',
        'department',
        'employment_type',
        'start_date',
        'pay_rate',
        'pay_type',
        'location',
        'password',
    ];

    private function flattenUser(User $u): array
    {
        $emp = $u->employee;
        return [
            'id'              => $emp?->id,
            'user_id'         => $u->id,
            'first_name'      => $u->first_name,
            'last_name'       => $u->last_name,
            'email'           => $u->email,
            'phone'           => $emp?->phone         ?? null,
            'user_role'       => $u->role,
            'role'            => $emp?->role           ?? '',
            'department'      => $emp?->department     ?? '',
            'employment_type' => $emp?->employment_type ?? '',
            'start_date'      => $emp?->start_date ? $emp->start_date->toDateString() : null,
            'pay_rate'        => $emp?->pay_rate,
            'pay_type'        => $emp?->pay_type       ?? '',
            'location'        => $emp?->location       ?? '',
            'is_active'       => $u->is_active,
            'trainings'       => $emp?->trainings?->toArray() ?? [],
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $query = User::with(['employee' => fn($q) => $q->with('trainings')])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('first_name', 'like', "%{$s}%")
                ->orWhere('last_name',  'like', "%{$s}%")
                ->orWhere('email',      'like', "%{$s}%"));
        }

        $users = $query->orderBy('last_name')->get();
        $flat  = $users->map(fn($u) => $this->flattenUser($u))->values();

        return response()->json(['data' => $flat, 'total' => $flat->count()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|email|unique:employees,email|unique:users,email',
            'password'        => 'required|string|min:8',
            'phone'           => 'nullable|string|max:50',
            'role'            => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'department'      => 'required|in:Admin,HR Staff,Employee',
            'start_date'      => 'nullable|date',
            'pay_rate'        => 'nullable|numeric|min:0',
            'pay_type'        => 'nullable|in:hourly,salary',
            'location'        => 'nullable|string|max:255',
        ]);

        $data['is_active'] = true;
        $userRole = self::DEPT_ROLE_MAP[$data['department']];

        // Create a User account so the employee can log in
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => $userRole,
            'is_active'  => true,
        ]);

        unset($data['password']);
        $data['user_id'] = $user->id;
        $employee = Employee::create($data);

        return response()->json($employee->load('trainings'), 201);
    }

    public function show(Employee $employee): JsonResponse
    {
        return response()->json($employee->load(['trainings', 'timeOffRequests', 'documents']));
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate([
            'first_name'      => 'sometimes|string|max:255',
            'last_name'       => 'sometimes|string|max:255',
            'email'           => 'sometimes|email|unique:employees,email,'.$employee->id,
            'password'        => 'sometimes|nullable|string|min:8',
            'phone'           => 'sometimes|nullable|string|max:50',
            'role'            => 'sometimes|string|max:255',
            'employment_type' => 'sometimes|in:full_time,part_time,contract',
            'department'      => 'sometimes|in:Admin,HR Staff,Employee',
            'start_date'      => 'sometimes|nullable|date',
            'pay_rate'        => 'sometimes|nullable|numeric|min:0',
            'pay_type'        => 'sometimes|nullable|in:hourly,salary',
            'location'        => 'sometimes|nullable|string|max:255',
            'is_active'       => 'sometimes|boolean',
        ]);

        // Sync password change to the linked User account
        if (! empty($validated['password'])) {
            if ($employee->user_id) {
                User::where('id', $employee->user_id)->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }
        }
        unset($validated['password']);

        // Sync department → user.role when department changes
        if (isset($validated['department']) && $employee->user_id) {
            $newRole = self::DEPT_ROLE_MAP[$validated['department']] ?? null;
            if ($newRole) {
                User::where('id', $employee->user_id)->update(['role' => $newRole]);
            }
        }

        // Sync name/email changes to the linked User account
        $userFields = array_filter([
            'first_name' => $validated['first_name'] ?? null,
            'last_name'  => $validated['last_name']  ?? null,
            'email'      => $validated['email']       ?? null,
        ]);
        if ($userFields && $employee->user_id) {
            User::where('id', $employee->user_id)->update($userFields);
        }

        $employee->update($validated);
        return response()->json($employee->fresh('trainings'));
    }

    public function destroy(Employee $employee): JsonResponse
    {
        // Also remove the linked User account so they can no longer log in
        if ($employee->user_id) {
            User::where('id', $employee->user_id)->delete();
        }
        $employee->delete();
        return response()->json(['message' => 'Employee removed']);
    }

    public function importTemplate()
    {
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->fromArray(self::IMPORT_COLUMNS, null, 'A1');
        $sheet->fromArray([
            'Jane',
            'Doe',
            'jane.doe@example.com',
            '555-123-4567',
            'HR Generalist',
            'HR Staff',
            'full_time',
            now()->toDateString(),
            '24.50',
            'hourly',
            'Main Office',
            'Welcome123!',
        ], null, 'A2');

        foreach (range(1, count(self::IMPORT_COLUMNS)) as $idx) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($idx))->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($sheet) {
            $writer = new Xlsx($sheet->getParent());
            $writer->save('php://output');
        }, 'employee-import-template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        $sheet = IOFactory::load($validated['file']->getRealPath())->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return response()->json([
                'message' => 'The file has no employee rows to import.',
                'created' => 0,
                'updated' => 0,
                'failed' => 0,
                'errors' => [],
            ]);
        }

        $headers = array_map(fn($h) => Str::of((string) $h)->trim()->lower()->value(), array_values($rows[1] ?? []));
        $missingRequired = array_values(array_diff(['first_name', 'last_name', 'email'], $headers));
        if (! empty($missingRequired)) {
            return response()->json([
                'message' => 'Missing required columns: '.implode(', ', $missingRequired),
            ], 422);
        }

        $results = [
            'created' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        for ($i = 2; $i <= count($rows); $i++) {
            $line = array_values($rows[$i] ?? []);
            if ($this->isRowEmpty($line)) {
                continue;
            }

            $rowData = [];
            foreach ($headers as $colIdx => $header) {
                if ($header === '') {
                    continue;
                }
                $rowData[$header] = trim((string) ($line[$colIdx] ?? ''));
            }

            try {
                $this->importEmployeeRow($rowData, $results);
            } catch (\Throwable $e) {
                $results['failed']++;
                $results['errors'][] = 'Row '.$i.': '.$e->getMessage();
            }
        }

        return response()->json([
            'message' => 'Import complete.',
            ...$results,
        ]);
    }

    private function importEmployeeRow(array $rowData, array &$results): void
    {
        $first = trim((string) ($rowData['first_name'] ?? ''));
        $last = trim((string) ($rowData['last_name'] ?? ''));
        $email = strtolower(trim((string) ($rowData['email'] ?? '')));

        if ($first === '' || $last === '' || $email === '') {
            throw new \RuntimeException('first_name, last_name, and email are required.');
        }

        $department = $this->normalizeDepartment($rowData['department'] ?? null, $rowData['role'] ?? null);
        $userRole = self::DEPT_ROLE_MAP[$department];
        $employmentType = $this->normalizeEmploymentType($rowData['employment_type'] ?? null);
        $payType = $this->normalizePayType($rowData['pay_type'] ?? null);
        $startDate = $this->normalizeDate($rowData['start_date'] ?? null) ?: now()->toDateString();
        $payRate = $this->normalizePayRate($rowData['pay_rate'] ?? null);

        DB::transaction(function () use (
            $first,
            $last,
            $email,
            $department,
            $userRole,
            $employmentType,
            $payType,
            $startDate,
            $payRate,
            $rowData,
            &$results
        ) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'first_name' => $first,
                    'last_name' => $last,
                    'role' => $userRole,
                    'is_active' => true,
                ]);
            } else {
                $password = trim((string) ($rowData['password'] ?? ''));
                if ($password === '') {
                    $password = Str::random(12);
                }
                $user = User::create([
                    'first_name' => $first,
                    'last_name' => $last,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => $userRole,
                    'is_active' => true,
                ]);
            }

            $employee = Employee::where('email', $email)->first();
            $payload = [
                'user_id' => $user->id,
                'first_name' => $first,
                'last_name' => $last,
                'email' => $email,
                'phone' => trim((string) ($rowData['phone'] ?? '')) ?: null,
                'role' => trim((string) ($rowData['role'] ?? '')) ?: 'HR Staff',
                'department' => $department,
                'employment_type' => $employmentType,
                'start_date' => $startDate,
                'pay_rate' => $payRate,
                'pay_type' => $payType,
                'location' => trim((string) ($rowData['location'] ?? '')) ?: null,
                'is_active' => true,
            ];

            if ($employee) {
                $employee->update($payload);
                $results['updated']++;
                return;
            }

            Employee::create($payload);
            $results['created']++;
        });
    }

    private function isRowEmpty(array $line): bool
    {
        foreach ($line as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeDepartment(?string $department, ?string $role): string
    {
        $value = strtolower(trim((string) ($department ?: $role ?: '')));
        return match (true) {
            in_array($value, ['admin', 'administrator'], true) => 'Admin',
            in_array($value, ['employee', 'staff', 'team member'], true) => 'Employee',
            default => 'HR Staff',
        };
    }

    private function normalizeEmploymentType(?string $value): string
    {
        $clean = strtolower(str_replace(['-', ' '], '_', trim((string) $value)));
        return match ($clean) {
            'part_time', 'parttime' => 'part_time',
            'contract', '1099', 'contractor' => 'contract',
            default => 'full_time',
        };
    }

    private function normalizePayType(?string $value): string
    {
        return strtolower(trim((string) $value)) === 'salary' ? 'salary' : 'hourly';
    }

    private function normalizePayRate(?string $value): ?float
    {
        $clean = trim((string) $value);
        if ($clean === '') {
            return null;
        }

        if (! is_numeric($clean)) {
            throw new \RuntimeException('pay_rate must be numeric when provided.');
        }

        return (float) $clean;
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        try {
            return Carbon::parse((string) $value)->toDateString();
        } catch (\Throwable) {
            throw new \RuntimeException('Invalid start_date value.');
        }
    }
}
