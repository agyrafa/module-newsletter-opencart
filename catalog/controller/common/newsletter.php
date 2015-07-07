<?php
class ControllerCommonNewsletter extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('common/newsletter');
        $this->load->model('extension/newsletter');

        $data['text_email'] = $this->language->get('text_email');
        $data['register'] = $this->language->get('register');


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/newsletter.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/common/newsletter.tpl', $data);
        } else {
            return $this->load->view('default/template/common/newsletter.tpl', $data);
        }
    }

    public function validate() {

        if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }

        return !$this->error;
    }

    public function addNewsletter(){
        $json = array();

        $this->load->model('extension/newsletter');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $json['email'] = $this->request->post['email'];

            $this->model_extension_newsletter->addNewsletter($json);

            echo json_encode( array(
                'msg' =>  'Cadastrado com sucesso',
                'success' => true
            ) );

        } else {
            $this->load->language('common/newsletter');

            $email = '';


            if(isset($this->error['email']) && !empty($this->error['email'])) {
                $email = $this->language->get('error_email');
            }


            echo json_encode( array(
                'email'         =>  $email,
                'error'         =>  true
            ) );
        }
    }
}