<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OSS\OssClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 文件上传接口
 *
 * Class FileController
 * @package App\Http\Controllers\Api
 */
class FileController extends Controller
{
	/**
	 * 文件上传
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function postUploadTemp(Request $request)
	{
		$tempPath = config('path.upload_temp');
		$file = $request->file('file');
		// 判断文件是否有效
		if (!is_object($file) || !$file->isValid()) {
			return response(['status' => 0, 'msg' => '上传文件时遇到错误']);
		}
		$ext = $this->getExt($file);
		// 移动到目录
//			$target = $file->move($tempPath . $path, $name);
		$bucket = env('BucketName','');
		$ak = env('Ak','');
		$sk = env('Sk','');	
		$host = env('OssHost','');
		$ossClient = new OssClient($ak, $sk, $host, false);
		if (is_null($ossClient)) {
			return fail('','内部错误');
		}
		$res = $ossClient->uploadFile($bucket, uuid() . '.' . $ext, $file->getPathname());
		return isset($res['oss-request-url'])?success(['uri'=>$res['oss-request-url']]):fail('','上传失败,请稍后重试');
	}

	/**
	 * 图片裁剪
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function postImageCrop(Request $request)
	{
		$data = $request->all();
		$tempPath = config('path.upload_temp');
		$imagePath = array_get($data, 'path');

		// 读取原图片
		try {
			$image = \Image::make($tempPath . $imagePath);
		} catch (\Exception $e) {
			return response(['status' => 0, 'msg' => '找不到裁剪图片']);
		}

		$cropImagePath = 'crop/' . $imagePath;
		$cropImageFullPath = $tempPath . $cropImagePath;
		// 临时目录创建
		if (!$this->createDirIfNotExist(dirname($cropImageFullPath))) {
			return response(['status' => 0, 'msg' => '目录创建失败']);
		}

		// 图片裁剪开始
		$width = intval(array_get($data, 'width'));
		$height = intval(array_get($data, 'height'));
		if ($width && $height) {
			$x = intval(array_get($data, 'x'));
			$y = intval(array_get($data, 'y'));

			$image->crop($width, $height, $x, $y);
		}

		$destWidth = intval(array_get($data, 'destWidth'));
		$destHeight = intval(array_get($data, 'destHeight'));
		if ($destWidth && $destHeight) {
			$image->resize($destWidth, $destHeight);
		}

		// 保存图片并删除原图
		$image->save($cropImageFullPath, 100);
		@unlink($tempPath . $imagePath);

		return response([
			'status' => 1,
			'msg' => '剪裁成功',
			'path' => $cropImagePath,
			'org_name' => array_get($data, 'org_name'),
			'name' => $image->basename,
			'ext' => $image->extension,
			'uri' => '/upload/temp/' . $cropImagePath,
			'size' => $image->filesize(),
			'url' => upload_url($cropImagePath, 'temp')
		]);
	}

	/**
	 * @param UploadedFile $file
	 * @return string
	 */
	protected function getExt(UploadedFile $file)
	{
		return $file->getClientOriginalExtension();

//        $ext = $file->guessExtension();
//
//        return $ext ?: $file->getClientOriginalExtension();
	}

	/**
	 * 获取格式化目录
	 *
	 * @return string
	 */
	protected function getFormatPath()
	{
		return date('Y/m/d/');
	}

	/**
	 * 获取格式化文件名
	 *
	 * @return string
	 */
	protected function getFormatName()
	{
		return uniqid();
	}

	/**
	 * 目录不存在时进行创建
	 *
	 * @param string $directory
	 * @return bool
	 */
	protected function createDirIfNotExist($directory)
	{
		if (!is_dir($directory)) {
			if (false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
				return false;
			}
		} elseif (!is_writable($directory)) {
			return false;
		}

		return true;
	}

	/**
	 * 下载
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 */
	public function getDownload(Request $request)
	{
		ob_clean();
		ob_flush();
		return response()->download(public_path($request->get('path')), $request->get('name', ''));
	}

	public function gmt_iso8601($time)
	{
		$dtStr = date("c", $time);
		$mydatetime = new \DateTime($dtStr);
		$expiration = $mydatetime->format(\DateTime::ISO8601);
		$pos = strpos($expiration, '+');
		$expiration = substr($expiration, 0, $pos);
		return $expiration . "Z";
	}

	public function getOssInfo()
	{
		$id = 'LTAITYEbsi2WOSCd';
		$key = 'd9MuUqH9rN8ctF6AklWsXcxI3dyEVP';
		$host = 'cryptobox.oss-cn-shenzhen.aliyuncs.com';

		$now = time();
		$expire = 300000000; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
		$end = $now + $expire;
		$expiration = $this->gmt_iso8601($end);

		$dir = 'cryptobox/';
		//最大文件大小.用户可以自己设置
		$condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
		$conditions[] = $condition;

		//表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
		$start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
		$conditions[] = $start;


		$arr = array('expiration' => $expiration, 'conditions' => $conditions);
		//echo json_encode($arr);
		//return;
		$policy = json_encode($arr);
		$base64_policy = base64_encode($policy);
		$string_to_sign = $base64_policy;
		$signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

		$response = array();
		$response['accessid'] = $id;
		$response['host'] = $host;
		$response['policy'] = $base64_policy;
		$response['signature'] = $signature;
		$response['expire'] = $end;
		//这个参数是设置用户上传指定的前缀
		$response['dir'] = $dir;
		return success($response);
	}
}
