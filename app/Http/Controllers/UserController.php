<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('modules.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'status' => 'nullable|boolean',
            'password' => 'required|string',
        ]);

        User::create($request->all());

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_photo' => 'required|image|max:2048',
            'status' => 'nullable|boolean',
        ]);

        return $request->file('profile_photo')->store('public/profile_photos');
        // $user->update($request->all());
        // // Borrar la imagen anterior si existe
        // if ($user->profile_photo) {
        //     Storage::delete('public/profile_photos' . $user->profile_photo);
        // }

        // // Subir la nueva imagen
        // $path = $request->file('profile_photo')->store('profile_photos', 'public/profile_photos');

        // // Guardar la nueva ruta en la base de datos
        // $user->profile_photo = $path;
        // $user->save();

        // return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $users = User::where('id', 'LIKE', "%$searchTerm%")
            ->orWhere('name', 'LIKE', "%$searchTerm%")
            ->orWhere('last_name', 'LIKE', "%$searchTerm%")
            ->orWhere('email', 'LIKE', "%$searchTerm%")
            ->get();

        return view('modules.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function deleteSelectedUsers(Request $request)
    {
        $userIds = $request->input('userIds');

        // Eliminar los usuarios seleccionados de la base de datos
        $users = User::whereIn('id', $userIds)->get();
        foreach ($users as $user) {
            $user->delete();
        }

        return response()->json(['message' => 'Usuarios eliminados correctamente.']);
    }
}
