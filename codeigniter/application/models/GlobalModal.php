<?php
class GlobalModal extends  CI_Model{
	public function __construct()
	{
		parent::__construct();
	}

	public function addData($tableName,$insertData){
		try {
			$this->db->trans_start();
			$this->db->insert($tableName, $insertData);
			$result['user_id'] = $this->db->insert_id();
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				log_message('info', "insert Data Transaction Rollback");

				$result['status'] = FALSE;
			} else {
				$this->db->trans_commit();
				log_message('info', "insert Data Transaction Commited");
				$result['status'] = TRUE;

			}
			$this->db->trans_complete();
		} catch (Exception $exc) {
			log_message('error', $exc->getMessage());
			$result['status'] = FALSE;
		}
		return $result;
	}

	public function deleteData($tableName,$whereData){
		try {
			$this->db->trans_start();
			$this->db->where($whereData);
			$this->db->delete($tableName);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				log_message('info', "insert Data Transaction Rollback");
				$result = FALSE;
			} else {
				$this->db->trans_commit();
				log_message('info', "insert Data Transaction Commited");
				$result = TRUE;
			}
			$this->db->trans_complete();
		} catch (Exception $exc) {
			log_message('error', $exc->getMessage());
			$result = FALSE;
		}
		return $result;
	}

	public function getDataArray($tableName,$selectArray,$whereArray,$order_by=0){

		$this->db->select($selectArray);
		$this->db->where($whereArray);
		if($order_by==1){
			$this->db->order_by('position asc');
		}
		$data=$this->db->get($tableName)->result_array();
		if(count($data)==0){
			return false;
		}else{
			return $data;
		}
	}
	
		public function getDataObject($tableName,$selectArray,$whereArray,$order_by=0){

		$this->db->select($selectArray);
		$this->db->where($whereArray);
		if($order_by==1){
			$this->db->order_by('position asc');
		}
		$data=$this->db->get($tableName)->row();
		if($data==false){
			return false;
		}else{
			return $data;
		}
	}

	public function executeQuery($query){
		$data=$this->db->query($query)->result_array();
		if(count($data)==0){
			return false;
		}else{
			return $data;
		}
	}

	public function executeQueryObject($query){
		$data=$this->db->query($query)->row();
		if(count($data)==0){
			return false;
		}else{
			return $data;
		}
	}

	public  function updateData($tableName,$setData,$whereData){
		try {
			$this->db->trans_start();
			$this->db->where($whereData);
			$this->db->set($setData);
			$this->db->update($tableName);
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				log_message('info', "insert Data Transaction Rollback");
				$result = FALSE;
			} else {
				$this->db->trans_commit();
				log_message('info', "insert Data Transaction Commited");
				$result = TRUE;
			}
			$this->db->trans_complete();
		} catch (Exception $exc) {
			log_message('error', $exc->getMessage());
			$result = FALSE;
		}
		return $result;
	}

	public function genratedIds($tableName,$checkColumn,$idPreFix){
		$return_id = $idPreFix . rand(100, 100000000);
		$this->db->select($checkColumn);
		$this->db->from($tableName);
		$this->db->where($checkColumn, $return_id);
		$this->db->get();
		if ($this->db->affected_rows() > 0) {
			return $this->genratedIds();
		} else {
			return $return_id;
		}
	}

	/*************************** Returns State list from state_master table************************************/
    
    public function get_all_state_model()
    {
    return $query = $this->db
    		 ->select('*')
    		 ->from('state_master')
    		 ->get()->result();
    }
    
    
    public function get_all_cities_by_state_id($state_id)
    {
          return $query = $this->db
    			 ->select('city_id, city_name')
    			 ->from('city_master')
    			 ->where('state_id', $state_id)
    			 ->order_by('city_name', 'ASC')
    			 ->get()->result();
    }
    

    
      function upload_multiple_file_new($upload_path, $inputname, $combination = "") {

        $combination = (explode(",", $combination));

        $check_file_exist = $this->check_file_exist($upload_path);
        if (isset($_FILES[$inputname]) && $_FILES[$inputname]['error'] != '4') {

            $files = $_FILES;
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = '*';
//            $config['max_size'] = '20000000';    //limit 10000=1 mb
            $config['remove_spaces'] = true;
            $config['overwrite'] = false;

            $this->load->library('upload', $config);

            if (is_array($_FILES[$inputname]['name'])) {
                $count = count($_FILES[$inputname]['name']); // count element
                $files = $_FILES[$inputname];
                $images = array();
                $dataInfo = array();

                if (in_array("1", $combination)) {
                    for ($j = 0; $j < $count; $j++) {
                        $fileName = $files['name'][$j];
                        if (in_array($fileName, $check_file_exist)) {
                            $response['status'] = 201;
                            $response['body'] = $fileName . " Already exist";
                            return $response;
                        }
                    }
                }
                $inputname = $inputname . "[]";
                for ($i = 0; $i < $count; $i++) {
                    $_FILES[$inputname]['name'] = $files['name'][$i];
                    $_FILES[$inputname]['type'] = $files['type'][$i];
                    $_FILES[$inputname]['tmp_name'] = $files['tmp_name'][$i];
                    $_FILES[$inputname]['error'] = $files['error'][$i];
                    $_FILES[$inputname]['size'] = $files['size'][$i];
                    $fileName = $files['name'][$i];
                    //get system generated File name CONCATE datetime string to Filename
                    if (in_array("2", $combination)) {
                        $date = date('Y-m-d H:i:s');
                        $randomdata = strtotime($date);
                        $fileName = $randomdata . $fileName;
                    }
                    $images[] = $fileName;

                    $config['file_name'] = $fileName;

                    $this->upload->initialize($config);
                    $up = $this->upload->do_upload($inputname);
                    // var_dump($up);
                    $dataInfo[] = $this->upload->data();
                }
                // var_dump($dataInfo);

                $file_with_path = array();
                foreach ($dataInfo as $row) {
                    $raw_name = $row['raw_name'];
                    $file_ext = $row['file_ext'];
                    $file_name = $raw_name . $file_ext;
                    $file_with_path[] = $upload_path . "/" . $file_name;
                }
                $response['status'] = 200;
                $response['body'] = $file_with_path;
                return $response;
            }
        } else {
            $response['status'] = 201;
            $response['body'] = array();
            return $response;
        }
    }
    
     function check_file_exist($upload_path) {
        $filesnames = array();

        foreach (glob('./' . $upload_path . '/*.*') as $file_NAMEEXISTS) {
            $file_NAMEEXISTS;
            $filesnames[] = str_replace("./" . $upload_path . "/", "", $file_NAMEEXISTS);
        }
        return $filesnames;
    }
    
    
    public function getDataArrayJoin($tableName,$selectArray,$whereArray,$joinArray){
        	$this->db->select($selectArray);
        	foreach($joinArray as $new_join){
        $this->db->join($new_join['table_name'],$new_join['column_names'],$new_join['join_type']);	    
        	}
		$this->db->where($whereArray);
	
		$data=$this->db->get($tableName)->result_array();
		if(count($data)==0){
			return false;
		}else{
			return $data;
		}
    }
	
	
}
?>
