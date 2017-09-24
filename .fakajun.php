<?php
/**
 * 发卡君API类
 * @link   (fakajun, https://www.fakajun.com)
 * @author lidangao <ilidangao@gmail.com>
 * /
class Fakajun
{
    /**
     * Http请求
     * @param  array $params 请求参数
     * @return json         返回数据
     */
    public static function http($params = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.fakajun.com/gateway.do');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $res = curl_exec($ch);
        return $res;
    }
    /**
     * 签名算法
     * @param  array $params     请求参数
     * @param  string $app_key AppKey
     * @return string             签名字符串
     */
    public static function sign($params, $app_key)
    {
        $para_filter = array();
        while (list ($key, $val) = each ($params)) {
            if($key == "sign" || $key == "sign_type" || $val == "")continue;
            else    $para_filter[$key] = $params[$key];
        }
        ksort($para_filter);
        reset($para_filter);
        $arg  = "";
        while (list ($key, $val) = each ($para_filter)) {
            // 不是数组的时候才会组合，否则传入数组会出错
            if (!is_array($val)) {
                $arg.=$key."=".$val."&";
            }
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);
        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){
            $arg = stripslashes($arg);
        }
        $string = $arg . $app_key;
        // md5签名
        return strtoupper(md5($string));
    }
}
// 以下是实例
/**
$params =   [
    'app_id'        =>  '201709241628581001',
    'method'        =>  'user.withdraw',
    'format'        =>  'json', // 可选
    'charset'       =>  'utf-8', // 可选
    'sign_type'     =>  'md5', // 可选
    'timestamp'     =>  date('Y-m-d H:i:s'),
    'version'       =>  1 // 可选
];
// 获得签名
$sign = Fakajun::sign($params, '$2y$10$8zoxKJuMQF4FXLgZJZ2cIOmZSOn9BTjOiRPcXsK3ziac9yPn/ETNq');
$params['sign'] = $sign;
// 发送请求获得结果
$res = Fakajun::http($params);
echo $res;
**/
