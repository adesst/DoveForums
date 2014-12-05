<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

class users_m extends CI_Model {

    public function count_members()
    {
        // Query.
        $query = $this->db->select('*')
                            ->get($this->tables['users']);

        // Result.
        return ( $query->num_rows() > 0 ? $query->num_rows() : 0 );
    }

    public function get_all_members($params = array(), $count = false)
    {
        // Query.
        if( isset( $params['limit']) )
            $this->db->limit($params['limit']);

        if( isset( $params['offset']) )
            $this->db->offset($params['offset']);

        if (isset($params['_startswith']))
        {
            if ( $params['_startswith'] == 'u')
                $this->db->where("username regexp '^[^a-zA-Z]'");
            elseif ( $params['_startswith'] == 'a')
                null;
            elseif ( preg_match('/^[a-zA-Z]/', $params['_startswith']) )
                $this->db->where('substr(username,1,1) = \''.$params['_startswith'].'\'');
        }

        $this->db->order_by('username');

        $query = $this->db->get($this->tables['users']);

        // Result.
        return ( $query->num_rows() > 0 ? $query->result() : NULL );
    }

    public function get_sidebar_members()
    {
        // Query.
        $query = $this->db->select('email, username')
                            ->order_by('id', 'RANDOM')
                            ->limit(4)
                            ->get($this->tables['users']);

        // Result.
        return ( $query->num_rows() > 0 ? $query->result() : NULL );
    }

    public function get_user_profile()
    {
        // Query.
        $query = $this->db->select('id, username, email, created_on, last_login, first_name, last_name, signature, XP')
                            ->where('id', $this->session->userdata('user_id'))
                            ->limit(1)
                            ->get($this->tables['users']);

        // Result.
        return ( $query->num_rows() > 0 ? $query->result() : NULL );
    }
}
