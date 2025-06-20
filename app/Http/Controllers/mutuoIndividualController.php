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

class mutuoIndividualController extends Controller
{
    public function obtenerdatosclientes($id)
    {

        $id_cliente = $id;
        $id_centro = '1';
        $id_grupo = '1';

        $historial = DB::table('historial_prestamos')
            ->where('id_cliente', $id_cliente)
            ->where('centro', $id_centro)
            ->where('grupo', $id_grupo)
            ->select(
                'id_cliente',
                'monto',
                'fecha_apertura',
                'fecha_vencimiento'
            )
            ->get();
        return response()->json($historial);
    }

    public function pdfmutuoindi(Request $request)
    {
        $id_cliente = $request->input('id_cliente');
        $nombrecliente = $request->input('nombrecliente');
        $montoprestamo = $request->input('montoprestamo');
        $deptomutuoind = $request->input('deptomutuoind');
        $municipiomutuoind = $request->input('municipiomutuoind');
        $textoDepto = $request->input('textoDepto');
        $textoMunicipio = $request->input('textoMunicipio');
        $fecha_generada = $request->input('fecha_generada');
        $fecha_apertura = $request->input('fecha_apertura');
        $fecha_vencimiento = $request->input('fecha_vencimiento');
        $id_centro = '1';
        $id_grupo = '1';
        $centro = 'INDIVIDUAL';

        $clienteindi = DB::table('saldoprestamo as sl')
            ->join('clientes as cl', 'sl.id_cliente', '=', 'cl.id')
            ->join('departamentos as dp', 'cl.id_departamento', '=', 'dp.id')
            ->join('municipios as mn', 'cl.id_municipio', '=', 'mn.id')
            ->where('sl.id_cliente', $id_cliente)
            ->where('sl.FECHAAPERTURA', $fecha_apertura)
            ->where('sl.FECHAVENCIMIENTO', $fecha_vencimiento)
            ->where('sl.centro', $id_centro)
            ->where('sl.groupsolid', $id_grupo)
            ->select(
                'cl.id',
                'cl.nombre as nombre_cliente',
                'cl.apellido',
                'cl.fecha_nacimiento',
                'cl.sector as profesion',
                'cl.dui',
                'sl.interes',
                'sl.plazo',
                'sl.cuota',
                'sl.MONTO',
                'dp.nombre as nombre_departamento',
                'mn.nombre as nombre_municipio',
                'sl.FECHAAPERTURA',
                'sl.FECHAVENCIMIENTO',
                DB::raw("(
                SELECT dias
                FROM debeser
                WHERE debeser.id_cliente = sl.id_cliente
                AND debeser.fecha_apertura = '$fecha_apertura'
                AND debeser.fecha_vencimiento = '$fecha_vencimiento'
                LIMIT 1
            ) as dias")
            )
            ->first();


        if (!$clienteindi) {
            return back()->with('error', 'No se encontró información del cliente con los datos proporcionados.');
        }

        function strtoupper_utf8($text)
        {
            return mb_strtoupper($text, 'UTF-8');
        }

        $edad = Carbon::parse($clienteindi->fecha_nacimiento)->age;
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es');

        $edadEnLetras = $numberTransformer->toWords($edad);

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
        function plazoEnTextoSeparado($plazo, $dias, $numberTransformer)
        {
            if ($dias <= 0 || $plazo <= 0) return ['cantidad' => 'PLAZO INVÁLIDO', 'unidad' => ''];

            $unidad = '';

            switch ((int) $dias) {
                case 1:
                    $unidad = 'día';
                    break;
                case 7:
                    $unidad = 'semanale';
                    break;
                case 14:
                    $unidad = 'catorcenale';
                    break;
                case 15:
                    $unidad = 'quincenale';
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

        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es');
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

            $diaTexto = mb_strtoupper($numberTransformer->toWords($dia), 'UTF-8');
            $mesTexto = $meses[$mes];
            $anioTexto = mb_strtoupper($numberTransformer->toWords($anio), 'UTF-8');

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

        $fechamutuocreadoTexto = fechaEnTextoFormal($fecha_generada, $numberTransformer);



        $plazoValor = (int) ($clienteindi->plazo ?? 0);
        $diasValor = (int) ($clienteindi->dias ?? 1);
        $plazoPartes = plazoEnTextoSeparado($plazoValor, $diasValor, $numberTransformer);
        $cantidadTexto = $plazoPartes['cantidad']; // Ejemplo: CUATRO
        $unidadTexto = $plazoPartes['unidad'];

        $currencyTransformer = $numberToWords->getCurrencyTransformer('es');
        $montoEnPalabras = $currencyTransformer->toWords($clienteindi->MONTO * 100, 'USD'); // monto en centavos

        $duiTexto = duiEnLetras($clienteindi->dui);

        $fechaVencimientoTexto = fechaEnTexto($clienteindi->FECHAVENCIMIENTO, $numberTransformer);

        $cuotaNumero = $clienteindi->cuota ?? 0;
        // Si cuota tiene decimales, puedes manejar con round o cast a entero si es apropiado
        $cuotaEntero = (int) round($cuotaNumero);
        $cuotaTexto = strtoupper($numberTransformer->toWords($cuotaEntero));


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
        $horayfecha = "a las " . mb_strtolower($horaTexto, 'UTF-8') . " horas del día " .
            mb_strtolower($diaTexto, 'UTF-8') . " de " .
            mb_strtolower($mesTexto, 'UTF-8') . " de " .
            mb_strtolower($anioTexto, 'UTF-8') . ".";

        $estiloRojo = ['color' => 'FF0000'];


        // Crear documento Word
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $numberToWords = new NumberToWords();
        $currencyTransformer = $numberToWords->getCurrencyTransformer('es');
        $fontStyle = ['name' => 'Calibri', 'size' => 11, 'lang' => 'es-ES'];
        $paragraphStyle = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH, 'lineHeight' => 2.0];
        $textrun = $section->addTextRun($paragraphStyle, $fontStyle);
        $textrun->addText("YO, ");
        $textrun->addText(strtoupper_utf8($clienteindi->nombre_cliente . ' ' . $clienteindi->apellido));
        $textrun->addText(" de, " . ("{$edadEnLetras}") . "años de edad, ");
        $textrun->addText(strtoupper_utf8($clienteindi->profesion));
        $textrun->addText(" del domicilio de ");
        $textrun->addText(strtoupper_utf8(trim($clienteindi->nombre_municipio)));
        $textrun->addText(", Departamento de ");
        $textrun->addText(strtoupper_utf8(trim($clienteindi->nombre_departamento)));
        $textrun->addText(", Con Documento Único de Identidad número: ");
        $textrun->addText(strtoupper_utf8($duiTexto));
        $textrun->addText("; que en lo sucesivo del presente instrumento me denominare ");
        $textrun->addText("“EL (LA) DEUDOR (A)”, ", ['bold' => true]);
        $textrun->addText("OTORGO:  a) ");
        $textrun->addText("MONTO: ", ['bold' => true]);
        $textrun->addText("que la “SOCIEDAD EDUCREDI RURAL, S.A. DE C.V.”, Institución Privada que en adelante se denominará “LA ACREEDORA”, del domicilio de la ciudad de Santa Ana, con número de identificación tributaria: CERO SEIS CATORCE GUION CIENTO CUARENTA MIL SETECIENTOS NUEVE GUION CIENTO TRES GUION CERO, representada legalmente por el señor EDER WALTER RAMIREZ FLORES, de cincuenta años de edad, Licenciado en contaduría pública, del domicilio de la ciudad de Santa Ana, con Documento Único de Identidad número cero cero ocho cero dos tres tres cinco guion tres; me otorgara un crédito y entregado a título de MUTUO por la suma de ");
        $textrun->addText(strtoupper_utf8($montoEnPalabras));
        $textrun->addText(" DE LOS ESTADOS UNIDOS DE AMÉRICA; b) ");
        $textrun->addText("DESTINO: ", ['bold' => true]);
        $textrun->addText("PARA CAPITAL DE TRABAJO; c) ");
        $textrun->addText("INTERESES: ", ['bold' => true]);
        $textrun->addText("La suma mutuada devengará el interés del SIETE   PUNTO TREINTA Y OCHO POR CIENTO MENSUAL  sobre saldo de capital, el cual podrá modificarse de acuerdo a las variaciones que determinen las autoridades monetarias del país y/o las autoridades competentes; d) ");
        $textrun->addText("MOROSIDAD: ", ['bold' => true]);
        $textrun->addText("En caso de mora, se reconocerá una tasa de interés del VEINTE POR CIENTO MENSUAL, sobre saldos de capital morosos a partir de un día después de la fecha de vencimiento en el pago de la cuota respectiva; e) ");
        $textrun->addText("PLAZO: ", ['bold' => true]);
        $textrun->addText("El plazo del presente préstamo es de ");
        $textrun->addText(strtoupper_utf8($cantidadTexto));
        $textrun->addText(" CUOTAS ");
        $textrun->addText(strtoupper_utf8($unidadTexto));
        $textrun->addText(", y comenzará a partir de la fecha de contratación, con vencimiento el día ");
        $textrun->addText($fechaVencimientoTexto);
        $textrun->addText(", y bajo las condiciones que adelante se dirán; f) ");
        $textrun->addText("DESEMBOLSO: ", ['bold' => true]);
        $textrun->addText("un solo desembolso el cual se llevará a cabo al momento de escriturar; g) ");
        $textrun->addText("PLAN DE AMORTIZACIÓN: ", ['bold' => true]);
        $textrun->addText("Cancelare la cantidad mutuada por medio de ");
        $textrun->addText(strtoupper_utf8($cantidadTexto));
        $textrun->addText(" CUOTAS ");
        $textrun->addText(strtoupper_utf8($unidadTexto));
        $textrun->addText(" fijas sucesivas y vencidas de ");
        $textrun->addText("$cuotaTexto ($$cuotaEntero)");
        $textrun->addText(", que comprende capital, intereses IVA y seguro de deuda, pagaderas los días LUNES de cada mes dentro del plazo; h) ");
        $textrun->addText("LUGAR DE IMPUTACIÓN DE PAGOS: ", ['bold' => true]);
        $textrun->addText("Todo pago lo hare en la institución bancaria que “LA ACREEDORA” establezca para tal efecto y por el monto de la cuota establecida; i) ");
        $textrun->addText("OBLIGACIONES GENERALES: ", ['bold' => true]);
        $textrun->addText("queda obligado (a) el (la) deudor (a) a: 1) la entrega mensual de un informe financiero de las operaciones que se han realizado con el dinero mutuado, durante la vigencia del préstamo; el no cumplimiento de esta condición dará a “LA ACREEDORA” el derecho de exigir la cancelación total de la deuda de inmediato; 2) La cuota de morosidad se aplicará a partir de un día después de la fecha de vencimiento de la cuota de pago y un mes de morosidad hará exigir el pago de la deuda de inmediato; 3) A informar los problemas que se susciten de naturaleza empresarial en el desarrollo de su proyecto; 4) Si cambiare su domicilio; 5) Cuando “LA ACREEDORA” así lo requiera deberá presentar inventario, balances, estados de resultados y demás contables que demuestren la situación real del proyecto financiado; 6) A permitir que “LA ACREEDORA” practique avalúos e inspecciones en el lugar de ejecución del proyecto cuando lo estime conveniente;  7) A permitir que “LA ACREEDORA” pueda realizar auditorías o verificar en cualquier forma el uso de los fondos provenientes de éste préstamo; y 8) Asimismo se compromete  el deudor (a)  a firmar en su oportunidad los documentos de Dación en Pago o adjudicación de Pago según sea el caso a favor de “LA ACREEDORA” o de la persona que ésta estime conveniente, para el efectivo pago de la presente deuda; j) ");
        $textrun->addText("GARANTIA: ", ['bold' => true]);
        $textrun->addText("El Presente crédito queda garantizado por el record crediticio del deudor y con prenda sin desplazamiento sobre los activos del negocio financiado; la prenda que hoy constituye el deudor a favor de la acreedora estará vigente durante todo el plazo del presente contrato y mientras exista saldo pendiente de pago a cargo del deudor y a favor de la acreedora. Si las prendas se destruyesen o deteriorasen, al grado que no sea suficiente para garantizar la obligación  de parte de la deudora, la acreedora tendrá derecho a exigir otras garantías y si la parte deudora no se allanare a ello o no pudiere cumplir con tal requisito  vencerá el plazo del presente contrato y se volverá exigible la suma prestada y sus respectivos intereses; K) ");
        $textrun->addText("SUSPENSIÓN DEL CRÉDITO Y CADUCIDAD DEL PLAZO: ", ['bold' => true]);
        $textrun->addText("El plazo señalado se tendrá por caducado y la obligación a cargo del deudor (a) se volverá exigible en su totalidad en los siguientes casos: 1) Por incumplimiento de las obligaciones contraídas en el presente instrumento; 2) Por ejecución que contra el deudor (a) inicien terceros, por deuda distinta a la presente; 3) Por contener la información o documentación proporcionada por el deudor (a), datos que no sean veraces, que contraríen o violen de cualquier forma las normas establecidas; L) ");
        $textrun->addText("DOMICILIO ESPECIAL: ", ['bold' => true]);
        $textrun->addText("En caso de acción judicial, el deudor (a) señala como domicilio especial el de la ciudad de Santa Ana, a cuyos tribunales se somete expresamente. Será depositario de los bienes que les embarguen, la persona que “LA ACREEDORA” designe, a quien relevan de la obligación de rendir fianza y cuentas, siendo a cargo del “DEUDOR (A)” el pago de las costas procesales y cuanto otros gastos se hicieren con motivo de la cancelación de la presente deuda, aunque conforme a las reglas generales no sean condenadas a ellas. Así se expresan los comparecientes y por encontrarse redactado conforme a su voluntad el presente instrumento, lo reconocen y ratifican en todo lo escrito, junto con los listados adjuntos de solicitud y acuerdo de préstamo que para constancia lo firma, ");
        $textrun->addText("en la ciudad de $textoMunicipio, Departamento de $textoDepto ");
        $textrun->addText(mb_strtolower($fechamutuocreadoTexto, 'UTF-8'));
        $textrun->addText(".-");
        $section->addPageBreak();
        $nuevoTextrun = $section->addTextRun($paragraphStyle, $fontStyle);
        $nuevoTextrun->addText("En la ciudad de $textoMunicipio ");
        $nuevoTextrun->addText("a las " . mb_strtolower($horaTexto, 'UTF-8') . " horas del día ");
        $nuevoTextrun->addText(mb_strtolower($diaTexto, 'UTF-8'), $estiloRojo);
        $nuevoTextrun->addText(" de ", $estiloRojo);
        $nuevoTextrun->addText(mb_strtolower($mesTexto, 'UTF-8'), $estiloRojo);
        $nuevoTextrun->addText(" de ", $estiloRojo);
        $nuevoTextrun->addText(mb_strtolower($anioTexto, 'UTF-8'), $estiloRojo);
        $nuevoTextrun->addText(". Ante mí JUAN JOSE MORAN PEÑATE, Notario del domicilio de Santa Ana, departamento de Santa Ana, comparece el (la) señor (a) , ");
        $nuevoTextrun->addText(strtoupper_utf8($clienteindi->nombre_cliente . ' ' . $clienteindi->apellido));
        $nuevoTextrun->addText(", de, " . ("{$edadEnLetras}") . "años de edad, ");
        $nuevoTextrun->addText(strtoupper_utf8($clienteindi->profesion));
        $nuevoTextrun->addText(" del domicilio de ");
        $nuevoTextrun->addText(strtoupper_utf8(trim($clienteindi->nombre_municipio)));
        $nuevoTextrun->addText(", Departamento de ");
        $nuevoTextrun->addText(strtoupper_utf8(trim($clienteindi->nombre_departamento)));
        $nuevoTextrun->addText(", Con Documento Único de Identidad número: ");
        $nuevoTextrun->addText(strtoupper_utf8($duiTexto));
        $nuevoTextrun->addText("; y ");
        $nuevoTextrun->addText("ME DICE: ", ['bold' => true]);
        $nuevoTextrun->addText("Que reconoce como suya la firma puesta al pie del anterior instrumento de MUTUO, todos los conceptos y obligaciones contenidas en el mismo, y en los listados adjuntos de solicitud y acuerdo de préstamo por medio del cual OTORGA: a) ");
        $nuevoTextrun->addText("MONTO: ", ['bold' => true]);
        $nuevoTextrun->addText("que la “SOCIEDAD EDUCREDI RURAL, S.A. DE C.V.”, Institución Privada que en adelante se denominará “LA ACREEDORA”, del domicilio de la ciudad de Santa Ana, con número de identificación tributaria número CERO SEIS CATORCE GUION CIENTO CUARENTA MIL SETECIENTOS NUEVE GUION CIENTO TRES GUION CERO, representada legalmente por el señor EDER WALTER RAMIREZ FLORES, de cincuenta años de edad, Licenciado en contaduría pública, del domicilio de Santa Ana, con Documento Único de Identidad número cero cero ocho cero dos tres tres cinco guion tres; personería que doy fe de ser legitima porque tuve a la vista: a) constitución de sociedad “SOCIEDAD EDUCREDI RURAL, S.A. DE C.V.” . b) CREDENCIAL DE ELECCION DE ADMINISTRADOR UNICO, PROPIETARIO Y SUPLENTE de la sociedad EDUCREDI RURAL, SOCIEDAD ANONIMA DE CAPITAL VARIABLE donde el señor EDER WALTER RAMIREZ FLORES fue electo para llevar la representación legal de la sociedad y que les ha otorgado un crédito y entregado a título de ");
        $nuevoTextrun->addText("MUTUO ", ['bold' => true]);
        $nuevoTextrun->addText("la suma de ");
        $nuevoTextrun->addText(strtoupper_utf8($montoEnPalabras));
        $nuevoTextrun->addText(" DE LOS ESTADOS UNIDOS DE AMÉRICA; b) ");
        $nuevoTextrun->addText("DESTINO: ", ['bold' => true]);
        $nuevoTextrun->addText("PARA CAPITAL DE TRABAJO; c) ");
        $nuevoTextrun->addText("INTERESES: ", ['bold' => true]);
        $nuevoTextrun->addText("La suma mutuada devengará el interés del SIETE   PUNTO TREINTA Y OCHO POR CIENTO MENSUAL, sobre saldo de capital, el cual podrá modificarse de acuerdo a las variaciones que determinen las autoridades monetarias del país y/o las autoridades competentes; d) ");
        $nuevoTextrun->addText("MOROSIDAD: ", ['bold' => true]);
        $nuevoTextrun->addText("En caso de mora, se reconocerá una tasa de interés del VEINTE POR CIENTO MENSUAL, sobre saldos de capital morosos a partir de un día después de la fecha de vencimiento en el pago de la cuota respectiva; e) ");
        $nuevoTextrun->addText("PLAZO: ", ['bold' => true]);
        $nuevoTextrun->addText("El plazo del presente préstamo es de ");
        $nuevoTextrun->addText(strtoupper_utf8($cantidadTexto));
        $nuevoTextrun->addText(" CUOTAS ");
        $nuevoTextrun->addText(strtoupper_utf8($unidadTexto));
        $nuevoTextrun->addText(", y comenzará a partir de la fecha de contratación, con vencimiento el día ");
        $nuevoTextrun->addText($fechaVencimientoTexto);
        $nuevoTextrun->addText(", y bajo las condiciones que adelante se dirán; f) ");
        $nuevoTextrun->addText("DESEMBOLSO: ", ['bold' => true]);
        $nuevoTextrun->addText("un solo desembolso el cual se llevará a cabo al momento de escriturar; g) ");
        $nuevoTextrun->addText("PLAN DE AMORTIZACIÓN: ", ['bold' => true]);
        $nuevoTextrun->addText("Cancelará la cantidad mutuada por medio de ");
        $nuevoTextrun->addText(strtoupper_utf8($cantidadTexto));
        $nuevoTextrun->addText(" CUOTAS ");
        $nuevoTextrun->addText(strtoupper_utf8($unidadTexto));
        $nuevoTextrun->addText(" fijas sucesivas y vencidas  de ");
        $nuevoTextrun->addText("$cuotaTexto ($$cuotaEntero)");
        $nuevoTextrun->addText(" CENTAVOS DE  DOLARES DE LOS ESTADOS UNIDOS DE NORTEAMERICA ");
        $nuevoTextrun->addText(", que comprende capital, intereses, IVA y seguro de deuda, pagaderas los días LUNES de cada mes dentro del plazo; h)");
        $nuevoTextrun->addText("LUGAR DE IMPUTACIÓN DE PAGOS: ", ['bold' => true]);
        $nuevoTextrun->addText("Todo pago lo hará en la institución bancaria que “LA ACREEDORA” establezca para tal efecto y por el monto de la cuota establecida; i) ");
        $nuevoTextrun->addText("OBLIGACIONES GENERALES: ", ['bold' => true]);
        $nuevoTextrun->addText("queda obligado el deudor (a) a: 1) la entrega mensual de un informe financiero de las operaciones que se han realizado con el dinero mutuado, durante la vigencia del préstamo; el no cumplimiento de esta condición dará a “LA ACREEDORA” el derecho de exigir la cancelación total de la deuda de inmediato; 2) La cuota de morosidad se aplicará a partir de un día después de la fecha de vencimiento de la cuota de pago y un mes de morosidad hará exigir el pago total de la deuda de inmediato; 3) A informar los problemas que se susciten de naturaleza empresarial en el desarrollo de su proyecto; 4) Si cambiaren su domicilio; 5) Cuando “LA ACREEDORA” así lo requiera deberán presentar inventario, balances, estados de resultados y demás contables que demuestren la situación real del proyecto financiado; 6) A permitir que “LA ACREEDORA” practique avalúos e inspecciones en el lugar de ejecución del proyecto cuando lo estime conveniente;  7) A permitir que “LA ACREEDORA” pueda realizar auditorías o verificar en cualquier forma el uso de los fondos provenientes de éste préstamo; y 8) Asimismo se compromete  la deudora a firmar en su oportunidad los documentos de Dación en Pago o adjudicación de Pago según sea el caso a favor de “LA ACREEDORA” o de la persona que ésta estime conveniente, para el efectivo pago de la presente deuda; j) ");
        $nuevoTextrun->addText("GARANTIA: ", ['bold' => true]);
        $nuevoTextrun->addText("El Presente crédito queda garantizado por el record crediticio del deudor y con prenda sin desplazamiento sobre los activos del negocio financiado; la prenda que hoy constituye el deudor a favor de la acreedora estará vigente durante todo el plazo del presente contrato y mientras exista saldo pendiente de pago a cargo del deudor y a favor de la acreedora. Si las prendas se destruyesen o deteriorasen, al grado que no sea suficiente para garantizar la obligación  de parte de la deudora, la acreedora tendrá derecho a exigir otras garantías y si la parte deudora no se allanare a ello o no pudiere cumplir con tal requisito  vencerá el plazo del presente contrato y se volverá exigible la suma prestada y sus respectivos intereses;  k) ");
        $nuevoTextrun->addText("SUSPENSIÓN DEL CRÉDITO Y CADUCIDAD DEL PLAZO: ", ['bold' => true]);
        $nuevoTextrun->addText("El plazo señalado se tendrá por caducado y la obligación a cargo del deudor (a) se volverá exigible en su totalidad en los siguientes casos: 1) Por incumplimiento de las obligaciones contraídas en el presente instrumento; 2) Por ejecución que contra el deudor (a) inicien terceros, por deuda distinta a la presente; 3) Por contener la información o documentación proporcionada por el deudor (a), datos que no sean veraces, que contraríen o violen de cualquier forma las normas establecidas; L) ");
        $nuevoTextrun->addText("DOMICILIO ESPECIAL: ", ['bold' => true]);
        $nuevoTextrun->addText("En caso de acción judicial, el deudor (a) señala como domicilio especial el de la ciudad de Santa Ana, a cuyos tribunales se somete expresamente. Será depositario de los bienes que les embarguen, la persona que “LA ACREEDORA” designe, a quien relevan de la obligación de rendir fianza y cuentas, siendo a cargo del “DEUDOR (A)” el pago de las costas procesales y cuanto otros gastos se hicieren con motivo de la cancelación de la presente deuda, aunque conforme a las reglas generales no sean condenadas a ellas. Yo, el Notario DOY FE: que las firmas puestas por los comparecientes SON AUTENTICAS por haber sido puesta a mi presencia y de su propio puño y letra a quien ya identifiqué con sus respectivo Documento Único de Identidad y Número de Identificación Tributaria, de generales antes expresada en este presente ACTA NOTARIAL que consta de tres hojas útiles y leída que se las hube íntegramente en un solo acto sin interrupción, la encuentran redactada conforme a su voluntad, la ratifican y para constancia firman conmigo. DE TODO DOY FE.");




        // Generar y devolver el documento
        $filename = 'Mutuo ' . strtoupper(str_replace(' ', ' ', $centro)) . '.docx';
        $temp_file = tempnam(sys_get_temp_dir(), 'word_');

        try {
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($temp_file);
            return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al generar el documento.');
        }
    }
}
