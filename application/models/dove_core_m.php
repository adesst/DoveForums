<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst. It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package Dove Forums
 * @copyright Copyright (c) 2012 - Christopher Baines
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link http://www.doveforums.com
 * @since Version 2.0.0
 * @author Christopher Baines
 *
 */

class Dove_core_m extends CI_Model
{
    public $tables = array();
    public $activation_code;
    public $forgotten_password_key;
    public $new_password;
    public $identity;
    protected $_dove_hooks;
    protected $response = NULL;
    protected $messages;
    protected $errors;
    protected $message_start_delimiter;
    protected $message_end_delimiter;
    protected $error_start_delimiter;
    protected $error_end_delimiter;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->config('dove_core', TRUE);
        $this->load->helper('cookie');
        $this->load->helper('date');
        $this->load->language('dove_core');
        $this->load->library('bcrypt');
        $this->load->library('session');

        // Initialize database tables.
        $this->tables = $this->config->item('tables', 'dove_core');

        // Initialize data.
        $this->identity_column = $this->config->item('identity', 'dove_core');

        // Initialize hooks object.
        $this->_dove_hooks = new stdClass;

        // Initialize messages and error.
        $this->messages = array();
        $this->errors = array();

        $this->message_start_delimiter = $this->config->item('message_start_delimiter', 'dove_core');
        $this->message_end_delimiter = $this->config->item('message_end_delimiter', 'dove_core');
        $this->error_start_delimiter = $this->config->item('error_start_delimiter', 'dove_core');
        $this->error_end_delimiter = $this->config->item('error_end_delimiter', 'dove_core');

        // Set hook.
        $this->trigger_events('model_constructor');
    }

    /*
     * Login Function
     *
     * @return bool
     */
    public function login($identity, $password)
    {
        // Get user information.
        $query = $this->db->select('username, email, id, group_id, password, active, last_login')
                            ->where($this->identity_column, $this->db->escape_str($identity))
                            ->limit(1)
                            ->get($this->tables['users']);

        if($query->num_rows() === 1)
        {
            // Store user data.
            $user = $query->row();

            // Has users password.
            $password = $this->hash_password_db($user->id, $password);

            if ($password === TRUE)
            {
                // See if user is active.
                if ($user->active == 0)
                {
                    // Set hook.
                    $this->trigger_events('post_login_unsuccessful');

                    // Set error.
                    $this->set_error('login_unsuccessful_not_active');

                    return FALSE;
                }

                // Set the session.
                $this->set_session($user);

                // Update last login.
                $this->update_last_login($user->id);

                // Clear login attempts.
                $this->clear_login_attempts($identity);

                // Get permissions.
                $this->permissions = $this->get_permissions($user->group_id);

                // Set hook.
                $this->trigger_events(array('post_login', 'post_login_successful'));

                // Set message.
                $this->set_message('login_successful');

                return TRUE;
            }
        }

        // Increase login attempts.
        $this->increase_login_attempts($identity);

        // Set hook.
        $this->trigger_events('post_login_unsuccessful');

        // Set error.
        $this->set_error('login_unsuccessful');
    }

    public function logout()
    {
        // Unset userdata.
        $identity = $this->config->item('identity', 'dove_core');
        $this->session->unset_userdata(array($identity => '', 'id' => '', 'user_id' => ''));

        // Destroy the session.
        $this->session->sess_destroy();

        // Clear permissions.
        unset($this->permissions);

        // Create a new session.
        $this->session->sess_create();

        $this->set_message('logout_successful');
        return TRUE;
    }

    public function register($username, $password, $email, $group=NULL)
    {
        /*
        if ( $this->identity_column == 'email' && $this->email_check($email) )
        {
            $this->set_error('account_creation_duplicate_email');
            return FALSE;
        }
        elseif ( $this->identity_column == 'username' && $this->username_check($username) )
        {
            $this->set_error('account_creation_duplicate_username');
            return FALSE;
        }
        */

        // Password.
        $db_password = $this->bcrypt->hash_password($password);

        // Build data.
        $data = array(
            'username' => $username,
            'password' => $db_password,
            'email' => $email,
            'ip_address' => $this->input->ip_address(),
            'created_on' => time(),
            'last_login' => time(),
            'active' => 1,
        );

        // Insert.
        $this->db->insert($this->tables['users'], $data);

        if( $this->db->affected_rows() > 0 )
        {
            $user_id = $this->db->insert_id();

            // See if a group is set, if not add to default.
            if ( isset($group) )
            {
                if( $this->add_to_group($group, $user_id) == TRUE )
                {
                    $this->set_message('account_creation_successful');
                    return TRUE;
                }
                else
                {
                    $this->set_error('account_creation_failed');
                    return FALSE;
                }
            }
            else
            {
                // Add to the default group.
                if ( $this->add_to_group($this->config->item('default_group', 'dove_core'), $user_id) == TRUE )
                {
                    $this->set_message('account_creation_successful');
                    return TRUE;
                }
                else
                {
                    $this->set_error('account_creation_failed');
                    return FALSE;
                }
            }
        }
        else
        {
            $this->set_error('account_creation_failed');
            return FALSE;
        }
    }

    public function hash_password_db($id, $password)
    {
        if (empty($password) || empty($id))
        {
            return FALSE;
        }

        $query = $this->db->select('password')
                            ->where('id', $id)
                            ->limit(1)
                            ->get($this->tables['users']);

        if ($query->num_rows() === 1)
        {
            $hash_password = $query->row();

            if ($this->bcrypt->check_password($password, $hash_password->password))
            {
                return TRUE;
            }

            return FALSE;
        }
        else
        {
            return FALSE;
        }
    }

    public function set_session($user)
    {
        // Set hook.
        $this->trigger_events('pre_set_session');

        // Session data.
        $session_data = array(
            'identity' => $user->{$this->identity_column},
            'username' => $user->username,
            'email' => $user->email,
            'user_id' => $user->id,
            'old_last_login' => $user->last_login,
            'group_id' => $user->group_id,
        );

        // Set session.
        $this->session->set_userdata($session_data);

        // Set hook.
        $this->trigger_events('post_set_session');

        return TRUE;
    }

    public function update_last_login($id)
    {
        if (empty($id))
        {
            return FALSE;
        }

        // Set hook.
        $this->trigger_events('pre_update_last_login');

        // Perform update.
        $this->db->update($this->tables['users'], array('last_login' => time()), array('id' => $id));

        if ($this->db->affected_rows() > 0)
        {
            return TRUE;
        }

        return FALSE;
    }

    public function clear_login_attempts($identity, $expire_period = 86400)
    {
        if(empty($identity))
        {
            return FALSE;
        }

        if ($this->config->item('track_login_attempts', 'dove_core'))
        {
            $ip_address = $this->input->ip_address();

            $this->db->where(array('ip_address' => $ip_address, 'login' => $identity))
                        ->or_where('time <', time() - $expire_period, FALSE);

            return $this->db->delete($this->tables['login_attempts']);
        }

        return FALSE;
    }

    public function increase_login_attempts($identity)
    {
        if (empty($identity))
        {
            return FALSE;
        }

        if ($this->config->item('track_login_attempts', 'dove_core'))
        {
            $ip_address = $this->input->ip_address();

            return $this->db->insert($this->tables['login_attempts'], array('ip_address' => $ip_address, 'login' => $identity, 'time' => time()));
        }

        return FALSE;
    }

    public function get_permissions($group_id)
    {
        if (empty($group_id))
        {
            return FALSE;
        }

        $query = $this->db->select('key, permission, category')
                            ->join($this->tables['permissions'], 'permissions.id = permissions_map.permission_id')
                            ->where('group_id', $group_id)
                            ->get($this->tables['permissions_map']);

        if($query->num_rows() > 0)
        {
            foreach($query->result_array() as $row)
            {
                    $permissions[$row['key']] = array(
                        'perm' => $row['key'],
                        'value' => true,
                        'description' => $row['permission'],
                        'category' => $row['category'],
                    );
            }

            return $permissions;
        }

        return false;
    }

    public function list_group_permissions($group_id)
    {
        if (empty($group_id))
        {
            return FALSE;
        }

        $query = $this->db->select('key, permission, category')
                            ->join($this->tables['permissions'], 'permissions.id = permissions_map.permission_id')
                            ->where('group_id', $group_id)
                            ->get($this->tables['permissions_map']);

        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $row)
            {
                $permissions[] = array(
                    'permission' => $row['permission'],
                    'key' => $row['key'],
                    'category' => $row['category'],
                );
            }

            return $permissions;
        }

        return FALSE;
    }

    public function user($id = NULL)
    {
        $this->trigger_events('pre_user');

        $id = ( !$id ? $this->session->userdata('user_id') : $id );

        $query = $this->db->select('username, email, id, group_id, last_login')
                            ->where('id', $id)
                            ->limit(1)
                            ->get($this->tables['users']);

        if ($query->num_rows() > 0)
        {
            return $query->row();
        }

        return FALSE;
    }

    public function in_group($id)
    {
        $query = $this->db->select('name')
                            ->where('id', $id)
                            ->limit(1)
                            ->get($this->tables['groups']);

        if($query->num_rows() > 0)
        {
            return $query->row('name');
        }

        return FALSE;
    }

    public function get_group($group_id)
    {
        // Query.
        $query = $this->db->select('name')
                            ->where('id', $group_id)
                            ->limit(1)
                            ->get($this->tables['groups']);

        if ( $query->num_rows() > 0 )
        {
            return $query->row('name');
        }

        return FALSE;
    }

    public function set_lang($lang = 'en')
    {
        $this->trigger_events('set_lang');

        if($this->config->item('user_expire', 'dove_core') === 0)
        {
            $expire = (60*60*24*365*2);
        }
        else
        {
            $expire = $this->config->item('user_expire', 'dove_core');
        }

        set_cookie(array(
            'name' => 'lang_code',
            'value' => $lang,
            'expire' => $expire,
        ));

        return TRUE;
    }

    public function set_hook($event, $name, $class, $method, $arguments)
    {
        $this->_dove_hooks->{$event}[$name] = new stdClass;
        $this->_dove_hooks->{$event}[$name]->class = $class;
        $this->_dove_hooks->{$event}[$name]->method = $method;
        $this->_dove_hooks->{$event}[$name]->arguments = $arguments;
    }

    public function remove_hook($event, $name)
    {
        if (isset($this->_dove_hooks->{$event}[$name]))
        {
            unset($this->_dove_hooks->{$event}[$name]);
        }
    }

    public function remove_hooks($event)
    {
        if (isset($this->_dove_hooks->$event))
        {
            unset($this->_dove_hooks->$event);
        }
    }

    protected function _call_hook($event, $name)
    {
        if (isset($this->_dove_hooks->{$event}[$name]) && method_exists($this->_dove_hooks->{$event}[$name]->class, $this->_dove_hooks->{$event}[$name]->method))
        {
            $hook = $this->_dove_hooks->{$event}[$name];

            return call_user_func_array(array($hook->class, $hook->method,), $hook->arguments);
        }
    }

    public function trigger_events($events)
    {
        if (is_array($events) && !empty($events))
        {
            foreach ($events as $event)
            {
                $this->trigger_events($event);
            }
        }
        else
        {
            if (isset($this->_dove_hooks->$events) && !empty($this->_dove_hooks->$events))
            {
                foreach ($this->_dove_hooks->$events as $name => $hook)
                {
                    $this->_call_hook($events, $name);
                }
            }
        }
    }

    public function set_message_delimiters($start_delimiter, $end_delimiter)
    {
        $this->message_start_delimiter = $start_delimiter;
        $this->message_end_delimiter = $end_delimiter;

        return TRUE;
    }

    public function set_error_delimiters($start_delimiter, $end_delimiter)
    {
        $this->error_start_delimiter = $start_delimiter;
        $this->error_end_delimiter = $end_delimiter;

        return TRUE;
    }

    public function set_message($message)
    {
        $this->messages[] = $message;
        return $message;
    }

    public function messages()
    {
        $_output = '';
        foreach ($this->messages as $message)
        {
            $message_lang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
            $_output .= $this->message_start_delimiter . $message_lang . $this->message_end_delimiter;
        }

        return $_output;
    }

    public function messages_array()
    {

    }

    public function set_error($error)
    {
        $this->errors[] = $error;
        return $error;
    }

    public function errors()
    {
        $_output = '';
        foreach ($this->errors as $error)
        {
            $error_lang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
            $_output .= $this->error_start_delimiter . $error_lang . $this->error_end_delimiter;
        }

        return $_output;
    }

    public function errors_array()
    {

    }

    public function insert($data = array(), $table)
    {
        // Error checking.
        if ( empty($table) )
        {
            $this->set_error('no_table_selected');
            return FALSE;
        }

        // Trans start.
        $this->db->trans_start();

        // Query.
        $this->db->insert($table, $data);

        // Trans complete.
        $this->db->trans_complete();

        // Return.
        if( $this->db->trans_status() === FALSE )
        {
            $this->db->trans_rollback();
            return FALSE;
        }
        else
        {
            if ( $this->db->affected_rows() > 0 )
            {
                $this->db->trans_commit();
                return TRUE;
            }
            else
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }
    }

    public function update($data = array(), $conditions = array(), $table)
    {
        // Error checking.
        if ( empty($table) )
        {
            $this->set_error('no_table_selected');
            return FALSE;
        }

        // Trans start.
        $this->db->trans_start();

        // Query
        $this->db->where($conditions)
                    ->update($table, $data);

        // Trans complete.
        $this->db->trans_complete();

        // Return.
        if ( $this->db->trans_status() === FALSE )
        {
            $this->db->trans_rollback();
            return FALSE;
        }
        else
        {
            if ( $this->db->affected_rows() > 0 )
            {
                $this->db->trans_commit();
                return TRUE;
            }
            else
            {
                $this->db->trans_rollback();
                return FALSE;
            }
        }
    }

    public function delete($conditions = array(), $table)
    {
        if ( empty($table) )
        {
            $this->set_error('no_table_selected');
            return FALSE;
        }

        // Trans start.
        $this->db->trans_start();

        // Query.
        $this->db->where($conditions)
                    ->delete($table);

        // Trans complete.
        $this->db->trans_complete();

        // Return.
        if ( $this->db->trans_status() === FALSE )
        {
            $this->db->trans_rollback();
            return FALSE;
        }
        else
        {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    public function get_user_xp($user_id)
    {
        $query = $this->db->select('xp')
                            ->where('id', $user_id)
                            ->limit(1)
                            ->get($this->tables['users']);

        if ( $query->num_rows() > 0)
        {
            $rank = $this->get_rank( (int) $query->row('xp') );

            foreach($rank as $row)
            {
                $data = array(
                    'user_xp' => $query->row('xp'),
                    'rank' => $row->rank,
                    'min_xp' => $row->min_xp,
                    'max_xp' => $row->max_xp,
                );

            }

            return $data;
        }
        else
        {
            return FALSE;
        }
    }

    public function get_rank($xp)
    {
        $query = $this->db->select('rank, min_xp, max_xp')
                            ->where('max_xp >=', $xp)
                            ->where('min_xp <=', $xp)
                            ->get('ranks');

        if ( $query->num_rows() > 0 )
        {
            return $query->result();
        }
        else
        {
            return FALSE;
        }
    }

    public function add_xp($user_id)
    {
        // Get current XP.
        $query = $this->db->select('xp')
                            ->where('id', $user_id)
                            ->limit(1)
                            ->get($this->tables['users']);

        if ( $query->num_rows() > 0 )
        {
            $current_xp = $query->row('xp');

            $new_xp = $current_xp + 1;

            $this->db->where('id', $user_id)
                        ->update($this->tables['users'], array('xp' => $new_xp));

            // Insert new xp.
            if ( $this->db->affected_rows() > 0 )
            {
                $this->set_message('xp_added');
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    public function add_to_group($group, $user_id)
    {
        // Get the group ID from the database.
        $query = $this->db->select('id')
                            ->where('name', strtolower($group))
                            ->limit(1)
                            ->get($this->tables['groups']);

        // Result
        if ( $query->num_rows() > 0 )
        {
            $data = array(
                'group_id' => $query->row('id'),
            );

            $this->db->where('id', $user_id)->update($this->tables['users'], $data);

            if ( $this->db->affected_rows() > 0 )
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    public function get_language_packs()
    {
        $query = $this->db->select('*')
                            ->where('active', 'yes')
                            ->get($this->tables['language']);

        // Result.
        if ( $query->num_rows() > 0 )
        {
            return $query->result();
        } else {
            return NULL;
        }
    }
}