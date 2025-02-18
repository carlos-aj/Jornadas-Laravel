<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function mostrarPagos()
    {
        $pagos = Pago::all();
        return response()->jsonv('Pago eliminado', 204);
    }
}
