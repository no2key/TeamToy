<?php
if( !defined('IN') ) die('bad request');
include_once( CROOT . 'controller' . DS . 'core.class.php' );

ini_set( 'display_errors' , true );
error_reporting(E_ALL ^ E_NOTICE);

class appController extends coreController
{
	function __construct()
	{
		// 载入插件
		$plugins = c('plugins');
		if( mysql_query("SHOW COLUMNS FROM `plugin`",db()) )
		if($pinfos = get_data("SELECT * FROM `plugin`"))
		{
			foreach( $pinfos as $pinfo )
			{
				if( intval($pinfo['on']) == 0 )
					$plugins = array_remove( $pinfo['folder_name'] , $plugins );
				elseif( !in_array( $pinfo['folder_name'] , $plugins ) )
					$plugins[] = $pinfo['folder_name'];	
			}
		}
		
		if( is_array($plugins) ) $plugins = array_unique( $plugins );
		if( isset($plugins) && is_array( $plugins ) )
		{
			
			foreach( $plugins as $plugin )
			{
				$plugin_file = c('plugin_path') . DS . basename($plugin) . DS . 'app.php';				
				if( file_exists( $plugin_file ) )
					require_once( $plugin_file );
			}
		}

		$GLOBALS['config']['plugins'] = $plugins;
		// update config for this time

		// 载入默认的
		parent::__construct();

		apply_filter( 'CTRL_' . g('c').'_'.g('a') .'_INPUT_FILTER' );
		
		if( g('c') != 'api' )
		{
			// set session time
			session_set_cookie_params( c('session_time') );
			@session_start();
		} 
	}
	
	function check_login()
	{
		if( !is_login() ) return info_page('您访问的页面需要先<a href="?c=guest">登入</a>');
	}
	
}


?>