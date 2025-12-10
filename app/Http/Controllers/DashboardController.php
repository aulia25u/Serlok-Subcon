<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\TenantService;
use App\Models\TenantOwner;
use App\Models\User;
use App\Models\Outgoing;
use App\Models\MasterItem;
use App\Models\EmployeeJob;
use App\Models\SuratJalan;
use App\Models\Inventory;

use App\Models\ActivityLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isInternal = TenantService::isInternal();
        $currentCustomerId = TenantService::currentCustomerId();

        $data = [];
        $viewType = 'staff'; // Default

        if ($isInternal) {
            $viewType = 'internal_admin';

            // Platform Admin Stats
            $data['total_tenants'] = TenantOwner::count();
            $data['total_users'] = User::count();
            $data['active_outgoings_30d'] = Outgoing::where('created_at', '>=', Carbon::now()->subDays(30))->count();
            $data['total_jobs_all_time'] = EmployeeJob::count();
            $data['recent_activities'] = EmployeeJob::with(['outgoing.masterItem', 'user'])
                ->latest()
                ->take(5)
                ->get(); // Just to show something

            // Production Chart (System-wide for Admin)
            $data['chart_data'] = $this->getProductionChartData(null);

            // Server Info
            $data['server_info'] = [
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
                'server_os' => php_uname('s') . ' ' . php_uname('r'),
                'database_connection' => DB::connection()->getDriverName(),
                'server_ip' => request()->server('SERVER_ADDR') ?? request()->server('LOCAL_ADDR') ?? '127.0.0.1',
                'load_average' => $this->getServerLoad(),
                'server_uptime' => $this->getServerUptime(),
                'db_uptime' => $this->getDbUptime(),
                'disk_usage' => $this->getDiskUsage(),
            ];

            // Recent Audit Logs
            $data['recent_audit_logs'] = ActivityLog::with('user')->latest()->take(10)->get();

            // Tenant Growth Data
            $data['tenant_growth'] = $this->getTenantGrowthData();

            // Client OS Stats
            $data['client_stats'] = $this->getClientStats();

            // Client Browser Stats
            $data['client_browser_stats'] = $this->getClientBrowserStats();

            // Top Tenants (Most Jobs)
            $data['top_tenants'] = $this->getTopTenants();

            // Most Active Users (Most Jobs)
            $data['active_users'] = $this->getActiveUsers();

            // Login Activity & Source IPs
            $data['top_ips'] = $this->getTopIps();
            $data['user_logins'] = $this->getMostLoginsUser();
            $data['tenant_logins'] = $this->getMostLoginsTenant();

        } elseif ($currentCustomerId) {
            // Tenant User
            $isTenantAdmin = $user->userDetail && $user->userDetail->role && $user->userDetail->role->role_name === 'Administrator';

            if ($isTenantAdmin) {
                $viewType = 'tenant_owner';

                // Master Items
                $data['total_items'] = MasterItem::whereHas('tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                })->count();

                // Monthly Productions
                $data['monthly_jobs'] = EmployeeJob::whereHas('outgoing.masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                })->whereMonth('created_at', Carbon::now()->month)->count();

                // Pending Surat Jalan (Draft)
                $data['pending_surat_jalan'] = SuratJalan::whereHas('employeeJob.outgoing.masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                })->where('status', 'Draft')->count();

                // Low Stock Items (Threshold < 10)
                // Inventory is linked to MasterItem
                $data['low_stock_count'] = Inventory::whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                })->where('quantity', '<', 10)->count();

                // Chart Data
                $data['chart_data'] = $this->getProductionChartData($currentCustomerId);

            } else {
                $viewType = 'tenant_staff';

                // My Recent Jobs
                $data['my_recent_jobs'] = EmployeeJob::with('outgoing.masterItem')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->take(10)
                    ->get();

                // My Pending Jobs (Assigned but not finished?)
                // Assuming no finished_time means pending, but structure allows nulls? 
                // Let's assume just recent jobs for now.
            }
        }

        return view('dashboard', compact('data', 'viewType'));
    }

    private function getProductionChartData($customerId = null)
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $query = EmployeeJob::select(
            DB::raw('DATE(created_datetime) as date'),
            DB::raw('SUM(qty_ok) as total_ok'),
            DB::raw('SUM(qty_ng) as total_ng')
        )
            ->whereBetween('created_datetime', [$startDate, $endDate]);

        if ($customerId) {
            $query->whereHas('outgoing.masterItem.tenantOwner', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            });
        }

        $results = $query->groupBy('date')->orderBy('date')->get();

        // Format for Chart.js
        $labels = [];
        $dataOk = [];
        $dataNg = [];

        // Fill in missing days
        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            $endDate->modify('+1 day') // Include end date
        );

        foreach ($period as $dt) {
            $dateStr = $dt->format('Y-m-d');
            $labels[] = $dt->format('d M');

            $dayData = $results->firstWhere('date', $dateStr);
            $dataOk[] = $dayData ? $dayData->total_ok : 0;
            $dataNg[] = $dayData ? $dayData->total_ng : 0;
        }

        return [
            'labels' => $labels,
            'ok' => $dataOk,
            'ng' => $dataNg
        ];
    }

    private function getServerLoad()
    {
        $load = sys_getloadavg();
        if (!$load)
            return 'N/A';

        // Attempt to get number of cores
        $cores = 1;
        if (PHP_OS_FAMILY === 'Windows') {
            $cores = 1; // Difficult to get on Windows without COM/WMI
        } else {
            // Linux/Mac
            if (is_file('/proc/cpuinfo')) {
                $cpuinfo = file_get_contents('/proc/cpuinfo');
                preg_match_all('/^processor/m', $cpuinfo, $matches);
                $cores = count($matches[0]);
            } else {
                $cores = shell_exec('sysctl -n hw.ncpu'); // Mac
            }
        }

        $cores = intval($cores) > 0 ? intval($cores) : 1;
        $usage = round(($load[0] / $cores) * 100, 1);

        return "{$usage}%";
    }

    private function getServerUptime()
    {
        try {
            // Try uptime command
            $uptime = shell_exec('uptime');
            if ($uptime) {
                // Parse "up ...," from output
                // Mac: 14:48  up 1 day,  4:38, 2 users, load averages: ...
                // Linux: 14:48:00 up 10 days,  4:38,  2 users,  load average: ...
                if (preg_match('/up\s+(.*?),\s+\d+\s+user/', $uptime, $matches)) {
                    return $matches[1];
                }
                return $uptime; // Return raw if parse fails
            }
        } catch (\Exception $e) {
            return 'N/A';
        }
        return 'N/A';
    }

    private function getDbUptime()
    {
        try {
            // MySQL/MariaDB only
            $result = DB::select("SHOW GLOBAL STATUS LIKE 'Uptime'");
            if (!empty($result)) {
                $seconds = $result[0]->Value;
                $dtF = new \DateTime('@0');
                $dtT = new \DateTime("@$seconds");
                // Calculate difference
                return $dtF->diff($dtT)->format('%a days, %h hrs, %i mins');
            }
        } catch (\Exception $e) {
            return 'N/A';
        }
        return 'N/A';
    }

    private function getDiskUsage()
    {
        try {
            $path = '/'; // Check root partition or current app path
            $total = disk_total_space($path);
            $free = disk_free_space($path);
            $used = $total - $free;

            $usedGb = round($used / 1024 / 1024 / 1024, 2);
            $totalGb = round($total / 1024 / 1024 / 1024, 2);
            $percent = round(($used / $total) * 100, 1);

            return "{$usedGb} GB / {$totalGb} GB ({$percent}%)";
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    private function getTenantGrowthData()
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $results = TenantOwner::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('sum(case when is_active = 1 then 1 else 0 end) as active_count'),
            DB::raw('sum(case when is_active = 0 then 1 else 0 end) as inactive_count')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $dataActive = [];
        $dataInactive = [];

        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1M'),
            $endDate->modify('+1 day')
        );

        foreach ($period as $dt) {
            $monthStr = $dt->format('Y-m');
            $labels[] = $dt->format('M Y');

            $monthData = $results->firstWhere('month', $monthStr);
            $dataActive[] = $monthData ? $monthData->active_count : 0;
            $dataInactive[] = $monthData ? $monthData->inactive_count : 0;
        }

        return [
            'labels' => $labels,
            'active' => $dataActive,
            'inactive' => $dataInactive
        ];
    }

    private function getClientStats()
    {
        // Analyze last 1000 logs for active user environment
        $logs = ActivityLog::select('user_agent')->latest()->take(1000)->get();
        $stats = ['Windows' => 0, 'Mac' => 0, 'Linux' => 0, 'Mobile' => 0, 'Other' => 0];

        foreach ($logs as $log) {
            $agent = $log->user_agent;
            if (empty($agent))
                continue;

            if (preg_match('/windows/i', $agent))
                $stats['Windows']++;
            elseif (preg_match('/macintosh|mac os x/i', $agent))
                $stats['Mac']++;
            elseif (preg_match('/linux/i', $agent))
                $stats['Linux']++;
            elseif (preg_match('/iphone|ipad|android/i', $agent))
                $stats['Mobile']++;
            else
                $stats['Other']++;
        }

        return $stats;
    }

    private function getClientBrowserStats()
    {
        $logs = ActivityLog::select('user_agent')->latest()->take(1000)->get();
        $stats = [];

        foreach ($logs as $log) {
            $browserFull = $log->browser; // Uses model accessor
            $parts = explode(' ', $browserFull);
            $name = $parts[0] ?? 'Unknown';

            if (!isset($stats[$name])) {
                $stats[$name] = 0;
            }
            $stats[$name]++;
        }

        return $stats;
    }
    private function getTopTenants()
    {
        return EmployeeJob::join('outgoings', 'employee_jobs.outgoing_id', '=', 'outgoings.id')
            ->join('master_items', 'outgoings.master_item_id', '=', 'master_items.id')
            ->join('tenant_owners', 'master_items.tenant_id', '=', 'tenant_owners.id')
            ->join('customers', 'tenant_owners.customer_id', '=', 'customers.id')
            ->select('customers.customer_name', DB::raw('count(*) as total_jobs'))
            ->where('employee_jobs.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('customers.id', 'customers.customer_name')
            ->orderByDesc('total_jobs')
            ->take(5)
            ->get();
    }

    private function getActiveUsers()
    {
        return EmployeeJob::join('users', 'employee_jobs.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(*) as total_jobs'))
            ->where('employee_jobs.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_jobs')
            ->take(5)
            ->get();
    }

    private function getTopIps()
    {
        return ActivityLog::select('ip_address', DB::raw('count(*) as total'))
            ->groupBy('ip_address')
            ->orderByDesc('total')
            ->take(5)
            ->get();
    }

    private function getMostLoginsUser()
    {
        return ActivityLog::where('action', 'login')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(*) as total_logins'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_logins')
            ->take(5)
            ->get();
    }

    private function getMostLoginsTenant()
    {
        // Linking ActivityLog -> Tenant (via tenant_id potentially null for internal)
        // Assuming tenant_id in ActivityLog maps to Customer ID directly (ActivityLogService: $tenantId = $user->userDetail->customer_id)
        // But wait, ActivityLog migration tenant_id might be integer.
        // Let's rely on the join to customers table.
        return ActivityLog::where('action', 'login')
            ->whereNotNull('tenant_id')
            ->join('customers', 'activity_logs.tenant_id', '=', 'customers.id')
            ->select('customers.customer_name', DB::raw('count(*) as total_logins'))
            ->groupBy('customers.id', 'customers.customer_name')
            ->orderByDesc('total_logins')
            ->take(5)
            ->get();
    }
}

