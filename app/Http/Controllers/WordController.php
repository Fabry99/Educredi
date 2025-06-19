<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;

class WordController extends Controller
{
    public function test()
    {
        $phpWord = new PhpWord();

        // 🔧 Configurar estilo global (fuente, tamaño, color)
        $phpWord->setDefaultFontName('Calibri');   // Tipo de letra
        $phpWord->setDefaultFontSize(11);          // Tamaño

        // (Opcional) Establecer color y otros estilos usando el DefaultParagraphStyle
        $phpWord->setDefaultParagraphStyle([
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH, // LEFT | CENTER | RIGHT | BOTH
            'spaceAfter' => 200, // Espaciado después de párrafos
        ]);

        $section = $phpWord->addSection();

        // Este texto usará el estilo global
        $section->addText('Este texto usa la fuente global (Calibri, 14pt)');
        $section->addText('Otro texto más con el mismo estilo global.');

        $fileName = 'doc_estilo_global.docx';
        $tempPath = storage_path($fileName);
        $phpWord->save($tempPath, 'Word2007');

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}
