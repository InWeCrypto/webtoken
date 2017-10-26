<?php

namespace App\Http\Controllers\Api;

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class LoginController
 * @package App\Http\Controllers\Api
 */
class LoginController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array|\Illuminate\Http\JsonResponse
	 */
	public function store(Request $request)
	{
		$this->validate($request,[
			'open_id'=>'required'
		]);
		$openId = $request->get('open_id', null);
		$user = User::where('open_id',$openId)->first();
		if (!$user) {
//			//注册
//			$this->validate($request, [
//				'app_id' => 'required',
//				'secret' => 'required',
//				'code' => 'required',
//				'grant_type' => 'required'
//			]);
//			$result = $this->getOpenId($request->get('app_id'), $request->get('secret'), $request->get('code'), $request->get('grant_type'));
//			$openId = $result['open_id'];
//			if(!$user = User::where('open_id',$openId)->first()){
				$user = User::create(['open_id' => $openId, 'password' => password_hash(123456, PASSWORD_DEFAULT),'nickname'=>$openId,'img'=>'http://cryptobox.oss-cn-shenzhen.aliyuncs.com/avarta']);
//			}
		}
//		else{
//			if (!$user = User::where('open_id',$openId)->first()) {
//				return fail('', '创建token失败,可能的原因是该用户尚未注册,请提供完整信息');
//			}
//		}

		$token = JWTAuth::fromUser($user);
		return success(compact('token','user'));
	}


	/**
	 * @param $appid
	 * @param $secret
	 * @param $jsCode
	 * @param $grantType
	 * @return mixed
	 * @throws \Exception
	 */
	public function getOpenId($appid, $secret, $jsCode, $grantType)
	{
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$jsCode&grant_type=$grantType";
		$res = json_decode(file_get_contents($url), true);
		if (isset($res['errcode'])) {
			throw new \Exception($res['errmsg']);
		}
		$res = $this->getUserInfo($res['open_id'], $res['access_token']);
		return $res;
	}

	/**
	 * @param $openId
	 * @param $accessToken
	 * @return mixed
	 * @throws \Exception
	 */
	public function getUserInfo($openId, $accessToken)
	{
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=$accessToken&openid=$openId";
		$res = json_decode(file_get_contents($url), true);
		if (isset($res['errcode'])) {
			throw new \Exception($res['errmsg']);
		}
		return $res;
	}
}
