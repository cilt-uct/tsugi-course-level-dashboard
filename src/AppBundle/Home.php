<?php

namespace AppBundle;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \Tsugi\Core\Settings;
use \Tsugi\Util\Net;

class Home {

    public function get(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;

        $EID = $app['tsugi']->context->launch->ltiRawParameter('lis_person_sourcedid', $app['tsugi']->user->id);
        
        $context = array();
        $context['style'] = str_replace("\\","/",$CFG->getCurrentFileUrl('static/user.css')) .'?t='. time();
        $context['tooltip_css'] = str_replace("\\","/",$CFG->getCurrentFileUrl('static/tooltipster.bundle.min.css'));
        $context['tooltip_js'] = str_replace("\\","/",$CFG->getCurrentFileUrl('static/tooltipster.bundle.min.js'));

	    $context['submit'] = addSession(str_replace("\\","/",$CFG->getCurrentFileUrl('index.php')));
        
        $rows = $PDOX->allRowsDie("SELECT answer, updated FROM {$p}Orientation_Questions
                    WHERE EID = :EID and user_id = :user_id",
                    array(':user_id' => $app['tsugi']->user->id, ':EID' => $EID)
                );
        if (count($rows) > 0) {
            $context['selected'] = $rows[0];
        } else {
            $context['selected'] = array('answer' => -1, 'updated' => '');
        }
                
        $context['path'] = $CFG->staticroot;
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
        ));

        return $app['twig']->render('Home.twig', $context);
    }

    public function post(Request $request, Application $app) {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;

        $EID = $app['tsugi']->context->launch->ltiRawParameter('lis_person_sourcedid', $app['tsugi']->user->id);
        $PDOX->queryDie("INSERT INTO {$p}Orientation_Questions
            (link_id, user_id, ipaddr, EID, answer, updated)
            VALUES ( :LI, :UI, :IP, :EID, :answer, NOW() )
            ON DUPLICATE KEY UPDATE updated = NOW(), answer = :answer",
            array(
                ':LI' => $app['tsugi']->link->id,
                ':UI' => $app['tsugi']->user->id,
                ':IP' => Net::getIP(),
                'EID' => $app['tsugi']->context->launch->ltiRawParameter('lis_person_sourcedid','none'),
                ':answer' => $_POST['val']
            )
        );
        // $app->tsugiFlashSuccess(__('Attendance Recorded...'));
        return json_encode(['done' => 1, 'answer' => $_POST['val']]);
    }
}
