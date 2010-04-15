<?php

class Stories extends Controller {

	function Stories()
	{
		parent::Controller();
                $this->load->database();
                $this->load->model('Story_model');
	}

	function index()
	{
            $this->load->helper('form');
            $this->load->database();

            $this->load->library('form_validation');
            $config = array(
               array(
                     'field'   => 'asA',
                     'label'   => 'As A...',
                     'rules'   => 'trim|required'
                  ),
               array(
                     'field'   => 'iNeed',
                     'label'   => '... I need ...',
                     'rules'   => 'trim|required'
                  ),
               array(
                     'field'   => 'soThat',
                     'label'   => '... so that.',
                     'rules'   => 'trim|required'
                  ),
                array(
                     'field'   => 'estimate',
                     'label'   => 'Estimate',
                     'rules'   => 'trim'
                  ),
                array(
                     'field'   => 'remaining',
                     'label'   => 'Remaining',
                     'rules'   => 'trim'
                  ),
                array(
                     'field'   => 'nickname',
                     'label'   => 'Nickname',
                     'rules'   => 'trim|required'
                  )
                );
            $this->form_validation->set_rules($config);
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            if ($this->form_validation->run() !== FALSE)
            {

                // Do some monkeying with the acceptance criteria
                $acceptance = explode("\n", $this->input->post('acceptanceCriteria'));
                $ja = json_encode($acceptance);

                // Is this a new theme?
                if ($this->input->post('themeName'))
                {
                    // Yes, new theme
                    $themeId = $this->_addNewStoryName($this->input->post('themeName'));
                }
                else {
                    $themeId = $this->input->post('themes');
                }
                $data = array(
                           'themeId'            => $themeId,
                           'asA'                => $this->input->post('asA'),
                           'iNeed'              => $this->input->post('iNeed'),
                           'soThat'             => $this->input->post('soThat'),
                           'acceptanceCriteria' => $ja,
                           'nickname'           => $this->input->post('nickname'),
                           'estimate'           => $this->input->post('estimate'),
                           'remaining'          => $this->input->post('estimate'),
                           'date_modified'      => date('Y-m-d H:i:s'),
                           'date_added'         => date('Y-m-d H:i:s')
                        );

                $this->db->insert('stories', $data);

            }

            // Get the stories and the storynames
            $data['stories'] = $this->Story_model->getAllStories();
            $data_for_form['themes'] = $this->Story_model->getThemeNames();

            // load the form etc
            $data['form'] = $this->load->view('form', $data_for_form, TRUE);
            $this->load->view('siteHeader');
            $this->load->view('stories', $data);
            $this->load->view('siteFooter');
	}

        private function _addNewStoryName($themeName)
        {
            $this->db->insert('themes', array('themeName' => $themeName));
            return $this->db->insert_id();
        }

        function delete($id = FALSE, $index = TRUE)
        {
            $this->Story_model->deleteStory(
                        (FALSE != $id ? $id : $this->uri->segment(3))
                    );
            if ($index)
                $this->index();
        }

        function markDone($id, $done = TRUE)
        {
            $this->Story_model->markStoryDone(
                        $id, $done
                    );
            $this->index();
        }

        function multiaction()
        {
            $ids = $this->input->get('stories');
            switch($this->input->get('action'))
            {
                case 'del':
                    foreach($ids AS $id)
                    {
                        $this->delete($id, FALSE);
                    }
                    $this->allnicks();
                    break;
                case 'print':
                    $this->printer($ids);
                    break;
                case 'done':
                    foreach($ids AS $id)
                    {
                        $this->markDone($id);
                    }
                    $this->allnicks();
                    break;
                case 'export':
                    $this->load->helper('download');
                    $stories = $this->Story_model->getManyStories($ids);
                    $row = 'Story Id, Theme Name, Nickname, Estimate, As a.., I need..., So that..., Acceptance Criteria, Done?'."\n";
                    foreach ($stories AS $story)
                    {
                        $row .= '"'. $story->id         .'",'.
                                '"'. $story->themeName  .'",'.
                                '"'. $story->nickname   .'",'.
                                '"'. $story->estimate   .'",'.
                                '"'. $story->asA        .'",'.
                                '"'. $story->iNeed      .'",'.
                                '"'. $story->soThat     .'",';
                        $acceptance = '';
                        foreach( json_decode($story->acceptanceCriteria) AS $acc)
                        {
                            $acceptance .= str_replace(array('"'), '', $acc) ."\n";
                        }
                        $row .= '"'. $acceptance .'",'.
                                '"'. ($story->done == 0 ? 'Not Done' : 'Done') ."\"\n";
                    }
                    // Now force a download
                    force_download('UserStories'. date('YmdHis') .'.csv', $row);
                    break;
                default:
                    $this->allnicks();
            }
            
        }

        function printer($ids = FALSE)
        {
           $this->allstories('showall', $ids);
        }

        function allnicks()
        {
            $this->allStories('allnicks');
        }

        function plan()
        {
            $this->allStories('plan');
        }
        
        function allstories($view = 'showall', $ids = FALSE)
        {
            // Load some stuff
            $this->load->database();
            $this->load->helper('form');

            // Set the orderby

            if ($view == 'plan')
            {
                $data['dir']            = 'ASC';
                $data['orderby']        = 'priorityOrder';
            }
            else {
                $data['dir']            = $this->input->get('dir', TRUE);
                $data['orderby']        = $this->input->get('orderby', TRUE);
            }
            
            $data['dirmod']         = $this->input->get('dirmod', TRUE);
            $data['resourceHours']  = $this->input->get('resourceHours', TRUE);
            $data['peeps']          = $this->input->get('peeps', TRUE);
            $data['hoursaday']      = $this->input->get('hoursaday', TRUE);
            $data['delivery']       = $this->input->get('delivery', TRUE);
            $data['workHours']      = FALSE;
            $data['estOrRem']       = $this->input->get('estOrRem', TRUE);;

            // If we've got a delivery date, workout how many days
            // @todo - workout EXACT weekends.
            if ($data['delivery'])
            {
                $current = time();
                $dateTime = explode("/", $data['delivery']);
                $then = mktime(0,0,0, $dateTime[1], $dateTime[0], $dateTime[2]);
               
                $data['workHours'] = $this->howManyWorkDays($then) //No. of workdays
                                    * $data['peeps'] // Times number of people
                                    * $data['hoursaday']; // Times hours a day
            }


            // If the theme isn't POSTed or GETed
            if (
                    (!$data['theme'] = $this->input->post('themes')) AND
                    (!$data['theme'] = $this->input->get('theme'))
               )
            {
                // Then it's in the segment (unless $ids is set, which is a bit hacky)
                // @todo - Unhack this
                $data['theme']      = (FALSE == $ids ? $this->uri->segment(3) : FALSE);
            }

            // Set the order
            $this->Story_model->setOrderby(
                                    $data['orderby'],
                                    $data['dir']
                                    );
            // Set done mode
            // Should it be checked?
            if ($this->input->post('showDone') == 1)
            {
                $data['showDoneChecked'] = TRUE;
                $this->Story_model->setDone($this->input->post('showDone'));
            }
            else {
                $data['showDoneChecked'] = FALSE;
            }

            // If the theme isn't false
            if (FALSE != $data['theme']) {
                $data['stories'] = $this->Story_model->getAllStories(
                                        $data['theme'],
                                        FALSE
                                    );
            }
            elseif (FALSE != $ids) {
                
                if (is_array($ids))
                {
                    $data['stories'] = $this->Story_model->getManyStories($ids);
                }
                else {
                    $data['stories'] = $this->Story_model->getStory($ids);
                }
                
            }
            else {
                $data['stories'] = $this->Story_model->getAllStories(
                                        FALSE,
                                        FALSE
                                   );
                $data['theme'] = FALSE;
            }

            // Check if the view is a number or not
            // @todo make this not suck.
            if (is_numeric($view)) $view = 'showall';

            // Sort the view
            $data['themes'] = $this->Story_model->getThemeNames();
            $this->load->view('siteHeader');
            $this->load->view($view, $data);
            $this->load->view('siteFooter');
        }
        /**
         * @desc    Return how many non-weekend days between two dates
         * @param   timestamp $toDate
         * @param   timestamp $fromDate
         * @author  Mike Pearce <mike@mikepearce.net>
         * @return  integer
         **/
        function howManyWorkDays($toDate, $fromDate = FALSE)
        {
            // The time RIGHT NOWs
            if (!$fromDate) $fromDate = time();
            $numberOfDays = 0;

            // Is it a timestamp? Maybe.
            if (
                is_int($toDate) AND
                $toDate > $fromDate
            )
            {
                // While the current time is less than the time in the future
                do {
                    // Add 1 day to the current time
                    $fromDate = strtotime("+1 day", $fromDate);

                    // If that day is a WEEKDAY, increment.
                    if (date("N", $fromDate) < 6)
                    {
                        $numberOfDays++;
                    }
                } while($fromDate < $toDate);
            }
            // Number of work days between now and then
            return $numberOfDays;

        }


        function edit()
        {
            $this->load->database();
            $this->load->helper('form');
            $this->load->library('form_validation');


             $config = array(
               array(
                     'field'   => 'asA',
                     'label'   => 'As A...',
                     'rules'   => 'trim|required'
                  ),
               array(
                     'field'   => 'iNeed',
                     'label'   => '... I need ...',
                     'rules'   => 'trim|required'
                  ),
               array(
                     'field'   => 'soThat',
                     'label'   => '... so that.',
                     'rules'   => 'trim|required'
                  ),
                 array(
                     'field'   => 'estimate',
                     'label'   => 'Estimate',
                     'rules'   => 'trim'
                  ),
                array(
                     'field'   => 'nickname',
                     'label'   => 'Nickname',
                     'rules'   => 'trim|required'
                  )
                );
            $this->form_validation->set_rules($config);
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

            if ($this->form_validation->run() == TRUE)
            {

                // Do some monkeying with the acceptance criteria
                $acceptance = explode("\n", $this->input->post('acceptanceCriteria'));
                $ja = json_encode($acceptance);


                // Is this a new theme?
                if ($this->input->post('theme'))
                {
                    // Yes, new theme
                    $themeId = $this->_addNewStoryName($this->input->post('themeName'));
                }
                else {
                    $themeId = $this->input->post('themes');
                }


                $data = array(
                           'themeId'            => $themeId,
                           'asA'                => $this->input->post('asA'),
                           'iNeed'              => $this->input->post('iNeed'),
                           'soThat'             => $this->input->post('soThat'),
                           'acceptanceCriteria' => $ja,
                           'nickname'           => $this->input->post('nickname'),
                           'estimate'           => $this->input->post('estimate'),
                           'date_modified'      => date('Y-m-d H:i:s')
                        );

                $this->db->where('id', $this->input->post('id'));
                $this->db->update('stories', $data);
                $data['message'] = '<div class="message">That story has been updated.</div>';

                $this->_doRemaining(
                            $this->input->post('id'),
                            $this->input->post('remaining')
                        );


            }
            $seg = $this->uri->segment(3);
            $id = ( FALSE !== $seg  ? $seg : $this->input->post('id'));
            $data1['story'] = $this->Story_model->getStory($id);
            $data1['themes'] = $this->Story_model->getThemeNames();

            // load the form etc
            $data1['edit'] = TRUE;
            $data['form'] = $this->load->view('form', $data1, TRUE);
            $this->load->view('siteHeader');
            $this->load->view('edit', $data);
            $this->load->view('siteFooter');
        }

        private function _doRemaining($id, $remaining)
        {
            $data = array('remaining' => $remaining);
            $this->db->where('id', $id);
            $this->db->update('stories', $data);

            // Also, if we've modified the remaining, keep a historial log.
            $historical_json = $this->Story_model->getHistoricalRemaining($id);

            if (!$array = json_decode($historical_json, TRUE))
            {
                $array = array();
            }
            array_push($array, array(date('Y-m-d: H:i:s') => $remaining));

            $json = json_encode($array);
            $this->Story_model->updateHistoricalRemaining($json, $id);
        }

        public function saveremaining()
        {
            $id = $this->input->post('id');
            $remaining = $this->input->post('value');
            $id = substr($id, 4);

            // Do historical and current
            $this->_doRemaining($id, $remaining);

            print $remaining;
        }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
