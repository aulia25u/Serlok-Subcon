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
use App\Models\Plant;
use App\Models\Receiving;

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
            // Direct check against tenant_owners table
            $isTenantOwner = TenantOwner::where('user_id', $user->id)
                ->where('customer_id', $currentCustomerId)
                ->where('is_active', true)
                ->exists();

            if ($isTenantOwner) {
                $viewType = 'tenant_owner';

                // Master Items
                $data['total_items'] = MasterItem::whereHas('tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                })->count();

                // Total Employees (Users in this tenant)
                $data['total_employees'] = User::whereHas('userDetail', function ($q) use ($currentCustomerId) {
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

                // Total Plants
                $data['total_plants'] = Plant::where('customer_id', $currentCustomerId)->count();

                // Total Receiving
                $data['total_receiving'] = Receiving::whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                })->count();

                // Total Outgoing
                $data['total_outgoing'] = Outgoing::whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                })->count();

                // Total Outstanding (Sum of Inventory Quantity)
                $data['total_outstanding'] = Inventory::whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                })->sum('quantity');

                // Low Stock Items (Threshold < 10)
                $data['low_stock_count'] = Inventory::whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                })->where('quantity', '<', 10)->count();

                // Recent Activities (Scoped to Tenant)
                // We'll fetch logs where tenant_id matches
                $data['recent_activities'] = ActivityLog::with('user')
                    ->where('tenant_id', $currentCustomerId)
                    ->latest()
                    ->take(10)
                    ->get();

                // Chart Data (Production)
                $data['chart_data'] = $this->getProductionChartData($currentCustomerId);

                // Chart Data (Inventory Flow)
                $data['inventory_chart_data'] = $this->getInventoryChartData($currentCustomerId);

                // User Distribution Data
                $data['user_distribution'] = $this->getUserDistributionData($currentCustomerId);

            } else {
                $viewType = 'tenant_staff';

                // My Recent Jobs
                $data['my_recent_jobs'] = EmployeeJob::with('outgoing.masterItem')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->take(10)
                    ->get();
            }
        }

        return view('dashboard', compact('data', 'viewType'));
    }

    public function getChartData(Request $request)
    {
        $customerId = TenantService::currentCustomerId();
        if (!$customerId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $type = $request->input('type');
        $period = $request->input('period', 'weekly');

        if ($type === 'inventory') {
            return response()->json($this->getInventoryChartData($customerId, $period));
        } elseif ($type === 'production') {
            return response()->json($this->getProductionChartData($customerId, $period));
        } elseif ($type === 'employee_performance') {
            $limit = $request->input('limit', 10);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            return response()->json($this->getEmployeePerformanceData($customerId, $limit, $startDate, $endDate));
        } elseif ($type === 'top_defects') {
            return response()->json($this->getTopDefectItems($customerId));
        } elseif ($type === 'top_ok') {
            return response()->json($this->getTopOkItems($customerId));
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }

    private function getTopDefectItems($customerId)
    {
        $defects = \App\Models\EmployeeJob::where('qty_ng', '>', 0)
            ->whereHas('outgoing', function ($q) use ($customerId) {
                // Ensure correct tenant scoping
                $q->whereHas('masterItem', function ($qq) use ($customerId) {
                    $qq->whereHas('tenantOwner', function ($qqq) use ($customerId) {
                        $qqq->where('customer_id', $customerId);
                    });
                });
            })
            ->with(['outgoing.masterItem'])
            ->get();

        // Aggregate by Item Name
        $aggregated = $defects->groupBy(function ($job) {
            return $job->outgoing->masterItem->item_name ?? 'Unknown Item';
        })->map(function ($jobs) {
            return $jobs->sum('qty_ng');
        });

        // Sort Descending and Take 5
        $sorted = $aggregated->sortDesc()->take(5);

        return [
            'labels' => $sorted->keys()->values()->all(),
            'data' => $sorted->values()->all(),
        ];
    }

    private function getTopOkItems($customerId)
    {
        $okItems = \App\Models\EmployeeJob::where('qty_ok', '>', 0)
            ->whereHas('outgoing', function ($q) use ($customerId) {
                // Ensure correct tenant scoping
                $q->whereHas('masterItem', function ($qq) use ($customerId) {
                    $qq->whereHas('tenantOwner', function ($qqq) use ($customerId) {
                        $qqq->where('customer_id', $customerId);
                    });
                });
            })
            ->with(['outgoing.masterItem'])
            ->get();

        // Aggregate by Item Name
        $aggregated = $okItems->groupBy(function ($job) {
            return $job->outgoing->masterItem->item_name ?? 'Unknown Item';
        })->map(function ($jobs) {
            return $jobs->sum('qty_ok');
        });

        // Sort Descending and Take 5
        $sorted = $aggregated->sortDesc()->take(5);

        return [
            'labels' => $sorted->keys()->values()->all(),
            'data' => $sorted->values()->all(),
        ];
    }


    public function exportEmployeePerformance(Request $request)
    {
        $customerId = TenantService::currentCustomerId();
        if (!$customerId) {
            return abort(403, 'Unauthorized');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        // For export, we might want more data, defaulting to 1000 or unlimited
        $limit = $request->input('limit', 1000);

        $data = $this->getEmployeePerformanceData($customerId, $limit, $startDate, $endDate);

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=employee_performance.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array('Employee Name', 'Role', 'Assigned (Qty)', 'Executed (Qty)', 'OK (Qty)', 'NG (Qty)', 'Completion %'));

            foreach ($data as $row) {
                $completion = ($row['assigned'] > 0) ? round(($row['executed'] / $row['assigned']) * 100, 1) : 0;
                fputcsv($file, array(
                    $row['name'],
                    $row['role'],
                    $row['assigned'],
                    $row['executed'],
                    $row['qty_ok'],
                    $row['qty_ng'],
                    $completion . '%'
                ));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getEmployeePerformanceData($customerId, $limit = 10, $startDate = null, $endDate = null)
    {
        // Get all users for this customer (tenant)
        // Using userDetail to link employees to customer.
        // We filter by userDetail.customer_id.

        $users = \App\Models\User::whereHas('userDetail', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
            // Exclude Administrator role as they are likely the Owner
            // Also exclude explicit 'Owner' role if it exists
            $q->whereHas('role', function ($qq) {
                $qq->whereNotIn('role_name', ['Administrator', 'Super Admin', 'Owner']);
            });
        })
            ->with(['userDetail.position', 'userDetail.role']) // Load needed relations via userDetail
            ->get();

        // If the above query is too broad or inaccurate (e.g. gets owners too), we can filter in the loop or strict roles.
        // Let's use UserDetail customer_id as primary link for employees.

        $performanceData = $users->map(function ($user) use ($customerId, $startDate, $endDate) {
            // Assigned: Sum of Outgoing quantities assigned to this user
            $assignedQuery = \App\Models\Outgoing::where('user_id', $user->id)
                // We should ensure these outgoings belong to the tenant
                ->whereHas('masterItem', function ($q) use ($customerId) {
                    $q->whereHas('tenantOwner', function ($q2) use ($customerId) {
                        $q2->where('customer_id', $customerId);
                    });
                });

            if ($startDate) {
                $assignedQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $assignedQuery->whereDate('created_at', '<=', $endDate);
            }

            $assigned = $assignedQuery->sum('quantity');

            // Executed: Sum from EmployeeJob
            $jobsQuery = \App\Models\EmployeeJob::where('user_id', $user->id)
                ->whereHas('outgoing', function ($q) use ($customerId) {
                    $q->whereHas('masterItem', function ($qq) use ($customerId) {
                        $qq->whereHas('tenantOwner', function ($qqq) use ($customerId) {
                            $qqq->where('customer_id', $customerId);
                        });
                    });
                });

            if ($startDate) {
                $jobsQuery->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $jobsQuery->whereDate('created_at', '<=', $endDate);
            }

            $jobs = $jobsQuery->get();

            $qtyOk = $jobs->sum('qty_ok');
            $qtyNg = $jobs->sum('qty_ng');
            $executed = $qtyOk + $qtyNg;

            // Skip if no assignment (optional, but requested "performance", maybe skip zeros?)
            // User requested "sorted by most assigned", so 0 assigned might be at bottom. Keep them if useful?
            // "performance employee (selain owner)" -> maybe exclude owner role?
            // Assuming owner doesn't get assigned outgoing usually.

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'role' => $user->userDetail->role->role_name ?? 'N/A', // Access role via userDetail
                'assigned' => (int) $assigned,
                'executed' => (int) $executed,
                'qty_ok' => (int) $qtyOk,
                'qty_ng' => (int) $qtyNg,
            ];
        });

        // Filter out those with 0 assigned AND 0 executed to reduce noise? 
        // User didn't explicitly ask, but "performance" implies activity.
        // Let's keep non-owners.
        // Actually, we should filter by specific roles if we knew them.

        $sorted = $performanceData->sortByDesc('assigned')->values();

        return $sorted->take($limit);
    }

    public function getEmployeeHistory(Request $request, $id)
    {
        $customerId = TenantService::currentCustomerId();
        if (!$customerId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $jobs = \App\Models\EmployeeJob::where('user_id', $id)
            ->whereHas('outgoing', function ($q) use ($customerId) {
                $q->whereHas('masterItem', function ($qq) use ($customerId) {
                    $qq->whereHas('tenantOwner', function ($qqq) use ($customerId) {
                        $qqq->where('customer_id', $customerId);
                    });
                });
            })
            ->with(['outgoing.masterItem'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $history = $jobs->map(function ($job) {
            $assignedQty = $job->outgoing->quantity ?? 0;
            $executedQty = $job->qty_ok + $job->qty_ng;
            $completion = ($assignedQty > 0) ? round(($executedQty / $assignedQty) * 100, 1) : 0;

            $duration = '-';
            if ($job->finished_datetime && $job->created_datetime) {
                $duration = $job->finished_datetime->diff($job->created_datetime)->format('%H:%I:%S');
            }

            return [
                'date_assign' => $job->created_datetime ? $job->created_datetime->format('Y-m-d H:i') : '-',
                'item' => $job->outgoing->masterItem->item_name ?? 'Unknown',
                'assign_qty' => $assignedQty,
                'qty_ok' => $job->qty_ok,
                'qty_ng' => $job->qty_ng,
                'completion' => $completion . '%',
                'duration' => $duration
            ];
        });

        return response()->json([
            'user' => \App\Models\User::find($id)->name,
            'history' => $history
        ]);
    }


    private function getInventoryChartData($customerId, $period = 'weekly')
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = match ($period) {
            'monthly' => Carbon::now()->startOfMonth(),
            'yearly' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subDays(6)->startOfDay(), // Weekly
        };

        $groupByFormat = $period === 'yearly' ? '%Y-%m' : '%Y-%m-%d';
        $selectFormat = $period === 'yearly' ? "DATE_FORMAT(created_at, '%Y-%m') as date" : "DATE(created_at) as date";

        // Incoming
        $incomingQuery = Receiving::select(
            DB::raw($selectFormat),
            DB::raw('SUM(qty_pack * qty_per_pack) as total_incoming')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('masterItem.tenantOwner', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Outgoing
        $outgoingQuery = Outgoing::select(
            DB::raw($selectFormat),
            DB::raw('SUM(quantity) as total_outgoing')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('masterItem.tenantOwner', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();


        $labels = [];
        $dataIncoming = [];
        $dataOutgoing = [];

        $interval = $period === 'yearly' ? 'P1M' : 'P1D';
        $format = $period === 'yearly' ? 'Y-m' : 'Y-m-d';
        $labelFormat = $period === 'yearly' ? 'M Y' : 'd M';

        $periodRange = new \DatePeriod(
            $startDate,
            new \DateInterval($interval),
            $endDate->modify($period === 'yearly' ? '+1 month' : '+1 day')
        );

        foreach ($periodRange as $dt) {
            $dateStr = $dt->format($format);

            // Adjust label logic if needed (e.g. show day name for weekly)
            $labels[] = $dt->format($labelFormat);

            $in = $incomingQuery->firstWhere('date', $dateStr);
            $dataIncoming[] = $in ? $in->total_incoming : 0;

            $out = $outgoingQuery->firstWhere('date', $dateStr);
            $dataOutgoing[] = $out ? $out->total_outgoing : 0;
        }

        return [
            'labels' => $labels,
            'incoming' => $dataIncoming,
            'outgoing' => $dataOutgoing
        ];
    }

    private function getProductionChartData($customerId = null, $period = 'weekly')
    {
        $endDate = Carbon::now()->endOfDay();
        $startDate = match ($period) {
            'day' => Carbon::now()->subDays(6)->startOfDay(), // Last 7 days
            'monthly' => Carbon::now()->subMonths(11)->startOfMonth(), // Last 12 months
            'yearly' => Carbon::now()->subYears(4)->startOfYear(), // Last 5 years
            default => Carbon::now()->startOfWeek(), // Current Week
        };

        if ($period == 'weekly') {
            $endDate = Carbon::now()->endOfWeek();
        }

        $selectFormat = match ($period) {
            'yearly' => "DATE_FORMAT(finished_datetime, '%Y') as date",
            'monthly' => "DATE_FORMAT(finished_datetime, '%Y-%m') as date",
            default => "DATE(finished_datetime) as date",
        };

        $query = EmployeeJob::select(
            DB::raw($selectFormat),
            DB::raw('SUM(qty_ok) as total_ok'),
            DB::raw('SUM(qty_ng) as total_ng')
        )
            ->whereBetween('finished_datetime', [$startDate, $endDate])
            ->whereNotNull('finished_datetime');

        if ($customerId) {
            $query->whereHas('outgoing.masterItem.tenantOwner', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            });
        }

        $results = $query->groupBy('date')->orderBy('date')->get();

        $labels = [];
        $dataOk = [];
        $dataNg = [];

        $interval = $period === 'yearly' ? 'P1M' : 'P1D';
        $format = $period === 'yearly' ? 'Y-m' : 'Y-m-d';
        $labelFormat = $period === 'yearly' ? 'M Y' : 'd M';

        $periodRange = new \DatePeriod(
            $startDate,
            new \DateInterval($interval),
            $endDate->modify($period === 'yearly' ? '+1 month' : '+1 day')
        );

        foreach ($periodRange as $dt) {
            $dateStr = $dt->format($format);
            $labels[] = $dt->format($labelFormat);

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

    private function getUserDistributionData($customerId)
    {
        // Gender Distribution
        $genderStats = \App\Models\UserDetail::where('customer_id', $customerId)
            ->whereNotNull('gender')
            ->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get()
            ->mapWithKeys(function ($item) {
                // Normalize gender labels if necessary
                $label = $item->gender;
                if (strtoupper($item->gender) == 'L' || strtoupper($item->gender) == 'M')
                    $label = 'Male';
                if (strtoupper($item->gender) == 'P' || strtoupper($item->gender) == 'F')
                    $label = 'Female';
                return [$label => $item->count];
            });

        // Position Distribution
        $positionStats = \App\Models\UserDetail::where('customer_id', $customerId)
            ->whereNotNull('position_id')
            ->with('position')
            ->get()
            ->groupBy(function ($item) {
                return $item->position->position_name ?? 'Unknown';
            })
            ->map(function ($group) {
                return $group->count();
            });

        // Section Distribution
        // Join Position -> Section
        $sectionStats = \App\Models\UserDetail::where('user_details.customer_id', $customerId)
            ->join('positions', 'user_details.position_id', '=', 'positions.id')
            ->join('sections', 'positions.section_id', '=', 'sections.id')
            ->select('sections.section_name', DB::raw('count(*) as count'))
            ->groupBy('sections.section_name')
            ->pluck('count', 'section_name');

        // Department Distribution
        // Join Position -> Section -> Dept
        $deptStats = \App\Models\UserDetail::where('user_details.customer_id', $customerId)
            ->join('positions', 'user_details.position_id', '=', 'positions.id')
            ->join('sections', 'positions.section_id', '=', 'sections.id')
            ->join('depts', 'sections.dept_id', '=', 'depts.id')
            ->select('depts.dept_name', DB::raw('count(*) as count'))
            ->groupBy('depts.dept_name')
            ->pluck('count', 'dept_name');

        return [
            'gender' => $genderStats,
            'position' => $positionStats,
            'section' => $sectionStats,
            'department' => $deptStats
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

