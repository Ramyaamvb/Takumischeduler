<?php
// fixed by sirderno 2013

if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class MY_Session extends CI_Session
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Update an existing session
     *
     * @access  public
     * @return  void
     */
    public function sess_update()
    {
        // We only update the session every five minutes by default
        if (($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now)
        {
            return;
        }

        // Save the old session id so we know which record to
        // update in the database if we need it
        $old_sessid = $this->userdata['session_id'];
        $new_sessid = '';
        while (strlen($new_sessid) < 32)
        {
            $new_sessid .= mt_rand(0, mt_getrandmax());
        }

        // To make the session ID even more secure we'll combine it with the user's IP
        $new_sessid .= $this->CI->input->ip_address();

        // Turn it into a hash
        $new_sessid = md5(uniqid($new_sessid, TRUE));

        // Update the session data in the session data array
        $this->userdata['session_id'] = $new_sessid;
        $this->userdata['last_activity'] = $this->now;

        // _set_cookie() will handle this for us if we aren't using database sessions
        // by pushing all userdata to the cookie.
        $cookie_data = NULL;

        // Update the session ID and last_activity field in the DB if needed
        if ($this->sess_use_database === TRUE)
        {
            // set cookie explicitly to only have our session data
            $cookie_data = array();
            foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
            {
                $cookie_data[$val] = $this->userdata[$val];
            }

            $cookie_data['session_id'] = $new_sessid;  // added to solve bug

                    //added to solve bug
            if (!empty($this->userdata['user_data']))
                $cookie_data['user_data'] = $this->userdata['user_data'];

            $this->CI->db->query($this->CI->db->update_string($this->sess_table_name, array('last_activity' => $this->now, 'session_id' => $new_sessid), array('session_id' => $old_sessid)));

        }

        // Write the cookie
        $this->_set_cookie($cookie_data);
    }

    /**
     * Write the session cookie
     *
     * @access  public
     * @return  void
     */
    public function _set_cookie($cookie_data = NULL)
    {
        if (is_null($cookie_data))
        {
            $cookie_data = $this->userdata;
        }

        // Serialize the userdata for the cookie
        $cookie_data = $this->_serialize($cookie_data);

        if ($this->sess_encrypt_cookie == TRUE)
        {
            $cookie_data = $this->CI->encrypt->encode($cookie_data);
        }
        else
        {
            // if encryption is not used, we provide an md5 hash to prevent userside tampering
            $cookie_data = $cookie_data.md5($cookie_data.$this->encryption_key);
        }

        $_COOKIE[ $this->sess_cookie_name ] = $cookie_data;  // added to solve bug

        $expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();

        // Set the cookie
        setcookie(
                    $this->sess_cookie_name,
                    $cookie_data,
                    $expire,
                    $this->cookie_path,
                    $this->cookie_domain,
                    $this->cookie_secure
                );
    }   
}


?>