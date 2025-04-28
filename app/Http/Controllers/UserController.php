<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function nuevousuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:250',
            'apellido' => 'required|string|max:250',
            'correo' => 'required|string|max:250',
            'password' => 'required|string|max:250',
            'rol' => 'required|in:administrador,contador,caja',
            'actividad' => 'required|in:activo,inactivo',
            'nacimiento' => 'required|date',
        ]);
        try {
            User::create([
                'name' => $request->nombre,
                'last_name' => $request->apellido,
                'fecha_nacimiento' => $request->nacimiento,
                'email' => $request->correo,
                'password' => bcrypt($request->password),
                'rol' => $request->rol,
                'estado' => $request->actividad,
            ]);
            return redirect()->back()->with('success', 'Usuario creado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el usuario ');
        }
    }

    public function obtenerUser($id)
    {
        // Buscar el usuario por su ID
        $user = User::find($id);

        // Si el usuario existe, devolver los datos en formato JSON
        if ($user) {
            return response()->json($user);
        }

        // Si no se encuentra el usuario, devolver un error
        return response()->json(['error' => 'Usuario no encontrado'], 404);
    }
    public function updateuser(Request $request)
    {
        // Validación de datos
        $request->validate([
            'nombreupdate' => 'required|string|max:250',
            'apellidoupdate' => 'required|string|max:250',
            'correoupdate' => 'required|string|email|max:250',
            'passwordupdate' => 'nullable|string|min:8',
            'rolupdate' => 'required|in:administrador,contador,caja',
            'actividadupdate' => 'required|in:activo,inactivo',
            'nacimientoupdate' => 'required|date',
        ]);

        // Obtener el ID
        $id = $request->id;

        // Buscar usuario
        $user = User::findOrFail($id);

        // Preparar datos
        $data = [
            'name' => $request->nombreupdate,
            'last_name' => $request->apellidoupdate,
            'fecha_nacimiento' => $request->nacimientoupdate,
            'email' => $request->correoupdate,
            'rol' => $request->rolupdate,
            'estado' => $request->actividadupdate,
        ];

        // Solo actualizar la contraseña si se envía
        if ($request->filled('passwordupdate')) {
            $data['password'] = bcrypt($request->passwordupdate);
        }

        // Actualizar usuario
        $user->update($data);

        return redirect()->back()->with('success', 'Usuario actualizado con éxito');
    }
}
