<?php
defined('BASEPATH') OR exit('No direct script acess allowed');

//pemanggilan library REST_Controller
require APPPATH . 'libraries/REST_Controller.php';

class Pengguna extends REST_Controller{ //merupaakan class pengguna ayng extend kepada Rest_controller yanng ada di library


    function __construct(){
        parent::__construct();
        $this->load->model('pengguna_model');// fungsi construk untuk memanggil model
    }




    function index_get(){ // fungsi untuk menjalankan index get atau penampilan data

//      $this->token_check();
        $nama = $this->get('nama');
        $data = $this->pengguna_model->getPengguna($nama);//pemanggilan fungsi getPengguna yanga ada di model
        $result = array( // akan menampilkan respon jika data berhasil di cari
            'success' => true,
            'message' => 'data success',
            'data' => array('pengguna' => $data)
        );

        $this->response($result, REST_Controller::HTTP_OK);
    }

    
 // merupakan fungsi untuk menambahkan data (post)
    function index_post(){

 //      $this->token_check();

  //validasi
        $validasi_message = [];//merupakan validasi jika ada suatu kesalahan agar mudah di ketahui oleh user
        if($this->post('username') == ''){
            array_push($validasi_message,'Email not be null');
        } 
        if($this->post('username') != '' && !filter_var($this->post('username'),FILTER_VALIDATE_EMAIL)){
            array_push($validasi_message,'Email invalid!');
        }
        if($this->post('nama') == ''){
            array_push($validasi_message,'Name not be null');
        }
        if($this->post('level') == ''){
            array_push($validasi_message,'Level not be null');
        }
        if($this->post('password') == ''){
            array_push($validasi_message,'Password not be null');
        }
        if(count($validasi_message) > 0){
            $output = array(// penampilan validasi
                'success' => false,
                'message' => 'Input false',
                'data' => $validasi_message
            );
            $this->response($output,REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        $data = array(//proses penambahan data
            'username' => $this->post('username'),
            'nama' => $this->post('nama'),
            'level' => $this->post('level'),
            'password' => $this->post('password')
        );

        $result = $this->pengguna_model->insertPengguna($data);
        if(empty($result)){    //jika data nya ada yang sama maka data tidak akan dapat ditambahkan
            //syarat data ditambahkan dalah data harus ada satu tidak boleh ada 2 atau lebih
            $output = array(  
                'success' => false,
                'message' => 'data is alviable !',
                'data' => null
            );            
        }else{
            $output = array(
                'success' => true,
                'message' => 'input data succes',
                'data' => array(
                    'pengguna' => $result
                )
            );
        }
        $this->response($output,REST_Controller::HTTP_OK);
    }


//merupakan function untuk melakukan proses put atau pengubahan data
    function index_put(){

        //panggil token check
//       $this->token_check();


        //validasi --> memunculkan validasi lagi jika ada data yang error guna untuk memudahkan user menemukan letak kesalahan nya
        $validasi_message = [];

        $username = $this->put('username');

        if( $username == ''){
            array_push($validasi_message,'Email not be null');
        }

        if($username  != '' && !filter_var($username ,FILTER_VALIDATE_EMAIL)){
            array_push($validasi_message,'Email invalid');
        }

        if($this->put('nama') == ''){
            array_push($validasi_message,'Nama not be null');
        }

        if($this->put('level') == ''){
            array_push($validasi_message,'Level not be null');
        }

        if($this->put('password') == ''){
            array_push($validasi_message,'Password not be null');
        }

        if(count($validasi_message) > 0){
            $output = array(
                'success' => false,
                'message' => 'Update failed',
                'data' => $validasi_message
            );
            $this->response($output,REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
        //pengubahan data
        $data = array(
            'nama'  => $this->put('nama'),
            'level' =>$this->put('level'),
            'password' => $this->put('password')
        );

        $result = $this -> pengguna_model->updatePengguna($data, $username );

        $output = array( //jika sukses maka akan menampilkan tampilan seperti ini
            'success' => true,
            'message' => 'success',
            'data' => array( 
                'pengguna' => $result
            )
        );
        $this->response($output,REST_Controller::HTTP_OK);

    }

    function index_delete(){ //merupakan fungsi untuk mendelete atau menghapus data 

 //     $this->token_check();
        $username =$this->delete('username');
        $validasi_message = [];


// sebuah validasi lagi apakah data yang akan dihapus tersedia atau tidak
        if( $username == ''){
            array_push($validasi_message,'Email can not be null');
        }

        if($username  != '' && !filter_var($username ,FILTER_VALIDATE_EMAIL)){
            array_push($validasi_message,'invalid');
        }

        if(count($validasi_message) > 0){
            $output = array(
                'success' => false,
                'message' => 'delete data filed',
                'data' => $validasi_message
            );
            $this->response($output,REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
//pemanggilan fungsi deletePengguna yang ada di pengguna_model
        $result = $this->pengguna_model->deletePengguna($username);

        //respons
        if(empty($result)){//jika data yang ditemukan bernilai kosong maka akan menampilkan deletet failed
            $output = array(
                'success' => false,
                'message' => 'delete failed',
                'data' => null
            );
        }else{ //sedangkan jika data ditemukan makan proses delete akan terjadi
            $output = array(
                'success' => true,
                'message' => 'success',
                'data' => array(
                    'pengguna' => $result
                )
            );
        }
        $this->response($output,REST_Controller::HTTP_OK);
    }


//ini merupakan fungsi untuk login saat ada di postmant
    function login_post(){

        $username = $this->post('username'); //pengambilan data username dan password
        $password = $this->post('password');
        $data = $this->pengguna_model->login($username, $password);


        if(empty($data)){ //jika  nilai variabeel data kosong maka akan memunculkan peringatan username atau pasword salah
            $output = array(
                'success' => false,
                'message' => 'username / password wrong',
                'data' => null,
                'error_code' => 1308
            );
            $this->response($output,REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }else{
            $jwt = new JWT(); //jika benar akan menampilkan proses sukses dengan token encode

            //deklarasi secret key
            $secret_key = 'Davidindera12324454323'; //secret key berfungsi untuk menjadikan data tersebut sebagai  hak milik kita
            $date = new DateTime();
            $payload = array(
                'username' => $data['username'],
                'name' => $data ['nama'],
                'user type' => $data['level'],
                'login_time' =>$date->getTimeStamp(),
                'experied_time' => $date->getTimeStamp() + 1800
            );

            //Encode --> proses encode
            $result = array(
                'success' => true,
                'message' => 'Login success !',
                'data' => $data,
                'token' => $jwt->encode($payload, $secret_key) 
            );
            $this->response($result,REST_Controller::HTTP_OK);
        }
    }


    //merupakan fungsi token check yang akan di panggil di setiap fungsi
    function token_check(){
        try{
            $token = $this->input->get_request_header('Authorization');

            if(!empty($token)){
                $token = explode(' ', $token)[1];
            }

            

            $jwt = new JWT(); 
            $secret_key = 'Davidindera12324454323';
            $token_decode = $jwt->decode($token, $secret_key);

        }catch(Exception $e){
            $result = array(
                'success' =>false,
                'message' => 'token invalid !',
                'data' => null,
                'error_code' => 1204
            );
            $this->response($result, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
    }
}