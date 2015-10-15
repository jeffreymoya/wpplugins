<?php
/*
Plugin Name: Hicaliber Document Handler
Plugin URI: http://hicaliber.net.au
Description: Handles generation, storing and request handling of documents
Version: 1.0
Author: hicaliber
Author URI: http://hicaliber.net.au
*/
include 'includes/doc-generator.php';

class DocHandler
{
  const REQUEST_URL = 'user/files';
  const GET_ID = 'id';
  const UPLOAD_FOLDER_PDF = 'pdf';
  const UPLOAD_FOLDER = 'uploads';

  public static function generate_pdf($view, $params=null)
  {
    return DocGenerator::generate_pdf($params, $view);
  }

  public static function request_url($filename)
  {
    return home_url( self::REQUEST_URL . '?' . self::GET_ID . '=' . $filename );
  }

  public static function pdf_upload_dir($filename=null)
  {
    $upload_path = plugin_dir_path( __FILE__ ) . DIRECTORY_SEPARATOR . self::UPLOAD_FOLDER . DIRECTORY_SEPARATOR;

    return $upload_path . self::UPLOAD_FOLDER_PDF . DIRECTORY_SEPARATOR . (empty($filename) ? '' : $filename);
  }

  public static function home($url)
  {
    return plugins_url($url, __FILE__);
  }
}

add_action('parse_request', 'hicaliber_doc_handler');
function hicaliber_doc_handler() 
{
  global $wp;

  if(strpos($wp->request, DocHandler::REQUEST_URL) !== false && isset($_GET[DocHandler::GET_ID]))
  {

  	  $filename = $_GET[DocHandler::GET_ID];
  	  
  	  if(!is_user_logged_in())
  	  {
  	  	wp_redirect(home_url('members/login?file=' . $filename));
  	  }
  	  
  	  $fileparts = explode('.', $filename);
  	  if(sizeof($fileparts) !== 2)
  	  {
  	  	die('Invalid File');
  	  }
  	  $ext = strtolower($fileparts[1]);

  	  switch ($ext) 
  	  {
  	  	case 'pdf':
  	  		{
  	  			$file = DocHandler::pdf_upload_dir($filename);
  	  			if(!file_exists($file))
  	  			{
  	  				die('File not found');
  	  			}

  	  			header('Content-type: application/pdf');
    				header('Content-Disposition: inline; filename="' . $filename . '"');
    				header('Content-Transfer-Encoding: binary');
    				header('Accept-Ranges: bytes');
    				@readfile($file);
  	  			break;
  	  		}
  	  	default:
  	  		# code...
  	  		break;
  	  }
  }
}

function redirect_to_file($redirect_to, $request) {
	$referer = parse_url($_SERVER['HTTP_REFERER']);
	if(isset($referer['query']))
	{
		$parts = explode('=', $referer['query']);
		if($parts[0] === 'file')
		{
			return DocHandler::request_url($parts[1]);
		}
	}
  return $redirect_to;
}
add_action('login_redirect', 'redirect_to_file', 10, 2);
?>