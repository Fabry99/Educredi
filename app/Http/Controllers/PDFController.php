<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use NumberToWords\NumberToWords;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

class PDFController extends Controller
{


    public function estadoCuenta(Request $request)
    {
        $datosCuotas = $request->input('datosCuotas', []);
        $datosPagos = $request->input('datosPagos', []);
        $fechaSeleccionada = $request->input('fechaSeleccionada', '');
        $valorPonerseAlDia = $request->input('valorPonerseAlDia', '');
        $monto = $request->input('monto', '');
        $nombreCentro = $request->input('nombreCentro', '');
        $nombreGrupo = $request->input('nombreGrupo', '');
        $pdf = Pdf::loadView('pdf.estadoCuenta', [
            'datosCuotas' => $datosCuotas,
            'datosPagos' => $datosPagos,
            'fechaSeleccionada' => $fechaSeleccionada,
            'valorPonerseAlDia' => $valorPonerseAlDia,
            'monto' => $monto,
            'nombreCentro' => $nombreCentro,
            'nombreGrupo' => $nombreGrupo,
        ]);

        return $pdf->stream('estado_cuenta.pdf'); // Para ver en el navegador
        // return $pdf->download('estado_cuenta.pdf'); // Si quieres descargar
    }

    public function obtenerdepartamento()
    {
        $departamento = DB::table('departamentos')
            ->get();

        return response()->json($departamento);
    }
    public function obtenermunicipio(Request $request)
    {
        $id_departamento = $request->input('id_departamento');

        $municipios = DB::table('municipios')
            ->where('id_departamento', $id_departamento)
            ->get();

        return response()->json($municipios);
    }

    public function obtenerprestamos(Request $request)
    {
        $id_centro = $request->input('id_centro');
        $id_grupo = $request->input('id_grupo');


        $prestamos = DB::table('historial_prestamos')
            ->where('centro', $id_centro)
            ->where('grupo', $id_grupo)
            ->orderBy('fecha_apertura')
            ->get();


        // Agrupar manualmente en PHP solo por fecha_apertura
        $grupos = $prestamos->groupBy('fecha_apertura');


        $resultado = [];
        foreach ($grupos as $fecha_apertura => $items) {

            // Calcular monto total del grupo
            $monto_total = $items->sum('monto'); // Asegúrate que el campo sea 'monto'


            $resultado[] = [
                'fecha_apertura' => $fecha_apertura,
                'monto_total' => $monto_total,
                'prestamos' => $items
            ];
        }


        return response()->json($resultado);
    }



    public function pdfmutuogrupal(Request $request)
    {


        $tipomutuo = $request->input('tipomutuo');
        $deptomutuo = $request->input('deptomutuo');
        $municipiomutuo = $request->input('municipiomutuo');
        $fechamutuocreado = $request->input('fechamutuocreado');
        $centro = $request->input('centro');
        $grupo = $request->input('grupo');

        $filaSeleccionada = $request->input('filaSeleccionada', []);

        $fecha_apertura = $filaSeleccionada['fecha_apertura'] ?? null;
        $monto_total = $filaSeleccionada['monto_total'] ?? 0;


        $hayPrestamos = DB::table('historial_prestamos')
            ->where('fecha_apertura', $fecha_apertura)
            ->exists();


        // Obtener la fecha de vencimiento si es necesario
        $fecha_vencimiento = DB::table('historial_prestamos')
            ->where('fecha_apertura', $fecha_apertura)
            ->value('fecha_vencimiento');


        $clientes = DB::table('historial_prestamos as sl')
            ->join('clientes as cl', 'sl.id_cliente', '=', 'cl.id')
            ->join('departamentos as dp', 'cl.id_departamento', '=', 'dp.id')
            ->join('municipios as mn', 'cl.id_municipio', '=', 'mn.id')
            ->leftJoin('debeser as deb', function ($join) use ($fecha_apertura) {
                $join->on('deb.id_cliente', '=', 'sl.id_cliente')
                    ->where('deb.fecha_apertura', $fecha_apertura);
            })
            ->where('sl.fecha_apertura', $fecha_apertura)
            ->select(
                'cl.id',
                'cl.nombre',
                'cl.apellido',
                'cl.fecha_nacimiento',
                'cl.sector as profesion',
                'cl.dui',
                'sl.interes',
                'sl.plazo',
                DB::raw('MIN(deb.dias) as dias'),
                'sl.cuota',
                'dp.nombre as nombre_departamento',
                'mn.nombre as nombre_municipio'
            )
            ->groupBy(
                'cl.id',
                'cl.nombre',
                'cl.apellido',
                'cl.fecha_nacimiento',
                'cl.sector',
                'cl.dui',
                'sl.interes',
                'sl.plazo',
                'sl.cuota',
                'dp.nombre',
                'mn.nombre'
            )
            ->get();

        // Función para convertir DUI a letras (mantén tu función)
        function duiEnLetras($dui)
        {
            $numerosEnLetras = [
                '0' => 'CERO',
                '1' => 'UNO',
                '2' => 'DOS',
                '3' => 'TRES',
                '4' => 'CUATRO',
                '5' => 'CINCO',
                '6' => 'SEIS',
                '7' => 'SIETE',
                '8' => 'OCHO',
                '9' => 'NUEVE',
                '-' => 'GUION',
                ' ' => ' '
            ];

            $resultado = [];

            for ($i = 0; $i < strlen($dui); $i++) {
                $char = $dui[$i];
                $resultado[] = $numerosEnLetras[$char] ?? $char;
            }

            return implode(' ', $resultado);
        }

        function fechaEnTexto($fecha, $numberTransformer)
        {
            $carbonFecha = \Carbon\Carbon::parse($fecha);

            $dia = (int) $carbonFecha->day;
            $mes = (int) $carbonFecha->month;
            $anio = (int) $carbonFecha->year;

            $meses = [
                1 => 'ENERO',
                2 => 'FEBRERO',
                3 => 'MARZO',
                4 => 'ABRIL',
                5 => 'MAYO',
                6 => 'JUNIO',
                7 => 'JULIO',
                8 => 'AGOSTO',
                9 => 'SEPTIEMBRE',
                10 => 'OCTUBRE',
                11 => 'NOVIEMBRE',
                12 => 'DICIEMBRE',
            ];

            $diaTexto = strtoupper($numberTransformer->toWords($dia));
            $mesTexto = $meses[$mes];
            $anioTexto = strtoupper($numberTransformer->toWords($anio));

            return "$diaTexto DE $mesTexto DE $anioTexto";
        }
        function fechaEnTextoFormal($fecha, $numberTransformer)
        {
            $carbonFecha = \Carbon\Carbon::parse($fecha);

            $dia = (int) $carbonFecha->day;
            $mes = (int) $carbonFecha->month;
            $anio = (int) $carbonFecha->year;

            $meses = [
                1 => 'enero',
                2 => 'febrero',
                3 => 'marzo',
                4 => 'abril',
                5 => 'mayo',
                6 => 'junio',
                7 => 'julio',
                8 => 'agosto',
                9 => 'septiembre',
                10 => 'octubre',
                11 => 'noviembre',
                12 => 'diciembre',
            ];

            $diaTexto = mb_strtolower($numberTransformer->toWords($dia), 'UTF-8');
            $mesTexto = $meses[$mes]; // ya está en minúsculas
            $anioTexto = mb_strtolower($numberTransformer->toWords($anio), 'UTF-8');

            return "a los $diaTexto días del mes de $mesTexto de $anioTexto";
        }



        // Función para mayúsculas con UTF-8
        function strtoupper_utf8($text)
        {
            return mb_strtoupper($text, 'UTF-8');
        }

        // Convertir monto a palabras con la librería
        $numberToWords = new NumberToWords();
        $currencyTransformer = $numberToWords->getCurrencyTransformer('es');
        $montoEnPalabras = $currencyTransformer->toWords($monto_total * 100, 'USD'); // monto en centavos

        // Obtener interés del primer cliente o 0 si no hay
        $interesNumero = $clientes->first()->interes ?? 0;

        // Función para convertir el plazo a texto
        function plazoEnTextoSeparado($plazo, $dias, $numberTransformer)
        {
            if ($dias <= 0 || $plazo <= 0) return ['cantidad' => 'PLAZO INVÁLIDO', 'unidad' => ''];

            $unidad = '';

            switch ((int) $dias) {
                case 1:
                    $unidad = 'día';
                    break;
                case 7:
                    $unidad = 'semana';
                    break;
                case 14:
                    $unidad = 'catorcena';
                    break;
                case 15:
                    $unidad = 'quincena';
                    break;
                case 30:
                    $unidad = 'mensuale';
                    break;
                case 60:
                    $unidad = 'bimestrale';
                    break;
                case 90:
                    $unidad = 'trimestrale';
                    break;
                case 180:
                    $unidad = 'semestrale';
                    break;
                case 360:
                    $unidad = 'anuale';
                    break;
                default:
                    $unidad = 'día'; // fallback si es desconocido
                    break;
            }

            $unidadTexto = $plazo === 1 ? $unidad : $unidad . 's';
            $cantidadTexto = strtoupper($numberTransformer->toWords($plazo));
            $unidadTexto = strtoupper($unidadTexto);

            return ['cantidad' => $cantidadTexto, 'unidad' => $unidadTexto];
        }
        $fechaActual = Carbon::now(); // o ->setTimezone('America/El_Salvador') si necesitas zona

        $hora = (int) $fechaActual->format('H');   // 24h
        $dia = (int) $fechaActual->day;
        $mes = (int) $fechaActual->month;
        $anio = (int) $fechaActual->year;

        // Inicializar librería para convertir números
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es');

        // Convertir a texto
        $horaTexto = $numberTransformer->toWords($hora);
        $diaTexto = $numberTransformer->toWords($dia);
        $anioTexto = $numberTransformer->toWords($anio);

        $meses = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];

        $mesTexto = $meses[$mes];

        // Construir la frase completa
        $frase = "a las " . mb_strtolower($horaTexto, 'UTF-8') . " horas del día " .
            mb_strtolower($diaTexto, 'UTF-8') . " de " .
            mb_strtolower($mesTexto, 'UTF-8') . " de " .
            mb_strtolower($anioTexto, 'UTF-8') . ".";

        // Convertir interés a texto
        $numberTransformer = $numberToWords->getNumberTransformer('es');
        $interesTexto = $numberTransformer->toWords((int)$interesNumero);

        $plazoValor = (int) ($clientes->first()->plazo ?? 0);
        $diasValor = (int) ($clientes->first()->dias ?? 1);

        $plazoPartes = plazoEnTextoSeparado($plazoValor, $diasValor, $numberTransformer);
        $cantidadTexto = $plazoPartes['cantidad']; // Ejemplo: CUATRO
        $unidadTexto = $plazoPartes['unidad'];     // Ejemplo: MESES

        $fechaVencimientoTexto = fechaEnTexto($fecha_vencimiento, $numberTransformer);

        $numberTransformer = $numberToWords->getNumberTransformer('es');
        $fechamutuocreadoTexto = fechaEnTextoFormal($fechamutuocreado, $numberTransformer);


        $cuotaNumero = $clientes->first()->cuota ?? 0;
        // Si cuota tiene decimales, puedes manejar con round o cast a entero si es apropiado
        $cuotaEntero = (int) round($cuotaNumero);
        $cuotaTexto = strtoupper($numberTransformer->toWords($cuotaEntero));
        // Construir texto de clientes
        $fragmentos = [];
        foreach ($clientes as $cliente) {
            $edad = Carbon::parse($cliente->fecha_nacimiento)->age;
            $duiLetras = duiEnLetras($cliente->dui);

            $fragmentos[] =
                strtoupper_utf8("{$cliente->nombre} {$cliente->apellido}") .
                ", de " .
                strtoupper_utf8("{$edad}") .
                " años de edad, " .
                strtoupper_utf8("{$cliente->profesion}") .
                ", del domicilio de " .
                strtoupper_utf8("{$municipiomutuo}") .
                ", departamento de " .
                strtoupper_utf8("{$deptomutuo}") .
                ", Con Documento Único de Identidad número: " .
                strtoupper_utf8($duiLetras);
        }

        $textoClientes = implode('; ', $fragmentos);

        // Crear documento Word
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Estilo texto general
        $fontStyle = ['name' => 'Calibri', 'size' => 11, 'lang' => 'es-ES'];
        $paragraphStyle = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH, 'lineHeight' => 2.0];

        // Añadir el texto con partes en negrita (usando addTextRun)
        $textrun = $section->addTextRun($paragraphStyle, $fontStyle);

        $textrun->addText("Nosotros, $textoClientes; que en lo sucesivo del presente instrumento nos denominaremos “$centro ”, OTORGAMOS: ");

        $textrun->addText("A) ", ['bold' => true]);
        $textrun->addText("MONTO: Que hemos recibido en calidad de mutuo en forma COMUN Y SOLIDARIA la suma de ");
        $textrun->addText(strtoupper_utf8($montoEnPalabras));
        $textrun->addText(" de parte de la “SOCIEDAD EDUCREDI RURAL, SOCIEDAD ANONIMA DE CAPITAL VARIABLE.”, que puede abreviarse como: EDUCREDI RURAL S.A. DE C.V., Institución Privada que en adelante denominaremos como “LA ACREEDORA”, institución del domicilio de la ciudad de Santa Ana, con número de identificación tributaria: CERO SEIS UNO CUATRO-UNO CUATRO CERO SIETE CERO NUEVE-UNO CERO TRES-CERO, representada legalmente por el señor ");
        $textrun->addText("EDER WALTER RAMIREZ FLORES", ['bold' => true]);
        $textrun->addText(", de cincuenta años de edad, Licenciado en Contaduría Pública, del domicilio de la ciudad y Departamento de Santa Ana, con Documento Único de Identidad número CERO CERO OCHO CERO DOS TRES TRES CINCO - TRES; ");

        $textrun->addText("B) ", ['bold' => true]);
        $textrun->addText("DESTINO DEL CREDITO: El dinero mutuado será utilizado para capital de trabajo o desarrollo de un proyecto; ");

        $textrun->addText("C) ", ['bold' => true]);
        $textrun->addText(" INTERESES: La suma mutuada devengará el interés ");
        $textrun->addText(strtoupper_utf8($interesTexto));
        $textrun->addText(" POR CIENTO");
        $textrun->addText(strtoupper_utf8($unidadTexto));
        $textrun->addText(", sobre saldo de capital, el cual podrá modificarse de acuerdo a las variaciones que determinen las autoridades monetarias del país y/o las autoridades competentes de la sociedad;");
        $textrun->addText(" D) ", ['bold' => true]);
        $textrun->addText("MOROSIDAD: En caso de mora, reconocemos pagar un interés del ");
        $textrun->addText(strtoupper_utf8($interesTexto));
        $textrun->addText(" POR CIENTO");
        $textrun->addText(strtoupper_utf8($unidadTexto));
        $textrun->addText(", adicional al interés pactado, sobre saldos de capital morosos a partir de un día después de la fecha de vencimiento en el pago de la cuota respectiva;");
        $textrun->addText(" E) ", ['bold' => true]);
        $textrun->addText("PLAZO: El plazo del presente préstamo es de ");
        $textrun->addText(strtoupper_utf8($cantidadTexto));
        $textrun->addText(" CUOTAS ");
        $textrun->addText(strtoupper_utf8($unidadTexto));
        $textrun->addText(", y comenzará  a partir de la fecha de contratación, y cuyo vencimiento es el día ");
        $textrun->addText(strtoupper_utf8($fechaVencimientoTexto));
        $textrun->addText(", y bajo las condiciones que adelante se dirán; ");
        $textrun->addText(" F) ", ['bold' => true]);
        $textrun->addText(" PLAN DE AMORTIZACIÓN: Cancelaremos la cantidad mutuada por medio de ");
        $textrun->addText(strtoupper_utf8($cantidadTexto));
        $textrun->addText(" CUOTAS ");
        $textrun->addText(strtoupper_utf8($unidadTexto));
        $textrun->addText(", fijas, sucesivas y vencidas de ");
        $textrun->addText(strtoupper_utf8($fechaVencimientoTexto));
        $textrun->addText($cuotaTexto);
        $textrun->addText(($cuotaEntero));
        $textrun->addText(", cada una que comprende capital, intereses IVA y seguro de deuda, pagaderas los días MARTES de cada  Catorcenal comprendidas dentro del plazo;");
        $textrun->addText(" G) ", ['bold' => true]);
        $textrun->addText(" LUGAR DE IMPUTACIÓN DE PAGOS: Todo pago lo haremos en la institución financiera que “LA ACREEDORA” establezca para tal efecto y por el monto de la cuota establecida; ");
        $textrun->addText("H) ", ['bold' => true]);
        $textrun->addText("OBLIGACIONES GENERALES: Nos obligamos los deudores a: ");
        $textrun->addText("1) ", ['bold' => true]);
        $textrun->addText("entregas ");
        $textrun->addText($unidadTexto);
        $textrun->addText(" de un informe financiero de las operaciones que se han realizado con el dinero mutuado, durante la vigencia del préstamo; el no cumplimiento de esta condición dará a “LA ACREEDORA” el derecho de exigir la cancelación total de la deuda de inmediato; ");
        $textrun->addText("2) ", ['bold' => true]);
        $textrun->addText("La cuota de morosidad se aplicará a partir de un día después de la fecha de vencimiento de la cuota de pago y un mes de morosidad hará exigir el pago de la deuda de inmediato como si fuera plazo vencido; ");
        $textrun->addText("3) ", ['bold' => true]);
        $textrun->addText("A informar los problemas, retrasos o complicaciones que se susciten de naturaleza empresarial en el desarrollo de su proyecto o inversión en capital de trabajo; ");
        $textrun->addText("4) ", ['bold' => true]);
        $textrun->addText("A informar a la acreedora si cambiaremos de domicilio; ");
        $textrun->addText("5) ", ['bold' => true]);
        $textrun->addText("Cuando “LA ACREEDORA” así lo requiera deberá presentar inventario, balances, estados de resultados y demás contables que demuestren la situación real del proyecto financiado o de la inversión de capital de  trabajo realizada; ");
        $textrun->addText("6) ", ['bold' => true]);
        $textrun->addText("A permitir que “LA ACREEDORA” practique avalúos e inspecciones en el lugar de ejecución del proyecto o de inversión de capital de trabajo, cuando lo estime conveniente; ");
        $textrun->addText("7) ", ['bold' => true]);
        $textrun->addText("“LA ACREEDORA” podrá realizar auditorías o verificar en cualquier forma el uso de los fondos provenientes de éste préstamo; ");
        $textrun->addText("8) ", ['bold' => true]);
        $textrun->addText("A utilizar el dinero mutuado únicamente para realizar actividades de comercio licitas, liberando de cualquier responsabilidad legal a LA ACREEDORA por el incumplimiento de este numeral; y ");
        $textrun->addText("9) ", ['bold' => true]);
        $textrun->addText("Así mismo nos comprometemos todos los deudores solidarios a firmar en su oportunidad los documentos de Dación en Pago o adjudicación de Pago según sea el caso a favor de “LA ACREEDORA” o de la persona que ésta estime conveniente, para el efectivo pago de la presente deuda; ");
        $textrun->addText("I) ", ['bold' => true]);
        $textrun->addText("SUSPENSIÓN DEL CRÉDITO Y CADUCIDAD DEL PLAZO: ", ['bold' => true]);
        $textrun->addText("El plazo señalado se tendrá por caducado y la obligación a cargo de los deudores se volverá exigible en su totalidad en los siguientes casos: ");
        $textrun->addText("1) ", ['bold' => true]);
        $textrun->addText("Por incumplimiento de las obligaciones contraídas en el presente instrumento; ");
        $textrun->addText("2) ", ['bold' => true]);
        $textrun->addText("Por ejecución que en contra de nosotros inicien terceros, por deuda distinta a la presente; ");
        $textrun->addText("3) ", ['bold' => true]);
        $textrun->addText("Por determinación de la información brindada por nosotros los deudores en la hoja de descripción del negocio sea falsa, que contraríen o violen de cualquier forma las normas establecidas, en cualquiera de sus postulados; ");
        $textrun->addText("4) ", ['bold' => true]);
        $textrun->addText("Por el atraso en el pago de una cuota de capital y/o intereses; ");
        $textrun->addText("J) ", ['bold' => true]);
        $textrun->addText("DOMICILIOS ESPECIALES: ", ['bold' => true]);
        $textrun->addText("En caso de acción judicial, nosotros las deudores señalamos como domicilios especiales los  de la ciudad de Sonsonate y la ciudad de Santa Ana, a cuyos tribunales nos sometemos expresamente. Será depositario de los bienes que nos embarguen, la persona que “LA ACREEDORA” designe, a quien relevamos de la obligación de rendir fianza y cuentas, siendo por nuestro costo el pago de las costas procesales y cuanto otros gastos se hiciere con motivo de la cancelación de la presente deuda, aunque conforme a las reglas generales no seamos condenados a ellas. ");
        $textrun->addText("K) ", ['bold' => true]);
        $textrun->addText("ACEPTACIÓN DE DERECHOS: ", ['bold' => true]);
        $textrun->addText("Por otra parte yo ");
        $textrun->addText("EDER WALTER RAMIREZ FLORES ", ['bold' => true]);
        $textrun->addText(", de cincuenta  años de edad, Licenciado en Contaduría Pública, del domicilio de la ciudad y Departamento de Santa Ana, con Documento Único de Identidad número CERO CERO OCHO CERO DOS TRES TRES CINCO - TRES, que actúo en mi calidad de Representante Legal de la Sociedad EDUCREDI RURAL S.A. DE C.V., lo cual compruebo con la respectiva credencial certificada y registrada en la oficina del Registro de Comercio de la Ciudad de San Salvador, y que he estado presente desde el inicio de este instrumento");
        $textrun->addText("DIGO: ", ['bold' => true]);
        $textrun->addText("Que acepto en todas sus partes el presente instrumento, así como la garantía  de DEUDORES SOLIDARIOS constituida. ");
        $textrun->addText("Y TODOS DECIMOS: ", ['bold' => true]);
        $textrun->addText("Que para todos los efectos judiciales y extrajudiciales a la que pudiere dar lugar la presente obligación señalamos como domicilios especiales los tribunales de las Ciudades de Sonsonate y Santa Ana, a cuyas jurisdicciones nos sometemos expresamente.  Así nos expresamos los comparecientes y por encontrarse redactado conforme a nuestra voluntad el presente instrumento, lo reconocemos y ratificamos en todo lo escrito, junto con los listados adjuntos de solicitud y acuerdo de préstamo que para constancia lo firmamos, en la ciudad de ");
        $textrun->addText($municipiomutuo);
        $textrun->addText(", Departamento ");
        $textrun->addText($deptomutuo);
        $textrun->addText(mb_strtolower($fechamutuocreadoTexto, 'UTF-8'));

        $section->addPageBreak();

        $segundofragmentos = [];
        $duifragmentos = [];

        foreach ($clientes as $cliente) {
            $edad = Carbon::parse($cliente->fecha_nacimiento)->age;
            $duiLetras = duiEnLetras($cliente->dui);

            // Solo el nombre completo en mayúsculas
            $segundofragmentos[] =
                strtoupper_utf8(trim("{$cliente->nombre} {$cliente->apellido}")) . ", de " .
                $edad . " años de edad, " .
                trim($cliente->profesion) . ", del domicilio de " .
                trim($cliente->nombre_municipio) . ", departamento de " .
                trim($cliente->nombre_departamento) . ";";
        }
        foreach ($clientes as $clientedui) {
            // Convertir el DUI a letras
            $duiEnTexto = duiEnLetras(trim($clientedui->dui));
            $duifragmentos[] = strtoupper_utf8($duiEnTexto);
        }

        // Agregar coma entre todos menos el último, y punto y coma al final
        $duiClientes = '';

        $count = count($duifragmentos);
        foreach ($duifragmentos as $index => $dui) {
            if ($index === $count - 1) {
                $duiClientes .= $dui . ';';
            } else {
                $duiClientes .= $dui . ', ';
            }
        }

        // Unir el texto de los clientes
        $segundotextoClientes = implode(' ', $segundofragmentos);

        // Crear nuevo textrun después del salto de página
        $nuevoTextrun = $section->addTextRun($paragraphStyle, $fontStyle);

        // Convertir a mayúsculas los datos que vienen de la base de datos
        $municipiomutuoMayus = strtoupper_utf8($municipiomutuo);
        $deptomutuoMayus = strtoupper_utf8($deptomutuo);

        // Continuar escribiendo después del salto
        $nuevoTextrun->addText("En la ciudad de, $municipiomutuoMayus, Departamento de $deptomutuoMayus, ");
        $nuevoTextrun->addText(mb_strtolower($frase, 'UTF-8'));
        $nuevoTextrun->addText("- ");
        $nuevoTextrun->addText("Ante mí JUAN JOSE MORAN PEÑATE, Notario, del domicilio de la ciudad de Santa Ana, Departamento de Santa Ana, comparecen los señores, ");
        $nuevoTextrun->addText($segundotextoClientes);
        $nuevoTextrun->addText(" que en lo sucesivo del presente instrumento se denominarán ");
        $nuevoTextrun->addText(strtoupper_utf8("$centro "));
        $nuevoTextrun->addText("personas a quienes no conozco y por ello las identifico por medio de sus Documentos Únicos de Identidad números en su orden: ");
        $nuevoTextrun->addText($duiClientes);
        $nuevoTextrun->addText(" Y ME DICEN: Que reconocen como suyas las firmas de cada una puestas al pie del anterior instrumento de MUTUO, todos los conceptos y obligaciones contenidas en el mismo, y en los listados adjuntos de solicitud y acuerdo de préstamo por medio del cual OTORGAN: ");
        $nuevoTextrun->addText("A) ", ['bold' => true]);
        $nuevoTextrun->addText("MONTO: Que han recibido en calidad de mutuo en forma COMUN Y SOLIDARIA la suma de ");
        $nuevoTextrun->addText(strtoupper_utf8($montoEnPalabras));
        $nuevoTextrun->addText("DÓLARES DE LOS ESTADOS UNIDOS DE AMÉRICA de parte de la “SOCIEDAD EDUCREDI RURAL, SOCIEDAD ANONIMA DE CAPITAL VARIABLE.”, que puede abreviarse como: EDUCREDI RURAL S.A. DE C.V., Institución Privada que en adelante denominaré como “LA ACREEDORA”, institución del domicilio de la ciudad de Santa Ana, con número de identificación tributaria: CERO SEIS UNO CUATRO-UNO CUATRO CERO SIETE CERO NUEVE-UNO CERO TRES-CERO, representada legalmente por el señor ");
        $nuevoTextrun->addText("EDER WALTER RAMIREZ FLORES", ['bold' => true]);
        $nuevoTextrun->addText(", de cincuenta  años de edad, Licenciado en Contaduría Pública, del domicilio de la ciudad y Departamento de Santa Ana, con Documento Único de Identidad número CERO CERO OCHO CERO DOS TRES TRES CINCO - TRES; ");
        $nuevoTextrun->addText("B) ", ['bold' => true]);
        $nuevoTextrun->addText("DESTINO DEL CREDITO: El dinero mutuado será utilizado para capital de trabajo o desarrollo de un proyecto; ");
        $nuevoTextrun->addText("C) INTERESES: ", ['bold' => true]);
        $nuevoTextrun->addText("La suma mutuada devengará el interés del ");
        $nuevoTextrun->addText(strtoupper_utf8($interesTexto));
        $nuevoTextrun->addText("POR CIENTO ");
        $nuevoTextrun->addText($unidadTexto);
        $nuevoTextrun->addText(", sobre saldo de capital, el cual podrá modificarse de acuerdo a las variaciones que determinen las autoridades monetarias del país y/o las autoridades competentes de la sociedad;");
        $nuevoTextrun->addText("D) MOROSIDAD: ", ['bold' => true]);
        $nuevoTextrun->addText("En caso de mora, reconocen pagar un interés del ");
        $nuevoTextrun->addText(strtoupper_utf8($interesTexto));
        $nuevoTextrun->addText("POR CIENTO ");
        $nuevoTextrun->addText($unidadTexto);
        $nuevoTextrun->addText(", adicional al interés pactado, sobre saldos de capital morosos a partir de un día después de la fecha de vencimiento en el pago de la cuota respectiva;");
        $nuevoTextrun->addText(" E) PLAZO: ", ['bold' => true]);
        $nuevoTextrun->addText("El plazo del presente préstamo es de ");
        $nuevoTextrun->addText(strtoupper_utf8($cantidadTexto));
        $nuevoTextrun->addText(" CUOTAS ");
        $nuevoTextrun->addText(strtoupper_utf8($unidadTexto));
        $nuevoTextrun->addText(", y comenzará el plazo a partir de la fecha de contratación, y cuyo vencimiento es el día");
        $nuevoTextrun->addText(strtoupper_utf8($fechaVencimientoTexto));
        $nuevoTextrun->addText(", y bajo las condiciones que adelante se dirán; ");
        $nuevoTextrun->addText(" F) PLAN DE AMORTIZACIÓN: ", ['bold' => true]);
        $nuevoTextrun->addText("Cancelaran la cantidad mutuada por medio de ");
        $nuevoTextrun->addText(strtoupper_utf8($cantidadTexto));
        $nuevoTextrun->addText(" CUOTAS ");
        $nuevoTextrun->addText(strtoupper_utf8($unidadTexto));
        $nuevoTextrun->addText(", fijas, sucesivas y vencidas de ");
        $textrun->addText($cuotaTexto);
        $textrun->addText(($cuotaEntero));
        $nuevoTextrun->addText(", cada una comprende capital e intereses, pagaderos los días MARTES ");
        $nuevoTextrun->addText(strtoupper_utf8($unidadTexto));
        $nuevoTextrun->addText(" comprendidas dentro del plazo;");
        $nuevoTextrun->addText(" G) LUGAR DE IMPUTACIÓN DE PAGOS: ", ['bold' => true]);
        $nuevoTextrun->addText("Todo pago lo harán en la institución financiera que “LA ACREEDORA” establezca para tal efecto y por el monto de la cuota establecida; ");
        $nuevoTextrun->addText(" H) OBLIGACIONES GENERALES: ", ['bold' => true]);
        $nuevoTextrun->addText("Se obligan los deudores a: ");
        $nuevoTextrun->addText("1) ", ['bold' => true]);
        $nuevoTextrun->addText("Entregar catorcenalmente un informe financiero de las operaciones que se han realizado con el dinero mutuado, durante la vigencia del préstamo; el no cumplimiento de esta condición dará a “LA ACREEDORA” el derecho de exigir la cancelación total de la deuda de inmediato; ");
        $nuevoTextrun->addText("2) ", ['bold' => true]);
        $nuevoTextrun->addText("La cuota de morosidad se aplicará a partir de un día después de la fecha de vencimiento de la cuota de pago y un mes de morosidad hará exigir el pago de la deuda de inmediato ");
        $nuevoTextrun->addText("como si fuera plazo vencido; ", ['bold' => true]);
        $nuevoTextrun->addText("3) ", ['bold' => true]);
        $nuevoTextrun->addText("A informar los problemas, retrasos o complicaciones que se susciten de naturaleza empresarial en el desarrollo de su proyecto o inversión en capital de trabajo; ");
        $nuevoTextrun->addText("4) ", ['bold' => true]);
        $nuevoTextrun->addText("A informar a la acreedora si cambiaremos de domicilio; ");
        $nuevoTextrun->addText("5) ", ['bold' => true]);
        $nuevoTextrun->addText("Cuando “LA ACREEDORA” así lo requiera deberá presentar inventario, balances, estados de resultados y demás contables que demuestren la situación real del proyecto financiado o de la inversión de capital de  trabajo realizada; ");
        $nuevoTextrun->addText("6) ", ['bold' => true]);
        $nuevoTextrun->addText("A permitir que “LA ACREEDORA” practique avalúos e inspecciones en el lugar de ejecución del proyecto o de inversión de capital de trabajo, cuando lo estime conveniente; ");
        $nuevoTextrun->addText("7) ", ['bold' => true]);
        $nuevoTextrun->addText("“LA ACREEDORA” podrá realizar auditorías o verificar en cualquier forma el uso de los fondos provenientes de éste préstamo; ");
        $nuevoTextrun->addText("8) ", ['bold' => true]);
        $nuevoTextrun->addText("A utilizar el dinero mutuado únicamente para realizar actividades de comercio licitas, liberando de cualquier responsabilidad legal a LA ACREEDORA por el incumplimiento de este numeral; y ");
        $nuevoTextrun->addText("9) ", ['bold' => true]);
        $nuevoTextrun->addText("Así mismo los DEUDORES SOLIDARIOS SE COMPROMETEN a firmar en su oportunidad los documentos de Dación en Pago o adjudicación de Pago según sea el caso a favor de “LA ACREEDORA” o de la persona que ésta estime conveniente, para el efectivo pago de la presente deuda; ");
        $nuevoTextrun->addText("I) SUSPENSIÓN DEL CRÉDITO Y CADUCIDAD DEL PLAZO: ", ['bold' => true]);
        $nuevoTextrun->addText("El plazo señalado se tendrá por caducado y la obligación a cargo de los deudores se volverá exigible en su totalidad en los siguientes casos: ");
        $nuevoTextrun->addText("1) ", ['bold' => true]);
        $nuevoTextrun->addText("Por incumplimiento de las obligaciones contraídas en el presente instrumento; ");
        $nuevoTextrun->addText("2) ", ['bold' => true]);
        $nuevoTextrun->addText("Por ejecución que en contra de los deudores inicien terceros, por deuda distinta a la presente; ");
        $nuevoTextrun->addText("3) ", ['bold' => true]);
        $nuevoTextrun->addText("Por determinación  que la información brindada por nosotros los deudores en la hoja de descripción del negocio sea falsa, que contraríen o violen de cualquier forma las normas establecidas, en cualquiera de sus postulados; ");
        $nuevoTextrun->addText("4) ", ['bold' => true]);
        $nuevoTextrun->addText("Por el atraso en el pago de una cuota de capital y/o intereses; ");
        $nuevoTextrun->addText("J) DOMICILIOS ESPECIALES:", ['bold' => true]);
        $nuevoTextrun->addText("En caso de acción judicial, nosotros los deudores señalamos como domicilios especiales los  de la ciudad de Sonsonate y la ciudad de Santa Ana, a cuyos tribunales nos sometemos expresamente. Será depositario de los bienes que les embarguen, la persona que “LA ACREEDORA” designe, a quien relevan de la obligación de rendir fianza y cuentas, siendo por cuenta de los deudores el pago de las costas procesales y cuanto otros gastos se hiciere con motivo de la cancelación de la presente deuda, aunque conforme a las reglas generales no sean condenadas en costas. ");
        $nuevoTextrun->addText("K) ACEPTACION DE DERECHOS: ", ['bold' => true]);
        $nuevoTextrun->addText("Por otra parte presente desde el inicio de este acto el señor ");
        $nuevoTextrun->addText("EDER WALTER RAMIREZ FLORES", ['bold' => true]);
        $nuevoTextrun->addText(", de cincuenta años de edad, Licenciado en Contaduría Pública, del domicilio de la ciudad y Departamento de Santa Ana, con Documento Único de Identidad número CERO CERO OCHO CERO DOS TRES TRES CINCO – TRES;, quien actúa en calidad de Representante Legal de EDUCREDI RURAL S.A. DE C.V., calidad que compruebo por haber tenido a la vista  la respectiva Credencial Certificada y Registrada en las Oficina del Registro de Comercio, de la Ciudad de San Salvador. ");
        $nuevoTextrun->addText("Y ME DICE: ", ['bold' => true]);
        $nuevoTextrun->addText("Que acepta en todas sus partes el presente instrumento, así como la garantía  de DEUDORES SOLIDARIOS constituida. ");
        $nuevoTextrun->addText("Y TODOS ME DICEN: ", ['bold' => true]);
        $nuevoTextrun->addText("Que para todos los efectos judiciales y extrajudiciales a la que pudiere dar lugar la presente obligación señalan como domicilio especial los tribunales de la Ciudad de Sonsonate, a cuya jurisdicción se someten expresamente.- Yo, el Notario DOY FE: Que las firmas puestas por las comparecientes se encuentran al documento que antecede la presente acta notarial SON AUTENTICAS por haber sido puestas a mi presencia y de su propio puño y letra por todos los comparecientes a quienes  expliqué los efectos legales de la presente ACTA NOTARIAL que consta de dos hojas útiles y leídas que se las hube íntegramente en un solo acto sin interrupción, la cual se encuentra redactada conforme su voluntad, la ratifican y para constancia firman conmigo. DE TODO DOY FE.- ");

        // Guardar y devolver el documento
        $filename = 'Mutuo Grupal ' . strtoupper(str_replace(' ', '_', $centro)) . ' '  . '.docx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
