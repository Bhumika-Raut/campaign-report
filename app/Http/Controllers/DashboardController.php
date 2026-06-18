<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Campaign;
use App\Models\ClientSummary;
use App\Services\ExcelImportService;
use App\Services\CSVExportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller {
    protected $importService;
    protected $exportService;

    public function __construct(ExcelImportService $importService, CSVExportService $exportService) {
        $this->importService = $importService;
        $this->exportService = $exportService;
    }


    public function clientConsumption(Request $request) {
        $query = Client::query();

        // Standard text search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Dropdown Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        // Shared Date Range Filter
        if ($request->filled('last_shared_from') && $request->filled('last_shared_to')) {
            $query->whereBetween('last_shared', [$request->last_shared_from, $request->last_shared_to]);
        }

        // Created Date Range Filter
        if ($request->filled('post_created_from') && $request->filled('post_created_to')) {
            $query->whereBetween('account_created', [$request->post_created_from, $request->post_created_to]);
        }

        // Calculate KPI Aggregates
        $totalClients = (int) (clone $query)->count();
        $premiumClients = (int) (clone $query)->where('plan', 'Premium')->count();
        $liveClients = (int) (clone $query)->where('status', 'Live')->count();
        $totalConsumed = (float) (clone $query)->sum('consumed');
        $totalRemaining = (float) (clone $query)->sum('remaining');
        $totalPosts = (int) (clone $query)->sum('posts');

        // 1. Plan Distribution (Doughnut Chart)
        $planDistribution = (clone $query)
            ->select('plan', \Illuminate\Support\Facades\DB::raw('count(id) as count'))
            ->groupBy('plan')
            ->get()
            ->map(function($c) {
                return ['label' => $c->plan, 'count' => (int) $c->count];
            });

        // 2. Status Distribution (Doughnut Chart)
        $statusDistribution = (clone $query)
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(id) as count'))
            ->groupBy('status')
            ->get()
            ->map(function($c) {
                return ['label' => $c->status, 'count' => (int) $c->count];
            });

        // 3. Top Consumers (Horizontal Bar Chart)
        $topConsumers = (clone $query)
            ->where('consumed', '>', 0)
            ->select('company', 'email', 'consumed')
            ->orderByDesc('consumed')
            ->limit(10)
            ->get()
            ->map(function($c) {
                return [
                    'name' => $c->company ?: $c->email,
                    'consumed' => (float) $c->consumed
                ];
            });

        // 4. Registration Trend (Line Chart)
        $registrationTrend = (clone $query)
            ->select('account_created', \Illuminate\Support\Facades\DB::raw('count(id) as count'))
            ->groupBy('account_created')
            ->orderBy('account_created')
            ->get()
            ->map(function($c) {
                return [
                    'date' => $c->account_created->toDateString(),
                    'count' => (int) $c->count
                ];
            });

        // 5. Top Engaged Clients (Bar Chart)
        $topEngagedClients = (clone $query)
            ->select('company', 'email', \Illuminate\Support\Facades\DB::raw('(clicks + posts + comments) as engagement'))
            ->orderByDesc('engagement')
            ->limit(10)
            ->get()
            ->map(function($c) {
                return [
                    'name' => $c->company ?: $c->email,
                    'engagement' => (int) $c->engagement
                ];
            });

        $analytics = [
            'planDistribution' => $planDistribution,
            'statusDistribution' => $statusDistribution,
            'topConsumers' => $topConsumers,
            'registrationTrend' => $registrationTrend,
            'topEngagedClients' => $topEngagedClients
        ];

        $clients = $query->orderByRaw("CASE WHEN plan = 'Premium' THEN 1 ELSE 2 END")
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.client_consumption', compact(
            'clients', 'analytics', 
            'totalClients', 'premiumClients', 'liveClients', 'totalConsumed', 'totalRemaining', 'totalPosts'
        ));
    }

    public function campaignReport(Request $request) {
        // Parse date range (using standardized 'from' and 'to')
        [$from, $to] = $this->getDateRange($request);

        // --- FULL CAMPAIGN QUERY FOR SUMMARY CARDS (FILTERED BY DATE RANGE) ---
        $fullCampaignQuery = \App\Models\RemovedCampaign::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        // --- REMOVED CAMPAIGN DETAILS VIEW DATA (WITH FILTERS AND DATE RANGE) ---
        $campaignQuery = \App\Models\RemovedCampaign::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        if ($request->filled('client_name')) {
            $campaignQuery->where('client_name', 'like', "%{$request->client_name}%");
        }

        if ($request->filled('campaign_id')) {
            $campaignQuery->where('id', 'like', "%{$request->campaign_id}%");
        }

        if ($request->filled('campaign_name')) {
            $campaignQuery->where('campaign_title', 'like', "%{$request->campaign_name}%");
        }

        if ($request->filled('paid_or_not')) {
            $campaignQuery->where('paid_or_not', $request->paid_or_not);
        }

        if ($request->filled('email')) {
            $email = $request->email;
            $campaignQuery->whereIn('client_name', function ($q) use ($email) {
                $q->select('client_name')->from('client_summaries')->where('email', 'like', "%{$email}%");
            });
        }

        if ($request->filled('campaign_date')) {
            $campaignQuery->whereRaw('DATE(created_at) = ?', [$request->campaign_date]);
        }

        $search = $request->input('search');
        if ($search) {
            $campaignQuery->where(function ($q) use ($search) {
                $q->where('campaign_title', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        // --- CLIENT SUMMARY VIEW DATA (FILTERED BY ACTIVE CLIENTS IN DATE RANGE) ---
        $activeClients = \App\Models\RemovedCampaign::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->distinct('client_name')
            ->pluck('client_name');

        $summaryQuery = ClientSummary::whereIn('client_name', $activeClients);

        if ($search) {
            $summaryQuery->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_name')) {
            $summaryQuery->where('client_name', 'like', "%{$request->client_name}%");
        }

        if ($request->filled('email')) {
            $summaryQuery->where('email', 'like', "%{$request->email}%");
        }

        if ($request->filled('campaign_date')) {
            $date = $request->campaign_date;
            $summaryQuery->whereIn('client_name', function ($q) use ($date) {
                $q->select('client_name')->from('removed_campaigns')->whereRaw('DATE(created_at) = ?', [$date]);
            });
        }

        if ($request->filled('campaign_id')) {
            $campId = $request->campaign_id;
            $summaryQuery->whereIn('client_name', function ($q) use ($campId) {
                $q->select('client_name')->from('removed_campaigns')->where('id', $campId);
            });
        }

        if ($request->filled('campaign_name')) {
            $campName = $request->campaign_name;
            $summaryQuery->whereIn('client_name', function ($q) use ($campName) {
                $q->select('client_name')->from('removed_campaigns')->where('campaign_title', $campName);
            });
        }

        if ($request->filled('paid_or_not')) {
            $paidOrNot = $request->paid_or_not;
            $summaryQuery->whereIn('client_name', function ($q) use ($paidOrNot) {
                $q->select('client_name')->from('removed_campaigns')->where('paid_or_not', $paidOrNot);
            });
        }

        if ($request->filled('custom_condition')) {
            $this->applyRemovedCustomCondition($campaignQuery, $request->custom_condition);
            $this->applyCustomCondition($summaryQuery, $request->custom_condition, false);
        }

        // Campaigns KPIs - Using filtered campaigns dynamically
        $totalReach = (int) (clone $campaignQuery)->sum('potential_reach');
        $totalShares = (int) (clone $campaignQuery)->sum('total_shares');
        $totalClicks = (int) (clone $campaignQuery)->sum('total_clicks');
        $totalComments = (int) (clone $campaignQuery)->sum('total_comments');
        $totalLikes = (int) (clone $campaignQuery)->sum('total_likes');
        $totalEngagement = $totalShares + $totalClicks + $totalComments + $totalLikes;
        $totalSubmissions = (int) (clone $campaignQuery)->sum('registrations'); // Filtered campaigns registrations
        
        $sumRoi = (double) (clone $campaignQuery)->sum('roi');
        $totalCount = (clone $campaignQuery)->count();
        $averageRoi = $totalCount > 0 ? ($sumRoi / $totalCount) : 0;
        $ugcRatio = $averageRoi; // Mapped to ugcRatio for backwards compatibility in other bindings if any

        // Paginate Campaigns directly (by id desc)
        $campaigns = $campaignQuery->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'campaigns_page')
            ->withQueryString();

        // Paginate Client Summaries directly (by client_name asc)
        $clients = $summaryQuery->orderBy('client_name')
            ->paginate(15, ['*'], 'clients_page')
            ->withQueryString();

        // Campaigns Charts Data (using filtered campaign query)
        // 1. Engagement Breakdown
        $campaignEngagementBreakdown = [
            'shares' => $totalShares,
            'clicks' => $totalClicks,
            'comments' => $totalComments,
            'likes' => $totalLikes,
        ];

        // 2. Top Engaged Campaigns
        $campaignTopEngaged = (clone $campaignQuery)
            ->select('campaign_title as campaign_name', \Illuminate\Support\Facades\DB::raw('(total_shares + total_clicks + total_comments + total_likes) as engagement'))
            ->orderByDesc('engagement')
            ->limit(10)
            ->get()
            ->map(function($c) {
                return ['name' => $c->campaign_name, 'engagement' => (int) $c->engagement];
            });

        // 3. UGC Performance (mapped to Paid vs Unpaid)
        $campaignUgcPerformance = (clone $campaignQuery)
            ->select('paid_or_not', \Illuminate\Support\Facades\DB::raw('sum(total_shares) as shares'), \Illuminate\Support\Facades\DB::raw('count(id) as count'))
            ->groupBy('paid_or_not')
            ->get()
            ->map(function($c) {
                return [
                    'label' => $c->paid_or_not ?: 'Unspecified',
                    'shares' => (int) $c->shares,
                    'count' => (int) $c->count
                ];
            });

        // 4. Daily Trends
        $campaignDailyTrends = (clone $campaignQuery)
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), 
                \Illuminate\Support\Facades\DB::raw('sum(total_shares) as shares'), 
                \Illuminate\Support\Facades\DB::raw('sum(total_clicks) as clicks'),
                \Illuminate\Support\Facades\DB::raw('sum(registrations) as submissions')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($c) {
                return [
                    'date' => $c->date ?: 'N/A',
                    'shares' => (int) $c->shares,
                    'clicks' => (int) $c->clicks,
                    'submissions' => (int) $c->submissions
                ];
            });

        // 5. Reach by Client
        $campaignReachByClient = (clone $campaignQuery)
            ->select('client_name', \Illuminate\Support\Facades\DB::raw('sum(potential_reach) as reach'))
            ->groupBy('client_name')
            ->orderByDesc('reach')
            ->limit(10)
            ->get()
            ->map(function($c) {
                return ['name' => $c->client_name, 'reach' => (int) $c->reach];
            });

        // Client Summary KPIs - Using filtered campaigns dynamically
        $activeClientsFiltered = (clone $campaignQuery)->distinct('client_name')->pluck('client_name');
        
        $clientsCount = $activeClientsFiltered->count(); // Unique clients in filtered campaigns
        $summaryCampaignsCount = $totalCount; // Total campaigns in filtered campaigns
        $summarySharesCount = $totalShares; // Total shares from filtered campaigns
        $summarySubmissionsCount = $totalSubmissions; // Total form submissions from filtered campaigns
        $creditsConsumed = $activeClientsFiltered->map(function($clientName) {
            return \App\Models\Client::where('company', $clientName)->sum('consumed');
        })->sum(); // Credits consumed by all unique active filtered clients

        // Client Summary Charts Data (using filtered campaign query)
        // 1. Campaign Performance
        $summaryCampaignPerformance = (clone $campaignQuery)
            ->orderByDesc('total_shares')
            ->limit(10)
            ->get(['campaign_title', 'total_shares'])
            ->map(function($c) {
                return ['name' => $c->campaign_title, 'shares' => (int) $c->total_shares];
            });

        // 2. Top Clients
        $summaryTopClients = (clone $campaignQuery)
            ->select('client_name', \Illuminate\Support\Facades\DB::raw('sum(total_shares) as total_shares'))
            ->groupBy('client_name')
            ->orderByDesc('total_shares')
            ->limit(10)
            ->get()
            ->map(function($c) {
                return ['name' => $c->client_name, 'shares' => (int) $c->total_shares];
            });

        // 3. Shares Trend
        $summarySharesTrend = (clone $campaignQuery)
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('sum(total_shares) as total_shares'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($c) {
                return ['date' => $c->date ?: 'N/A', 'shares' => (int) $c->total_shares];
            });

        // 4. Form Submissions Trend
        $summarySubmissionsTrend = (clone $campaignQuery)
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('sum(registrations) as total_submissions'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($c) {
                return ['date' => $c->date ?: 'N/A', 'submissions' => (int) $c->total_submissions];
            });

        // 5. Credits Consumed
        $summaryCreditsByClient = Client::whereIn('company', $activeClientsFiltered)
            ->where('consumed', '>', 0)
            ->select('company', 'email', 'consumed')
            ->orderByDesc('consumed')
            ->limit(10)
            ->get()
            ->map(function($c) {
                return [
                    'name' => $c->company ?: $c->email,
                    'consumed' => (float) $c->consumed
                ];
            });

        // Aggregate all analytics
        $analytics = [
            'campaign' => [
                'engagementBreakdown' => $campaignEngagementBreakdown,
                'topEngaged' => $campaignTopEngaged,
                'ugcPerformance' => $campaignUgcPerformance,
                'dailyTrends' => $campaignDailyTrends,
                'reachByClient' => $campaignReachByClient,
            ],
            'client' => [
                'campaignPerformance' => $summaryCampaignPerformance,
                'topClients' => $summaryTopClients,
                'sharesTrend' => $summarySharesTrend,
                'submissionsTrend' => $summarySubmissionsTrend,
                'creditsConsumed' => $summaryCreditsByClient,
                'funnel' => [
                    'campaigns' => $summaryCampaignsCount,
                    'shares' => $summarySharesCount,
                    'submissions' => $summarySubmissionsCount,
                ]
            ]
        ];

        // Unique clients for filter dropdown (filtered by date range)
        $uniqueClients = \App\Models\RemovedCampaign::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->select('client_name')->distinct()->pluck('client_name');
        $uniqueEmails = ClientSummary::whereIn('client_name', $activeClients)->whereNotNull('email')->select('email')->distinct()->pluck('email');
        $uniqueDates = \App\Models\RemovedCampaign::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($row) {
                return Carbon::parse($row->date);
            });
        $uniqueCampaignIds = \App\Models\RemovedCampaign::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->select('id', 'client_name')->distinct()->orderBy('id', 'desc')->get();
        $uniqueCampaignNames = \App\Models\RemovedCampaign::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->select('campaign_title', 'client_name')->distinct()->orderBy('campaign_title')->get();
        $uniquePaidStatuses = \App\Models\RemovedCampaign::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->select('paid_or_not')->whereNotNull('paid_or_not')->distinct()->pluck('paid_or_not');

        $emailsByClient = ClientSummary::whereNotNull('email')
            ->select('client_name', 'email')
            ->get()
            ->groupBy('client_name')
            ->map(fn ($rows) => $rows->pluck('email')->unique()->values());

        $filterRecords = \App\Models\RemovedCampaign::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->select('id', 'client_name', 'campaign_title', 'paid_or_not', 'created_at')
            ->get()
            ->flatMap(function ($campaign) use ($emailsByClient) {
                $emails = $emailsByClient->get($campaign->client_name, collect());
                $emailList = $emails->isNotEmpty() ? $emails : collect([null]);

                return $emailList->map(fn ($email) => [
                    'client_name' => $campaign->client_name,
                    'email' => $email,
                    'campaign_date' => $campaign->created_at
                        ? Carbon::parse($campaign->created_at)->toDateString()
                        : null,
                    'campaign_id' => (string) $campaign->id,
                    'campaign_name' => $campaign->campaign_title,
                    'paid_or_not' => $campaign->paid_or_not,
                ]);
            })
            ->values();

        return view('dashboard.campaign_report', compact(
            'from', 'to', 'uniqueClients', 'uniqueEmails', 'uniqueDates', 'uniqueCampaignIds', 'uniqueCampaignNames', 'uniquePaidStatuses',
            'campaigns', 'clients', 'totalReach', 'totalEngagement', 'totalShares', 'totalClicks', 'totalLikes', 'totalComments', 'ugcRatio', 'totalSubmissions',
            'clientsCount', 'summaryCampaignsCount', 'summarySharesCount', 'summarySubmissionsCount', 'creditsConsumed',
            'analytics', 'averageRoi', 'filterRecords'
        ));
    }
    public function showSync() {
        return view('dashboard.sync');
    }

    public function import(Request $request) {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('excel_file');
        $this->importService->import($file->getRealPath());

        return redirect()->route('sync.show')->with('success', 'Excel file sheets successfully imported!');
    }

    public function syncGoogleSheet(Request $request) {
        $request->validate([
            'sheet_url' => 'required|url'
        ]);

        $url = $request->input('sheet_url');

        // Extract Spreadsheet ID
        if (preg_match('/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            $spreadsheetId = $matches[1];
            $exportUrl = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=xlsx";

            try {
                // Fetch the xlsx file from Google Sheets
                $response = \Illuminate\Support\Facades\Http::timeout(60)->get($exportUrl);

                if ($response->successful()) {
                    $tempPath = tempnam(sys_get_temp_dir(), 'sheet_sync_');
                    file_put_contents($tempPath, $response->body());

                    $this->importService->import($tempPath);
                    unlink($tempPath); // Clean up temp file

                    return redirect()->route('sync.show')->with('success', 'Google Sheet successfully synchronized with database!');
                } else {
                    return redirect()->route('sync.show')->with('error', 'Failed to fetch the spreadsheet from Google Sheets. Ensure it is shared with "Anyone with the link can view" option.');
                }
            } catch (\Exception $e) {
                return redirect()->route('sync.show')->with('error', 'An error occurred during synchronization: ' . $e->getMessage());
            }
        }

        return redirect()->route('sync.show')->with('error', 'Invalid Google Sheets URL format. Please make sure the URL contains the spreadsheet ID.');
    }

    public function exportConsumption(Request $request) {
        $headers = [
            'ID', 'Email', 'Company', 'Account Created', 'Last Created Post', 
            'Plan', 'Status', 'Consumed', 'Remaining', 'Last Shared', 'Clicks', 'Posts', 'Comments'
        ];

        return $this->exportService->export(
            'client_consumption.csv',
            $headers,
            Client::query(),
            function ($client) {
                return [
                    $client->id, $client->email, $client->company ?? 'N/A',
                    $client->account_created->toDateString(), 
                    $client->last_created_post ? $client->last_created_post->toDateString() : 'N/A',
                    $client->plan, $client->status, $client->consumed, $client->remaining,
                    $client->last_shared ? $client->last_shared->toDateString() : 'N/A',
                    $client->clicks, $client->posts, $client->comments
                ];
            }
        );
    }

    public function exportCampaigns(Request $request) {
        $headers = [
            'Client Name', 'Company', 'Campaign ID', 'Campaign Title', 'Paid',
            'Potential Reach', 'Total Shares', 'Total Clicks', 'Total Comments', 
            'Total Likes', 'Total Posts', 'ROI'
        ];

        return $this->exportService->export(
            'removed_campaigns.csv',
            $headers,
            \App\Models\RemovedCampaign::query(),
            function ($camp) {
                return [
                    $camp->client_name, $camp->company ?? 'N/A', $camp->id,
                    $camp->campaign_title, $camp->paid_or_not ?? 'N/A',
                    $camp->potential_reach, $camp->total_shares, $camp->total_clicks,
                    $camp->total_comments, $camp->total_likes, $camp->total_posts,
                    number_format($camp->roi, 2) . 'x'
                ];
            }
        );
    }

    private function getDateRange(Request $request): array {
        $quickSelect = $request->input('quick_select');
        $from = $request->input('from');
        $to = $request->input('to');

        // Check if manual from/to are set and different from selected quick select computed range
        if ($request->filled('from') && $request->filled('to')) {
            if (!$quickSelect) {
                return [$from, $to];
            }
            [$qsFrom, $qsTo] = $this->calculateQuickSelectDates($quickSelect);
            if ($from !== $qsFrom || $to !== $qsTo) {
                // Manual overrides: clear quick_select active button status
                $request->offsetUnset('quick_select');
                return [$from, $to];
            }
            return [$qsFrom, $qsTo];
        }

        $quickSelect = $quickSelect ?: 'This Week';
        return $this->calculateQuickSelectDates($quickSelect);
    }

    private function calculateQuickSelectDates(string $quickSelect): array {
        switch ($quickSelect) {
            case 'This Week':
                $from = now()->startOfWeek();
                $to = now()->endOfWeek();
                break;
            case 'Last Week':
                $from = now()->subWeek()->startOfWeek();
                $to = now()->subWeek()->endOfWeek();
                break;
            case 'This Month':
                $from = now()->startOfMonth();
                $to = now()->endOfMonth();
                break;
            case 'Last Month':
                $from = now()->subMonth()->startOfMonth();
                $to = now()->subMonth()->endOfMonth();
                break;
            case 'All Time':
                $from = Carbon::parse('2020-01-01');
                $to = Carbon::parse('2030-12-31');
                break;
            default:
                $from = now()->startOfWeek();
                $to = now()->endOfWeek();
                break;
        }

        return [$from->toDateString(), $to->toDateString()];
    }

    private function applyCustomCondition($query, $customInput, $isCampaignQuery) {
        if (empty($customInput)) {
            return;
        }

        // Match format: column operator value, e.g. "likes > 10"
        if (preg_match('/^([a-zA-Z_0-9\-\s]+)\s*([>=<!]+)\s*([0-9\.\-]+)$/', trim($customInput), $matches)) {
            $columnAlias = strtolower(str_replace(' ', '', $matches[1]));
            $operator = trim($matches[2]);
            $value = (float) $matches[3];

            $validOperators = ['=', '>', '<', '>=', '<=', '!=', '<>'];
            if (!in_array($operator, $validOperators)) {
                return;
            }

            // Mappings for Campaigns (mapped to Removed Campaigns columns)
            $campaignMap = [
                'likes' => 'total_likes',
                'totallikes' => 'total_likes',
                'shares' => 'total_shares',
                'totalshares' => 'total_shares',
                'clicks' => 'total_clicks',
                'totalclicks' => 'total_clicks',
                'comments' => 'total_comments',
                'totalcomments' => 'total_comments',
                'reach' => 'potential_reach',
                'potentialreach' => 'potential_reach',
                'posts' => 'total_posts',
                'totalposts' => 'total_posts',
                'submissions' => 'registrations',
                'formsubmissions' => 'registrations',
                'registrations' => 'registrations',
                'roi' => 'roi'
            ];

            // Mappings for Client Summaries
            $summaryMap = [
                'campaigns' => 'campaigns_count',
                'campaignscount' => 'campaigns_count',
                'sharescount' => 'shares_count',
                'submissionscount' => 'form_submissions_count',
                'credits' => 'credits_consumed',
                'creditsconsumed' => 'credits_consumed'
            ];

            if ($isCampaignQuery) {
                // If it is a campaign column
                if (isset($campaignMap[$columnAlias])) {
                    $query->where($campaignMap[$columnAlias], $operator, $value);
                } elseif (in_array($columnAlias, ['total_likes', 'total_shares', 'total_clicks', 'total_comments', 'potential_reach', 'total_posts', 'registrations', 'roi'])) {
                    $query->where($columnAlias, $operator, $value);
                }
                // If it is a summary column, filter campaigns via clientSummary relation
                elseif (isset($summaryMap[$columnAlias])) {
                    $dbCol = $summaryMap[$columnAlias];
                    $query->whereHas('clientSummary', function ($q) use ($dbCol, $operator, $value) {
                        $q->where($dbCol, $operator, $value);
                    });
                } elseif (in_array($columnAlias, ['campaigns_count', 'shares_count', 'form_submissions_count', 'credits_consumed'])) {
                    $query->whereHas('clientSummary', function ($q) use ($columnAlias, $operator, $value) {
                        $q->where($columnAlias, $operator, $value);
                    });
                }
            } else {
                // Client Summary query
                // If it is a summary column
                if (isset($summaryMap[$columnAlias])) {
                    $query->where($summaryMap[$columnAlias], $operator, $value);
                } elseif (in_array($columnAlias, ['campaigns_count', 'shares_count', 'form_submissions_count', 'credits_consumed'])) {
                    $query->where($columnAlias, $operator, $value);
                }
                // If it is a campaign column, filter summaries via subquery on removed_campaigns
                elseif (isset($campaignMap[$columnAlias])) {
                    $dbCol = $campaignMap[$columnAlias];
                    $query->whereIn('client_name', function ($q) use ($dbCol, $operator, $value) {
                        $q->select('client_name')->from('removed_campaigns')->where($dbCol, $operator, $value);
                    });
                } elseif (in_array($columnAlias, ['total_likes', 'total_shares', 'total_clicks', 'total_comments', 'potential_reach', 'total_posts', 'registrations', 'roi'])) {
                    $query->whereIn('client_name', function ($q) use ($columnAlias, $operator, $value) {
                        $q->select('client_name')->from('removed_campaigns')->where($columnAlias, $operator, $value);
                    });
                }
            }
        }
    }

    private function applyRemovedCustomCondition($query, $customInput) {
        if (empty($customInput)) {
            return;
        }

        // Match format: column operator value, e.g. "likes > 10"
        if (preg_match('/^([a-zA-Z_0-9\-\s]+)\s*([>=<!]+)\s*([0-9\.\-]+)$/', trim($customInput), $matches)) {
            $columnAlias = strtolower(str_replace(' ', '', $matches[1]));
            $operator = trim($matches[2]);
            $value = (float) $matches[3];

            $validOperators = ['=', '>', '<', '>=', '<=', '!=', '<>'];
            if (!in_array($operator, $validOperators)) {
                return;
            }

            // Mappings for RemovedCampaigns
            $removedMap = [
                'reach' => 'potential_reach',
                'potentialreach' => 'potential_reach',
                'shares' => 'total_shares',
                'totalshares' => 'total_shares',
                'reachpershare' => 'reach_per_share',
                'reach_per_share' => 'reach_per_share',
                'clicks' => 'total_clicks',
                'totalclicks' => 'total_clicks',
                'clickspershare' => 'clicks_per_share',
                'clicks_per_share' => 'clicks_per_share',
                'comments' => 'total_comments',
                'totalcomments' => 'total_comments',
                'commentspershare' => 'comments_per_share',
                'comments_per_share' => 'comments_per_share',
                'likes' => 'total_likes',
                'totallikes' => 'total_likes',
                'likespershare' => 'likes_per_share',
                'likes_per_share' => 'likes_per_share',
                'posts' => 'total_posts',
                'totalposts' => 'total_posts',
                'reshare' => 'reshare',
                'registrations' => 'registrations',
                'emv' => 'emv',
                'directsavings' => 'direct_savings',
                'direct_savings' => 'direct_savings',
                'totalreturn' => 'total_return',
                'total_return' => 'total_return',
                'roi' => 'roi',
                'registrationpershare' => 'registration_per_share',
                'registration_per_share' => 'registration_per_share',
            ];

            if (isset($removedMap[$columnAlias])) {
                $query->where($removedMap[$columnAlias], $operator, $value);
            } elseif (in_array($columnAlias, array_values($removedMap))) {
                $query->where($columnAlias, $operator, $value);
            }
        }
    }
}
