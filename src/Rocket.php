<?php
/**
 * Created by PhpStorm.
 * User: bahaaodeh
 * Date: 6/19/18
 * Time: 3:39 PM
 */

namespace Baha2Odeh\RocketChat;


use yii\base\Component;
use Httpful\Request;

class Rocket extends Component
{
    public $rocket_chat_instance;
    public $rest_api_root;
    protected $api;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        if(empty($this->rocket_chat_instance)){
            throw new \Exception('Required rocket chat instance');
        }
        if(empty($this->rest_api_root)){
            throw new \Exception('Required rest api root');
        }
        $this->api = ROCKET_CHAT_INSTANCE . REST_API_ROOT;

        // set template request to send and expect JSON
        $tmp = Request::init()
            ->sendsJson()
            ->expectsJson();
        Request::ini( $tmp );
    }


    /**
     * Get version information. This simple method requires no authentication.
     */
    public function version() {
        $response = \Httpful\Request::get( $this->api . 'info' )->send();
        return $response->body->info->version;
    }

    /**
     * Quick information about the authenticated user.
     */
    public function me() {
        $response = Request::get( $this->api . 'me' )->send();

        if( $response->body->status != 'error' ) {
            if( isset($response->body->success) && $response->body->success == true ) {
                return $response->body;
            }
        } else {
            echo( $response->body->message . "\n" );
            return false;
        }
    }

    /**
     * List all of the users and their information.
     *
     * Gets all of the users in the system and their information, the result is
     * only limited to what the callee has access to view.
     */
    public function list_users(){
        $response = Request::get( $this->api . 'users.list' )->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            return $response->body->users;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    /**
     * List the private groups the caller is part of.
     */
    public function list_groups() {
        $response = Request::get( $this->api . 'groups.list' )->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            $groups = array();
            foreach($response->body->groups as $group){
                $groups[] = new Group($group);
            }
            return $groups;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }

    /**
     * List the channels the caller has access to.
     */
    public function list_channels() {
        $response = Request::get( $this->api . 'channels.list' )->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            $groups = array();
            foreach($response->body->channels as $group){
                $groups[] = new Channel($group);
            }
            return $groups;
        } else {
            echo( $response->body->error . "\n" );
            return false;
        }
    }


    /**
     * @param $username
     * @param $password
     * @param array $fields
     * @return \Baha2Odeh\RocketChat\User
     */
    public function user($username, $password, $fields = array()){
        return new User($this->api,$username, $password, $fields);
    }

    /**
     * @param $name
     * @param array $members
     * @return \Baha2Odeh\RocketChat\Channel
     */
    public function channel($name, $members = array()){
        return new Channel($this->api,$name, $members);
    }

    /**
     * @param $name
     * @param array $members
     * @return \Baha2Odeh\RocketChat\Group
     */
    public function group($name, $members = array()){
        return new Group($this->api,$name, $members);
    }

}