<?php
/**
 * CodeIgniter Log Library
 *
 * @category   Applications
 * @package    CodeIgniter
 * @subpackage Libraries
 * @author     Bo-Yi Wu <appleboy.tw@gmail.com>
 * @license    BSD License
 * @link       http://blog.wu-boy.com/
 * @since      Version 1.0
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Lib_log
{
    /**
     * ci
     *
     * @param instance object
     */
    private $_ci;

    /**
     * log table name
     *
     * @param string
     */
    private $_log_table_name;

    public $levels = array(
        E_ERROR             => 'Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parsing Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable error',
        E_DEPRECATED        => 'Runtime Notice',
        E_USER_DEPRECATED   => 'User Warning'
    );

    /**
     * constructor
     *
     */
    public function __construct()
    { 
        $this->_ci =& get_instance();

        if ($_SERVER['LOCAL_ADDR'] == '192.168.0.14' && constant('ENVIRONMENT') != 'development' && get_cookie('show_errors') != '0754b9cc1dc52f241567a16374546132') { 
        
            set_error_handler(array($this, 'error_handler'));
            set_exception_handler(array($this, 'exception_handler'));
            
            // Load database driver
            $this->_ci->load->database();
            $this->_ci->_last_query = $this->_ci->db->last_query();
            
            // Load config file
            $this->_log_table_name = 'errors';
            
        }
    }
    
    
    
    
    private function error_send_mail($filepath, $line, $message, $severity, $trace=false) {
        
        $check = $this->_ci->db->select('err_id')
            ->where('err_str', $message)
            ->where('err_cleared', null)
            ->limit(1)
            ->get($this->_log_table_name);
        
        if ($check->num_rows() == 0) {
            
            ob_start();
            
            print $message;
            
            /*print "

-----------
LAST QUERY:
            
".$this->_ci->_last_query;*/
            
            if ($trace != false) {
                print "
                
----------
BACKTRACE:";
                foreach ($trace as $t_error) {
                    if (isset($t_error['file']) && strpos($t_error['file'], realpath(constant('BASEPATH'))) !== 0) { 
                        print "
        
File: ".$t_error['file']."
Line: ".$t_error['line']."
Function: ".$t_error['function'];
                    }
                }
            }
            
            $message_content = ob_get_contents();
            ob_end_clean();
            
            $this->_ci->load->library('email');

            $config['mailtype'] = 'text';

            $this->_ci->email->initialize($config);
            
            $uri = (is_cli()) ? 'cli' : $_SERVER['REQUEST_URI']; 

            $message_body = "
Path: ".$filepath."  
Line: ".$line." 
URI: ".$uri."
".(defined('UID')?"User: ".constant('UID'):'')."
 
Message: 

" . $message_content . " 
 
";
        
            $this->_ci->email->from("noreply@takumimado.com")
                ->to("tony.ryan@takumiprecision.com")
                ->cc("keith@assureweb.ie")
                ->subject('Takumi Mado Error: '.(isset($this->levels[$severity])?$this->levels[$severity]:$severity))
                ->message($message_body)	
                ->send();
            
        }
        
    }

    /**
     * PHP Error Handler
     *
     * @param   int
     * @param   string
     * @param   string
     * @param   int
     * @return void
     */
    public function error_handler($severity, $message, $filepath, $line)
    {
        
        $this->error_send_mail($filepath, $line, $message, $severity);
		
		$post_raw = $this->_ci->input->post();
        $post_log = array();
        foreach($post_raw as $k=>$v) {
            if (strpos($k, 'pass') === false) {
                $post_log[$k] = $v;
            }   
        }
        
        $uri = (is_cli()) ? 'cli' : $_SERVER['REQUEST_URI']; 
        
        $data = array(
			'err_site'		=> 'mado',
            'err_uid' 		=> defined('UID') ? constant('UID') : null,
            'err_no' 		=> $severity,
            'err_type' 		=> isset($this->levels[$severity]) ? $this->levels[$severity] : $severity,
            'err_str' 		=> $message,
            'err_file' 		=> $filepath,
            'err_line' 		=> $line,
            'err_ua' 		=> $this->_ci->input->user_agent(),
            'err_ip' 		=> $this->_ci->input->ip_address(),
            'err_time' 		=> date('Y-m-d H:i:s'),
            'err_class'     => $this->_ci->router->class,
            'err_method'    => $this->_ci->router->method,
            'err_uri'       => $uri,
            'err_post'      => json_encode($post_log),
        );

        $this->_ci->db->insert($this->_log_table_name, $data);
        
    }

    /**
     * PHP Error Handler
     *
     * @param   object
     * @return void
     */
    public function exception_handler($exception)
    {
        
        $this->error_send_mail($exception->getFile(), $exception->getLine(), $exception->getMessage(), $exception->getCode(), $exception->getTrace());
		
		$post_raw = $this->_ci->input->post();
        $post_log = array();
        foreach($post_raw as $k=>$v) {
            if (strpos($k, 'pass') === false) {
                $post_log[$k] = $v;
            }   
        }
        
        $uri = (is_cli()) ? 'cli' : $_SERVER['REQUEST_URI']; 
        
        $data = array(
			'err_site'		=> 'mado',
            'err_uid' 		=> defined('UID') ? constant('UID') : null,
            'err_no' 		=> $exception->getCode(),
            'err_type' 		=> isset($this->levels[$exception->getCode()]) ? $this->levels[$exception->getCode()] : $exception->getCode(),
            'err_str' 		=> $exception->getMessage(),
            'err_file' 		=> $exception->getFile(),
            'err_line' 		=> $exception->getLine(),
            'err_ua' 		=> $this->_ci->input->user_agent(),
            'err_ip' 		=> $this->_ci->input->ip_address(),
            'err_time' 		=> date('Y-m-d H:i:s'),
            'err_class'     => $this->_ci->router->class,
            'err_method'    => $this->_ci->router->method,
            'err_uri'       => $uri,
            'err_post'      => json_encode($post_log),
        );

        $this->_ci->db->insert($this->_log_table_name, $data);
        
        echo '<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">
        <h4>An error has occured</h4>
        <p>This has been reported to IT</p>';
    
        if (isset($_SERVER['HTTP_REFERER']))
            echo "<p><a href='".$_SERVER['HTTP_REFERER']."'>&laquo; go back</a></p>";
    
        echo '<p><a href="https://takumimado.com">&raquo; Takumi Mado Homepage</a></p>';
        
        echo '</div>';
    }
}

/* End of file Lib_log.php */
