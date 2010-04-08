<?php

/**
 * Description of prioritise
 *
 * @author mikepearce
 */
class Prioritise extends Controller
{
    public function Prioritise()
    {
        parent::Controller();
        $this->load->database();
        $this->load->model('Story_model');
    }
    
    public function index($id)
    {
        $data['stories'] = $this->Story_model->getStoriesInOrder($id);
        $data['id'] = $id;
        $this->load->view('siteHeader');
        $this->load->view('prioritise/index', $data);
        $this->load->view('siteFooter');
    }

    public function save()
    {
        $priorityOrder = implode(',', $this->input->post('recordsArray'));
        if ($rows = $this->Story_model->savePriorityOrder($this->input->post('themeId'), $priorityOrder))
        {
            echo 'Priority saved successfully - well done you!';
        }
        else {
            echo 'FAIL Rows: '. $priorityOrder;
        }
        
    }
}