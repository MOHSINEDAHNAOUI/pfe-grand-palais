<?php
namespace App\Http\Controllers\Api\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
 
class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // Manager peut voir les clients seulement
        $user = auth()->user();
        $query = User::with('roles')
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
            )
            ->when($request->role, fn($q) =>
                $q->whereHas('roles', fn($q2) => $q2->where('name', $request->role))
            );
 
        // Manager voit seulement les clients
        if ($user->hasRole('manager')) {
            $query->whereHas('roles', fn($q) => $q->where('name', 'client'));
        }
 
        return response()->json($query->orderByDesc('created_at')->paginate(15));
    }
 
    public function show(User $user)
    {
        return response()->json($user->load('roles', 'permissions'));
    }
 
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Autorisations : Admin peut tout, Manager peut créer réceptionnistes et clients
        $canCreate = $user->hasRole('admin') || ($user->hasRole('manager') && in_array($request->role, ['receptionist', 'client']));

        if (!$canCreate) {
            return response()->json(['message' => 'Permission refusée : Vous n\'avez pas les droits pour créer ce type de profil.'], 403);
        }
 
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,manager,receptionist,client',
            'permissions' => 'nullable|array',
        ]);
 
        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
 
        $user->assignRole($data['role']);
 
        // Permissions personnalisées (retirer certaines permissions du rôle)
        if (!empty($data['permissions'])) {
            // Donner uniquement les permissions sélectionnées
            $user->syncPermissions($data['permissions']);
        }
 
        return response()->json($user->load('roles', 'permissions'), 201);
    }
 
    public function update(Request $request, User $user)
    {
        // Admin peut tout faire, manager peut seulement voir
        if (!auth()->user()->can('manage_clients') && !auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Permission refusée'], 403);
        }
 
        $data = $request->validate([
            'role'        => 'nullable|in:admin,manager,receptionist,client',
            'permissions' => 'nullable|array',
            'is_active'   => 'nullable|boolean',
        ]);
 
        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }
 
        // Admin peut personnaliser les permissions
        if (isset($data['permissions']) && auth()->user()->hasRole('admin')) {
            $user->syncPermissions($data['permissions']);
        }
 
        if (isset($data['is_active'])) {
            $user->update(['is_active' => $data['is_active']]);
        }
 
        return response()->json($user->load('roles', 'permissions'));
    }
 
    public function toggleStatus(User $user)
    {
        if (!auth()->user()->can('manage_clients') && !auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Permission refusée'], 403);
        }
        $user->update(['is_active' => !$user->is_active]);
        return response()->json($user);
    }
 
    public function destroy(User $user)
    {
        if (!auth()->user()->can('delete_user')) {
            return response()->json(['message' => 'Permission refusée'], 403);
        }
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }
 
    public function permissions()
    {
        // Retourner toutes les permissions disponibles pour l'admin
        $permissions = Permission::all()->groupBy(fn($p) => explode('_', $p->name, 2)[0]);
        return response()->json($permissions);
    }
 
    public function rolePermissions(string $role)
    {
        $roleModel = Role::where('name', $role)->where('guard_name', 'web')->first();
        if (!$roleModel) return response()->json([]);
        return response()->json($roleModel->permissions->pluck('name'));
    }
}