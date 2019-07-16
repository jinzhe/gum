<?php
class rsa {
    public static function sign($privatekey, $data) {
        $res = openssl_get_privatekey($privatekey);//必须是没有经过pkcs8转换的私钥
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }

    // RSA验证签名
    public static function verify($publickey, $data, $sign) {
        $res = openssl_get_publickey($publickey);
        //调用openssl内置方法验签，返回bool值
        $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);
        return $result;
    }

    //公钥加密
    public static function encode($data, $publickey) {
        $result = "";
        $lines  = str_split($data, 117);
        foreach ($lines as $line) {
            $temp = "";
            openssl_public_encrypt($line, $temp, $publickey); //公钥加密
            $result .= base64_encode($temp);
        }
        return $result;
    }

    //私钥解密
    public static function decode($data, $privatekey) {
        $result = "";
        $data   = base64_decode($data);
        $lines  = str_split($data, 256);
        foreach ($lines as $line) {
            openssl_private_decrypt($line, $temp, $privatekey); //私钥解密
            $result .= $temp;
        }
        return $result;
    }
}
