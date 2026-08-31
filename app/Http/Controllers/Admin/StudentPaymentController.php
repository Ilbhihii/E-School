<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $studentsQuery = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->with(['studentPayments' => function ($query) {
                $query->orderByDesc('paid_at')->orderByDesc('id');
            }]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('plan_type')) {
            $planType = $request->input('plan_type');
            $studentsQuery->whereHas('studentPayments', function ($query) use ($planType, $today) {
                $query->validOn($today)->where('plan_type', $planType);
            });
        }

        if ($request->input('payment_status') === 'paid') {
            $studentsQuery->where(function ($query) use ($today) {
                $query->whereHas('studentPayments', fn ($payments) => $payments->validOn($today))
                    ->orWhere(function ($legacy) {
                        $legacy->whereDoesntHave('studentPayments')->where('is_paid', true);
                    });
            });
        } elseif ($request->input('payment_status') === 'unpaid') {
            $studentsQuery->where(function ($query) use ($today) {
                $query->whereDoesntHave('studentPayments', fn ($payments) => $payments->validOn($today))
                    ->where(function ($legacy) {
                        $legacy->whereHas('studentPayments')
                            ->orWhere('is_paid', false);
                    });
            });
        }

        $students = $studentsQuery
            ->orderBy('name')
            ->paginate(20)
            ->appends($request->query());

        $totalStudents = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->count();

        $paidStudents = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->where(function ($query) use ($today) {
                $query->whereHas('studentPayments', fn ($payments) => $payments->validOn($today))
                    ->orWhere(function ($legacy) {
                        $legacy->whereDoesntHave('studentPayments')->where('is_paid', true);
                    });
            })
            ->count();

        $annualStudents = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->whereHas('studentPayments', function ($query) use ($today) {
                $query->validOn($today)->where('plan_type', StudentPayment::PLAN_ANNUAL);
            })
            ->count();

        $fourMonthStudents = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->whereHas('studentPayments', function ($query) use ($today) {
                $query->validOn($today)->where('plan_type', StudentPayment::PLAN_FOUR_MONTHS);
            })
            ->count();

        $unpaidStudents = max(0, $totalStudents - $paidStudents);

        return view('admin.student-payments.index', compact(
            'students',
            'totalStudents',
            'paidStudents',
            'unpaidStudents',
            'annualStudents',
            'fourMonthStudents'
        ));
    }

    public function create(Request $request)
    {
        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $selectedStudent = null;
        if ($request->filled('student')) {
            $selectedStudent = User::query()
                ->where('role', User::ROLE_STUDENT)
                ->find((int) $request->query('student'));
        }

        return view('admin.student-payments.create', compact('students', 'selectedStudent'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_STUDENT)),
            ],
            'plan_type' => ['required', Rule::in([
                StudentPayment::PLAN_FOUR_MONTHS,
                StudentPayment::PLAN_ANNUAL,
            ])],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'paid_at' => ['required', 'date'],
            'starts_at' => ['nullable', 'date'],
            'payment_method' => ['nullable', Rule::in(['cash', 'bank_transfer', 'paypal', 'other'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $startsAt = Carbon::parse($data['starts_at'] ?: $data['paid_at'])->startOfDay();
        $expiresAt = $data['plan_type'] === StudentPayment::PLAN_ANNUAL
            ? $startsAt->copy()->addYearNoOverflow()
            : $startsAt->copy()->addMonthsNoOverflow(4);

        $student = User::findOrFail($data['user_id']);

        DB::transaction(function () use ($data, $student, $startsAt, $expiresAt) {
            StudentPayment::create([
                'user_id' => $student->id,
                'plan_type' => $data['plan_type'],
                'amount' => $data['amount'] ?? null,
                'paid_at' => $data['paid_at'],
                'starts_at' => $startsAt->toDateString(),
                'expires_at' => $expiresAt->toDateString(),
                'payment_method' => $data['payment_method'] ?? null,
                'status' => StudentPayment::STATUS_PAID,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Compatibilité avec les anciennes parties de l'application.
            $student->forceFill([
                'is_paid' => true,
                'payment_date' => $data['paid_at'],
            ])->save();
        });

        return redirect()
            ->route('admin.student-payments.show', $student)
            ->with('success', 'Paiement enregistré avec succès.');
    }

    public function show(User $student)
    {
        abort_unless($student->role === User::ROLE_STUDENT, 404);

        $payments = $student->studentPayments()
            ->with('creator')
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->get();

        $currentPayment = $payments->first(fn (StudentPayment $payment) => $payment->isCurrentlyValid());

        return view('admin.student-payments.show', compact(
            'student',
            'payments',
            'currentPayment'
        ));
    }

    public function cancel(StudentPayment $payment)
    {
        $student = $payment->student;

        if ($payment->status !== StudentPayment::STATUS_CANCELLED) {
            $payment->update(['status' => StudentPayment::STATUS_CANCELLED]);
        }

        $hasValidPayment = $student->studentPayments()
            ->validOn(now()->toDateString())
            ->exists();

        $student->forceFill([
            'is_paid' => $hasValidPayment,
        ])->save();

        return back()->with('success', 'Le paiement a été annulé. L’historique est conservé.');
    }
}
