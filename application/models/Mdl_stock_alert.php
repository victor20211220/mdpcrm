<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_stock_alert extends Response_Model
{
    public $table = 'ip_stock_alert';
    public $primary_key = 'ip_stock_alert.alert_id';

    /**
     * Default select
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * Default where
     */
    public function default_where()
    {
        $this->db->where('ip_stock_alert.company_id', $this->session->userdata('company_id'));
    }

    /**
     * Default join
     */
    public function default_join()
    {
        $this->db->join('ip_products', 'ip_stock_alert.product_id = ip_products.product_id', 'left');
        $this->db->join('ip_families', 'ip_products.family_id = ip_families.family_id', 'left');
    }

    /**
     * TODO: remove
     * @param null $id
     * @return mixed
     */
    public function get_by_id($id = null)
    {
        return $this->db->get_where('ip_stock_alert', ['alert_id' => $id])->row();
    } 

    /**
     * Check alerts
     * @param $productId
     * @param $companyId
     */
    public function check_alert($productId, $companyId)
    {
        $this->load->model('Mdl_products');

        $this->Mdl_stock_alert->where('ip_stock_alert.product_id', $productId);
        $this->Mdl_stock_alert->where('ip_stock_alert.company_id', $companyId);
        $alertOld = $this->Mdl_stock_alert->get()->row();

        if (isset($alertOld->alert_id)) {
            $this->Mdl_stock_alert->delete($alertOld->alert_id);
        }

        $products = $this->Mdl_products->getByPk($productId);
        if ($products->company_id == $companyId && ($products->stock <= $products->stock_alert)) {
            $this->Mdl_stock_alert->save(null, [
                'company_id'  => $companyId,
                'product_id'  => $productId,
                'stock'       => $products->stock,
                'stock_alert' => $products->stock_alert
            ]);
        }
    }

    /**
     * Get alerts number
     * @return string
     */
    public function get_alerts_number()
    {
        $count = $this->db->query('select * from ip_products where stock <= stock_alert and stock_alert > 0')->num_rows();
			
        return $count > 0 ? $count : '';
    }

    public function stock_alerts_new()
    {
        $data = [];
        if ($this->db->query('select * from ip_products where stock <= stock_alert and stock_alert > 0')) {
            $total = '';
            $alert = '';
            $quantity = [];
            $family = '';
            $current = '';
            $i = 0;
            foreach ($this->db->query('select * from ip_products where stock <= stock_alert and stock_alert > 0')->result_array() as $row) {

                    if ($current < $row['stock_alert']) {
                        if ($row['family_id'] != 0) {
                            $family = $this->db->get_where('ip_families',
                                ['family_id' => $row['family_id']])->row('family_name');
                        } else {
                            $family = '-';
                        }

                        $data[] = '<tr role="row" class="' . ($i % 2 ? 'odd' : 'even') . '">
                          <td class="text-center">' . $row['product_name'] . '</td>
                          <td class="text-center">' . $family . '</td>
                          <td class="text-center">' . $row['stock'] . '</td>
                          <td class="text-center">' . $row['stock_alert'] . '</td>
                        </tr>';
                        $i++;
                    }
            }

            if (!empty($data)) {
                $data[] = $data;
            } else {
                $data[] = '<tr><td class="text-center">' . lang('no_results_found') . '</td><td> </td><td> </td><td> </td></tr>';
            }
        } else {
            $data[] = '<tr><td class="text-center">' . lang('no_results_found') . '</td><td> </td><td> </td><td> </td></tr>';
        }

        return $data;
    }
}
