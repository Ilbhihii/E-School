<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ParentController extends Controller
{
    public function index()
    {
        $parents = User::where('role', 'parent')
            ->orderBy('name')
            ->get();

        $parents->each(function (User $parent) {
            $parent->setAttribute(
                'children_list',
                $this->childrenOf($parent->id)
            );
        });

        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get();

        return view(
            'admin.parents.index',
            compact('parents', 'students')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:190',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'relationship' => [
                'required',
                'string',
                'max:40',
            ],
            'student_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'student_ids.*' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ]);

        $studentIds = User::where('role', 'student')
            ->whereIn('id', $validated['student_ids'])
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->values();

        $requestedStudentIds = array_unique(
            array_map('intval', $validated['student_ids'])
        );

        if ($studentIds->count() !== count($requestedStudentIds)) {
            return back()
                ->withInput()
                ->withErrors([
                    'student_ids' =>
                        'Un ou plusieurs comptes sélectionnés '
                        . 'ne sont pas des étudiants.',
                ]);
        }

        DB::transaction(function () use (
            $validated,
            $studentIds
        ) {
            $parent = User::create([
                'name' => trim($validated['name']),
                'email' => strtolower(
                    trim($validated['email'])
                ),
                'password' => Hash::make(
                    $validated['password']
                ),
                'role' => 'parent',
                'is_active' => true,
                'is_paid' => true,
            ]);

            foreach ($studentIds as $studentId) {
                DB::table('parent_student')->insert([
                    'parent_id' => $parent->id,
                    'student_id' => $studentId,
                    'relationship' => trim(
                        $validated['relationship']
                    ),
                    'is_primary' => true,
                    'can_view_schedule' => true,
                    'can_view_absences' => true,
                    'can_view_assignments' => true,
                    'can_view_results' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('admin.parents.index')
            ->with(
                'success',
                'Le compte Parent a été créé et associé '
                . 'aux enfants.'
            );
    }

    /**
     * Modifie uniquement le compte Parent.
     * Les associations avec les enfants restent gérées séparément
     * depuis la carte du parent afin d'éviter toute suppression
     * accidentelle d'une relation parent/enfant.
     */
    public function update(Request $request, User $parent)
    {
        $this->assertParent($parent);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],
            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users', 'email')
                    ->ignore($parent->id),
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'name.required' =>
                'Le nom du parent est obligatoire.',
            'email.required' =>
                'L’adresse e-mail est obligatoire.',
            'email.email' =>
                'L’adresse e-mail n’est pas valide.',
            'email.unique' =>
                'Cette adresse e-mail est déjà utilisée.',
            'is_active.required' =>
                'Veuillez choisir le statut du compte.',
            'password.min' =>
                'Le nouveau mot de passe doit contenir '
                . 'au moins 8 caractères.',
            'password.confirmed' =>
                'La confirmation du mot de passe ne correspond pas.',
        ]);

        DB::transaction(function () use (
            $parent,
            $validated
        ) {
            $parent->name = trim($validated['name']);
            $parent->email = strtolower(
                trim($validated['email'])
            );
            $parent->is_active = (bool) $validated['is_active'];

            if (!empty($validated['password'])) {
                $parent->password = Hash::make(
                    $validated['password']
                );
            }

            // Sécurité : ce compte doit rester un compte Parent.
            $parent->role = 'parent';
            $parent->save();
        });

        return redirect()
            ->route('admin.parents.index')
            ->with(
                'success',
                'Le compte Parent a été modifié avec succès.'
            );
    }

    public function linkChild(
        Request $request,
        User $parent
    ) {
        $this->assertParent($parent);

        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'relationship' => [
                'required',
                'string',
                'max:40',
            ],
        ]);

        $student = User::whereKey(
            $validated['student_id']
        )
            ->where('role', 'student')
            ->first();

        abort_unless(
            $student,
            422,
            'Le compte sélectionné n’est pas un étudiant.'
        );

        DB::table('parent_student')->updateOrInsert(
            [
                'parent_id' => $parent->id,
                'student_id' => $student->id,
            ],
            [
                'relationship' => trim(
                    $validated['relationship']
                ),
                'is_primary' => $request->boolean(
                    'is_primary'
                ),
                'can_view_schedule' => $request->boolean(
                    'can_view_schedule'
                ),
                'can_view_absences' => $request->boolean(
                    'can_view_absences'
                ),
                'can_view_assignments' => $request->boolean(
                    'can_view_assignments'
                ),
                'can_view_results' => $request->boolean(
                    'can_view_results'
                ),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with(
            'success',
            'L’enfant a été associé au parent.'
        );
    }

    public function unlinkChild(
        User $parent,
        User $student
    ) {
        $this->assertParent($parent);

        DB::table('parent_student')
            ->where('parent_id', $parent->id)
            ->where('student_id', $student->id)
            ->delete();

        return back()->with(
            'success',
            'Association supprimée.'
        );
    }

    public function destroy(User $parent)
    {
        $this->assertParent($parent);

        /*
         * La suppression du parent supprime automatiquement
         * ses lignes parent_student grâce au cascadeOnDelete.
         * Les comptes étudiants eux-mêmes ne sont pas supprimés.
         */
        $parent->delete();

        return back()->with(
            'success',
            'Compte Parent supprimé. Les comptes des enfants '
            . 'ont été conservés.'
        );
    }

    private function assertParent(User $parent): void
    {
        abort_unless($parent->role === 'parent', 404);
    }

    private function childrenOf(int $parentId)
    {
        return User::query()
            ->select([
                'users.*',
                'parent_student.relationship '
                . 'as parent_relationship',
            ])
            ->join(
                'parent_student',
                'parent_student.student_id',
                '=',
                'users.id'
            )
            ->where(
                'parent_student.parent_id',
                $parentId
            )
            ->where('users.role', 'student')
            ->orderBy('users.name')
            ->get();
    }
}
