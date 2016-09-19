<?php
namespace App\Pube\Controller;
use Library\Model\MemberModel;
use Library\Model\ProductModel;
use Library\Ngine;
use Library\Platform\EC21Platform;
use Library\ProductProvider;
use Sharin\Core\Controller;
use Sharin\Core\Response;

include_once dirname(__DIR__).'/Library/Ngine.class.php';

class Index extends Controller{

    public function index(){
        echo <<< endline
        <ul>
            <li><a href="member" target="_blank">member</a></li>
            <li><a href="product" target="_blank">product</a></li>
            <li><a href="published" target="_blank">published</a></li>
        </ul>
endline;
    }

    public function member($code='',$email='',$action=''){
        if($action === 'capture'){
            $image = EC21Platform::getInstance()->getRegisterCapture();
            Response::ajaxBack([
                'src' => SR_PUBLIC_URL.'/'.$image,
            ]);
        }else if($code){
            $platform = new EC21Platform();
            $info = $platform->setCapture($code)->setEmail($email)->register();
            if($info){
                //登录平台
                if(!$platform->login($info['username'])) throw new \Exception(var_export($info,true));
                //创建默认分组
                $ginfo = $platform->createCategory();
                if(false !== $ginfo){
                    $info['gid'] = $ginfo[0];
                    $info['gname'] = $ginfo[1];
                }else{
                    \Sharin\dumpout(htmlspecialchars($ginfo));
                    Response::ajaxBack([
                        'type'  => 0,
                        'value'=> '添加产品默认分组失败',
                    ]);
                }
            }
            $type = 1;
            if($platform->update()){
                $memberModel = MemberModel::getInstance();
                $memberModel->username = $info['username'];
                $memberModel->passwd = $info['passwd'];
                $memberModel->email = $info['email'];
                $memberModel->phone = '';
                $memberModel->cateid = isset($info['gid'])?$info['gid']:'';
                $memberModel->total = 0;
                $memberModel->platform = 'ec21';
                if(!$memberModel->create()){
                    $info = '添加到数据库失败！';
                    $type = 0;
                }
            }else{
                $info = '完善公司信息失败';
                $type = 0;
            }
            Response::ajaxBack([
                'type'  => $type,
                'value' => $info,
            ]);
        }
        $this->assign('data',json_encode(MemberModel::getInstance()->select('platform = \'ec21\'')));
        $this->display();
    }

    public function product($pid='',$name='',$describe='',$keywork='',$image=''){
        if(SR_IS_POST){
            $meminfo = MemberModel::getInstance()->getAvailableAccount();
            $type = 0;
            if(!$meminfo){
                $message = '无可用的账号';
            } else {
                $platform = EC21Platform::getInstance();
                if($platform->login($meminfo['username'])){
                    //设置产品属性
                    $platform->setName($name);
                    $platform->addKeywords($keywork);
                    $platform->setImage($image);
                    $platform->setDescription($describe);
                    $platform->gcatalog_id = $meminfo['cateid'];//自定义分类ID，各个账号均不同
                    $platform->categorymId = '212815';
                    $platform->categoryNm = 'Pharmaceutical Intermediates';
                    //提交产品
                    $result = $platform->submit()?1:0;

                    //整理提交结果，无论成功与否
                    $productModel = ProductModel::getInstance();
                    $productModel->type = $result;
                    $productModel->pid = $pid;
                    $productModel->name = $name;
                    $productModel->image = $image;
                    $productModel->atime = NOW;
                    $productModel->platform = 'ec21';
                    $productModel->uname = $meminfo['username'];

                    if($result){
                        //提交成功额外增加产品URL
                        $productModel->url = $platform->getLastSubmit();
                        if(!MemberModel::getInstance()->inc($meminfo['username'])) {
                            $message =  "无法增加账户下产品累计!";
                        }else{
                            $type = 1;
                            $message = '提交成功';
                        }
                    } else {
                        $message = '提交失败';
                    }
                    $insert = $productModel->create();
                    if(!$insert){
                        $message .=  " ,无法将产品信息插入到数据库中!";
                    }
                }else{
                    $message = '账号无法登录';
                }
            }
            Response::ajaxBack([
                'type'  => $type,
                'message'   => $message,
            ]);
        }
        $this->assign('data',json_encode((new ProductProvider())->getlist(10)));
        $this->display();
    }

    public function published(){
        $product = ProductModel::getInstance();
        $this->assign('data',json_encode($product->select()));
        $this->display();
    }
}