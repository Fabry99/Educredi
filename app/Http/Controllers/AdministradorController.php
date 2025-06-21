<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Centros;
use App\Models\Centros_Grupos_Clientes;
use App\Models\Clientes;
use App\Models\Departamentos;
use App\Models\Grupos;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdministradorController extends Controller
{
    public function home()
    {
        $rol = Auth::user()->rol; // Obtener el rol del usuario autenticado
        $departamentos = Departamentos::all();

        $centros = Centros::all();
        $grupo = Grupos::all();
        $usuarios = User::with('latestSession')->get(); // Carga solo la última sesión

        $centros_grupos_clientes = Centros_Grupos_Clientes::with('clientes', 'grupos', 'centros')->get();
        $clientes = Clientes::with('departamento', 'municipio', 'Centros_Grupos_Clientes.grupos', 'Centros_Grupos_Clientes.centros')->get();
        if ($rol == 'administrador') {
            return view('modules.dashboard.home', compact('rol', 'clientes', 'centros_grupos_clientes', 'centros', 'grupo', 'departamentos', 'usuarios')); // Pasar el rol a la vista

        } else {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }
    }
    public function clientesadmin()
    {
        $rol = Auth::user()->rol;
        $departamentos = Departamentos::all();
        $centros = Centros::all();
        $grupo = Grupos::all();
        $centros_grupos_clientes = Centros_Grupos_Clientes::with('clientes', 'grupos', 'centros')->get();
        $clientes = Clientes::with('departamento', 'municipio', 'Centros_Grupos_Clientes.grupos', 'Centros_Grupos_Clientes.centros')->get();
        if ($rol == 'administrador') {
            return view('modules.dashboard.clientesAdmin', compact('rol', 'departamentos', 'clientes', 'centros_grupos_clientes', 'centros', 'grupo'));
        } else {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }
    }
    public function bitacora()
    {

        $rol = Auth::user()->rol;
        $bitacora = Bitacora::with('user:id,name,last_name')->get();
        if ($rol == 'administrador') {
            return view('modules.dashboard.bitacora', compact('rol', 'bitacora'));
        } else {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }
    }

    public function usuarios()
    {
        $rol = Auth::user()->rol;
        $usuarios = User::with('latestSession')->get(); // Carga solo la última sesión
        if ($rol == 'administrador') {
            return view('modules.dashboard.usuarios', compact('rol', 'usuarios'));
        } else {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }
    }

    public function actualziarllave(Request $request)
    {

        $password = $request->input('password');

        // Inicia la transacción
        DB::beginTransaction();

        try {
            $registro = DB::table('password_special')->select('id')->first();

            if (!$registro) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontró el registro de contraseña especial'
                ]);
            }

            $id = $registro->id;

            $passwordHasheado = Hash::make($password);

            // Actualizar contraseña
            DB::table('password_special')
                ->where('id', $id)
                ->update([
                    'password' => $passwordHasheado,
                    'updated_at' => Carbon::now()
                ]);

            // Registrar en bitácora
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'Contraseña Especial',
                'accion' => 'ACTUALIZACIÓN',
                'datos' => 'Contraseña Especial Actualizada',
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
            ]);

            // Confirmar transacción
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Contraseña actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            // Si ocurre error, revertimos
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al actualizar la contraseña'
            ]);
        }
    }
}
