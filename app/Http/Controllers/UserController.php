<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

            // Crear usuario
            $usuario = User::create([
                'name' => $request->nombre,
                'last_name' => $request->apellido,
                'fecha_nacimiento' => $request->nacimiento,
                'email' => $request->correo,
                'password' => bcrypt($request->password),
                'rol' => $request->rol,
                'estado' => $request->actividad,
            ]);


            // Crear texto plano para la bitácora (sin contraseña)
            $textoBitacora = "Usuario creado:\n";
            $textoBitacora .= "- Nombre: {$request->nombre} {$request->apellido}\n";
            $textoBitacora .= "- Correo: {$request->correo}\n";
            $textoBitacora .= "- Rol: {$request->rol}\n";
            $textoBitacora .= "- Estado: {$request->actividad}\n";
            $textoBitacora .= "- Fecha de nacimiento: {$request->nacimiento}\n";
            $textoBitacora .= "-------------------------\n";

            // Guardar en la bitácora
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'USUARIOS',
                'accion' => 'CREACIÓN DE USUARIO',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => null
            ]);


            return redirect()->back()->with('success', 'Usuario creado correctamente');
        } catch (\Exception $e) {


            return redirect()->back()->with('error', 'Error al crear el usuario');
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

        $id = $request->id;
        $user = User::findOrFail($id);

        $cambios = "";

        if ($user->name !== $request->nombreupdate) {
            $cambios .= "- Nombre: {$user->name} → {$request->nombreupdate}\n";
        }
        if ($user->last_name !== $request->apellidoupdate) {
            $cambios .= "- Apellido: {$user->last_name} → {$request->apellidoupdate}\n";
        }
        if ($user->email !== $request->correoupdate) {
            $cambios .= "- Correo: {$user->email} → {$request->correoupdate}\n";
        }
        if ($user->rol !== $request->rolupdate) {
            $cambios .= "- Rol: {$user->rol} → {$request->rolupdate}\n";
        }
        if ($user->estado !== $request->actividadupdate) {
            $cambios .= "- Estado: {$user->estado} → {$request->actividadupdate}\n";
        }
        if ($user->fecha_nacimiento !== $request->nacimientoupdate) {
            $cambios .= "- Fecha de nacimiento: {$user->fecha_nacimiento} → {$request->nacimientoupdate}\n";
        }
        if ($request->filled('passwordupdate')) {
            $cambios .= "- Contraseña actualizada\n";
        }

        // Preparar datos actualizados
        $data = [
            'name' => $request->nombreupdate,
            'last_name' => $request->apellidoupdate,
            'fecha_nacimiento' => $request->nacimientoupdate,
            'email' => $request->correoupdate,
            'rol' => $request->rolupdate,
            'estado' => $request->actividadupdate,
        ];

        if ($request->filled('passwordupdate')) {
            $data['password'] = bcrypt($request->passwordupdate);
        }

        $user->update($data);

        // Guardar en la bitácora solo si hubo cambios
        if ($cambios !== "") {
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'USUARIOS',
                'accion' => 'ACTUALIZACIÓN DE USUARIO',
                'datos' => "Cambios realizados al usuario {$user->name} {$user->last_name}:\n" . $cambios,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => null
            ]);
        }

        return redirect()->back()->with('success', 'Usuario actualizado con éxito');
    }
}
