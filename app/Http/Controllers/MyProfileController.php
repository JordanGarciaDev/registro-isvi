<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MyProfileController extends Controller
{
    public function index()
    {
        $id = Auth::user()->id;

        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request)
    {
        try {
            $user = auth()->user();

            // Actualizar datos básicos
            $user->name      = $request->names;
            $user->document  = $request->document;
            $user->birthdate = $request->birthdate;
            $user->gender    = $request->gender;
            $user->phone     = $request->phone;

            $passwordChanged = false;

            // Si viene contraseña, cambiarla
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
                $passwordChanged = true;
            }

            $user->save();

            // Si la contraseña cambió → cerrar sesión y redirigir al login
            if ($passwordChanged) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('success', 'Tu contraseña se actualizó correctamente. Inicia sesión nuevamente.');
            }

            // Si no cambió la contraseña → mantener sesión
            return redirect()->back()
                ->with('success', 'Tu información se ha actualizado correctamente.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Oops, algo salió mal. Intenta más tarde.');
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
        ];
    }
}
