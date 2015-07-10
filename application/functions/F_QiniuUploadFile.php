<?php

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

Class QiniuUpload
{
    private $accessKey;
    private $secretKey;
    private $bucket;//库名
    private $opts;//上传参数
    private $auth;//连接认证对象

    //相关资源初始化
    public function __construct($accessKey,$secretKey,$bucket=null,$opts=[])
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucket = empty($bucket)?null:$bucket;
        $this->auth = new Auth($accessKey,$secretKey);
        if(empty($this->bucket))
        {
            die('未指定bucket库');
        }
        $this->opts = $opts;
    }

    /**
     * 列举库中的所有文件
     *
     * @param $prefix
     */
    public function listFile($prefix)
    {

    }

    /**
     * 删除文件
     *
     * @param $fileName
     * @return \Qiniu\Storage\成功返回NULL|string
     */
    public function delete($fileName)
    {
        $bucketManager = new BucketManager($this->auth);

        $error = $bucketManager->delete($this->bucket,$fileName);

        if($error !== null)
        {
            return $error;
        }
        else
        {
            return "Success";
        }
    }

    /**
     * 移动文件
     *
     * @param $fromBucket
     * @param $fromFile
     * @param $toBucket
     * @param $toFile
     * @return \Qiniu\Storage\成功返回NULL|string
     */
    public function move($fromBucket,$fromFile,$toBucket,$toFile)
    {
        $bucketManager = new BucketManager($this->auth);

        $error = $bucketManager->move($fromBucket,$fromFile,$toBucket,$toFile);

        if($error !== null)
        {
            return $error;
        }
        else
        {
            return "Success";
        }
    }
    /**
     * 复制文件到另外一个位置
     *
     * @param $fromBucket
     * @param $fromFile
     * @param $toBucket
     * @param $toFile
     * @return \Qiniu\Storage\成功返回NULL|string
     */
    public function copy($fromBucket,$fromFile,$toBucket,$toFile)
    {
        $bucketManager = new BucketManager($this->auth);

        $error = $bucketManager->copy($fromBucket,$fromFile,$toBucket,$toFile);

        if($error !== null)
        {
            return $error;
        }
        else
        {
            return "Success";
        }
    }

    /**
     * 查看文件状态
     *
     * @param $fileName
     * @return
     */
    public function checkStatus($fileName)
    {
        $bucketManager = new BucketManager($this->auth);

        list($result,$error)= $bucketManager->stat($this->bucket,$fileName);

        if(empty($result))
        {
            return $error->getResponse()->error;
        }
        else
        {
            return $result;
        }
    }

    /**
     * 上传字符串
     *
     * @param $string
     * @return mixed
     */
    public function uploadString($string)
    {
        $token = $this->auth->uploadToken($this->bucket,null,3600,$this->opts);

        $uploadManager = new UploadManager();

        list($result,$error) = $uploadManager->put($token,null,$string);

        if(empty($result))
        {
            return $error->getResponse()->error;
        }
        else
        {
            return $result;
        }
    }

    /**
     * 上传文件
     * @return mixed
     * @throws Exception
     */
    public function uploadFile()
    {
        $token = $this->auth->uploadToken($this->bucket,null,3600,$this->opts);

        $uploadManager = new UploadManager();

        list($result, $error) = $uploadManager->putFile($token, null, __FILE__);

        if (empty($result))
        {
            return $error->getResponse()->error;
        }
        else
        {
            return $result;
        }
    }
}