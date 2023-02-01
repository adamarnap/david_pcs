<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class pengguna_model extends CI_Model{

    //deklarasi constructor
    function __construct(){
        parent::__construct();
        
        //untuk akses database
        $this->load->database();
    }

//ini merupakan model untuk menjalankan pemanggilan data
    public function getPengguna($nama){
        if($nama==''){// jika data nama nya tidak disi maka akan menampikan seluruh data yang ada
            $data = $this->db->get('pengguna');
        }else{
            $this->db->like('nama', $nama);
            $this->db->or_like('username', $nama); // jika data nama di isi maka akan menampikan data yang di cari saja
            $data = $this->db->get('pengguna');
        }
        return $data->result_array();
    }

//ini merupakan model untuk menjalankan POST atau tambah data
        public function insertPengguna($data){
            $this->db->where('username', $data['username']);
            $check_data = $this->db->get('pengguna');// proses check data pada tabel pengguna
            $result = $check_data->result_array();
            
            if(empty($result)){ 
                $this->db->insert('pengguna',$data);
            }else{
                $data = array();
            }
            return $data; 
        }
// ini merupakan model untuk menjalankan put / update data
        public function updatePengguna($data, $username){  
          $this->db->where('username',$username);
          $this->db->update('pengguna', $data);

          $result = $this->db->get_where('pengguna',array('username'=>$username));

          return $result->row_array();
        }


//  ini merupakan model untuk menghapus data
        public function deletePengguna($username){
        $result = $this->db->get_where('pengguna', array('username' => $username));
        $this->db->where('username', $username);
        $this->db->delete('pengguna');

        return $result->row_array();

        }


//ini merupakan model untuk mejalankan fungsi login
        public function login($username, $password){
            //cek data
            $this->db->where('username', $username);
            $this->db->where('password', $password);
            $data = $this->db->get('pengguna');

            return $data->row_array();
        }

}