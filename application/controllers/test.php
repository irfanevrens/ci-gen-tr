<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Test class
 *
 * @author Istvan Pusztai
 * @version 1.0.2 $Id: test.php 7 2009-09-29 06:23:41Z Istvan $
 * @copyright Copyright (C) 2010 Istvan Pusztai (twitter.com/istvanp)
 **/
 
class Test extends CI_Controller {

	var $timings = array();
	var $tests = array();

	public function __construct()
	{
		parent::__construct();
		
		// sadece lokalde
		if (!LOCAL) die();
		
		// Set time marker for the start of the test suite
		$this->benchmark->mark('first');
		
		log_message('debug', 'Test Controller Initialized');
		
		// Load the unit test library
		$this->load->library('unit_test');
		
		// Load syntax highlighting helper
		$this->load->helper('text');
		
		// Set mode to strict
		$this->unit->use_strict(TRUE);
		
		// Disable database debugging so we can test all units without stopping
		// at the first SQL error
		$this->db->db_debug = FALSE;
		
		// Create list of tests
		$this->_map_tests();
	}
	
	function deneme_model() {
	
		// goster
		$this->benchmark->mark('start');
		$test = 1 + 1;
		$this->unit->run($test, 2, 'deneme_model->goster()');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		
		// iptal
		$this->benchmark->mark('start');
		$test = 1 + 2;
		$this->unit->run($test, 3, 'deneme_model->iptal()');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
	}
	
	/**
	 * Remap function
	 * Maps the requested action to a method and performs the tests.
	 * Do not modify/delete this function.
	 *
	 * @author Istvan Pusztai
	 * @return void
	 **/
	function _remap()
	{	
		$view_data = array();
		$action = $this->uri->rsegment(2);
		$view_data['headings'] = array("types" => array(), "tests" => array());
		
		switch (strtolower($action))
		{
			case 'index':
				$view_data['msg'] = "Please pick a test suite";
			break;
			case 'all':
				$i = 0;
				foreach ($this->tests as $key => $type)
				{
					$view_data['headings']['types'][count($this->timings)] = ucfirst($key);
					foreach($type as $key2 => $method)
					{
						$view_data['headings']['tests'][count($this->timings)] = $method;
						call_user_func(array($this, $method));
					}
				}
			break;
			case 'models':
			case 'views':
			case 'libraries':
			case 'helpers':
				if (array_key_exists($action, $this->tests) && count($this->tests[$action]) > 0)
				{
					foreach ($this->tests[$action] as $method)
					{
						$view_data['headings']['tests'][count($this->timings)] = $method;
						call_user_func(array($this, $method));
					}
				}
				else
				{
					$view_data['msg'] = "There are no test suites for $action";
				}
			break;
			default:			
				if (array_search_recursive($action, $this->tests))
				{
					call_user_func(array($this, $action));
				}
				else
				{
					$view_data['msg'] = "<em>$action</em> is an invalid test suite";
				}
		}
		
		// Prepare report
		$report = $this->unit->result();
		
		// Prepare totals
		$view_data['totals']['all'] = count($report);
		$view_data['totals']['failed'] = 0;
		
		// Count failures
		foreach($report as $key => $test)
		{
			if ($test['Result'] == 'Failed')
			{
				++$view_data['totals']['failed'];
			}
		}
		
		// Count passes
		$view_data['totals']['passed'] = $view_data['totals']['all'] - $view_data['totals']['failed'];
		
		// Calculate the total time taken for the test suite
		$view_data['total_time'] = $this->benchmark->elapsed_time('first', 'end');
		
		// Other useful data
		$view_data['tests']		= $this->tests;
		$view_data['type']		= $action;
		$view_data['report']	= $report;
		$view_data['timings']	= $this->timings;
		
		$this->load->view('unit_test', $view_data);
	}

	/**
	 * Map Tests
	 * Creates a list of tests from the functions defined in this class.
	 * Do not modify/delete this function.
	 *
	 * @author Istvan Pusztai
	 * @return void
	 **/
	function _map_tests()
	{
		$methods = get_class_methods($this);
		natsort($methods);
		
		foreach ($methods as $method)
		{
			if (strpos($method, '_') !== 0
				AND $method != __CLASS__
				AND $method != "CI_Base"
				AND $method != "Controller"
				AND $method != "get_instance"
			)
			{
				$length = strlen($method);
				
				if (strripos($method, 'model') === $length - 5)
				{
					$this->tests['models'][] = $method;
				}
				else if (strripos($method, 'view')  === $length - 4)
				{
					$this->tests['views'][] = $method;
				}
				else if (strripos($method, 'library')  === $length - 7)
				{
					$this->tests['libraries'][] = $method;
				}
				else if (strripos($method, 'helper')  === $length - 6)
				{
					$this->tests['helpers'][] = $method;
				}
			}
		}
		
		return $this->tests;
	}
}
/**
 * Array Search (Rescursive)
 * Searches through an array for a value recursively
 * >>> Place this code in a helper if you use it elsewhere <<<
 *
 * @author Istvan Pusztai
 * @since 1.0.2
 * @param string $needle The value to look for
 * @param array $haystack The array to search
 * @param bool $strict Use strict comparison
 * @return bool
 **/
function array_search_recursive($needle, $haystack, $strict = FALSE, $path = array())
{
	if ( ! is_array($haystack))
	{
		return FALSE;
	}
 
	foreach ($haystack as $key => $val)
	{
		if (is_array($val) && $subPath = array_search_recursive($needle, $val, $strict, $path))
		{
			$path = array_merge($path, array($key), $subPath);
			return $path;
		}
		else if (( ! $strict && $val == $needle) || ($strict && $val === $needle))
		{
			$path[] = $key;
			return $path;
		}
	}
	
	return FALSE;
}
/* End of file test.php */