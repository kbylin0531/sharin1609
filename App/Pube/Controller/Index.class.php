<?php
namespace App\Pube\Controller;
use Library\Member\EC21Member;
use Library\MemberRegister\EC21MemberRegister;
use Library\Model\MemberModel;
use Library\Model\ProductModel;
use Library\Ngine;
use Library\Product\EC21Product;
use Library\ProductProvider;
use Library\Utils\RecordSaver;
use Sharin\Core\Controller;
use Sharin\Core\Response;
use Sharin\Library\Session;

include_once dirname(__DIR__).'/Library/Ngine.class.php';
Ngine::init();

class Index extends Controller{

    public function test(){
        \Sharin\dump(
            1 or 'param1',
            0 or 'param2'
        );
    }

    public function testDb(){
        $member = MemberModel::getInstance();
        $product = ProductModel::getInstance();
        \Sharin\dump(
//            $member->create(['zbg1473301807','zbg1473301807','2452142619@qq.com','','0','0']),
//            $member->delete('zbg1473301807'),
            $member->select(),
            $product->select()
            );
    }


    public function group(){
        $register = new EC21MemberRegister();
        $member = new EC21Member('zhangyishang','zhangyishang');
        $content = $register->createGroup($member);
        \Sharin\dump($content);
    }

    public function member($code='',$email=''){
        if($code){
            $register = new EC21MemberRegister();
            $code = $register->postCode($code,Session::get('childId'));
            $info = $register->setCapture($code)->setEmail($email)->register();
            if($info){
                $member = new EC21Member($info['username'],$info['username']);
                $content = $register->createGroup($member);
                if(false !== $content){
                    $info['dftgroupid'] = $content[0];
                    $info['dftgroupnm'] = $content[1];
                }else{
                    \Sharin\dump(htmlspecialchars($content));
                    Response::ajaxBack([
                        'type'  => 0,
                        'value'=> '添加产品默认分组失败',
                    ]);
                }
            }
            if($register->update()){
                $memberModel = MemberModel::getInstance();
                $memberModel->username = $info['username'];
                $memberModel->passwd = $info['passwd'];
                $memberModel->email = $info['email'];
                $memberModel->phone = '';
                $memberModel->cateid = isset($info['dftgroupid'])?$info['dftgroupid']:'';
                $memberModel->total = 0;
                $memberModel->create();//添加到SQLite数据库
                Response::ajaxBack([
                    'type'  => 1,
                    'value'=> $info,
                ]);
            }else{
                Response::ajaxBack([
                    'type'  => 0,
                    'value'=> '修改失败',
                ]);
            }
        }
        $this->assign('data',json_encode(MemberModel::getInstance()->select('platform = \'ec21\'')));
        $this->display();
    }

    public function product($pid='',$name='',$describe='',$keywork='',$image=''){
        if(SR_IS_POST){
            $meminfo = EC21Member::getAvailableAccount();
            if(!$meminfo){
                Response::ajaxBack([
                    'type'  => 0,
                    'massage'   => '',
                ]);
            }
            $member = new EC21Member($meminfo['username'],$meminfo['passwd']);
            $product = new EC21Product();
            $product->setName($name);
            $product->addKeywords($keywork);
            $product->setImage($image);
            $product->setDescription($describe);
            $product->gcatalog_id = $meminfo['cateid'];//自定义分类ID，各个账号均不同
            //系统分类
            $product->categorymId = '212815';
            $product->categoryNm = 'Pharmaceutical Intermediates';

            if(!$member->login()){
                throw new \Exception('用户登录出错！');
            }
            $result = $product->submit($member)?1:0;

            $productModel = ProductModel::getInstance();
            $productModel->type = $result;
            $productModel->pid = $pid;
            $productModel->name = $name;
            $productModel->image = $image;
            $productModel->atime = NOW;
            $productModel->platform = 'ec21';
            $productModel->uname = $meminfo['username'];

            if($result){
                $productModel->url = $product->getLastSubmit($member);
                $message = '提交成功';
                if(!MemberModel::getInstance()->inc($meminfo['username'])){
                    $message .=  " ,failed to increate total field!";
                }
            } else {
                $message = '提交失败';
            }
            $insert = $productModel->create();
            if(!$insert){
                $message .=  " ,failed to insert result!";
            }
            Response::ajaxBack([
                'type'  => 1,
                'massage'   => $message,
            ]);
        }
        $this->assign('data',json_encode((new ProductProvider())->getlist(10)));
        $this->display();
    }

    public function result(){
        $product = ProductModel::getInstance();
        $this->assign('data',json_encode($product->select()));
        $this->display();
    }

    public function publish(\Library\Member $member,array $keyworks=[]){
        $product = new EC21Product();
        $product->setName('New PRODUCT23'.time());
        $product->setDescription('New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23New PRODUCT23');
        $product->categorymId = '212815';
        $product->categoryNm = 'Pharmaceutical Intermediates';
        $product->gcatalog_id = $member->getDefaultCateid();

        $keyworks = $product['keyworks'];
        array_map(function ($k) use($product){
            $product->addKeywork($k);
        },$keyworks);
        $product->setImage(SR_PATH_BASE.'/b.jpg');

        if(!$member->login()){
            throw new \Exception('用户登录出错！');
        }

        if($product->submit($member)){
            $href = $product->getLastSubmit($member);
            echo "提交成功:<a href='{$href}' target='_blank'>{$href}</a>";
        }else{
            echo '提交失败';
        }
    }

    public function published(){
        //todo:查看发布的
        $this->display();
    }

    public function showList(){
        echo '<pre>';
        var_export(RecordSaver::get());
    }

    public function getImage(){
        $register = new EC21MemberRegister();
        $register->getRegisterPage();
        $magic = $register->getMagic();
        $childId = $register->getChidId($magic);
        Session::set('childId',$childId);

        $image = $register->saveImage('',$childId);
        Response::ajaxBack([
            'src' => SR_PUBLIC_URL.'/'.$image,
        ]);
    }

    public function testRigister($code='',$email=''){
        $register = new EC21MemberRegister();
        if($code){
            $code = $register->postCode($code,Session::get('childId'));

            $info = $register->setCapture($code)->setEmail($email)->register();

            echo " <a href='".SR_SCRIPT_URL."/Pube/Index/testRigister'>继续注册</a><pre>";
            var_dump($info);
            RecordSaver::set($info['username'],$info);

            echo $register->update()? 'Y':'N';
        }else{
            $register->getRegisterPage();
            $magic = $register->getMagic();
            $childId = $register->getChidId($magic);
            Session::set('childId',$childId);

            $image = $register->saveImage('',$childId);
            $image = SR_PUBLIC_URL.'/'.$image;
            echo <<< endline
<form action="{$_SERVER['REQUEST_URI']}" method="get">
    <label>www.ec21.com</label><br>
    <img src='{$image}' />
    <label>Code:</label>
    <input name="code" type="text">
    <label>Email:</label>
    <input name="email" type="text">
    <input type="submit" value="submit">
</form> 
endline;
        }
    }

}