<?php

/**
 * CodeIgniter CRUD Model 2
 * A base model providing CRUD, pagination and validation.
 *
 * Install this file as application/core/MY_Model.php
 *
 * @package    CodeIgniter
 * @author        Kovah (www.kovah.de)
 * @copyright    Copyright (c) 2012, Jesse Terry
 * @link        http://developer13.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
class MY_Model extends CI_Model
{
    public $table;
    public $primary_key;
    public $default_limit = 15;
    public $page_links;
    public $query;
    public $form_values = [];
    protected $default_validation_rules = 'validation_rules';
    protected $validation_rules;
    public $validation_errors;
    public $total_rows;
    public $date_created_field;
    public $date_modified_field;
    public $total_pages = 0;
    public $current_page;
    public $next_page;
    public $previous_page;
    public $offset;
    public $next_offset;
    public $previous_offset;
    public $last_offset;
    public $id;
    public $filter = [];
    public $native_methods = [
        'select',
        'select_max',
        'select_min',
        'select_avg',
        'select_sum',
        'join',
        'where',
        'or_where',
        'where_in',
        'or_where_in',
        'where_not_in',
        'or_where_not_in',
        'like',
        'or_like',
        'not_like',
        'or_not_like',
        'group_by',
        'distinct',
        'having',
        'or_having',
        'order_by',
        'limit'
    ];

    /**
     * Magic __call
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 7) == 'filter_') {
            $this->filter[] = [substr($name, 7), $arguments];
        } else {
            call_user_func_array([$this->db, $name], $arguments);
        }

        return $this;
    }

    /**
     * Sets CI query object and automatically creates active record query
     * based on methods in child model.
     * $this->model_name->get()
     * @param bool $includeDefaults
     * @param bool $includeFilters
     * @param bool $includeCompanyFilter
     * @return $this
     */
    public function get($includeDefaults = true, $includeFilters = true, $includeCompanyFilter = false)
    {
        if ($includeDefaults) {
            $this->set_defaults();
        }

        if ($includeFilters) {
            $this->applyFilters();
        }

        if ($includeCompanyFilter) {
            $ci = CI::get_instance();
            $this->db->where(["{$this->table}.company_id" => $ci->session->userdata('company_id')]);
        }

        $this->query = $this->db->get($this->table);
        $this->filter = [];

        return $this;
    }

    /**
     * Run filters
     */
    private function applyFilters()
    {
        if (is_array($this->filter) && count($this->filter) > 0) {
            foreach ($this->filter as $filter) {
                call_user_func_array([$this->db, $filter[0]], $filter[1]);
            }
        }

        $this->filter = [];
    }

    /**
     * Query builder which listens to methods in child model.
     * @param array $exclude
     */
    private function set_defaults($exclude = [])
    {
        $native_methods = $this->native_methods;

        foreach ($exclude as $unset_method) {
            unset($native_methods[array_search($unset_method, $native_methods)]);
        }

        foreach ($native_methods as $native_method) {
            $native_method = 'default_' . $native_method;

            if (method_exists($this, $native_method)) {
                $this->$native_method();
            }
        }
    }

    /**
     * Call when pagination results
     * @param $baseUrl
     * @param int $offset
     * @param int $uriSegment
     * @return $this
     */
    public function paginate($baseUrl, $offset = 0, $uriSegment = 3)
    {
        $this->load->library('pagination');

        $this->offset = $offset;
        $defaultListLimit = $this->Mdl_settings->setting('default_list_limit');
        $perPage = (empty($defaultListLimit) ? $this->default_limit : $defaultListLimit);

        $this->set_defaults();
        $this->applyFilters();

        $this->db->limit($perPage, $this->offset);
        $this->query = $this->db->get($this->table);

        $this->total_rows = $this->db->query("SELECT FOUND_ROWS() AS num_rows")->row()->num_rows;
        $this->total_pages = ceil($this->total_rows / $perPage);
        $this->previous_offset = $this->offset - $perPage;
        $this->next_offset = $this->offset + $perPage;

        $config = [
            'base_url'   => $baseUrl,
            'total_rows' => $this->total_rows,
            'per_page'   => $perPage
        ];

        $this->last_offset = ($this->total_pages * $perPage) - $perPage;

        if ($this->config->item('pagination_style')) {
            $config = array_merge($config, $this->config->item('pagination_style'));
        }

        $this->pagination->initialize($config);
        $this->page_links = $this->pagination->create_links();

        return $this;
    }

    /**
     * Retrieves a single record based on primary key value.
     */
    public function get_by_id($id)
    {
        return $this->where($this->primary_key, $id)->get()->row();
    }

    /**
     * Get by primary key
     * @param $id
     * @return mixed
     */
    public function getByPk($id)
    {
        return $this->where($this->primary_key, $id)->get()->row();
    }

    /**
     * Save data
     * @param null $id
     * @param null $data
     * @return null
     */
    public function save($id = NULL, $data = NULL)
    {
        $data = $data == null ? $this->db_array() : $data;
        $datetime = date('Y-m-d H:i:s');

        if (!$id) {
            if ($this->date_created_field) {
                if (is_array($data)) {
                    $data[$this->date_created_field] = $datetime;

                    if ($this->date_modified_field) {
                        $data[$this->date_modified_field] = $datetime;
                    }
                } else {
                    $data->{$this->date_created_field} = $datetime;

                    if ($this->date_modified_field) {
                        $data->{$this->date_modified_field} = $datetime;
                    }
                }
            } elseif ($this->date_modified_field) {
                if (is_array($data)) {
                    $data[$this->date_modified_field] = $datetime;
                } else {
                    $data->{$this->date_modified_field} = $datetime;
                }
            }

            $this->db->insert($this->table, $data);

            return $this->db->insert_id();
        } else {
            if ($this->date_modified_field) {
                if (is_array($data)) {
                    $data[$this->date_modified_field] = $datetime;
                } else {
                    $data->{$this->date_modified_field} = $datetime;
                }
            }

            $this->db->where($this->primary_key, $id);
            $this->db->update($this->table, $data);

            return $id;
        }
    }

    /**
     * Returns an array based on $_POST input matching the rules used to validate the form
     * @return array
     */
    public function db_array()
    {
        $array = [];

        $validationRules = $this->{$this->validation_rules}();

        foreach ($this->input->post() as $key => $value) {
            if (array_key_exists($key, $validationRules)) {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * Delete by primary key
     * @param $id
     */
    public function delete($id)
    {
        $this->db->where($this->primary_key, $id);
        $this->db->delete($this->table);
    }

    /**
     * Returns the CI query result object.
     * $this->model_name->get()->result();
     */
    public function result()
    {
        return $this->query->result();
    }

    /**
     * Returns the CI query row object.
     * $this->model_name->get()->row();
     */
    public function row()
    {
        return $this->query->row();
    }

    /**
     * Returns CI query result array.
     * $this->model_name->get()->result_array();
     */
    public function result_array()
    {
        return $this->query->result_array();
    }

    /**
     * Returns CI query row array.
     * $this->model_name->get()->row_array();
     */
    public function row_array()
    {
        return $this->query->row_array();
    }

    /**
     * Returns CI query num_rows().
     * $this->model_name->get()->num_rows();
     */
    public function num_rows()
    {
        return $this->query->num_rows();
    }

    /**
     * Used to retrieve record by ID and populate $this->form_values.
     * @param int $id
     * @return boolean
     */
    public function prep_form($id = null)
    {
        if ($_POST == false && $id) {
            $row = $this->get_by_id($id);
            if ($row) {
                foreach ($row as $key => $value) {
                    $this->form_values[$key] = $value;
                }

                return true;
            }

            return false;
        } elseif (!$id) {
            return true;
        }
    }

    /**
     * Performs validation on submitted form. By default, looks for method in
     * child model called validation_rules, but can be forced to run validation
     * on any method in child model which returns array of validation rules.
     * @param string $validationRules
     * @return boolean
     */
    public function run_validation($validationRules = null)
    {
        if (!$validationRules) {
            $validationRules = $this->default_validation_rules;
        }

        foreach (array_keys($_POST) as $key) {
            $this->form_values[$key] = $this->input->post($key);
        }

        if (method_exists($this, $validationRules)) {
            $this->validation_rules = $validationRules;
            $this->form_validation->set_rules($this->$validationRules());

            $run = $this->form_validation->run();
            $this->validation_errors = validation_errors();

            return $run;
        }
    }

    /**
     * Returns the assigned form value to a form input element.
     * @param $key
     * @return mixed|string
     */
    public function form_value($key)
    {
        return (isset($this->form_values[$key])) ? $this->form_values[$key] : '';
    }

    public function set_form_value($key, $value)
    {
        $this->form_values[$key] = $value;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }
}
