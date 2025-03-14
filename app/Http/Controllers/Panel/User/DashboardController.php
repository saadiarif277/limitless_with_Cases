<?php

namespace App\Http\Controllers\Panel\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ReferralStatus;
use App\Repositories\AppointmentRepository;
use App\Repositories\ReferralRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private AppointmentRepository $appointmentRepository;
    private ReferralRepository $referralRepository;

    /**
     * Create a new instance.
     */
    public function __construct(
        AppointmentRepository $appointmentRepository,
        ReferralRepository $referralRepository
    ) {
        $this->appointmentRepository = $appointmentRepository;
        $this->referralRepository = $referralRepository;
    }
    
    /**
     * Display the user dashboard.
     */
    public function index(Request $request): Response
    {
        $colors = [
            'primary' => [
                "#1363DF", // 'DEFAULT'
                "#B1CDF8", // '50'
                "#9EC1F7", // '100'
                "#79A9F4", // '200'
                "#5391F0", // '300'
                "#2E79ED", // '400'
                "#1363DF", // '500'
                "#0F4CAB", // '600'
                "#0A3578", // '700'
                "#061E44", // '800'
                "#010710", // '900'
                "#000000", // '950'
            ],
            'secondary' => [
                '#F59E0B',
                '#FCE4BB',
                '#FBDCA8',
                '#FACD81',
                '#F8BD59',
                '#F7AE32',
                '#F59E0B',
                '#C07C08',
                '#8A5906',
                '#543603',
                '#1E1401',
                '#030200'
            ],
            'tertiary' => [
                '#EC4899',
                '#FDEEF6',
                '#FBDCEB',
                '#F8B7D7',
                '#F492C2',
                '#F06DAE',
                '#EC4899',
                '#E4187D',
                '#B11261',
                '#7F0D45',
                '#4C0829',
                '#32051B'
            ],
        ];

        $stats = [
            [
                'title' => 'Referral Status Distribution',
                'component' => 'v-chart-doughnut',
                // 'data' => ReferralStatus::query()
                //     ->leftJoin('referrals', 'referral_statuses.referral_status_id', '=', 'referrals.referral_status_id')
                //     ->whereIn('referrals.referral_id', $this->referralRepository->getItems($request)->pluck('referral_id')->toArray())
                //     ->selectRaw('referral_statuses.*, COUNT(referrals.referral_id) as referrals_count')
                //     //->groupBy('referral_statuses.referral_status_id,referral_statuses.name')
                //     ->orderByDesc('referrals_count')
                //     ->get()
                //     ->reduce(function ($stats, $referralStatus, $index) use ($colors) {
                //         $stats['labels'][] = $referralStatus->name;
                //         $stats['datasets'][0]['data'][] = $referralStatus->referrals_count;
                //         $stats['datasets'][0]['backgroundColor'][] = $colors['secondary'][$index] ?? 'defaultColor';
                //         return $stats;
                //     }, [
                'data' => ReferralStatus::query()
    ->leftJoin('referrals', 'referral_statuses.referral_status_id', '=', 'referrals.referral_status_id')
    ->whereIn('referrals.referral_id', $this->referralRepository->getItems($request)->pluck('referral_id')->toArray())
    ->select('referral_statuses.referral_status_id', 'referral_statuses.name')
    ->selectRaw('COUNT(referrals.referral_id) as referrals_count')
    ->groupBy('referral_statuses.referral_status_id', 'referral_statuses.name') // Group by each column explicitly
    ->orderByDesc('referrals_count')
    ->get()
    ->reduce(function ($stats, $referralStatus, $index) use ($colors) {
        $stats['labels'][] = $referralStatus->name;
        $stats['datasets'][0]['data'][] = $referralStatus->referrals_count;
        $stats['datasets'][0]['backgroundColor'][] = $colors['secondary'][$index] ?? 'defaultColor';
        return $stats;
    }, [
                        'labels' => [],
                        'datasets' => [[
                            'data' => [],
                            'backgroundColor' => [],
                        ]],
                    ]),
                'options' => [],
            ],
        ];        

        return Inertia::render('panel/user/dashboard', [
            'appointments' => AppointmentResource::collection(
                Appointment::query()
                    ->with('appointmentType')
                    ->with('clinic')        
                    ->whereDate('start_date', today())
                    ->whereIn('appointments.appointment_id', $this->appointmentRepository->getItems($request)->pluck('referral_id')->toArray())
                    ->limit(4)
                    ->orderBy('start_date', 'desc')
                    ->get()
            ),
            'stats' => $stats,
        ]);
    }
}
