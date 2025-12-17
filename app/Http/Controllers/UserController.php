<?php

namespace App\Http\Controllers;

use Carbon\Factory;
use Dotenv\Exception\ValidationException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios = User::where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        $hasRoles = Role::all();

        return view('users.index', compact('usuarios', 'hasRoles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $rules = $this->getValidationRules();
            $messages = $this->getValidationMessages();
            $request->validate($rules, $messages);

            $verify_email = User::where('email', $request->email)->exists();
            if ($verify_email) {
                return redirect()->route('usuarios.index')->with('error', 'El correo electronico ya está en uso, intenta con otro.');
            }

            $user = new User();
            $user->name = $request->names;
            $user->document = $request->document;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->gender = $request->gender;
            $user->birthdate = $request->birthdate;
            $user->password = Hash::make($request->password);
            $user->status = 1; // 1: Activo, 0: Inactivo

            // Asignar el rol al usuario
            if ($request->role_name) {
                $user->assignRole($request->role_name);
                $user->save();
            }

            return redirect()->route('usuarios.index')->with('success', 'El usuario ha sido creado con exito.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Oops, algo salió mal. Por favor, intenta mas tarde. ' . $e);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Oops, algo salió mal. Por favor, intenta mas tarde. ' . $e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = $this->getValidationRules();
            $messages = $this->getValidationMessages();
            $request->validate($rules, $messages);

            $user = User::find($id);

            $verify_email = User::where('email', $request->email)
                ->where('id', '!=', $user->id)
                ->exists();
            if ($verify_email) {
                return redirect()->route('usuarios.index')->with('error', 'El correo electronico ya está en uso, intenta con otro.');
            }

            $user->name = $request->names;
            $user->document = $request->document;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->gender = $request->gender;
            $user->birthdate = $request->birthdate;
            $user->password = Hash::make($request->password);
            $user->status = 1; // 1: Activo, 0: Inactivo

            // Asignar el rol al usuario
            if ($request->role_name) {
                $user->syncRoles([$request->role_name]);
                $user->save();
            }

            return redirect()->route('usuarios.index')->with('success', 'El usuario ha sido editado con exito.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Oops, algo salió mal. Por favor, intenta mas tarde. ' . $e);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Oops, algo salió mal. Por favor, intenta mas tarde. ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): Response|RedirectResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('usuarios.index')->with('success', 'El usuario ha sido eliminado correctamente.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('usuarios.index')->with('error', 'El usuario no existe.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Ocurrió un error al intentar eliminar el usuario.');
        }
    }

    private function getValidationRules()
    {
        return [
            'names' => 'required',
            'document' => 'required',
            'birthdate' => 'required',
            'gender' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    private function getValidationMessages()
    {
        return [
            'names.required' => 'El campo Nombres es obligatorio.',
            'document.required' => 'El campo Documento es obligatorio.',
            'phone.required' => 'El campo Celular es obligatorio.',
            'gender.required' => 'El campo género electrónico es obligatorio.',
            'email.required' => 'El campo Correo electrónico es obligatorio.',
            'email.email' => 'El campo Correo electrónico debe ser una dirección de correo válida.',
            'birthdate.required' => 'El campo Nacimiento es obligatorio.',
            'birthdate.date' => 'El campo Nacimiento debe ser una fecha válida.',
            'password.required' => 'El campo Contraseña es obligatorio.',
        ];
    }

    public function export()
    {
        return Excel::download(new UsersExport, 'usuarios_export.xlsx');
    }
}
