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

class Categories extends Front_Controller {

    public function view($category_permalink=null)
    {
        // See if a category has been provided.
        if($category_permalink)
        {
            // $discussions = $this->discussions->get_all_discussions();
        } else {
            // $discussions = $this->dicussions->get_category_discussions($category_permalink);
        }

        $page_data = array(
            'test' => 'Test content',
        );

        $this->construct_template($page_data, 'view_template', $this->lang->line('page_view'));
    }

    public function create_category()
    {
        $page_data = array();


    }
}
