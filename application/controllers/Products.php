<?php

/**
 * undocumented class
 */
class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_admin_product');
        if ($this->session->userdata('actived') == 1) {
        } else {
            redirect('Login_Admin');
        }
    }

    public function index()
    {
        $this->load->view('admin/header');
        $this->load->view('admin/data/product');
        $this->load->view('admin/footer');
    }

    public function readall()
    {
        $takedata = $this->Model_admin_product->read_all();
        $data = array();
        $no = 0;
        foreach ($takedata->result() as $row) {
            $no++;
            $list = array();
            $list[] = $no;
            $list[] = '<img src="' . base_url() . 'assets/front/img/product/' . $row->picture . '" class="img-thumbnail" width="100" height="50" />';
            $list[] = $row->name;
            $list[] = substr($row->description, 0, 20);
            if ($row->status == 1) {
                $list[] = '<span class="badge badge-success">Active</span>';
            } else {
                $list[] = '<span class="badge badge-warning">Non Aktive</span>';
            }
            $list[] =
                '<button type="button" class="btn btn-info btn-sm btn-flat" onclick="detail_id(' . "'" . $row->idproduct . "'" . ')">Detail</button>
                <button type="button" class="btn btn-primary btn-sm btn-flat" onclick="update_id(' . "'" . $row->idproduct . "'" . ')">Update</button>
                    <button type="button" class="btn btn-warning btn-sm btn-flat" onclick="delete_id(' . "'" . $row->idproduct . "'" . ')">Delete</button>';
            $data[] = $list;
        }
        $output = array('data' => $data);
        echo json_encode($output);
        exit();
    }

    public function detail($idproduct)
    {
        $data = $this->Model_product->read_by_id($idproduct);
        echo json_encode($data);
    }

    public function addsave($data)
    {
        # code...
    }

    public function updatesave($idproduct)
    {
        $status = array('success' => false, 'error' => false, 'messagess' => array());
        $this->form_validation->set_rules('txtname', 'Products Name', 'required|trim');
        $this->form_validation->set_rules('txttype', 'Type Name', 'required|trim');
        $this->form_validation->set_rules('txtstatus', 'Product Status', 'required|trim');
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');

        if ($this->form_validation->run() == FALSE) {
            foreach ($_POST as $key => $value) {
                $status['messagess'][$key] = form_error($key);
            }
        } else {
            $cek_name = $this->model_admin_product->cek_name($this->input->post('txtname'));
            if ($cek_name->num_rows() > 0) {
                $status['error'] = true; // error true but name ready on database
            } else {
                $idproduct = $this->input->post('txtidproduct');
                $data = array(
                    'name' => $this->input->post('txtname'),
                    'description' => $this->input->post('txtdescription'),
                    'picture' => $this->input->post('txtpicture'),
                    'idtype' => $this->input->post('txtidtype'),
                    'datecreate' => date('Y-m-d H:i:s'),
                    'usercreate' => $_SESSION['iduser'],
                    'status' => $this->input->post('txtstatus')
                );
                $this->Model_admin_product->update_save($idproduct, $data);
                $status['success'] = true; // save produc bat not valid data
            }
        }
        echo json_encode($status);
    }

    public function delete($idproduct)
    {
        $data = $this->Model_product->delete_by_id($idproduct);
        if ($data->num_rows == 0) {
            $messagess = true;
        } else {
            $messagess = false;
        }
        echo json_encode($messagess);
    }
}
