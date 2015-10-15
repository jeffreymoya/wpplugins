<?php

include_once plugin_dir_path ( __FILE__ ) . '../lib/MPDF57/mpdf.php';

class DocGenerator {
	public static function generate_pdf($params, $view, $save=true)
	{

		ob_start();
		include plugin_dir_path ( __FILE__ ) . '../templates/' . $view . '.php';
	    $html = ob_get_contents();
	    ob_end_clean();

	    $mpdf=new \mPDF();
	    $mpdf->useOnlyCoreFonts = true;
	    $mpdf->WriteHTML($html);

	    $filename = time() . '.pdf';

	    if(strlen($view) > 3)
	    {
	    	$filename = strtoupper(substr($view, 0, 3)) . $filename;
	    }
	    else
	    {
	    	$filename = $view . $filename;
	    }

	    if($save)
	    {
		    $mpdf->Output(DocHandler::pdf_upload_dir($filename), 'F');
		    return $filename;
	    }
	    else
	    {
		    $mpdf->Output();
	    }
	    exit;
	}
}