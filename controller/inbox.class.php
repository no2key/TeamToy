<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class inboxController extends appController
{
	function __construct()
	{
		parent::__construct();
		$this->check_login();
	}
	
	function index()
	{
		
		send_request( 'notice_mark_read' ,  array() , token());

		$data['title'] = $data['top_title'] = '收件箱';
		render( $data , 'web' , 'card' );
	}
	
	function qrlogin()
	{
		$token = $_SESSION['token'];
		$api = parse_url(c('api_server') , PHP_URL_HOST  );

		$data['url'] = $token . '|' . $api .'|'. $_SESSION['uname'] .'|' . uid() . '|' . $_SESSION['level'] ;
		render( $data , 'ajax' , 'raw' );
	}

	function client()
	{
		if(v('type') == 'android') $type = 'android';
		else $type = 'ios';
		$data['type'] = $type;

		$token = $_SESSION['token'];
		$api = parse_url(c('api_server') , PHP_URL_HOST  );

		$data['url'] = $token . '|' . $api .'|'. $_SESSION['uname'] .'|' . uid() . '|' . $_SESSION['level'] ;
		render( $data , 'ajax' , 'raw' );
	}

	
	function data()
	{
		$params = array();
		$params['max_id'] = intval(v('max_id'));
		
		if($content = send_request( 'notice_list' ,  $params , token()  ))
		{
			//echo $content;
			$data = json_decode($content , 1);
			if( intval($data['err_code']) != 0 ) 
				return false;
			
			return render( $data , 'ajax' , 'raw'  );

		}

		return null;
	}

	
}