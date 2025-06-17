<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteINFOREDExport implements FromCollection, WithHeadings
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        return $this->datos;
    }

    public function headings(): array
    {
        return [
            'Año',
            'mes',
            'nombre',
            'tipo_per',
            'Num_ptmo',
            'inst',
            'fect_otor',
            'monto',
            'plazo',
            'saldo',
            'mora',
            'forma_pago',
            'tipo_rel',
            'linea_cre',
            'dias',
            'ult_pag',
            'tipo_gar',
            'tipo_mon',
            'valcuota',
            'dia',
            'fechanac',
            'dui',
            'nit',
            'fecha_can',
            'fecha_ven',
            'ncuotascre',
            'ncuotasmor',
            'calif_act',
            'activi_eco',
            'sexo',
            'est_credito',
            'grupos',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            'A1:D1000' => [  // rango según columnas y filas
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
            ],
        ];
    }
}
