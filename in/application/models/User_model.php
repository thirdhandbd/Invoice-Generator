<?php
class User_model extends CI_Model{
	
	
	public function __construct()
	{
		$this->load->database();
	}
	/*public function get_promotions(){
		$this->db->order_by('resort_name','asc');
		$query = $this->db->get("promotions");
        return $query;
	}
    */

}
?>