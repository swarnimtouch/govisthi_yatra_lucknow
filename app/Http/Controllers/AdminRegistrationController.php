<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminRegistrationController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total'   => EventRegistration::count(),
            'today'   => EventRegistration::whereDate('created_at', today())->count(),
            'cities'  => EventRegistration::distinct('city')->count('city'),
            'banners' => EventRegistration::whereNotNull('generated_banner')->count(),
        ];

        $cityBreakdown = EventRegistration::select('city', DB::raw('count(*) as total'))
            ->groupBy('city')
            ->orderByDesc('total')
            ->get();

        $recentRegistrations = EventRegistration::latest()->take(5)->get();

        $dailyTrend = EventRegistration::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact('stats', 'cityBreakdown', 'recentRegistrations', 'dailyTrend'));
    }

    public function index(Request $request)
    {
        $query = EventRegistration::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('full_name', 'like', "%$s%")
                    ->orWhere('mobile', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $registrations = $query->paginate(20)->withQueryString();
        $cities        = EventRegistration::distinct()->pluck('city')->sort()->values();

        return view('admin.registrations.index', compact('registrations', 'cities'));
    }

    public function show(EventRegistration $registration)
    {
        return view('admin.registrations.show', compact('registration'));
    }

    public function destroy(EventRegistration $registration)
    {
        // Delete cropped image from S3
        if (!empty($registration->photo_cropped)
            && Storage::disk('s3')->exists($registration->photo_cropped)) {

            Storage::disk('s3')->delete($registration->photo_cropped);
        }

        // Delete generated banner from S3
        if (!empty($registration->generated_banner)
            && Storage::disk('s3')->exists($registration->generated_banner)) {

            Storage::disk('s3')->delete($registration->generated_banner);
        }

        // Delete database record
        $registration->delete();

        return redirect()
            ->route('admin.registrations.index')
            ->with('success', 'Registration deleted successfully.');
    }

    public function export(Request $request)
    {
        $filename = 'registrations_' . now()->format('Y-m-d_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RegistrationsExport($request),
            $filename
        );
    }

}
