<?php

namespace App\Http\Controllers;

use App\Models\Branche;
use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\Zone;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class WorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 1);

        $workers = Worker::with('branche')
            ->where('status', $status)
            ->orderBy('id', 'DESC')
            ->get();

        $zones = Zone::where('status', 1)
            ->orderBy('id', 'ASC')
            ->pluck('name', 'id');

        $inactiveCount = Worker::where('status', 0)->count();

        return view('workers.index', compact('workers', 'inactiveCount', 'zones'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branches = Branche::where('status', 1)->orderBy('id', 'DESC')->get();
        $zones = Zone::where('status', 1)->orderBy('id', 'DESC')->get();

        return view('workers.create', compact('zones', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = $this->getValidationRules();
        $messages = $this->getValidationMessages();
        $request->validate($rules, $messages);

        $verify_document = Worker::where('document', $request->document)->exists();
        if ($verify_document) {
            return redirect()->route('personal.create')->with('error', 'El número de documento ingresado ya se encuentra registrado, Por favor, valide bien la información.');
        }

        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');

            $photoPath = Storage::disk('public')->put('personal/photos', $photo);

            $worker = new Worker();
            $worker->document = $request->document;
            $worker->name = $request->name;
            $worker->lastname = $request->lastname;
            $worker->photo = $photoPath;
            $worker->phone = $request->phone;
            $worker->type = "Operativo";
            $worker->cost_center = $request->cost_center;
            $worker->bonding = $request->bonding;
            $worker->cargo = $request->cargo;
            $worker->save();
        } else {
            return redirect()->route('personal.create')->with('error', 'La foto no se cargó correctamente, Por favor, vuelva a intentar.');
        }

        return redirect()->route('personal.index')->with('success', 'El personal operativo ha sido creado con exito.');
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
        try {
            $worker = Worker::findOrFail($id);
            $branches = Branche::where('status', 1)->orderBy('id', 'DESC')->get();

            return view('workers.edit', compact('worker', 'branches'));
        } catch (\Exception $e) {
            return redirect()->route('personal.index')->with('error', "Oops, el registro no puede ser editado en estos momento, Intente más tarde.");
        }
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
        $rules = $this->getValidationRules();
        $messages = $this->getValidationMessages();
        $request->validate($rules, $messages);

        $worker = Worker::find($id);

        if (!$worker) {
            return redirect()->route('personal.edit')->with('error', 'El personal operativo no fue encontrado, Por favor, vuelva a intentar.');
        }

        $oldPhotoPath = $worker->photo;
        $newPhotoPath = $oldPhotoPath;

        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');

            $newPhotoPath = Storage::disk('public')->put('personal/photos', $photo);

            // Elimina la foto anterior
            if ($oldPhotoPath && Storage::disk('public')->exists($oldPhotoPath)) {
                Storage::disk('public')->delete($oldPhotoPath);
            }
        }

        $worker->document = $request->document;
        $worker->name = $request->name;
        $worker->lastname = $request->lastname;
        $worker->photo = $newPhotoPath;
        $worker->phone = $request->phone;
        $worker->type = "Operativo";
        $worker->cost_center = $request->cost_center;
        $worker->bonding = $request->bonding;
        $worker->cargo = $request->cargo;
        $worker->save();

        return redirect()->route('personal.index')->with('success', 'El personal operativo ha sido editado con exito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $worker = Worker::findOrFail($id);

            $worker->update([
                'status' => $worker->status === 0 ? 1 : 0
            ]);

            $statusMessage = $worker->status ? "Activado" : "Inactivado";

            return redirect()->route('personal.index')->with('success', "El usuario ha sido {$statusMessage} correctamente.");
        } catch (\Exception $e) {
            return redirect()->route('personal.index')->with('error', "Error al cambiar el estado del usuario.");
        }
    }

    public function getZonesByRegion($regionId)
    {
        $branch = Branche::find($regionId);

        if ($branch) {
            $zoneIds = json_decode($branch->zones, true);
            $zones = Zone::whereIn('id', $zoneIds)->get();

            return response()->json($zones);
        }

        return response()->json([]);
    }

    public function personalImport(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|file|mimes:xlsx,xls'
        ]);

        $archivo = $request->file('archivo_excel');
        $spreadsheet = IOFactory::load($archivo->getPathname());
        $hoja = $spreadsheet->getActiveSheet();
        $datos = $hoja->toArray();

        $cabecerasEsperadas = [
            "identificación",
            "nombres",
            "apellidos",
            "descripción cargo",
            "nombre centro costo",
            "nombre Área",
            "descripción proyecto",
            "telefonos",
            "e-mail",
        ];

        $cabecerasExcel = array_map('strtolower', array_map('trim', $datos[0]));

        if ($cabecerasExcel !== $cabecerasEsperadas) {
            return back()->with('error', 'Oops, El archivo no corresponde con la plantilla esperada. Por favor, carga la plantilla correcta.');
        }

        $duplicates = [];

        foreach ($datos as $i => $data) {

            if ($i === 0) continue;
            if ($this->filaVacia($data)) continue;

            $document = $data[0];
            if (Worker::where('document', $document)->exists()) {
                $duplicates[] = $document; 
                continue; 
            }

            $worker = new Worker();
            $worker->document = $document;
            $worker->name = $data[1];
            $worker->lastname = $data[2];
            $worker->type = $data[3];
            $worker->cost_center = $data[4];
            $worker->nombre_area = $data[5];
            $worker->proyecto = $data[6];
            $worker->phone = $data[7];
            $worker->email = $data[8];
            $worker->save();
        }

        if (!empty($duplicates)) {
            return back()->with('error', 'Oops, estos documentos ya se encuentran registrados: ' . implode(',', $duplicates));
        }

        return back()->with('success', 'Archivo importado exitosamente');
    }

    private function filaVacia(array $fila): bool
    {
        foreach ($fila as $valor) {
            if (!is_null($valor) && trim($valor) !== '') {
                return false;
            }
        }
        return true;
    }

    private function getValidationRules()
    {
        return [
            'name' => 'required',
            'lastname' => 'required',
            'document' => 'required',
            'phone' => 'required',
            'cost_center' => 'required',
            'bonding' => 'required',
        ];
    }

    private function getValidationMessages()
    {
        return [
            'name.required' => 'El campo Nombres es obligatorio.',
            'lastname.required' => 'El campo Apellidos es obligatorio.',
            'document.required' => 'El campo Documento es obligatorio.',
            'phone.required' => 'El campo Celular es obligatorio.',
            'cost_center.required' => 'El campo centro de costo es obligatorio.',
            'bonding.required' => 'El campo Vinculación es obligatorio.',
        ];
    }
}
