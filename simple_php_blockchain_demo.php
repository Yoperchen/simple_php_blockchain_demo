<?php
/**
 * 简单的区块链
 * @author Yoper
 * @PHP技术交流QQ群 370648191
 * @Email chen.yong.peng@foxmail.com
 * @wechat YoperMan 加我请发送验证"区块链"
 */
namespace common\library\block;
/**
 * 区块结构
 */
class block{
    private $index;
    private $timestamp;
    private $data;
    private $previous_hash;
    private $random_str;
    private $hash;
    public function __construct($index,$timestamp,$data,$random_str,$previous_hash)
    {
        $this->index=$index;
        $this->timestamp=$timestamp;
        $this->data=$data;
        $this->previous_hash=$previous_hash;
        $this->random_str=$random_str;
        $this->hash=$this->hash_block();
    }
    public function __get($name){
        return $this->$name;
    }
    private function hash_block(){
        $str=$this->index.$this->timestamp.$this->data.$this->random_str.$this->previous_hash;
        return hash("sha256",$str);
    }
}
/**
 * 创世区块
 * @return \common\library\block\block
 */
function create_genesis_block(){
    return new \common\library\block\block(0, time(),"第一个区块",0,0);
}
/**
 * 挖矿，生成下一个区块
 * 这应该是一个复杂的算法，但为了简单，我们这里挖到前1位是数字就挖矿成功。
 * @param \common\library\block\block $last_block_obj
 */
function dig(\common\library\block\block $last_block_obj){
    $random_str = $last_block_obj->hash.get_random();
    $index=$last_block_obj->index+1;
    $timestamp=time();
    $data='I am block '.$index;
    $block_obj = new \common\library\block\block($index,$timestamp,$data,$random_str,$last_block_obj->hash);
    
    //前一位不是数字
    if(!is_numeric($block_obj->hash{0})){
        return false;
    }
    //数数字，返回块
    return $block_obj;
}
/**
 * 验证区块
 * 这也是一个复杂的过程，为了简单，我们这里直接返回正确
 * @param array $data
 */
function verify(\common\library\block\block $last_block_obj){
    return true;
}
/**
 * 生成随机字符串
 * @param int $len
 * @return string
 */
function get_random($len=32){
    $str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $key = "";
    for($i=0;$i<$len;$i++)
    {
        $key.= $str{mt_rand(0,32)};//随机数
    }
    return $key;
}


header("Content-type:text/html;charset=utf-8");
//生成第一个区块
$blockchain=[\common\library\block\create_genesis_block()];
//模拟生成其他区块,我们直接循环生成。实际中，还需要跟踪互联网上多台机器上链的变化,像比特币会有工作量证明等算法，达到条件了才生成区块等
//我们的链是一个数组，实际生产中应该保存下来
$previous_block = $blockchain[0];
for($i=0;$i<=10;$i++){
    if(!($new_block=dig($previous_block))){
        continue;
    }
    $blockchain[]=$new_block;
    $previous_block=$new_block;
    
    //告诉大家新增了一个区块
    echo "区块已加入链中.新区块是 : {$new_block->index}<br/>";
    echo "新区块哈希值是 : {$new_block->hash}<br/>";
    print_r($new_block);
    echo "<br/><br/>";
}




















