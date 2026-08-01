<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ProfessorAccountCreatedMailable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProfessorController extends Controller
{
    public function index()
    {
        $professors = User::query()
            ->where('role', User::ROLE_PROF)
            ->orderByDesc('id')
            ->paginate(15);

        $totalProfessors = User::query()
            ->where('role', User::ROLE_PROF)
            ->count();

        $pendingPasswordChange = User::query()
            ->where('role', User::ROLE_PROF)
            ->where(
                'must_change_password',
                true
            )
            ->count();

        $activeProfessors = User::query()
            ->where('role', User::ROLE_PROF)
            ->where('is_active', true)
            ->count();

        return view(
            'admin.professors.index',
            compact(
                'professors',
                'totalProfessors',
                'pendingPasswordChange',
                'activeProfessors'
            )
        );
    }

    public function create()
    {
        return view(
            'admin.professors.create'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],
            ],
            [
                'name.required' =>
                    'Le nom complet est obligatoire.',
                'email.required' =>
                    'L’adresse e-mail est obligatoire.',
                'email.email' =>
                    'L’adresse e-mail est invalide.',
                'email.unique' =>
                    'Cette adresse e-mail est déjà utilisée.',
            ]
        );

        $temporaryPassword =
            $this->generateTemporaryPassword();

        DB::beginTransaction();

        try {
            $professor = new User();

            $professor->forceFill([
                'name' => trim(
                    $validated['name']
                ),
                'email' => mb_strtolower(
                    trim($validated['email'])
                ),
                'password' => Hash::make(
                    $temporaryPassword
                ),
                'role' => User::ROLE_PROF,
                'is_active' => true,
                'email_verified_at' => now(),
                'must_change_password' => true,
                'temporary_password_expires_at' =>
                    now()->addHours(48),
                'temporary_password_sent_at' =>
                    now(),
                'password_changed_at' => null,
                'created_by' => auth()->id(),
            ]);

            $professor->save();

            Mail::to($professor->email)
                ->send(
                    new ProfessorAccountCreatedMailable(
                        $professor,
                        $temporaryPassword
                    )
                );

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            report($exception);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Le compte n’a pas été créé, '
                    . 'car l’e-mail des accès '
                    . 'n’a pas pu être envoyé. '
                    . 'Vérifiez la configuration MAIL.'
                );
        }

        return redirect()
            ->route(
                'admin.professors.index'
            )
            ->with(
                'success',
                'Le compte professeur a été créé '
                . 'et les accès ont été envoyés à '
                . $professor->email
                . '.'
            );
    }

    public function resend(User $professor)
    {
        abort_unless(
            $professor->role
                === User::ROLE_PROF,
            404
        );

        $temporaryPassword =
            $this->generateTemporaryPassword();

        DB::beginTransaction();

        try {
            $professor->forceFill([
                'password' => Hash::make(
                    $temporaryPassword
                ),
                'is_active' => true,
                'must_change_password' => true,
                'temporary_password_expires_at' =>
                    now()->addHours(48),
                'temporary_password_sent_at' =>
                    now(),
                'password_changed_at' => null,
            ])->save();

            Mail::to($professor->email)
                ->send(
                    new ProfessorAccountCreatedMailable(
                        $professor,
                        $temporaryPassword
                    )
                );

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            report($exception);

            return back()->with(
                'error',
                'Les nouveaux accès n’ont pas '
                . 'pu être envoyés. Vérifiez '
                . 'la configuration MAIL.'
            );
        }

        return back()->with(
            'success',
            'Un nouveau mot de passe temporaire '
            . 'a été envoyé à '
            . $professor->email
            . '.'
        );
    }

    private function generateTemporaryPassword(): string
    {
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $digits = '23456789';
        $symbols = '!@#$%';

        $password = [
            $upper[random_int(
                0,
                strlen($upper) - 1
            )],
            $lower[random_int(
                0,
                strlen($lower) - 1
            )],
            $digits[random_int(
                0,
                strlen($digits) - 1
            )],
            $symbols[random_int(
                0,
                strlen($symbols) - 1
            )],
        ];

        $allCharacters =
            $upper
            . $lower
            . $digits
            . $symbols;

        while (count($password) < 12) {
            $password[] =
                $allCharacters[random_int(
                    0,
                    strlen($allCharacters) - 1
                )];
        }

        for (
            $index = count($password) - 1;
            $index > 0;
            $index--
        ) {
            $randomIndex = random_int(
                0,
                $index
            );

            [
                $password[$index],
                $password[$randomIndex],
            ] = [
                $password[$randomIndex],
                $password[$index],
            ];
        }

        return implode('', $password);
    }
}
