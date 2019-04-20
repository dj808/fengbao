<?php
namespace app\index\model;

use think\Model;

class User extends Model
{
    // 指定表名,不含前缀
    protected $name = 'user';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    /**
     * 生成一个六位数的邀请码
     * @return bool|string
     */
    public static  function getCodeById(){
        return   $unique_no = substr(base_convert(md5(uniqid(md5(microtime(true)),true)), 16, 10), 0, 6);
    }

    /**
     * 验证手机号码格式
     * @param  $mobile
     * @return   true|false
     */
    public static function isValidaMobile($phone){
        return preg_match('/^1[345789]{1}\d{9}$/' , $phone) ? true : false;
    }
    /**
     * 验证用户密码
     * @param  $mobile
     * @return   true|false
     */
    public static function isValidaPassword($password){
        return strlen($password)>3 &&  strlen($password)<25;
    }

    /**
     * 生存随机验证码
     * @param int $num code的长度
     * @return string
     */
   /* public static function getRandToken($num=12){
        $codeSeeds = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeSeeds .= "abcdefghijklmnopqrstuvwxyz";
        $codeSeeds .= "0123456789";
        $len=strlen($codeSeeds);
        $code="";
        for($i=0;$i<$num;$i++){
            $rand=rand(0,$len-1);
            $code.=$codeSeeds[$rand];
        }
        return $code;
    }*/

    /**
     *  生成密码
     * @param string $password
     * @return string
     */
    public static function getPassword($password){
       return md5($password);
    }

    /**
     * php验证身份证号码是否正确函数
     */
     public static function is_idcard($id)
     {

        $id = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if(!preg_match($regx, $id))
        {
            return FALSE;
        }
        if(15==strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if(!strtotime($dtm_birth))
            {
                return FALSE;
            } else {
                return TRUE;
            }
        }
        else      //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if(!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return FALSE;
            }
            else
            {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ( $i = 0; $i < 17; $i++ )
                {
                    $b = (int) $id{$i};
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id,17, 1))
                {
                    return FALSE;
                }
                else
                {
                    return TRUE;
                }
            }
        }
    }

    /**
     * 将一个字符串部分字符用$re替代隐藏
     * @param string    $string   待处理的字符串
     * @param int       $start    规定在字符串的何处开始，
     *                            正数 - 在字符串的指定位置开始
     *                            负数 - 在从字符串结尾的指定位置开始
     *                            0 - 在字符串中的第一个字符处开始
     * @param int       $length   可选。规定要隐藏的字符串长度。默认是直到字符串的结尾。
     *                            正数 - 从 start 参数所在的位置隐藏
     *                            负数 - 从字符串末端隐藏
     * @param string    $re       替代符
     * @return string   处理后的字符串
     */
    public static  function hidestr($string, $start = 0, $length = 0, $re = '*') {
        if (empty($string)) return false;
        $strarr = array();
        $mb_strlen = mb_strlen($string);

        while ($mb_strlen) {//循环把字符串变为数组
            $strarr[] = mb_substr($string, 0, 1, 'utf8');
            $string = mb_substr($string, 1, $mb_strlen, 'utf8');
            $mb_strlen = mb_strlen($string);
        }
        $strlen = count($strarr);
        $begin  = $start >= 0 ? $start : ($strlen - abs($start));
        $end    = $last   = $strlen - 1;
        if ($length > 0) {
            $end  = $begin + $length - 1;
        } elseif ($length < 0) {
            $end -= abs($length);
        }
        for ($i=$begin; $i<=$end; $i++) {
            $strarr[$i] = $re;
        }
        if ($begin >= $end || $begin >= $last || $end > $last) return false;
        return implode('', $strarr);
    }
    /**
     * 验证银行卡号验证是否正确
     */
    public static function check_bankCard($card_number){
        $arr_no = str_split($card_number);
        $last_n = $arr_no[count($arr_no)-1];
        krsort($arr_no);
        $i = 1;
        $total = 0;
        foreach ($arr_no as $n){
            if($i%2==0){
                $ix = $n*2;
                if($ix>=10){
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                }else{
                    $total += $ix;
                }
            }else{
                $total += $n;
            }
            $i++;
        }
        $total -= $last_n;
        $x = 10 - ($total % 10);
        if($x == $last_n){
            return 'true';
        }else{
            return 'false';
        }
    }
    /**
     * 验证银行卡号
     * @param  string $bankCardNo 银行卡号
     * @return bool             是否合法(true:合法,false:不合法)
     */
    public static function checkBankCard($bankCardNo){
        $strlen = strlen($bankCardNo);
        if($strlen < 15 || $strlen > 19){

            return false;
        }

        if (!preg_match("/^\d{15}$/i",$bankCardNo) && !preg_match("/^\d{16}$/i",$bankCardNo) &&
            !preg_match("/^\d{17}$/i",$bankCardNo) && !preg_match("/^\d{18}$/i",$bankCardNo) &&
            !preg_match("/^\d{19}$/i",$bankCardNo)) {

            return false;
        }

        $arr_no = str_split($bankCardNo);
        $last_n = $arr_no[count($arr_no)-1];
        krsort($arr_no);
        $i = 1;
        $total = 0;
        foreach ($arr_no as $n){
            if($i%2==0){
                $ix = $n*2;
                if($ix>=10){
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                }else{
                    $total += $ix;
                }
            }else{
                $total += $n;
            }
            $i++;
        }
        $total -= $last_n;
        $x = 10 - ($total % 10);
        if($x != $last_n){

            return false;
        }

        return true;
    }



}
