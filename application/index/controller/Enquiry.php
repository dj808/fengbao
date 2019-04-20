<?php
/**
 * tpAdmin [a web admin based ThinkPHP5]
 *
 * @author yuan1994 <tianpian0805@gmail.com>
 * @link http://tpadmin.yuan1994.com/
 * @copyright 2016 yuan1994 all rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

//------------------------
//询价查询控制器
//-------------------------

namespace app\index\controller;
header("content-type:text/html;charset=utf-8");
use think\Db;
use think\Exception;
use think\Request;
use think\Session;

const  agent = '';
const  mikey = '';
class Enquiry extends Base
{
    use \traits\controller\Jump;
    /**
     * 询价首页
     */

    public function one()
    {
        $openid = session::get('useropenid');
        if(!$openid){
          return $this->redirect('login/index');
        }
        $data=Db::name('carpai')->field('pid,tbd,province')->distinct(true)->select();
        $province=Db::name('carpai')->field('province')->distinct(true)->select();
        foreach ($province as $k=>$v){
            foreach ($data as $k1=>$v1){
                if($v1['province']==$v['province']){
                    $province[$k]['son'][]=$v1;
                }
            }
        }

//        echo "<pre>";
//        var_dump( $province);die;
        if($this->request->isAjax()){
            $car_id = strtoupper($this->request->param('car_id'));
            $car_gs = $this->request->param('car_gs');
            $tbd = $this->request->param('tbd');
            //  var_dump($car_id, $car_gs,$tbd);die;
            $identitycard = $this->request->Post('identitycard');
         //   $CustKey = "qiqi123456";


          //  $openid = "o59tW0hnIWqtk7tmn_fM5GqqbPVY";
//            $CustKey = $openid;
            $CustKey = "qiqi123456";
            $a="LicenseNo=".$car_gs.$car_id."&Group=1&CityCode=".$tbd."&ShowXiuLiChangType=1&CustKey=".$CustKey."&Agent=".agent.mikey;
            $SecCod = md5($a);
//            echo "<pre>";
//           var_dump(md5($a));
            $bm_zw= urlencode($car_gs);
            $url ="http://iu.91bihu.com/api/CarInsurance/getreinfo?LicenseNo=".$bm_zw.$car_id."&Group=1&CityCode=".$tbd."&ShowXiuLiChangType=1&CustKey=".$CustKey."&Agent=".agent."&SecCode=".$SecCod;
            //  var_dump($url);die;
            $data=$this->http_get($url);
            // var_dump($data);die;
//                $zz=explode('path=/',$data);
//                $info = json_decode($zz['1'],true);
            $info = json_decode($data,true);

            if(is_array($info)){
             //   if($info['BusinessStatus']=='1'){
                    $info['tbd']=$tbd;
                    session::set('qiqi',$info);
                    // var_dump(444);
                    return   $this->ok('0',$info['StatusMessage']);
//                }else{
//                    return   $this->ok('5',$info['StatusMessage']);
//                }
               // if($info['StatusMessage']=="续保成功"){

            }else{
                return $this->no('1',$info);
            }

        }
        $this->view->assign('data',$province);
        return $this->view->fetch();
    }

    /**
     * 询价下一步
     */
    public function two()
    {
        $a= session::get('qiqi');
//var_dump($a);die;

        if($this->request->isAjax()){
            $car_id = strtoupper($this->request->param('car_id'));
            $car_gs = $this->request->param('car_gs');
            $tbd = $this->request->param('tbd');
       //  var_dump($car_id, $car_gs,$tbd);die;
            $identitycard = $this->request->Post('identitycard');

             $openid = session::get('useropenid');
          //  $openid = "o59tW0hnIWqtk7tmn_fM5GqqbPVY";
          //  $CustKey = $openid;
           $CustKey = "";
            $b="LicenseNo=".$car_gs.$car_id."&Group=1&CityCode=".$tbd."&ShowXiuLiChangType=1&CustKey=".$CustKey."&Agent=".agent.mikey;
            $SecCod = md5($b);
//            echo "<pre>";
//           var_dump(md5($a));
            $bm_zw= urlencode($car_gs);
            $url ="http://******/api/CarInsurance/getreinfo?LicenseNo=".$bm_zw.$car_id."&Group=1&CityCode=".$tbd."&ShowXiuLiChangType=1&CustKey=".$CustKey."&Agent=".agent."&SecCode=".$SecCod;
       //  var_dump($url);die;
            $data=$this->http_get($url);
       // var_dump($data);die;
//                $zz=explode('path=/',$data);
//                $info = json_decode($zz['1'],true);
            $info = json_decode($data,true);
//             echo "<pre>";
//       var_dump($info);die;
            if(is_array($info)){
                if($info['StatusMessage']=="续保成功"){
                    return   $this->ok('0','请求成功！',$info);
                }else{
                    $info['UserInfo']['LicenseNo']=$car_gs . $car_id;
                    return   $this->no('5',$info['StatusMessage'],$info);
                }
            }else{
                return $this->no('1',$info);
            }

     }
//        $car_id = strtoupper($this->request->param('car_id'));
//        $car_gs = $this->request->param('car_gs');
//        $tbd = $this->request->param('tbd');
//        $this->view->assign('car_id',$car_id);
//        $this->view->assign('car_gs',$car_gs);
//        $this->view->assign('tbd',$tbd);
       /// var_dump($a);die;
        $qq=    Db::name('car')->where('car_no',$a['UserInfo']['LicenseNo'])->find();
     //   var_dump($qq);die;
        if(!$qq){
            $sb['sign_time']   =$a['UserInfo']['RegisterDate']; //注册日期
            $sb['brand_no']   =$a['UserInfo']['ModleName'];  //品牌型号
            $sb['frame_no']   =$a['UserInfo']['CarVin'];  //发动机号
            $sb['engine_no']   =$a['UserInfo']['EngineNo'];  //发动机号
            $sb['car_no']   =$a['UserInfo']['LicenseNo'];  //车牌号
            $sb['seat_num']   =$a['UserInfo']['SeatCount'];  //座位数
            $sb['car_name']   =$a['UserInfo']['LicenseOwner'];  //车主姓名
            $sb['id_cards']   =$a['UserInfo']['CredentislasNum'];  //身份证号码
            $sb['IdType']   =$a['UserInfo']['IdType'];  //证件类型
            $sb['create_time']   =time();

            Db::name('car')->insert($sb);
        }




        $this->view->assign('info',$a);
        return $this->view->fetch();
    }

    //二级联动
    public function getRegion(){
        $pid=$this->request->param("pid");
        // $map['type']=$this->request->param("type");
        $list=Db::name("carpai")->where('pid',$pid)->select();

        return $this->ok('0',"成功！",$list);
    }


    /**
     * 询价下下步
     */
    public function three()
    {
        if($this->request->isAjax()){
            $car_info= $this->request->param();

//            echo "<pre>";
//            var_dump($car_info);die;
          //  $tbd=8;
          $CustKey = "qiqi123456";
            $openid = session::get('useropenid');
          //  $openid = "o59tW0hnIWqtk7tmn_fM5GqqbPVY";
          //  $CustKey = $openid;
            // 注：CityCode暂时写死
           $a="LicenseNo=".$car_info['LicenseNo']."&Group=1&CityCode=8&ShowXiuLiChangType=1&CustKey=".$CustKey."&Agent=".agent.mikey;


         //  $a="LicenseNo=苏A5J5F3&QuoteGroup=1&CustKey=qiqi123456&Agent=1243840b5c8b6d8b";
            $SecCode = md5($a);
            //$t =
//            echo "<pre>";
//            var_dump($car_info['InsuredIdType']);die;
            if(array_key_exists('jiaoqiang',$car_info) && array_key_exists('jiaosy',$car_info) ){
                $ForceTax = 1;
            }elseif (array_key_exists('jiaoqiang',$car_info) && !array_key_exists('jiaosy',$car_info)  ){
                $ForceTax = 2;
            }elseif (!array_key_exists('jiaoqiang',$car_info) && array_key_exists('jiaosy',$car_info)  ){
                $ForceTax = 0;
            }else{
                return  $this->no('9','商业险和强险至少选择一个！');
            }

        if($ForceTax ==2){
            $car_info['CheSun']=0;
            $car_info['BuJiMianCheSun']=0;
            $car_info['DaoQiang']=0;
            $car_info['BuJiMianDaoQiang']=0;
            $car_info['ZiRan']=0;
            $car_info['BuJiMianZiRan']=0;
            $car_info['SheShui']=0;
            $car_info['BuJiMianSheShui']=0;
            $car_info['SanZhe']=0;
            $car_info['BuJiMianSanZhe']=0;
            $car_info['BuJiMianChengKe']=0;
            $car_info['ChengKe']=0;
            $car_info['BuJiMianSiJi']=0;
            $car_info['SiJi']=0;
            $car_info['BuJiMianHuaHen']=0;
            $car_info['HuaHen']=0;
            $car_info['BoLi']=0;

            $t = date("Y-m-d",time());
            // var_dump($t,$car_info['ForceTimeStamp'],$car_info['BizTimeStamp']);die;
            if($car_info['ForceTimeStamp']>$t){ //DaoQiang 0
                $car_info['ForceTimeStamp']=strtotime($car_info['ForceTimeStamp']);
            }elseif($car_info['ForceTimeStamp']==$t){
                $car_info['ForceTimeStamp']=time()+7200;
            }else{
                return $this->no('1','交强险起保日期不小于当天！');
            }

            $car_info['BizTimeStamp']=time()+7200;

        }elseif($ForceTax ==0){

                if(array_key_exists('CheSun',$car_info)){
                    $car_info['CheSun']=1;
                }else{
                    $car_info['CheSun']=0;
                }

                if($car_info['CheSun']==1 && array_key_exists('BuJiMianCheSun',$car_info) ){
                    $car_info['BuJiMianCheSun']=1;
                }else{
                    $car_info['BuJiMianCheSun']=0;
                }

                if(array_key_exists('DaoQiang',$car_info)){ //DaoQiang 0
                    $car_info['DaoQiang']=1;
                }else{
                    $car_info['DaoQiang']=0;
                }

                if($car_info['DaoQiang']==1 && array_key_exists('BuJiMianDaoQiang',$car_info)){ //DaoQiang 0
                    $car_info['BuJiMianDaoQiang']=1;
                }else{
                    $car_info['BuJiMianDaoQiang']=0;
                }

                if($car_info['InsuredIdType']==''){
                    $car_info['InsuredIdType']='1';
                }
//ZiRan 0 自燃
                if(array_key_exists('ZiRan',$car_info)){ //DaoQiang 0
                    $car_info['ZiRan']=1;
                }else{
                    $car_info['ZiRan']=0;
                }


                if($car_info['ZiRan']==1 && array_key_exists('BuJiMianZiRan',$car_info) ){ //DaoQiang 0
                    $car_info['BuJiMianZiRan']=1;
                }else{
                    $car_info['BuJiMianZiRan']=0;
                }


//SheShui 0 涉水
                if(array_key_exists('SheShui',$car_info)){ //DaoQiang 0
                    $car_info['SheShui']=1;
                }else{
                    $car_info['SheShui']=0;
                }
                if($car_info['SheShui']==1 && array_key_exists('BuJiMianSheShui',$car_info)){ //DaoQiang 0
                    $car_info['BuJiMianSheShui']=1;
                }else{
                    $car_info['BuJiMianSheShui']=0;
                }

                if($car_info['SanZhe']>0 && array_key_exists('BuJiMianSanZhe',$car_info)){ //DaoQiang 0
                    $car_info['BuJiMianSanZhe']=1;
                }else{
                    $car_info['BuJiMianSanZhe']=0;
                }

                if($car_info['ChengKe']>0  && array_key_exists('BuJiMianChengKe',$car_info)){ //DaoQiang 0
                    $car_info['BuJiMianChengKe']=1;
                }else{
                    $car_info['BuJiMianChengKe']=0;
                }

                if($car_info['SiJi']>0 && array_key_exists('BuJiMianSiJi',$car_info)){ //DaoQiang 0
                    $car_info['BuJiMianSiJi']=1;
                }else{
                    $car_info['BuJiMianSiJi']=0;
                }

                if($car_info['HuaHen']>0 && array_key_exists('BuJiMianHuaHen',$car_info)){ //DaoQiang 0
                    $car_info['BuJiMianHuaHen']=1;
                }else{
                    $car_info['BuJiMianHuaHen']=0;
                }

                $t = date("Y-m-d",time());
                // var_dump($t,$car_info['ForceTimeStamp'],$car_info['BizTimeStamp']);die;
            $car_info['ForceTimeStamp']=time()+7200;//交强险日期

                if($car_info['BizTimeStamp']>$t){ //DaoQiang 0
                    $car_info['BizTimeStamp']=strtotime($car_info['BizTimeStamp']);
                }elseif($car_info['BizTimeStamp']==$t){
                    //var_dump($car_info['BizTimeStamp']);
                    $car_info['BizTimeStamp']=time()+7200;
                    //var_dump($car_info['BizTimeStamp']);die;
                }else{
                    return $this->no('2','商业险起保日期不小于当天！');
                }


            }elseif($ForceTax ==1){
            if(array_key_exists('CheSun',$car_info)){
                $car_info['CheSun']=1;
            }else{
                $car_info['CheSun']=0;
            }

            if($car_info['CheSun']==1 && array_key_exists('BuJiMianCheSun',$car_info) ){
                $car_info['BuJiMianCheSun']=1;
            }else{
                $car_info['BuJiMianCheSun']=0;
            }

            if(array_key_exists('DaoQiang',$car_info)){ //DaoQiang 0
                $car_info['DaoQiang']=1;
            }else{
                $car_info['DaoQiang']=0;
            }

            if($car_info['DaoQiang']==1 && array_key_exists('BuJiMianDaoQiang',$car_info)){ //DaoQiang 0
                $car_info['BuJiMianDaoQiang']=1;
            }else{
                $car_info['BuJiMianDaoQiang']=0;
            }

            if($car_info['InsuredIdType']==''){
                $car_info['InsuredIdType']='1';
            }
//ZiRan 0 自燃
            if(array_key_exists('ZiRan',$car_info)){ //DaoQiang 0
                $car_info['ZiRan']=1;
            }else{
                $car_info['ZiRan']=0;
            }


            if($car_info['ZiRan']==1 && array_key_exists('BuJiMianZiRan',$car_info) ){ //DaoQiang 0
                $car_info['BuJiMianZiRan']=1;
            }else{
                $car_info['BuJiMianZiRan']=0;
            }


//SheShui 0 涉水
            if(array_key_exists('SheShui',$car_info)){ //DaoQiang 0
                $car_info['SheShui']=1;
            }else{
                $car_info['SheShui']=0;
            }
            if($car_info['SheShui']==1 && array_key_exists('BuJiMianSheShui',$car_info)){ //DaoQiang 0
                $car_info['BuJiMianSheShui']=1;
            }else{
                $car_info['BuJiMianSheShui']=0;
            }

            if($car_info['SanZhe']>0 && array_key_exists('BuJiMianSanZhe',$car_info)){ //DaoQiang 0
                $car_info['BuJiMianSanZhe']=1;
            }else{
                $car_info['BuJiMianSanZhe']=0;
            }

            if($car_info['ChengKe']>0  && array_key_exists('BuJiMianChengKe',$car_info)){ //DaoQiang 0
                $car_info['BuJiMianChengKe']=1;
            }else{
                $car_info['BuJiMianChengKe']=0;
            }

            if($car_info['SiJi']>0 && array_key_exists('BuJiMianSiJi',$car_info)){ //DaoQiang 0
                $car_info['BuJiMianSiJi']=1;
            }else{
                $car_info['BuJiMianSiJi']=0;
            }

            if($car_info['HuaHen']>0 && array_key_exists('BuJiMianHuaHen',$car_info)){ //DaoQiang 0
                $car_info['BuJiMianHuaHen']=1;
            }else{
                $car_info['BuJiMianHuaHen']=0;
            }

            $t = date("Y-m-d",time());
            // var_dump($t,$car_info['ForceTimeStamp'],$car_info['BizTimeStamp']);die;
            if($car_info['ForceTimeStamp']>$t){ //DaoQiang 0
                $car_info['ForceTimeStamp']=strtotime($car_info['ForceTimeStamp']);
            }elseif($car_info['ForceTimeStamp']==$t){
                $car_info['ForceTimeStamp']=time()+7200;
            }else{
                return $this->no('1','交强险起保日期不小于当天！');
            }

            if($car_info['BizTimeStamp']>$t){ //DaoQiang 0
                $car_info['BizTimeStamp']=strtotime($car_info['BizTimeStamp']);
            }elseif($car_info['BizTimeStamp']==$t){
                //var_dump($car_info['BizTimeStamp']);
                $car_info['BizTimeStamp']=time()+7200;
                //var_dump($car_info['BizTimeStamp']);die;
            }else{
                return $this->no('2','商业险起保日期不小于当天！');
            }

        }

            $qiqi= session::get('qiqi');

            $cars = array(
                'LicenseNo'=>$car_info['LicenseNo'],
                'CarOwnersName'=>$car_info['CarOwnersName'],
                'IdCard'=>strtoupper($car_info['IdCard']),
                'OwnerIdCardType'=>$car_info['InsuredIdType'],
                'QuoteGroup'=>'5',
                'SubmitGroup'=>'5',
                'InsuredName'=>$car_info['CarOwnersName'],
                'InsuredIdCard'=>strtoupper($car_info['IdCard']),
                'InsuredIdType'=>$car_info['InsuredIdType'],
                'CityCode'=>$qiqi['tbd'],
                'HolderIdCard'=>strtoupper($car_info['IdCard']),
                'HolderName'=>$car_info['CarOwnersName'],
                'HolderIdType'=>$car_info['InsuredIdType'],
                'EngineNo'=>$car_info['EngineNo'],
                'CarVin'=>$car_info['CarVin'],
                'RegisterDate'=>$car_info['RegisterDate'],
                'MoldName'=>$car_info['ModleName'],
                'ForceTax'=>$ForceTax,
                'BoLi'=>$car_info['BoLi'],
                'CheSun'=>$car_info['CheSun'],
                'BuJiMianCheSun'=>$car_info['BuJiMianCheSun'],
                'DaoQiang'=>$car_info['DaoQiang'],
                'BuJiMianDaoQiang'=>$car_info['BuJiMianDaoQiang'],
                'ZiRan'=>$car_info['ZiRan'],
                'BuJiMianZiRan'=>$car_info['BuJiMianZiRan'],
                'SheShui'=>$car_info['SheShui'],
                'BuJiMianSheShui'=>$car_info['BuJiMianSheShui'],
                'SanZhe'=>$car_info['SanZhe'],
                'BuJiMianSanZhe'=>$car_info['BuJiMianSanZhe'],
                'ChengKe'=>$car_info['ChengKe'],
                'BuJiMianChengKe'=>$car_info['BuJiMianChengKe'],
                'SiJi'=>$car_info['SiJi'],
                'BuJiMianSiJi'=>$car_info['BuJiMianSiJi'],
                'HuaHen'=>$car_info['HuaHen'],
                'BuJiMianHuaHen'=>$car_info['BuJiMianHuaHen'],
                'CustKey'=>$CustKey,
                'Agent'=>agent,
                'SecCode'=>$SecCode,
                'QuoteParalelConflict'=>0,
                'BizTimeStamp'=>$car_info['BizTimeStamp'],
                'ForceTimeStamp'=>$car_info['ForceTimeStamp'],
            );
            $qiqi = json_encode($cars);
            $url="http://iu.91bihu.com/api/CarInsurance/PostNewPrecisePrice";
            $data = $this->http_post($url,$qiqi);
            $result=json_decode($data,true);
//            echo "<pre>";
//            var_dump($result);die;
          if($result['StatusMessage']=='请求发送成功'){
            //  session::set('LicenseNo',$cars['LicenseNo']);
          return $this->ok('0',$result['StatusMessage']);
            //  $this->view->assign('LicenseNo',$cars['LicenseNo']);
          }else{
              return $this->no('3',$result['StatusMessage']);
          }
        }
        return $this->view->fetch();
    }
    /**
     * 询价结果
     */
    public function four()
    {
        $data = $this->request->param();
        if(array_key_exists('bg',$data)){
            $data['bg']=1;
        }else{
            $data['bg']=0;
        }
        if(array_key_exists('hb',$data)){
            $data['hb']=1;
        }else{
            $data['hb']=0;
        }

        if(array_key_exists('bg1',$data)){
            $data['bg1']=1;
        }else{
            $data['bg1']=0;
        }

        if(array_key_exists('hb1',$data)){
            $data['hb1']=1;
        }else{
            $data['hb1']=0;
        }
        //var_dump($data);die;
        $info = session::get('qiqi');
        $data['LicenseNo']=$info['UserInfo']['LicenseNo'];
        $data['PostedName']=$info['UserInfo']['PostedName'];
        $data['ModleName']=$info['UserInfo']['ModleName'];
        $data['RegisterDate']=$info['UserInfo']['RegisterDate'];
        $data['ForceExpireDate']=$info['UserInfo']['ForceExpireDate'];
        $data['BusinessExpireDate']=$info['UserInfo']['BusinessExpireDate'];
        $this->view->assign('data',$data);
        return $this->view->fetch();
    }
    //估价
    public function gujia()
    {
        if ($this->request->isPost()) {
            $QuoteGroup = $this->request->post('QuoteGroup');
            $pp = $this->request->post('pp');

           $qiqi = session::get('qiqi');
          // $openid = session::get('openid');

            $data['LicenseNo']=$qiqi['UserInfo']['LicenseNo'];
            //var_dump($QuoteGroup,$SubmitGroup);
           // die;
           // var_dump($data['LicenseNo']);die;
          $openid = session::get('useropenid');
         //  $openid = "o59tW0hnIWqtk7tmn_fM5GqqbPVY";
          //  $CustKey = $openid;
            $CustKey = "qiqi123456";

                    // 注：CityCode暂时写死
                    $a = "LicenseNo=" . $data['LicenseNo'] . "&ShowVehicleInfo=1&ShowTotalRate=1&QuoteGroup=" . $QuoteGroup . "&CustKey=" . $CustKey . "&Agent=" . agent . mikey;
                    //  $a="LicenseNo=苏A5J5F3&QuoteGroup=1&CustKey=qiqi123456&Agent=1243840b5c8b6d8b";
                    $SecCode = md5($a);
                    $url = "http://iu.91bihu.com/api/CarInsurance/GetPrecisePrice?LicenseNo=" . urlencode($data['LicenseNo']) . "&ShowVehicleInfo=1&ShowTotalRate=1&QuoteGroup=" . $QuoteGroup . "&CustKey=" . $CustKey . "&Agent=12438&SecCode=" . $SecCode;
        //  var_dump($url);die;
                       $info = json_decode($this->http_get($url),true);
        //  var_dump($info);die;

                       $info['price']=$info['Item']['BizTotal']+$info['Item']['ForceTotal']+$info['Item']['TaxTotal']; //拼接商业、强险、车船费
                   // $info = json_decode($data1, true);=
//echo "<pre>";
//var_dump($info);die;
          //  $zqi['user_id']='';
            $zqi['LicenseNo']=$qiqi['UserInfo']['LicenseNo'];

            $zqi['all_price']= $info['price'];
            $zqi['RegisterDate']=$qiqi['UserInfo']['RegisterDate']; //初次登记日期
            $zqi['ModleName']=$qiqi['UserInfo']['ModleName']; //品牌型号

            if($QuoteGroup=='1'){
                $zqi['insure_type']= '太平洋保险';
            }elseif($QuoteGroup=='4'){
                $zqi['insure_type']= '中国人保';
            }

             $zqi['ForceStartDate']=$info['UserInfo']['ForceStartDate']; //强险起保时间
             $zqi['ForceExpireDate']=$info['UserInfo']['ForceExpireDate']; //强险结束时间
             $zqi['BusinessExpireDate']=$info['UserInfo']['BusinessExpireDate']; //商业险结束日期
             $zqi['BusinessStartDate']=$info['UserInfo']['BusinessStartDate']; //商业险起保日期\
            $zqi['HolderName']=$info['UserInfo']['HolderName'];
             $zqi['VehicleInfo']=$info['UserInfo']['VehicleInfo']; //车型信息
             $zqi['BizTotal']=$info['Item']['BizTotal']; //商业险总额
             $zqi['ForceTotal']=$info['Item']['ForceTotal']; //交强险总额
             $zqi['TaxTotal']=$info['Item']['TaxTotal']; //车船税总额
             $zqi['TotalRate']=$info['Item']['TotalRate']; //折扣系数
             $zqi['CheSun_BaoFei']=$info['Item']['CheSun']['BaoFei']; //车损保费
             $zqi['SanZhe_BaoFei']=$info['Item']['SanZhe']['BaoFei']; //三者保费
             $zqi['DaoQiang_BaoFei']=$info['Item']['DaoQiang']['BaoFei']; //盗抢保费
             $zqi['SiJi_BaoFei']=$info['Item']['SiJi']['BaoFei']; //司机保费
             $zqi['ChengKe_BaoFei']=$info['Item']['ChengKe']['BaoFei']; //乘客保费
             $zqi['BoLi_BaoFei']=$info['Item']['BoLi']['BaoFei']; //玻璃保费
             $zqi['HuaHen_BaoFei']=$info['Item']['HuaHen']['BaoFei']; //划痕保费

             $zqi['BuJiMianCheSun_BaoFei']=$info['Item']['BuJiMianCheSun']['BaoFei']; //不计免赔车损保费
             $zqi['BuJiMianSanZhe_BaoFei']=$info['Item']['BuJiMianSanZhe']['BaoFei']; //不计免赔三者保费
             $zqi['BuJiMianDaoQiang_BaoFei']=$info['Item']['BuJiMianDaoQiang']['BaoFei']; //不计免赔盗抢保费
             $zqi['BuJiMianChengKe_BaoFei']=$info['Item']['BuJiMianChengKe']['BaoFei']; //不计免乘客保费
             $zqi['BuJiMianSiJi_BaoFei']=$info['Item']['BuJiMianSiJi']['BaoFei']; //不计免司机保费
             $zqi['BuJiMianHuaHen_BaoFei']=$info['Item']['BuJiMianHuaHen']['BaoFei']; //不计免划痕保额
             $zqi['BuJiMianSheShui_BaoFei']=$info['Item']['BuJiMianSheShui']['BaoFei']; //不计免涉水保费
             $zqi['BuJiMianZiRan_BaoFei']=$info['Item']['BuJiMianZiRan']['BaoFei']; //不计免自燃保费

            $zqi['BuJiMian_Price'] = $zqi['BuJiMianCheSun_BaoFei']+$zqi['BuJiMianSanZhe_BaoFei']+$zqi['BuJiMianDaoQiang_BaoFei']+$zqi['BuJiMianChengKe_BaoFei']+$zqi['BuJiMianSiJi_BaoFei']+ $zqi['BuJiMianHuaHen_BaoFei']+$zqi['BuJiMianSheShui_BaoFei']+$zqi['BuJiMianZiRan_BaoFei'];
//不计免保费总和
             $zqi['SheShui_BaoFei']=$info['Item']['SheShui']['BaoFei']; //涉水保费
             $zqi['ZiRan_BaoFei']=$info['Item']['ZiRan']['BaoFei']; //自燃保费
             $zqi['HcSanFangTeYue_BaoFei']=$info['Item']['HcSanFangTeYue']['BaoFei']; //车损无法找到第三方险
          //   $zqi['yongjin']=$info['UserInfo']['yongjin']; //佣金
             $zqi['QuoteResult']=$info['Item']['QuoteResult']; //
             $zqi['create_time']=time(); //
             $zqi['status']=$pp; //标志位：1：仅有太平洋； 2：仅有人保； 3：两者都有

            $id = Db::name('user')->where('open_id',$openid)->value('id');
            $zqi['user_id']=$id; //
            $zqi['insure_address'] = $qiqi['tbd'];//

            if($pp == 1){

                Db::name('log_enquiry')->where('LicenseNo',$data['LicenseNo'])->where('user_id',$zqi['user_id'])->delete();
                Db::name('log_enquiry')->insert($zqi);
            }elseif ($pp==2){
                Db::name('log_enquiry')->where('LicenseNo',$data['LicenseNo'])->where('user_id',$zqi['user_id'])->delete();
                Db::name('log_enquiry')->insert($zqi);
            }elseif ($pp==3){
                //判断标志位是1 or 2
                $sb1 = Db::name('log_enquiry')->where('LicenseNo',$data['LicenseNo'])->where('user_id',$zqi['user_id'])->where('status',1)->whereOr('status',2)->find();

                if(!$sb1){
                    $sb2 = Db::name('log_enquiry')->where('LicenseNo',$data['LicenseNo'])->where('user_id',$zqi['user_id'])->where('status',3)->where('insure_type',$zqi['insure_type'])->find();
                    if($sb2){
                        Db::name('log_enquiry')->where('LicenseNo',$data['LicenseNo'])->where('user_id',$zqi['user_id'])->where('status',3)->where('insure_type',$zqi['insure_type'])->update($zqi);
                    }else{
                        $gujia_info=Db::name('log_enquiry')->insert($zqi);
                    }
                }else{
                    Db::name('log_enquiry')->where('LicenseNo',$data['LicenseNo'])->where('user_id',$zqi['user_id'])->where('status',1)->whereOr('status',2)->delete();

                    $sb2 = Db::name('log_enquiry')->where('LicenseNo',$data['LicenseNo'])->where('user_id',$zqi['user_id'])->where('status',3)->where('insure_type',$zqi['insure_type'])->find();
                    if($sb2){
                        Db::name('log_enquiry')->where('LicenseNo',$data['LicenseNo'])->where('user_id',$zqi['user_id'])->where('status',3)->where('insure_type',$zqi['insure_type'])->update($zqi);
                    }else{
                        $gujia_info=Db::name('log_enquiry')->insert($zqi);
                    }

                }


            }

      //     $a=Db::name('log_enquiry')->getLastSql();
     //       return $this->ok('0',$a,$info);
            return $this->ok('0',$info['StatusMessage'],$info);
        }
    }








    //核保
    public function hebao()
    {
        if ($this->request->isPost()) {
            //$QuoteGroup = $this->request->post('QuoteGroup');
            $SubmitGroup = $this->request->post('SubmitGroup');
          //  $SubmitGroup = $this->request->post('SubmitGroup');

            $qiqi = session::get('qiqi');
            $data['LicenseNo']=$qiqi['UserInfo']['LicenseNo'];

          $openid = session::get('useropenid');

         //  $openid = "o59tW0hnIWqtk7tmn_fM5GqqbPVY";

          //  $CustKey = $openid;
            $CustKey = "qiqi123456";

            // 注：CityCode暂时写死
            $a = "LicenseNo=" . $data['LicenseNo'] . "&ShowChannel=1&SubmitGroup=" . $SubmitGroup . "&CustKey=" . $CustKey . "&Agent=" . agent . mikey;
            //  $a="LicenseNo=苏A5J5F3&QuoteGroup=1&CustKey=qiqi123456&Agent=1243840b5c8b6d8b";
            $SecCode = md5($a);
            $url = "http://iu.91bihu.com/api/CarInsurance/GetSubmitInfo?LicenseNo=" . urlencode($data['LicenseNo']) . "&ShowChannel=1&SubmitGroup=" . $SubmitGroup . "&CustKey=" . $CustKey . "&Agent=".agent."&SecCode=" . $SecCode;
       //  var_dump($url);die;
            $info = json_decode($this->http_get($url),true);
//            echo "<pre>";
//            var_dump($info);die;
            if($SubmitGroup=='1'){
                $insure_type = '太平洋保险';
            }elseif($SubmitGroup=='4'){
                $insure_type = '中国人保';
            }
            $zqi['BizNo']=$info['Item']['BizNo'];
            $zqi['Source']=$info['Item']['Source'];
            $zqi['ForceNo']=$info['Item']['ForceNo'];
            $zqi['ChannelId']=$info['Item']['ChannelId'];
            $zqi['SubmitResult']=$info['Item']['SubmitResult'];
            $hebao=Db::name('log_enquiry')->where('LicenseNo',$data['LicenseNo'])->where('insure_type',$insure_type)->update($zqi);
          //  var_dump(Db::name('log_enquiry')->getLastSql());die;
            return $this->ok('0',$info['StatusMessage'],$info);
        }
    }


    //报价详情
    public function baojia_info(){
        $type = $this->request->param('type');
        if($type=='1'){
            $insure_type = '太平洋保险';
        }elseif($type=='4'){
            $insure_type = '中国人保';
        }
        $info = session::get('qiqi');
        $LicenseNo=$info['UserInfo']['LicenseNo'];
        $data=Db::name('log_enquiry')->where('LicenseNo',$LicenseNo)->where('insure_type',$insure_type)->order('id desc')->find();
        $stage=Db::name('stage_set')->find();

        $data['ModleName']=$info['UserInfo']['ModleName'];
        $data['yj']=round($data['all_price']*$stage['one_stage']*0.01,2);

        $this->view->assign('data',$data);
        //电话
        $tel=Db::name('about_us')->field('tel')->find();
        $this->view->assign('tel',$tel);
        return $this->view->fetch();

    }
//报价详情
    public function toubao(){

    if($this->request->isPost()){

        $zz = $this->request->param();
       // echo "<pre>";
      // var_dump($zz);die;
        $Source = $zz['Source'];
        $ChannelId =$zz['ChannelId'];
        $BiztNo = $zz['BizNo'];
        $ForcetNo =$zz['ForceNo'];
      //  var_dump(111);die;
        $qiqi = session::get('qiqi');
        $LicenseNo=$qiqi['UserInfo']['LicenseNo'];
        $CarVin =$qiqi['UserInfo']['CarVin'];

        $openid = session::get('useropenid');
        //  $openid = "o59tW0hnIWqtk7tmn_fM5GqqbPVY";
       // $CustKey = $openid;
        $CustKey = "qiqi123456";




        if( $BiztNo!='' ){
            $a="LicenseNo=".$LicenseNo."&CarVin=".$CarVin."&PayMent=1&BiztNo=".$BiztNo."&Source=".$Source."&ChannelId=".$ChannelId."&CustKey=".$CustKey."&Agent=".agent.mikey;
            $SecCode = md5($a);
            $url = "http://buc.91bihu.com/api/PayOut/PayAddress?LicenseNo=".urlencode($LicenseNo)."&CarVin=".$CarVin."&PayMent=1&BiztNo=".$BiztNo."&Source=".$Source."&ChannelId=".$ChannelId."&CustKey=".$CustKey."&Agent=".agent."&SecCode=" . $SecCode;
        }elseif( $BiztNo=='' && $ForcetNo!='') {
            $a = "LicenseNo=" . $LicenseNo . "&CarVin=" . $CarVin . "&PayMent=1&ForcetNo=" . $ForcetNo . "&Source=" . $Source . "&ChannelId=" . $ChannelId . "&CustKey=" . $CustKey . "&Agent=" . agent . mikey;
            $SecCode = md5($a);
            $url = "http://buc.91bihu.com/api/PayOut/PayAddress?LicenseNo=" . urlencode($LicenseNo) . "&CarVin=" . $CarVin . "&PayMent=1&ForcetNo=" . $ForcetNo . "&Source=" . $Source . "&ChannelId=" . $ChannelId . "&CustKey=" . $CustKey . "&Agent=" . agent . "&SecCode=" . $SecCode;

        }elseif( $BiztNo=='' && $ForcetNo!=''){
            return $this->no('1',"强险和商业险保单不能同时为空！");
        }
   // var_dump($this->http_get($url));die;
       // $info = json_decode($this->http_get($url),true);
        $pay_info = $this->http_get($url);
        $info=   json_decode($pay_info,true);
        if($info['BusinessStatus']=='1'){
            $cc['openid'] = $openid;
            $cc['Source'] = $Source;
            $cc['BiztNo'] = $zz['BizNo'];
            $cc['ForcetNo'] =$zz['ForceNo'];
            $cc['Money'] = $info['Data']['Money'];
            $cc['Name'] = $info['Data']['Name'];
            $cc['LicenseNo'] = $LicenseNo;
            $cc['PayUrl'] = $info['Data']['PayUrl'];
            $cc['PayNum'] = $info['Data']['PayNum'];
            $cc['CarVin'] = $CarVin;
            $cc['ChannelId'] = $ChannelId;
            $cc['TransactionNum'] = $info['Data']['TransactionNum'];
            $cc['addtime'] = time();
            $cc['PayWay'] = 2;
            $cc['tbd'] = $qiqi['tbd'];
            $stage_set = Db::name('stage_set')->find();
            $cc['bonus']=$cc['Money']*$stage_set['one_stage']/100;
         //   $openid = '';
            //var_dump(555);
            $res=Db::name('pay_info')->where('LicenseNo',$LicenseNo)->where('Source',$Source)->where('openid',$openid)->find();
         // var_dump($res,555);die;
            if(!$res){
   // $cc['LicenseNo'] = $LicenseNo;


              // var_dump($cc,11);die;
                 Db::name('pay_info')->insert($cc);
                $res2=Db::name('pay_info')->where('LicenseNo',$LicenseNo)->where('Source',$Source)->where('openid',$openid)->find();

                return $this->ok('0',$info['StatusMessage'],$res2);

        }else{
                Db::name('pay_info')->where('id',$res['id'])->update($cc);
                return $this->ok('0',$info['StatusMessage'],$res);
            }

       }else{
            return $this->no('2',$info['StatusMessage']);
        }



      }



   //     return $this->view->fetch();

    }
//付款页面
    public function pay(){
        $id=$this->request->param('id');
        $cc = Db::name('pay_info')->where('id',$id)->find();

        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval(3) ;//容错级别
        $matrixPointSize = intval(4);//生成图片大小
        //生成二维码图片
       // $img = $this->qrcode($cc['PayUrl']);
        $filename = '';
        $object = new \QRcode();

        $path = "./public/Uploads/fukuanma/QRcode/";//生成的二维码所在目录
        if(!file_exists($path)){
            mkdir($path, 0777,true);
        }
        $time = time().'.png';//生成的二维码文件名
        $fileName = $path.$time;

//var_dump($fileName);die;

       $object->png($cc['PayUrl'], $fileName, $errorCorrectionLevel, $matrixPointSize, 2);
    //   var_dump( $fileName);die;

      $aaaa=explode('.',$fileName);
      $t =time();



    $cc['addtime']= ($cc['addtime']+7200)-$t;

        $cc['PayUrl']= $aaaa['1'].".png";
       $this->view->assign('cc',$cc);
        return $this->view->fetch();

      //  $img = $this->qrcode($cc['PayUrl']);
       // echo "<pre>";
     //   var_dump($a);
    }


//请求报价结果
    public function pay_info($BiztNo,$ForcetNo,$CarVin,$LicenseNo,$ChannelId,$Source){

         //  $data =  ;
//        $CustKey = session::get('openid');

        $openid = session::get('useropenid');
       // $openid = "o59tW0hnIWqtk7tmn_fM5GqqbPVY";
//        $CustKey = $openid;
        $CustKey = "qiqi123456";

    //    $CustKey = 'qiqi123456';

        if( $BiztNo!='' ){
            $a="LicenseNo=".$LicenseNo."&CarVin=".$CarVin."&BiztNo=".$BiztNo."&Source=".$Source."&ChannelId=".$ChannelId."&CustKey=".$CustKey."&Agent=".agent.mikey;
            $SecCode = md5($a);
            $url = "http://buc.91bihu.com/api/PayOut/PayInfo?LicenseNo=".urlencode($LicenseNo)."&CarVin=".$CarVin."&BiztNo=".$BiztNo."&Source=".$Source."&ChannelId=".$ChannelId."&CustKey=".$CustKey."&Agent=".agent."&SecCode=" . $SecCode;
        }elseif( $BiztNo=='' && $ForcetNo!='') {
            $a = "LicenseNo=" . $LicenseNo . "&CarVin=" . $CarVin . "&ForcetNo=" . $ForcetNo . "&Source=" . $Source . "&ChannelId=" . $ChannelId . "&CustKey=" . $CustKey . "&Agent=" . agent . mikey;
            $SecCode = md5($a);
            $url = "http://buc.91bihu.com/api/PayOut/PayInfo?LicenseNo=" . urlencode($LicenseNo) . "&CarVin=" . $CarVin . "&ForcetNo=" . $ForcetNo . "&Source=" . $Source . "&ChannelId=" . $ChannelId . "&CustKey=" . $CustKey . "&Agent=" . agent . "&SecCode=" . $SecCode;

        }elseif( $BiztNo=='' && $ForcetNo!=''){
            return $this->no('1',"强险和商业险保单不能同时为空！");
        }
        $pay_info = $this->http_get($url);
        return $pay_info;
        //$info=   json_decode($pay_info,true);

    }





    /**
     * 生成二维码的代码
     *
     */

    public function qrcode($url,$level=3,$size=4)
    {
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        //生成二维码图片
        $object = new \QRcode();
        $a = $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);

     return $a;
    }





    /**
     * 询价记录查询
     * @param
     *
     */
    public function findEnquiry(){

        //根据条件查询
        $map=[];
        $request=Request::instance();
        $userId=Db::name('user')
            ->field('id')
            ->where('open_id',session('useropenid'))
            ->find();
        //车牌号
        $search=trim($request->param('search'));

        if($search){
            $map['LicenseNo|HolderName']=["like", "%" . $search . "%"];
            
        }
        //车主姓名信息
      /* if($search){
           $map['HolderName']=["like", "%" . $search . "%"];
        }*/
        $status=Db::name('log_enquiry')
                    ->field('status')
                    ->where($map)
                    ->where('user_id',$userId['id'])
                    ->order('create_time desc')
                    ->find();

        if($status['status']==3){
            $enquiryInfo=Db::name('log_enquiry')
                ->field('id,LicenseNo,HolderName,create_time')
                ->where($map)
                ->where('user_id',(int)$userId['id'])
                ->group('LicenseNo')
                ->order('create_time desc')
                ->select();
        }else{
            $enquiryInfo=Db::name('log_enquiry')
                ->field('id,LicenseNo,HolderName,create_time')
                ->where($map)
                ->where('user_id',(int)$userId['id'])
                ->group('LicenseNo')
                ->order('create_time desc')
                ->select();
        }
        $this->view->assign('enquiryInfo',$enquiryInfo);
        return $this->view->fetch();
    }
    /**
     * 询价详情
     * @param
     */
    public function details(){

        $request=Request::instance();
        $LicenseNo=$request->param('LicenseNo');
       /* $details=Db::name('log_enquiry')
            ->alias('a')
            ->join('log_enquiry b','a.LicenseNo=b.LicenseNo','INNER')
            ->where('LicenseNo',$LicenseNo)

            ->select(); */

        $details=Db::name('log_enquiry')
            ->field('id,LicenseNo,user_id,HolderName,ModleName,RegisterDate,ForceExpireDate,BusinessExpireDate')
            ->where('LicenseNo',$LicenseNo)
            ->find();
        //
        $priceDetails=Db::name('log_enquiry')
            ->field('id,Source,all_price,BizNo,ForceNo')
            ->where('LicenseNo',$LicenseNo)
            ->where('user_id',$details['user_id'])
            ->select();
        foreach($priceDetails as $k=>$v){
            //判断报价情况
            if($v['all_price']>0) {
                $priceDetails[$k]['tpy'] = $v['all_price'];
            }else{
                $priceDetails[$k]['tpy']='报价失败';
            }
            //判断核保情况
            if($v['BizNo']!=='' || $v['ForceNo']!=='' ) {
                $priceDetails[$k]['hebao'] = '核保成功';
            }else{
                $priceDetails[$k]['hebao']='核保失败';
            }
        }

        $this->view->assign('details',$details);
        $this->view->assign('priceDetails',$priceDetails);
        return $this->view->fetch();
    }

    //详情具体页面
    public function detailsInfo()
    {
        $request = Request::instance();
        $id = $request->param('id');
        $data=Db::name('log_enquiry')
          //  ->field('id,LicenseNo,HolderName,ModleName,RegisterDate,ForceExpireDate,BusinessExpireDate')
            ->where('id',$id)
            ->find();
        $tel=Db::name('about_us')->field('tel')->find();
        $this->view->assign('tel',$tel);
        $this->view->assign('data',$data);
        return $this->view->fetch();
    }


//curl get 请求


    public function http_get($url){
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }

//curl post 请求 (含请求头)
    public function http_post($url,$cars){
        //  $accessToken = $cars['SecCode'];
        //$headers[]  =  "Content-Type:application/x-www-form-urlencoded";
        $headers[]  =  "Content-Type:application/json";
        // $headers[]  =  "Authorization: Bearer ". $accessToken;
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);//1:设置，0：否;
        //设置请求头
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $cars);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }




}