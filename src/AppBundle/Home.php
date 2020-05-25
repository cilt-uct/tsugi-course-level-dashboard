<?php

namespace AppBundle;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \Tsugi\Core\Settings;
use \Tsugi\Util\Net;
use DateTime;
use DateTimeZone;

class Home {

    public function getPage(Request $request, Application $app) {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;
        
        $context = array();
        $context['styles']  = [ addSession('static/tooltipster.bundle.min.css'), addSession('static/user.css') ];
        $context['scripts'] = [ addSession($CFG->staticroot .'/js/moment.min.js'), addSession('static/tooltipster.bundle.min.js') ];
        $context['loader_svg'] = addSession('static/grid.svg');
        $context['getUrl'] = addSession('info');

        $provider = $app['tsugi']->context->launch->ltiRawParameter('lis_course_offering_sourcedid','none') != $app['tsugi']->context->launch->ltiRawParameter('context_id','');
        $provider_st = $app['tsugi']->context->launch->ltiRawParameter('lis_course_offering_sourcedid','none');

        $has_provider = ($provider && (preg_match_all('/[A-Z]{3}[\d]{4}[A-Z]?,20[\d]{2}/i', $provider_st, $matches) > 0));
        $force_download = $app['tsugi']->context->launch->ltiRawParameter('custom_download','none') == "enable";
        // $force_download = true; // test

        if ($force_download) {
            $context['downloadUrl'] = 'download';
        } else {
            $context['downloadUrl'] = ($provider && (preg_match_all('/[A-Z]{3}[\d]{4}[A-Z]?,20[\d]{2}/i', $provider_st, $matches) > 0)) ? 'download' : '';
        }

        $context['config'] = $app['config'];
        $context['result'] = array( 
            'siteid' => $app['tsugi']->context->launch->ltiRawParameter('context_id','none')
            ,'download' => $force_download ? 1 : 0
            ,'has_provider' => $has_provider ? 1 : 0
        );

        return $app['twig']->render('Home.twig', $context);
    }

    private function getValues($title, $total, $arr) {

        $result = array();
        $cloned = array_replace([], $arr);
        $max = $total;

        for ($x = 0; $x < count($cloned); $x ++) {
        
            $arr[$x]->v = rand(0, $max);
            $max -= $arr[$x]->v;
            $max = $max > 0 ? $max : 0;
        }
        $cloned[count($cloned)-1]->v = $max;

        return array("title" => $title, "results" => $cloned);
    }

    public function getJSON(Application $app, $site_id, $is_csv = false) {

        $real_weeks = isset($app['config']['real_weeks']) ? ($app['config']['real_weeks'] == '1') : false;
        $real_weeks = ($app['tsugi']->context->launch->ltiRawParameter('custom_real_week_no','false') == "true") | $real_weeks;
        $data = array('site' => $site_id, 
                        'real_weeks' => $real_weeks ? 1 : 0,
                        'username' => $app['config']['username'], 
                        'password' => $app['config']['password']);
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
        return json_decode( file_get_contents($app['config']['url'], false, $context), $is_csv);
    }

    public function getInfo(Request $request, Application $app) {
        $result = $this->getJSON($app, $app['tsugi']->context->launch->ltiRawParameter('context_id','none'));

        // Testing: 
        // $result = $this->getJSON($app, '2dda0bd3-9100-4034-a404-ff0e34b1887c'); // MAM1000W (2020)
        // $result = $this->getJSON($app, '1f718456-7261-43b4-8e40-1dfbf8bdce23'); // PACA Orientation
        // $result = $this->getJSON($app, '4f6abcc6-84f1-4c5c-9df2-08712ea669df'); // CILT LT Team - Dev
        // $result = $this->getJSON($app, '996b25c5-9d5f-4dba-9c7a-507e4862c578'); // loadtest 2012
        // $result = $this->getJSON($app, 'a30edd67-9678-45cd-92de-7559c7e6a944'); // Sociology Courses
        // $result = $this->getJSON($app, 'ac4e7899-7600-4516-a6c2-702513cb0230'); // ISFAP Students

        return new Response(json_encode($result), 200, ['Content-Type' => 'application/json']);
    }

    public function getCSV(Request $request, Application $app) {
        
        $data = $this->getJSON($app, $app['tsugi']->context->launch->ltiRawParameter('context_id','none'), true);

        // Testing: 
        // $data = $this->getJSON($app, '2dda0bd3-9100-4034-a404-ff0e34b1887c', true); // MAM1000W (2020)
        // $data = $this->getJSON($app, '996b25c5-9d5f-4dba-9c7a-507e4862c578', true); // loadtest 2012
        // $data = $this->getJSON($app, 'ac4e7899-7600-4516-a6c2-702513cb0230', true); // ISFAP Students

        if ($data['success'] == 1) {
            $now = new DateTime();
            $now->setTimezone(new DateTimeZone('Africa/Johannesburg'));

            // Generate response
            $response = new Response();

            // Set headers
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Content-Disposition', 
                    'attachment; filename="Analytics '. $data['title'] . ' '. $now->format('Y-m-d_H-i'). '.csv"');
            //$response->headers->set('Content-length', length($this->outputCSV($data['result'])));

            // Send headers before outputting anything
            $response->sendHeaders();

            $response->setContent($this->outputCSV($data['result']));
            return $response;
        } else {
            
            $context = array();
            $context['styles']  = [ addSession('static/user.css') ];

            return $app['twig']->render('Error.twig', $context);
        }
    }

    public function outputCSV($data, $useKeysForHeaderRow = true) {
        if ($useKeysForHeaderRow) {
            array_unshift($data, array_keys(reset($data)));
        }
    
        $outputBuffer = fopen("php://output", 'w');
        foreach($data as $v) {
            fputcsv($outputBuffer, $v);
        }
        fclose($outputBuffer);
    }

    public function getFile(Request $request, Application $app, $file = '') {
        if (empty($file)) {
            $app->abort(400);
        }

        switch (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
            case 'css' : {
                $contentType = 'text/css';
                break;
            }
            case 'js' : {
                $contentType = 'application/javascript';
                break;
            }
            case 'xml' : {
                $contentType = 'text/xml';
                break;
            }
            case 'svg' : {
                $contentType = 'image/svg+xml';
                break;
            }
            default : {
                $contentType = 'text/plain';
            }
        }

        return new Response( file_get_contents( __DIR__ ."/../../static/". $file), 200, [ 
            'Content-Type' => $contentType
        ]);
    }
}
