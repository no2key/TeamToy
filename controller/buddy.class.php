<?php
if( !defined('IN') ) die('bad request');
include_once( AROOT . 'controller'.DS.'app.class.php' );

class buddyController extends appController
{
	function __construct()
	{
		parent::__construct();
		$this->check_login();
	}
	
	function index()
	{
		$data['title'] = $data['top_title'] = '团队成员';
		render( $data , 'web' , 'card' );
	}

	function data()
	{
		$params = array();
		
		if($content = send_request( 'team_members' ,  $params , token()  ))
		{
			$data = json_decode($content , 1);
			if( intval($data['error_code']) != 0 ) 
				return false;
			
			return render( $data , 'ajax' , 'raw'  );

		}

		return null;
	}

	function add()
	{

		//ajax_echo( print_r( $_REQUEST , 1 ) );
		$name = z(t(v('name')));
		if( strlen($name) < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

		$email = z(t(v('email')));
		if( strlen($email) < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

		$password = z(t(v('password')));
		if( strlen($password) < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );


		$params = array();
		$params['name'] = $name;
		$params['email'] = $email;
		$params['password'] = $password;

		if($content = send_request( 'user_sign_up' ,  $params , token()  ))
		{
			$data = json_decode($content , 1);
			
			if( $data['err_code'] != 0 ) return render( array( 'code' => $data['err_code'] , 'message' => $data['err_msg'] ) , 'rest' );

			return render( array( 'code' => 0 , 'data' =>  array( 'html' => render_html( array( 'item' => $data['data'] ) , AROOT . 'view' 
						. DS . 'layout' . DS . 'ajax' . DS . 'widget' . DS . 'buddy.tpl.html'  ) ) ) , 'rest' );
		}

		return render( array( 'code' => 100001 , 'message' => 'can not get api content' ) , 'rest' );
	}

	function admin_user()
	{
		$uid = intval(v('uid'));
		if( $uid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

		if( intval(v('set')) == 1 ) $level = '9';
		else $level = '1';

		$params = array();
		$params['uid'] = $uid;
		$params['level'] = $level;

		
		if($content = send_request( 'user_level' ,  $params , token()  ))
		{
			$data = json_decode($content , 1);
			if( $data['err_code'] != 0 ) return render( array( 'code' => $data['err_code'] , 'message' => $data['err_msg'] ) , 'rest' );
			
			return render( array( 'code' => 0 , 'data' =>  array( 'html' => render_html( array( 'item' => $data['data'] ) , AROOT . 'view' 
						. DS . 'layout' . DS . 'ajax' . DS . 'widget' . DS . 'buddy.tpl.html'  ) ) ) , 'rest' );
		}

		return render( array( 'code' => 1000012 , 'message' => 'can not get api content'.$content ) , 'rest' );
	}

	function user_close()
	{

		$uid = intval(v('uid'));
		if( $uid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );
		
		
		$params = array();
		$params['uid'] = $uid;

		if($content = send_request( 'user_close' ,  $params , token()  ))
		{
			$data = json_decode($content , 1);
			if( $data['err_code'] != 0 ) return render( array( 'code' => $data['err_code'] , 'message' => $data['err_msg'] ) , 'rest' );
			
			return render( array( 'code' => 0 , 'data' => $data['data'] ) , 'rest' );
		}

		return render( array( 'code' => 100001 , 'message' => 'can not get api content' ) , 'rest' );
	}

	

	
}