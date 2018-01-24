<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->helper(array('form'));
		$this->load->database();

		$this->load->model('User_model');	
	}
	public function index(){

		$all_query = $this->db->get('invoice');
		if ($all_query->num_rows()>0) {
		    foreach ($all_query->result_array() as $dData) {

		        if ($dData['alerts'] == 1) {
		            $this->db->where('invoice_id',$dData['id']);
		            $this->db->delete('items');

		            if ($dData['userfile'] !='') {		            	
        				unlink('./assets/images/'.$dData['userfile']);
		            } // FOR AVAILABLE IMAGES DELETED

		            $this->db->where('id',$dData['id']);
		        	$this->db->delete('invoice');            
		        } //SUCCESSFULLY INVOICE DELETE

		        $now = strtotime($dData['added_date']);
		        $plus1hour = $now+60*60;

		        if ($now>$plus1hour) {
		        	$this->db->where('invoice_id',$dData['id']);
		            $this->db->delete('items');

		            $this->db->where('id',$dData['id']);
		        	$this->db->delete('invoice');
		        } //BOUNCED USER DATA DELETE

		    }
		} //DELETED INVOICE DATA


		$session_id = session_id();

		$this->db->where('invoice_session',$session_id);
	    $query = $this->db->get('invoice');
	    if ($query->num_rows()>0) {
	        $data['invoice'] = $query;
	    }else{
	    	$insert = array('invoice_session' => $session_id );
	    	$this->db->insert('invoice',$insert);

	    	$this->db->where('invoice_session',$session_id);
	        $this->db->order_by('id','desc');
	        $this->db->limit(1);
	        $item_query = $this->db->get('invoice');

	    	$data['invoice'] = $item_query;
	    }

		$this->load->view('web/home',$data);

	}
	public function add_newline(){
		$session_id = session_id();
		$invoice_id = $this->input->post('invoice_id');

		$insert = array('user_session' => $session_id,'invoice_id' => $invoice_id );
        $this->db->insert('items',$insert);

        $this->db->where('user_session',$session_id);
        $this->db->order_by('id','desc');
        $this->db->limit(1);
        $item_query = $this->db->get('items');

		echo json_encode($item_query->result());
	}

	public function add_data_items(){
		$session_id = session_id();
		$id = $this->input->post('id');
		$qty = $this->input->post('qty');
		$price = $this->input->post('price');
		$discount = $this->input->post('discount');
		$subtotal = $this->input->post('total');
		$item_name = $this->input->post('item_name');

		$update = array(
			'qty' => $qty, 
			'price' => $price, 
			'discount' => $discount, 
			'subtotal' => $subtotal,
			'item_name' => $item_name
		);
		$this->db->where('id',$id);
		$this->db->where('user_session',$session_id);
		$query = $this->db->update('items',$update);


		$this->db->where('user_session',$session_id);
		$this->db->select_sum("subtotal");
		$subtotal_query = $this->db->get("items");
		if ($subtotal_query->num_rows()>0) {
		  $get_total = $subtotal_query->row()->subtotal;
		  $total['total'] = number_format($get_total,2);
		}
		$total['return'] = 1;

		echo json_encode($total);
	}

	public function delete_items(){
		$session_id = session_id();
		$id = $this->input->post('id');
		
		$this->db->where('id',$id);
		$this->db->where('user_session',$session_id);
		$query = $this->db->delete('items');

		$this->db->where('user_session',$session_id);
		$this->db->select_sum("subtotal");
		$subtotal_query = $this->db->get("items");
		if ($subtotal_query->num_rows()>0) {
		  $get_total = $subtotal_query->row()->subtotal;
		  $total['total'] = number_format($get_total,2);
		}
		$total['return'] = 1;

		echo json_encode($total);
	}

	// PDF CREATOR METHOD
	function pdf(){
	    $this->load->library('pdfgenerator');
		$data['title']='title';

		$session_id = session_id();
		$this->db->where('invoice_session',$session_id);
	    $data['invoice'] = $this->db->get('invoice');

	    $html = $this->load->view('web/pdf_style', $data, true);
	    $filename = 'Invoice_'.time();
	    $this->pdfgenerator->generate($html, $filename, true, 'A4', 'portrait');
	}



	public function create_pdf(){
		$this->load->helper(array('form','url'));
        $this->load->library('form_validation');

        $this->form_validation->set_rules('invoice_id','invoice_id','trim|required');
        $this->form_validation->set_rules('invoice_no','invoice_no','trim|required');
        $this->form_validation->set_rules('dated','dated','trim|required');
        $this->form_validation->set_rules('sub_total','sub_total','trim|required');

		$session_id = session_id();
        $invoice_id = $this->input->post('invoice_id');
        $invoice_no = $this->input->post('invoice_no');
        $dated = $this->input->post('dated');
        $due_dated = $this->input->post('due_dated');
        $bill_to = $this->input->post('bill_to');
        $sub_total = $this->input->post('sub_total');
        $tax = $this->input->post('tax');
        $total_krw = $this->input->post('total_krw');
        $notes = $this->input->post('notes');

        if ($this->form_validation->run()==false) {
            $this->index();
        }else{

            $update=array(
                'invoice_no' => $invoice_no,
                'dated' => $dated,
                'due_dated' => $due_dated,
                'bill_to' => $bill_to,
                'sub_total' => $sub_total,
                'tax' => $tax,
                'total_krw' => $total_krw,
                'alerts' => 1,
                'notes' => $notes
            );
            $this->db->where('invoice_session',$session_id);
            $this->db->where('id',$invoice_id);
            $this->db->update('invoice',$update); //update invoice data            


            if (!empty($_FILES['userfile']['name'])) {

	            $config['upload_path'] = './assets/images/';
	            $config['allowed_types'] = 'jpg|jpeg|png|gif';
	            $config['max_size'] = '200000';
	            $config['max_width'] = '1524000';
	            $config['max_height'] = '1000000';

	            $this->load->library('upload', $config);                        
	            $upload = $this->upload->do_upload('userfile');
	            
	            if($upload == true){
	                
	                $update_img=array(
	                    'userfile' => $_FILES['userfile']['name']
	                );
	                $this->db->where('invoice_session',$session_id);
	                $this->db->where('id',$invoice_id);
	                $this->db->update('invoice',$update_img);
	            }
	        }

            redirect('home/pdf');
        }
	}


	public function say_pdf(){
		$data['title']='title';

		$session_id = session_id();
		$this->db->where('invoice_session',$session_id);
	    $data['invoice'] = $this->db->get('invoice');

	    $html = $this->load->view('web/pdf_style', $data);
	}



}
?>