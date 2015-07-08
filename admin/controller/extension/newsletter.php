<?php
class ControllerExtensionNewsletter extends Controller {
    private $error = array();

    public function index() {
        $this->getList();
    }


    protected function getList() {
        $this->load->language('extension/newsletter');
        $this->load->model('extension/newsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'whats_name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $results = $this->model_extension_newsletter->getTotalNewsletter();

        $data['dados'] = '';

        foreach ($results as $result) {
            $data['dados'][]= array(
                'id'          => $result['id'],
                'email'       => $result['email'],
                'date_add'    => date('d/m/Y - H:i', strtotime($result['date_add'])),
                'edit'              => $this->url->link('extension/newsletter/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, 'SSL')
            );
        }

        
        
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/whats_banner', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['column_name'] = $this->language->get('column_name');
        $data['column_email'] = $this->language->get('column_email');
        $data['column_phone'] = $this->language->get('column_phone');
        $data['column_date'] = $this->language->get('column_date');
        $data['column_action'] = $this->language->get('column_action');
        $data['text_confirm'] = $this->language->get('text_confirm');


        $data['button_edit'] = $this->language->get('button_edit');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['button_delete'] = $this->language->get('button_delete');

        $data['delete'] = $this->url->link('extension/newsletter/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('extension/newsletter.tpl', $data));
    }

    public function delete() {
        $this->load->language('extension/newsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/newsletter');


        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $id) {
                $this->model_extension_newsletter->deleteEmail($id);
            }

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $this->getList();
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('extension/newsletter');

        foreach ($this->request->post['selected'] as $id) {
            $result_total = $this->model_extension_newsletter->getNewsletter($id);

        }

        return !$this->error;
    }

    public function edit() {
        $this->load->language('extension/newsletter');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/newsletter');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_extension_newsletter->editEmail($this->request->get['id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
        $this->getForm();
    }

    protected function getForm() {
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['whats_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_add'] = $this->language->get('text_add');

        $data['entry_email'] = $this->language->get('entry_email');


        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_general'] = $this->language->get('tab_general');

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->request->get['id'])) {
            $data['id'] = $this->request->get['id'];
        } else {
            $data['id'] = 0;
        }


        if (isset($this->error['whats_email'])) {
            $data['error_email'] = $this->error['whats_email'];
        } else {
            $data['error_email'] = '';
        }


        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/newsletter', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['id'])) {
            $data['action'] = $this->url->link('extension/newsletter/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('extension/newsletter/edit', 'token=' . $this->session->data['token'] . '&id=' . $this->request->get['id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('marketing/whats_banner', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
            $whats_banner = $this->model_extension_newsletter->getNewsletter($this->request->get['id']);
        }


        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } elseif (!empty($whats_banner)) {
            $data['email'] = $whats_banner['email'];
        } else {
            $data['email'] = '';
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');



        $this->response->setOutput($this->load->view('extension/newsletter_form.tpl', $data));
    }


    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['email']) < 3) || (utf8_strlen($this->request->post['email']) > 128)) {
            $this->error['email'] = $this->language->get('error_email');
        }

        $email_info = $this->model_extension_newsletter->getNewsletter($this->request->get['id'], $this->request->post);

        if ($email_info) {
            if (!isset($this->request->get['id'])) {
                $this->error['warning'] = $this->language->get('error_exists');
            } elseif ($email_info['id'] != $this->request->get['id']) {
                $this->error['warning'] = $this->language->get('error_exists');
            }
        }

        return !$this->error;
    }

}