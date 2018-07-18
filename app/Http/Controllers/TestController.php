<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    // public function test(Request $request){
    // 		$phpWord = new \PhpOffice\PhpWord\PhpWord();
    //     $section = $phpWord->addSection();
    //     $description = "How are you?";
    //     $section->addText($description);
    //     $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    //     try {
    //         $objWriter->save(storage_path('helloWorld.docx'));
    //     } catch (Exception $e) {
    //     }
    //     return response()->download(storage_path('helloWorld.docx'));
    // }

    public function test(Request $request){
    	dd($request->lang);
    	dd($request->group);

    	header("Content-Type: application/vnd.msword");
		header("Expires: 0");//no-cache
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");//no-cache
		header("content-disposition: attachment;filename=sampleword.doc");

		$doc_body ="<div>Hi</div><div>How are you?<br></div>";
		echo "<html>";
		echo "$doc_body";
		echo "</html>";
    }
}
