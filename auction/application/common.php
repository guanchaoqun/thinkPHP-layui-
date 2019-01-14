<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 打印函数
 * @param $var 打印内容
 */
function p($var){
    if($var===false){
        var_dump($var);
    }else{
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}

/**
 * 邀请码
 * @param $user_id
 * @return string
 */

function createCode($user_id) {
    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
    $num = $user_id;
    $code = '';
    while ( $num > 0) {
        $mod = $num % 35;
        $num = ($num - $mod) / 35;
        $code = $source_string[$mod].$code;
    }
    if(empty($code[3]))
        $code = str_pad($code,4,'0',STR_PAD_LEFT);
    return $code;
}

/**
 * 解码函数
 * @param $code
 * @return bool|int
 */
function decode($code) {
    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
    if (strrpos($code, '0') !== false)
        $code = substr($code, strrpos($code, '0')+1);
    $len = strlen($code);
    $code = strrev($code);
    $num = 0;
    for ($i=0; $i < $len; $i++) {
        $num += strpos($source_string, $code[$i]) * pow(35, $i);
    }
    return $num;
}

/**
 *  API返回信息格式函数 ；失败：code=110，成功：code=200
 * @param string $code
 * @param string $message
 * @param array $data
 */
function apiResponse($code = '110', $message = '',$data = array()){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type:application/json; charset=utf-8');
    $result = array(
        'code'=>$code,
        'message'=>$message,
        'data'=>$data,
        // 'nums'=>''.$nums
    );
    die(json_encode($result,JSON_UNESCAPED_UNICODE));
}

/**
 *
 * 根据id获取任意字段值
 * @return [string] [状态]
 */
function getName($model='',$field='',$id=0){
    if($id && $model && $field){
        return db($model)->where("id={$id}")->value($field).'';
    }else{
        return '';
    }
}
/**
 *
 * 根据id获取任意字段值
 * @return [string] [状态]
 */
function getAddress($model='',$field='',$id=0){
    if($id && $model && $field){
        return db($model)->where("areaid={$id}")->value($field).'';
    }else{
        return '';
    }
}
/**
 * 获取','隔开的数据的第一个元素
 * @param $string ','隔开字符串
 * @return json
 */
function getFirst($string){
    $string = explode(',',$string);
    return reset($string);
}
/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param string $sortby 排序类型 （asc正向排序 desc逆向排序 nat自然排序）
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list))
    {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
        {
            $refer[$i] = &$data[$field];
        }
        switch ($sortby)
        {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
        {
            $resultSet[] = &$list[$key];
        }
        return $resultSet;
    }
    return false;
}

/**
 * 验证码
 * @param int $flag 0数字字符混合 1字符 2数字
 * @param int $num 验证标识的个数
 * @return string
 */
function get_vc($num = 0, $flag = 0) {

    /**获取验证标识**/
    $arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',1,2,3,4,5,6,7,8,9,0);
    $vc  = '';
    switch($flag) {
        case 0 : $s = 0;  $e = 61; break;
        case 1 : $s = 0;  $e = 51; break;
        case 2 : $s = 52; $e = 61; break;
    }

    for($i = 0; $i < $num; $i++) {
        $index = rand($s, $e);
        $vc   .= $arr[$index];
    }
    return $vc;
}

function wxinit(){
    $wx = new \weixin\Wx();
    $wx->AppID=AppID;
    $wx->AppSecret=AppSecret;
    $wx->Token=Token;
    $wx->access_token=access_token();
    return $wx;
}
/**
 * curl请求
 */
function http_curl($url, $type = 'get', $res = 'json', $arr = ''){
    $cl = curl_init();
    curl_setopt($cl, CURLOPT_URL, $url);
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
    if($type == 'post'){
        curl_setopt($cl, CURLOPT_POST, 1);
        curl_setopt($cl, CURLOPT_POSTFIELDS, $arr);
    }
    $output = curl_exec($cl);
    curl_close($cl);
    return json_decode($output, true);
     if($res == 'json'){
         if( curl_error($cl)){
             return curl_error($cl);
         }else{
             return json_decode($output, true);
         }
     }
}

/**
 * 获取access_token
 * @return access_token
 */
function access_token(){
    $access_token = db('access_token')->whereTime('update_time','-2 hours')->value('access_token');
    if($access_token){
        return $access_token;
    }else{
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".AppID."&secret=".AppSecret;
        $access_token =  http_curl($url,'get');
        $save['update_time'] = time();
        $save['access_token'] = $access_token['access_token'];
        db('access_token')->where('id',1)->update($save);
        return $access_token;
    }
}

//获取用户基本信息
function getUser($data){
    $access_token = access_token();
    $openid=$data['FromUserName'];
    $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
    $res=http_curl($url,'get');
    return $res;
}

//订单状态 0-未支付  1--待进场 2--待离场 3--待评价 4--已完成 5--已取消 6--已支付
function orders_state($orders_state){
    switch ($orders_state){
        case 0:
            return "未支付";
        break;
        case 1:
            return "待进场";
            break;
        case 2:
            return "待离场";
            break;
        case 3:
            return "待评价";
            break;
        case 4:
            return "已完成";
            break;
        case 5:
            return "已取消";
            break;
        case 6:
            return "已支付";
            break;
        default:
            return '未定义';

    }
}

/**
 * 球童状态 1--未审核 2--审核通过 3--未通过 8--注销 9--禁用
 * @param string
 * @return json
 */
function caddie_state($caddie_state){
    switch ($caddie_state){
        case 1:
            return "未审核";
            break;
        case 2:
            return "审核通过";
            break;
        case 3:
            return "未通过";
            break;
        case 8:
            return "注销";
            break;
        case 9:
            return "禁用";
            break;
        case 6:
            return "已支付";
            break;
        default:
            return '未定义';

    }
}
/**
 * 文件下载
 * @param string
 * @return json
 */
function download($path,$name){
    //1.获取要下载图片的文件名和路径
    $file = $path.$name;
    //2.重设响应类型var_dump(getimagesize($file));exit;
    $info = getimagesize($file);
    header("content-type:".$info['mime']);
    //3.执行下载的文件名，设定配置
    header("content-disposition:attachment;filename=".$name);
    //4.指定文件的大小
    header("content-length:".filesize($file));
    //5.读取文件内容 或者 readfile($file);
    echo file_get_contents($file);
}

// 1. 生成原始的二维码(生成图片文件)
function scerweima($value='',$name=''){

    $errorCorrectionLevel = 'L';    //容错级别
    $matrixPointSize = 5;           //生成图片大小
    $dir = './usersqcode/'.date('Y-m-d',time());
    if(!is_dir($dir)){
        mkdir(iconv('UTF-8','GBK',$dir),0777,true);
    }
    //生成二维码图片
    $filename = './usersqcode/'.date('Y-m-d',time()).'/'.$name;
    $object = new \expand\QRcode();
    $object->png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2);
    return $filename;
}

/**
 * 将xml转为array
 * @param $xml
 * @return mixed
 */
function xmlToArray($xml){
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $array_data;
}
/**

 * excel表格导出

 * @param string $fileName 文件名称

 * @param array $headArr 表头名称

 * @param array $data 要导出的数据

 * @author static7

 */

function excelExport($fileName = '', $headArr = [], $data = []) {
    import('phpexcel.PHPExcel', EXTEND_PATH);
    $fileName .= "_" . date("Y_m_d") . ".xls";

    $objPHPExcel = new \PHPExcel();

    $objPHPExcel->getProperties();

    $key = ord("A"); // 设置表头

    foreach ($headArr as $v) {

        $colum = chr($key);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);

        $key += 1;

    }

    $column = 2;

    $objActSheet = $objPHPExcel->getActiveSheet();

    foreach ($data as $key => $rows) { // 行写入
        $span = ord("A");
        foreach ($rows as $keyName => $value) { // 列写入
            $objActSheet->setCellValue(chr($span) . $column, $value);
            $span++;
        }
        $column++;
    }

    $fileName = iconv("utf-8", "gb2312", $fileName); // 重命名表

    $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表

    header('Content-Type: application/vnd.ms-excel');

    header("Content-Disposition: attachment;filename='$fileName'");

    header('Cache-Control: max-age=0');

    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

    $objWriter->save('php://output'); // 文件通过浏览器下载

    exit();

}

/**
 * 验证手机号是否正确
 * @author honfei
 * @param number $mobile
 */
function isMobile($mobile) {

    if (!is_numeric($mobile)) {

        return false;
    }
    return preg_match('/^(1[34578]|20)\d{9}$/', $mobile) ? true : false;
}

/**
 * 微信地图
 * @param string
 * @return json
 */
function mapConfig($parameter){
    if(!cache('?jsapi_ticket')){
        $jsapi_ticket = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.access_token().'&type=jsapi'),true);
        cache('jsapi_ticket',$jsapi_ticket['ticket'],7000);
    }else{
        $jsapi_ticket['ticket'] =  cache('jsapi_ticket');
    }

    $config['time'] = time();
    $config['openid'] = config('weixin_golf.AppID');
    $config['noncestr'] = 'DRTYUIJHJKhhjj';
    $config['signature'] =  sha1('jsapi_ticket='.$jsapi_ticket['ticket'].'&noncestr='.$config['noncestr'].'&timestamp='.$config['time'].'&url=http://'.$_SERVER['HTTP_HOST'].'/'.$parameter);
    return $config;
}
