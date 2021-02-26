<?php
namespace Course\DAO;

class CourseDAO {

    private $PDOX;
    private $p;

    private $url;
    private $username;
    private $password;
    private $real_weeks;

    public function __construct($PDOX, $p, $url, $username, $password, $real_weeks) {
        $this->PDOX = $PDOX;
        $this->p = $p;
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->real_weeks = $real_weeks;
    }

    public function getJSON($site_id, $is_csv = false) {
        $data = array('site' => $site_id, 
                        'real_weeks' => $this->real_weeks ? 1 : 0,
                        'active_year' => 2020, # this tool is locked in 2020
                        'username' => $this->username, 
                        'password' => $this->password);
        if ($is_csv) {
            $data['csv'] = '1';
        }
        $options = array(
                'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            )
        );

        $context = stream_context_create($options);
        // return file_get_contents($this->url, false, $context);
        return json_decode( file_get_contents($this->url, false, $context), $is_csv);
    }

    public function getWeek($site_id, $week, $real, $start_date, $end_date) {

        $data = array('site' => $site_id, 
                        'real_weeks' => $this->real_weeks ? 1 : 0,
                        'active_year' => 2020, # this tool is locked in 2020
                        'username' => $this->username, 
                        'password' => $this->password,
                        'week' => $week,
                        'real' => $real,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                    );
        $options = array(
                'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            )
        );

        $context = stream_context_create($options);
        
        return json_decode( file_get_contents($this->url, false, $context), false);
    }
}