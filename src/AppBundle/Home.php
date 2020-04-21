<?php

namespace AppBundle;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \Tsugi\Core\Settings;
use \Tsugi\Util\Net;
use DateTime;

class Home {

    public function getPage(Request $request, Application $app) {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;

        $EID = $app['tsugi']->context->launch->ltiRawParameter('lis_person_sourcedid', $app['tsugi']->user->id);
        
        $context = array();
        $context['styles']  = [ addSession('static/tooltipster.bundle.min.css'), addSession('static/user.css') ];
        $context['scripts'] = [ addSession($CFG->staticroot .'/js/moment.min.js'), addSession('static/tooltipster.bundle.min.js') ];
        $context['loader_svg'] = addSession('static/grid.svg');
	    $context['getUrl'] = addSession('info');
        
        $rows = $PDOX->allRowsDie("SELECT answer, updated FROM {$p}Orientation_Questions
                    WHERE EID = :EID and user_id = :user_id",
                    array(':user_id' => $app['tsugi']->user->id, ':EID' => $EID)
                );
        if (count($rows) > 0) {
            $context['selected'] = $rows[0];
        } else {
            $context['selected'] = array('answer' => -1, 'updated' => '');
        }
                
        $context['config'] = $app['config'];

        $context['result'] = array( 
            'ext_sakai_server' => $app['tsugi']->context->launch->ltiRawParameter('ext_sakai_server','none')
            ,'ext_sakai_serverid' => $app['tsugi']->context->launch->ltiRawParameter('ext_sakai_serverid','none') 
            ,'instructor' => $app['tsugi']->user->instructor
            ,'siteid' => $app['tsugi']->context->launch->ltiRawParameter('context_id','none')
            ,'ownerEid' => $app['tsugi']->context->launch->ltiRawParameter('lis_person_sourcedid','none') 
            ,'ownerEmail' => $app['tsugi']->user->email
            ,'organizer' => $app['tsugi']->user->displayname
            ,'language' => 'eng'
            ,'title' => $app['tsugi']->context->title
            ,'description' => $app['tsugi']->context->title
            ,'publisher' => 'University of Cape Town'
            ,'done' => 0
            ,'msg'  => 'Application failure.'
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

    public function getInfo(Request $request, Application $app) {
        $result = json_decode( file_get_contents('https://srvslscet001.uct.ac.za/request/?site='. $app['tsugi']->context->launch->ltiRawParameter('context_id','none')));

        // Testing sites: 
        // $result = json_decode( file_get_contents('https://srvslscet001.uct.ac.za/request/?site=2dda0bd3-9100-4034-a404-ff0e34b1887c')); // MAM1000W (2020)
        // $result = json_decode( file_get_contents('https://srvslscet001.uct.ac.za/request/?site=1f718456-7261-43b4-8e40-1dfbf8bdce23')); // PACA Orientation
        // $result = json_decode( file_get_contents('https://srvslscet001.uct.ac.za/request/?site=4f6abcc6-84f1-4c5c-9df2-08712ea669df')); // CILT LT Team - Dev

        return new Response(json_encode($result), 200, ['Content-Type' => 'application/json']);
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
