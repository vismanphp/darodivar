<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Advertisment extends User_Controller{
        
    public function __construct(){
        parent::__construct();
        $this->load->model('Select_model');
        $this->load->model('Delete_model');
        $this->load->model('Insert_model');
        $this->load->model('Update_model');
        $this->load->model('file_upload_model');
    }

    public function register_advertisment(){
        
        $sql="select * from tbl_berands";
        $result=$this->Select_model->select_query($sql);
        $sqll="select * from tbl_province";
        $resultt=$this->Select_model->select_query($sqll);

        $sql_number_of_online="select count('user_id') as number from tbl_onlines";
        $result_number_of_onlines=$this->Select_model->select_query($sql_number_of_online);

        $this->template->load('advertisment/register_advertisment',array('page'=>$this->page,'result_number_of_onlines'=>$result_number_of_onlines,'result'=>$result,'resultt'=>$resultt));
    }
	
	public function insert_advertisment(){
		if($this->input->post('action')=='insert_ad'){
            $user_id=$this->input->post('user_id');
            $category_id=$this->input->post('category_id');
            $berand_id=$this->input->post('berand');
            $model_id=$this->input->post('model');
            $fuel=$this->input->post('fuel');
            $built_year=$this->input->post('built_year');
            $karkard=$this->input->post('karkard');
            $gearbox=$this->input->post('gearbox');
            $type_of_car=$this->input->post('type_of_car');
            $type_of_sale=$this->input->post('type_of_sale');
            $exchange_car=$this->input->post('exchange_car');
			
            
            $body_status=$this->input->post('body_status');
            $body_color=$this->input->post('body_color');
            $internal_body_color=$this->input->post('internal_body_color');
            $province=$this->input->post('province');
            $city=$this->input->post('city');
            $local=$this->input->post('local');
            $description=$this->input->post('description');
            $image_asli=substr($this->input->post('image_asli'),14);
            
            if($user_id!='' && $category_id!='' && $gearbox!='' && $berand_id!='' && $model_id!='' &&  $built_year!='' &&  $fuel!=''  && $type_of_car!='' 
            && $type_of_sale!=''  && $body_status!='' && $body_color!='' && $internal_body_color!='' && $province!=''
             && $city!='' && $local!='' && $description!='' )
            {
				if($this->input->post('price')!=''){
					$price=$this->input->post('price');	
					$price_number_array=explode(',',$price);
					$price_final=implode('',$price_number_array);
						}else {
								$price_final=NULL;
						}
				
                
            $data=array(
                            'user_id'=>$user_id,
                            'category_id'=>$category_id,
                            'berand_id'=>$berand_id,
                            'model_id'=>$model_id,
                            'car_fuel'=>$fuel,
                            'built_date'=>$built_year,
                            'car_karkard'=>$karkard,
                            'car_gearbox'=>$gearbox,
                            'car_type_of'=>$type_of_car,
                            'car_type_of_sale'=>$type_of_sale,
                            'exchange_car'=>$exchange_car,
                            'car_price'=>$price_final,
                            'car_body_status'=>$body_status,
                            'car_body_color'=>$body_color,
                            'car_internal_color'=> $internal_body_color,
                            'car_province'=>$province,
                            'car_city'=>$city,
                            'car_local'=>$local,
                            'car_description'=>html_escape($description),
                            'car_reg_date'=>time()
                            
                        );
						//$this->output->enable_profiler(TRUE);
                        $ins=$this->Insert_model->insert('user_advertisments',$data);
                        $ad1_id=$this->db->insert_id();
                        $new_array = array('advertisment_id'=>$ad1_id);
                        $test=$this->session->tempdata('item');
                        
						if(!empty($test)){
							for($k=0;$k<count($test);$k++){
                                $test[$k]=array_merge($test[$k],$new_array);
                                
                        }
						  // Insert files data into the database
                       $insert = $this->file_upload_model->insert($test);
                        if($insert){
							//update the tbl_user_advertisment
							$data_have_pic=array(
								'have_pic'=>1
							);
							$con_have_pic=array(
								'id'=>$ad1_id
							);
							$this->Update_model->update('user_advertisments',$data_have_pic,$con_have_pic);
							
						}
						

								$data=array(
									'asli'=>1
								);

								$con=array(
									'file_image_name'=>$image_asli
								);
									$this->Update_model->update('car_pictures',$data,$con);
								
						
						}else{
							
							$data=array(
							'advertisment_id'=>$ad1_id,
							'file_image_name'=>'',
							'uploaded_time'=>'',
							'status'=>'0',
							'asli'=>'',
							'user_id'=>$this->session->userdata('userid')
							
							);
							$this->Insert_model->insert('car_pictures',$data);
						}
                      
                        
                        if($ins>0){
									echo 1;
								}
                        
                        
                        
                        
                    

                    //////////////////////////////////////// چک کردن فقط فارسی وارد کردن
                        /* $pattern = "/^[\s\x{0600}-\x{06FF}0-9]*$/u"; 
 
 
                        if (preg_match($pattern, $yourtext, $matches)) {
                        echo "farsi";
                        } else {
                        echo "engilish!";
                        } */

                

            }else {
                echo "تمام موارد باید تکمیل شود";
                
            }   
            
                        

        }
	}
	
	public function advertisment_ajax(){
        if($this->input->post('action')=='fetch_model'){
                $id=$this->input->post('id');
                $query="select * from tbl_models where `berand_id`=$id";
				$models=$this->Select_model->select_query($query);
				if(!empty($models)){
					echo "<option selected value=\"all\">انتخاب کنید...</option>";
					foreach($models as $item){
						echo "<option value=".$item['id'].">".$item['model_name']."</option>";
					}
					
				} else{
					echo "<option>مدلی فعلا وجود ندارد</option>";
				}

                
        }
    }
    public function advertisment_ajax_city(){
        if($this->input->post('action')=='fetch_city'){
            $id=$this->input->post('id');
            $sq="select * from tbl_cities where `province_id`=$id";
            $cities=$this->Select_model->select_query($sq);
            if(!empty($cities)){
                foreach($cities as $itemcity){
                    echo "<option value=".$itemcity['id'].">".$itemcity['city_name']."</option>";
                }
                
            } else{
                echo "<option>شهری فعلا وجود ندارد</option>";
            }

            
    }

    }

    public function ad_details(){
        $ad_id=$this->uri->segment(3);
        $user_id=$this->uri->segment(4);
       // $user_id=$this->session->userdata('userid');
        $sql="select ad.user_id,ad.car_fuel,ad.built_date,ad.car_karkard,ad.car_type_of_sale,ad.car_price,ad.car_type_of,ad.car_body_status,
        ad.car_body_color,ad.car_internal_color,ad.car_province,ad.car_city,ad.car_local,ad.car_description,berand_name,
        model_name,ad.car_gearbox,car_reg_date from tbl_user_advertisments ad inner join tbl_models m  
              on ad.model_id=m.id inner join tbl_berands b on ad.berand_id=b.id  where ad.id='$ad_id'";
        
        $advertisment_details=$this->Select_model->select_query($sql);

        $query="select id,file_image_name from tbl_car_pictures where advertisment_id='$ad_id'";
        $list_image=$this->Select_model->select_query($query);

        $sql_mobile="select mobile from tbl_users where id='$user_id'";
        $mobile_number=$this->Select_model->select_query($sql_mobile);
        $sql_ostan="select province_name from tbl_province where id='{$advertisment_details[0]['car_province']}'";
        $ostan_name=$this->Select_model->select_query($sql_ostan);
        $sql_city="select city_name from tbl_cities where id='{$advertisment_details[0]['car_city']}'";
        $city_name=$this->Select_model->select_query($sql_city);
        
       

            if ($this->agent->is_browser())
            {
                    $agent = $this->agent->browser().' '.$this->agent->version();
            }
            elseif ($this->agent->is_robot())
            {
                    $agent = $this->agent->robot();
            }
            elseif ($this->agent->is_mobile())
            {
                    $agent = $this->agent->mobile();
            }
            else
            {
                    $agent = 'Unidentified User Agent';
            }

        $my_data_counting=array(
            'ads_id'=>$ad_id,
            'user_id'=>$user_id,
            'visit_time'=>time(),
            'user_agent'=>$agent,
            'visitor_ip'=>$this->input->ip_address()

        );

        $this->Insert_model->insert('site_log',$my_data_counting);
        


        $this->load->view('advertisment/advertisment_detail',array('advertisment_details'=>$advertisment_details,
        'list_image'=>$list_image,'mobile_number'=>$mobile_number,'panel_title'=>'صفحه جزئیات آگهی',
        'ostan_name'=>$ostan_name,'city_name'=>$city_name));
    }

    public function list_my_advertisment(){
        $user_id=$this->uri->segment(3);
        //$sql="select * from tbl_user_advertisments where `user_id`='$user_id'";
        $sql="select  ad.id,ad.user_id,berand_name,m.berand_id,model_name,car_province,car_price,car_reg_date,ad.status from tbl_user_advertisments ad inner join 
        tbl_models m on ad.model_id=m.id inner join tbl_berands b on ad.berand_id=b.id where `user_id`='$user_id'
         order by car_reg_date DESC ";

        $list_my_ads=$this->Select_model->select_query($sql);
        $this->template->load('user/list_my_ad',array('list_my_ads'=>$list_my_ads,'page'=>$this->page));

    }

    

    public function delete_my_advertisment(){
        $ad_id=$this->uri->segment(3);
        $con=array(
            'id'=>$ad_id
        );
        $this->Delete_model->delete_where('user_advertisments',$con); 
        $new_myfiles=[];
        $sql_del="select file_image_name from tbl_car_pictures where `advertisment_id`='$ad_id'";
        $list_image_for_del=$this->Select_model->select_query($sql_del);
        $this->output->enable_profiler(TRUE); 
        $this->load->helper('file');
        //$this->template->load('user/result',array('list_image_for_del'=>$list_image_for_del));
        for($i=0;$i<=count($list_image_for_del)-1;$i++){
          $new_myfiles[]=$list_image_for_del[$i]['file_image_name'];
       } 
     
       for($j=0;$j<=count($new_myfiles)-1;$j++){
           
               if (unlink("C:\\xampp\htdocs\darodivar\uploads\\files\\{$new_myfiles[$j]}")) {
                   echo "The file has been deleted";
                  
               } else {
                   echo "The file was not found or not readable and could not be deleted";
                   
               }
        } 
       
        $con_d=array(
            'advertisment_id'=>$ad_id
        );
        $this->Delete_model->delete_where('car_pictures',$con_d);
        // delete all phisical image file

          redirect('advertisment/list_my_advertisment/'.$this->session->userdata('userid'));
        
        
    }


    public function enable_my_advertisment(){
        $ad_id=$this->uri->segment(3);
        $data_enable=array(
            'status'=>1
        );
        $con_enabale=array(
            'id'=>$ad_id
        );
        $this->Update_model->update('user_advertisments',$data_enable,$con_enabale);
        redirect('advertisment/list_my_advertisment/'.$this->session->userdata('userid'));
    }

    public function disable_my_advertisment(){
        $ad_id=$this->uri->segment(3);
        $data_enable=array(
            'status'=>0
        );
        $con_enabale=array(
            'id'=>$ad_id
        );
        $this->Update_model->update('user_advertisments',$data_enable,$con_enabale);
        redirect('advertisment/list_my_advertisment/'.$this->session->userdata('userid'));
    }

    public function edit_my_advertisment(){
         $ad_id=$this->uri->segment(3);
        $berand_id=$this->uri->segment(4);
        $province_id=$this->uri->segment(5);
        
        //aquiring this id details for edit
        $sql="select ad.car_fuel,ad.built_date,ad.car_karkard,ad.car_type_of_sale,ad.car_price,ad.car_type_of,ad.car_body_status,
        ad.car_body_color,ad.car_internal_color,ad.car_province,ad.car_city,ad.car_local,ad.car_description,berand_name,
        model_name,ad.car_gearbox,ad.exchange_car,car_reg_date from tbl_user_advertisments ad inner join tbl_models m  
              on ad.model_id=m.id inner join tbl_berands b on ad.berand_id=b.id  where ad.id='$ad_id'";
        $my_ad=$this->Select_model->select_query($sql);

        $sql2="select * from tbl_berands";
        $result=$this->Select_model->select_query($sql2);

        $sql_models="select id,model_name from tbl_models where `berand_id`='$berand_id'";
        $result_models=$this->Select_model->select_query($sql_models);

        $sql_cities="select * from tbl_cities where `province_id`='$province_id'";
        $result_cities=$this->Select_model->select_query($sql_cities);

        $sqlp="select * from tbl_province";
        $resultp=$this->Select_model->select_query($sqlp);


        $sqlc="select * from tbl_cities where id='{$my_ad[0]['car_city']}'";
        $resultc=$this->Select_model->select_query($sqlc);

        $sql_pic="select file_image_name,asli from tbl_car_pictures where advertisment_id='$ad_id'";
        $pictures_name=$this->Select_model->select_query($sql_pic);
       //$this->output->enable_profiler(TRUE);
	   
		
       $this->template->load('user/edit_my_ad',array('page'=>$this->page,'my_ad'=>@$my_ad,'result'=>$result,'resultp'=>$resultp,
        'pictures_name'=>$pictures_name,'resultc'=>$resultc,'result_models'=>$result_models,
        'result_cities'=>$result_cities,'adver_id'=>$ad_id)); 
    }

    public function edit_advertisment_ajax(){
        if($this->input->post('action')=='edit_ad'){
            $user_id=$this->input->post('user_id');
            
            $berand_id=$this->input->post('berand');
            $model_id=$this->input->post('model');
            $fuel=$this->input->post('fuel');
            $built_year=$this->input->post('built_year');
            $karkard=$this->input->post('karkard');
            $gearbox=$this->input->post('gearbox');
            $type_of_car=$this->input->post('type_of_car');
            $type_of_sale=$this->input->post('type_of_sale');
            $exchange_car=$this->input->post('exchange_car');
            $price=$this->input->post('price');
            $body_status=$this->input->post('body_status');
            $body_color=$this->input->post('body_color');
            $internal_body_color=$this->input->post('internal_body_color');
            $province=$this->input->post('province');
            $city=$this->input->post('city');
            $local=$this->input->post('local');
            $description=$this->input->post('description');
            $image_asli=substr($this->input->post('image_asli'),14);
           
            
           
            if($user_id!=''  && $gearbox!='' && $berand_id!='' && $model_id!='' &&  $built_year!='' &&  $fuel!=''  && $type_of_car!='' && $type_of_sale!='' && $price!='' && 
            $body_status!='' && $body_color!='' && $image_asli!='' && $internal_body_color!='' && $province!='' && $city!='' && $local!='' && $description!='')
            {
                $price_number_array=explode(',',$price);
                $price_final=implode('',$price_number_array);
            $data=array(
                            'user_id'=>$user_id,
                            'berand_id'=>$berand_id,
                            'model_id'=>$model_id,
                            'car_fuel'=>$fuel,
                            'built_date'=>$built_year,
                            'car_karkard'=>$karkard,
                            'car_gearbox'=>$gearbox,
                            'car_type_of'=>$type_of_car,
                            'car_type_of_sale'=>$type_of_sale,
                            'car_price'=>$price_final,
                            'car_body_status'=>$body_status,
                            'car_body_color'=>$body_color,
                            'car_internal_color'=> $internal_body_color,
                            'car_province'=>$province,
                            'car_city'=>$city,
                            'car_local'=>$local,
                            'exchange_car'=>$exchange_car,
                            'car_description'=>html_escape($description)
                            
                        );

                        $con=array(
                            'id'=>$this->input->post('adv_id'),
                            'user_id'=>$this->session->userdata('userid')
                        );
    
                      $this->Update_model->update('user_advertisments',$data,$con);
                           
                        
                        $ad_id=$this->input->post('adv_id');
                        $new_array = array('advertisment_id'=>$ad_id);
                        $test=$this->session->tempdata('item');
						
                        for($k=0;$k<count($test);$k++){
                            $test[$k]=array_merge($test[$k],$new_array);
                            
                         }
                     
                        
                        
                       
                        if(!empty($test)){
						//file_put_contents('c:/test/filename.txt', print_r($test, TRUE));
						file_put_contents( 'c:/test/filename.txt', json_encode( $test ) );
                        // Insert files data into the database
                       $insert = $this->file_upload_model->insert($test);
					   $data_update=array(
						'have_pic'=>1
					   );
					   $con_update=array(
						'id'=>$ad_id
					   );
					   
					   $this->Update_model->update('user_advertisments',$data_update,$con_update);
                       
                    }
					
                   
                         $data_b=array(
                            'asli'=>1
                        );

                        $con_b=array(
                            'file_image_name'=>$image_asli
                        );
                        file_put_contents('c:/test/filename1.txt', json_encode($_POST, TRUE));
                        $number_of_image ="select file_image_name from `tbl_car_pictures` where `advertisment_id`='$ad_id'";
						
                        $list_of_this_ad_id_images=$this->Select_model->select_query($number_of_image);
						file_put_contents('c:/test/filename.txt', print_r( $list_of_this_ad_id_images, TRUE));
                                foreach($list_of_this_ad_id_images as $list_item){
                                         if($list_item['file_image_name']!=$_POST['image_asli']){
                                            $data_a=array(
                                                'asli'=>0
                                            );
                    
                                            $con_a=array(
                                                'file_image_name'=>$list_item['file_image_name']
                                            ); 
                                       
                                            $this->Update_model->update('car_pictures',$data_a,$con_a); 
                                         }
                        
                           $this->Update_model->update('car_pictures',$data_b,$con_b);
                                
                               

                            } 
                    

                   
                        echo 1;
						
                    
                

            }else {
                echo "تما موارد باید تکمیل شود";
            }            

        }
    }
}
