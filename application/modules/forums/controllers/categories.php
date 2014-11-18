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

    private $validation_rules = array(
        'new_category' => array(
            //0
            array(
                'field' => 'category_name',
                'rules' => 'required',
                'label' => 'lang: rules_category_name',
            ),
            array(
                'field' => 'category_description',
                'rules' => 'required',
                'label' => 'lang: rules_category_description',
            ),
        ),
    );

    private $form_fields = array(
        'new_category' => array(
            //0
            array(
                'name' => 'category_name',
                'id' => 'category_name',
                'placeholder' => 'Enter Category name.',
                'class' => 'form-control',
                'type' => 'text'
            ),
            array(
                'name' => 'category_description',
                'id' => 'category_description',
                'placeholder' => 'Category Description',
                'class' => 'form-control',
                'type' => 'textarea'
            ),
        ),
    );

    function __construct()
    {
        parent::__construct();

        $config = array(
            'field' => 'permalink',
            'title' => 'name',
            'table' => 'categories',
            'id' => 'category_id',
        );

        $this->load->library('slug', $config);
    }

    function new_category()
    {
        // login check.
        $this->login_check();

        if( !$this->dove_core->is_admin() )
        {
            $this->create_message('error', lang('admin_privilege_required'));
            redirect ( site_url() );
        }

        // todo add permission_check of create categories
        //$this->permission_check('create_discussions');

        // set the validation rules.
        $this->form_validation->set_rules($this->validation_rules['new_category']);

        // see if the form has been run.
        if($this->form_validation->run() === false)
        {
            // get categories from the database.
            $categories = $this->categories->get_categories();

            if($categories)
            {
                foreach($categories as $cat)
                {
                    $category_options[$cat->id] = $cat->name;
                }
            }

            $page_data = array(
                // form tags
                'form_open' => form_open(site_url('categories/new_category'), array('id' => 'new_category')),
                'form_close' => form_close(),
                'category_name_label' => form_label($this->lang->line('label_category_name'), $this->form_fields['new_category'][0]['id']),
                'category_name_field' => form_input(
                    $this->form_fields['new_category'][0]['name'],
                    null,
                    'placeholder = "New Category"
                        class="form-control" '
                ),
                'category_description_label' => form_label($this->lang->line('label_category_description')),
                'category_description_field' => form_textarea(
                    $this->form_fields['new_category'][1]['name'],
                    null,
                    'placeholder="Description"
                        class="form-control" '
                ),
                // buttons
                'clear_button' => form_reset('reset', $this->lang->line('btn_clear'), 'class="btn btn-danger btn-sm"'),
                'submit_button' => form_submit('submit', $this->lang->line('btn_create_category'), 'class="btn btn-success btn-sm"'),
            );

            $this->construct_template($page_data, 'new_category_template', $this->lang->line('page_new_category'));
        }
        else
        {
            $last_no_order = $this->categories->get_last_category_order();

            $category_data = array(
                'name' => $this->input->post('category_name'),
                'permalink' => $this->slug->create_uri(array('permalink' => $this->input->post('category_name'))),
                'order' => $last_no_order + 1,
            );

            $result = $this->categories->add_category($category_data);

            if ($result)
            {
                $this->create_message('success', $this->dove_core->messages());
                redirect ( site_url('categories/'.$this->categories->get_category_permalink_by_id( (int) $result )) );
            }
            else
            {
                $this->create_message('error', $this->dove_core->errors());
                redirect ( site_url() );
            }
        }
    }
}
