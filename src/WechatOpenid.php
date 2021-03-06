<?php

namespace FykEasyChat;

/**
 * Class WechatOpenid
 * @author fyk
 * @package FykWechat
 */
class WechatOpenid extends Common
{
    protected $app_id;
    protected $secret;
    protected $web_url;
    protected $snsapi;

    public function __construct($config){

        $this->app_id = $config['app_id'];
        $this->secret = $config['secret'];
        $this->web_url = $config['web_url']??"";
        $this->snsapi = $config['snsapi']??'snsapi_userinfo';

    }

    /**
     * code
     */
    public function getAccessCode(){

        $link = $this->web_url;
        $redirect_uri = urlencode($link);
        //WeChat appId
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->app_id.'&redirect_uri='.$redirect_uri.'&response_type=code&scope='.$this->snsapi.'&state=STATE#wechat_redirect';
        header('Location:'.$url);
    }

    /**
     * Personal information
     * @param $code
     * @return mixed
     */
    public function getInformation($code){

        //access_token
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->app_id.'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';

        try {
            $data = $this->curlGet($url);
            if(empty($data['access_token'])){
                throw new \Exception('access_token error');
            }
            if(empty($data['openid'])){
                throw new \Exception('openid error');
            }
            //openid
            $token = $data['access_token'];
            $openid = $data['openid'];
            $link = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$token.'&openid='.$openid.'&lang=zh_CN';
            //information
            $res = json_encode($this->curlGet($link));

            return json_decode($res,true);
        }catch (\Exception $e) {
            return $e->getMessage();
        }

    }

}