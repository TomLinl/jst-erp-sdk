<?php

namespace Jst;

class RpcClient
{
    protected $sandbox;
    protected $partner_id;
    protected $partner_key;
    protected $taobao_appkey;
    protected $taobao_secret;
    protected $debug_mode;
    protected $target_appkey = '23060081';
    private $__url_map = ['jst' => null, 'qm' => null];

    function __construct(
        $sandbox,
        $partner_id,
        $partner_key,
        $token,
        $taobao_appkey = '',
        $taobao_secret = '',
        $debug_mode = false,
        $target_appkey = '23060081'
    ) {
        $this->sandbox = $sandbox;
        $this->partner_id = $partner_id;
        $this->partner_key = $partner_key;
        $this->token = $token;
        $this->taobao_appkey = $taobao_appkey;
        $this->taobao_secret = $taobao_secret;
        $this->debug_mode = $debug_mode;
        $this->target_appkey = $target_appkey;
    }

    /**
     * 测试/正式接口地址
     * @return mixed
     */
    public function get_request_url()
    {
        if ($this->sandbox) {
            $this->__url_map['jst'] = 'http://c.sursung.com/api/open/query.aspx';
        } else {
            $this->__url_map['jst'] = 'http://open.erp321.com/api/open/query.aspx';
        }

        return $this->__url_map['jst'];
    }

    /**
     * 接口调用
     * @param $action
     * @param $parameters
     * @return mixed|null
     */
    public function call($action, $parameters = null)
    {

        if ($parameters == null) {
            $parameters = (object)array();
        }

        $system_params = $this->get_system_params($action, $parameters);
        $request_url = $this->get_request_url();
        $result = $this->post($request_url, $parameters, $system_params, $action);

        return $result;
    }


    public function get_system_params($action, $params, $sys_params = [])
    {
        # 默认系统参数
        $system_params = [
            'partnerid' => $this->partner_id,
            'token' => $this->token,
            'method' => $action,
            'ts' => time()
        ];

        return $this->generate_signature($system_params, $params);
    }

    //计算验签
    public function generate_signature($system_params, $params = null)
    {
        $sign_str = '';
        ksort($system_params);
        $no_exists_array = array('method', 'sign', 'partnerid', 'partnerkey');
        $sign_str = $system_params['method'] . $system_params['partnerid'];
        
        foreach ($system_params as $key => $value) {
            if (in_array($key, $no_exists_array)) {
                continue;
            }
            $sign_str .= $key . strval($value);
        }

        $sign_str .= $this->partner_key;
        if ($this->debug_mode) {
            echo '计算sign源串' . $sign_str;
        }
        $system_params['sign'] = md5($sign_str);

        return $system_params;

    }

    //发送请求
    public function post($url, $data, $url_params, $action)
    {
        $post_data = '';
        try {
            $post_data = json_encode($data);
            $url .= '?' . http_build_query($url_params);
            if ($this->debug_mode) {
                echo $url;
            }
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded'
            ));

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                print curl_error($ch);
            }
            curl_close($ch);
            return json_decode($result, true);
        } catch (\Exception $e) {
            return null;
        }

    }

}
