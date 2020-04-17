<?php

namespace AppBundle;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \Tsugi\Core\Settings;
use \Tsugi\Util\Net;

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

    public function getInfo(Request $request, Application $app) {

        $result = '[]';
        if ($app['config']["production"]) {
            $result = '[
                {"title": "Access Survey", "results": [{"t": "", "c": "green", "v": 6},{"t": "", "c": "orange", "v": 6}, {"t": "", "c": "red", "v": 6}]},
                {"title": "Orientation", "results":  [{"t": "", "c": "green", "v": 6},{"t": "", "c": "orange", "v": 6}, {"t": "", "c": "red", "v": 6}]},
                {"title": "Poll", "results":  [{"t": "", "c": "green", "v": 6},{"t": "", "c": "orange", "v": 6}, {"t": "", "c": "gray", "v": 6}]}
            ]';    
        }
        // {"production":"","url":"https:\/\/api.server.com\/v.1.0\/","username":"username","password":"password"}

        return new Response($result, 200, ['Content-Type' => 'application/json']);
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
