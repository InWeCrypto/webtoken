<?php
/**
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/5/18
 * Time: 下午4:36
 *
 * 结果码对照表
 */

/**
 *  4000    请求执行成功
 */
define('SUCCESS', 4000);
/**
 *4001    未登陆
 */
define('NOT_LOGIN', 4001);
/**
 *4002    无权限
 */
define('NOT_PERMISSION', 4002);
/**
 *4003    路由不存在
 */
define('NOT_FIND_METHOD', 4003);
/**
 *4004    验证不通过
 */
define('NOT_VALIDATED', 4004);
/**
 *4005    查询数据不存在
 */
define('NOT_FIND_RECORD', 4005);
/**
 *4006    请求执行失败
 */
define('FAIL', 4006);
/**
 *4007    请求执行成功,即将跳转
 */
define('WILL_REDIRECT', 4007);
/**
 *4008    未注册
 */
define('NOT_REGISTER', 4008);
/**
 *4009    token过期
 */
define('INVALID_TOKEN', 4009);

