<?php
class Banner extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Banner_model');
    }

    public function index()
    {
        $data = [
            'title' => 'Banner',
            'banner' => $this->Banner_model->view()
        ];
        
        template('banner/index', $data);
    }

    public function edit()
    {
        $data=[
            'title'=>'Edit Banner',
            'banner'=>$this->Banner_model->view()
        ];
        // echo '<pre>';print_r($data);die;
        template('banner/edit', $data);
    }

    public function update()
    {
        $data = [];

        if(!empty($_FILES['banner']['name'])){
            $data['banner'] = upload_image($_FILES['banner'], BANNER_URL);
        }
        if(!empty($_FILES['image1']['name'])){
            $data['image1'] = upload_image($_FILES['image1'], IMAGE_URL);
        }
        if(!empty($_FILES['image2']['name'])){
            $data['image2'] = upload_image($_FILES['image2'], IMAGE_URL);
        }
        if(!empty($_FILES['image3']['name'])){
            $data['image3'] = upload_image($_FILES['image3'], IMAGE_URL);
        }
        if(!empty($_FILES['image4']['name'])){
            $data['image4'] = upload_image($_FILES['image4'], IMAGE_URL);
        }
        if(!empty($_FILES['image5']['name'])){
            $data['image5'] = upload_image($_FILES['image5'], IMAGE_URL);
        }

        $UpdateProduct = $this->Banner_model->update($data,1);
        if ($UpdateProduct) {
            $this->session->set_flashdata('msg', array('message' => 'Banner Updated Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/banner');
    }
}
