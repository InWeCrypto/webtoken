<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		\Illuminate\Auth\AuthenticationException::class,
		\Illuminate\Auth\Access\AuthorizationException::class,
		\Symfony\Component\HttpKernel\Exception\HttpException::class,
		\Illuminate\Database\Eloquent\ModelNotFoundException::class,
		\Illuminate\Session\TokenMismatchException::class,
		\Illuminate\Validation\ValidationException::class,
	];

	/**
	 * @var array
	 *
	 */
	private $ignore = [
		'api', 'back'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception $exception
	 * @return void
	 */
	public function report(Exception $exception)
	{
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Exception $exception
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $exception)
	{
		if ($request->ajax() || in_array(strtolower(explode('/', $request->path())[0]), $this->ignore)) {
			if ($exception instanceof MethodNotAllowedHttpException) {
				return response(['msg' => '方法不存在', 'code' => NOT_FIND_METHOD, 'data' => []]);
			} else if ($exception instanceof ValidationException) {
				foreach ($exception->validator->errors()->getMessages() as $k => $v) {
					$data[$k] = $v[0];
				}
				return response(['msg' => array_values($data)[0], 'code' => NOT_VALIDATED, 'data' => $data]);
			} else if ($exception instanceof ModelNotFoundException) {
				return response(['msg' => '查询数据不存在', 'code' => NOT_FIND_RECORD, 'data' => []]);
			} else if ($exception instanceof NotFoundHttpException) {
				return response(['msg' => '路由不存在', 'code' => NOT_FIND_RECORD, 'data' => []]);
			} else {
				return response(['msg' => $exception->getMessage(), 'code' => $exception->getCode()?$exception->getCode():FAIL, 'data' => []]);
			}

		}
		return parent::render($request, $exception);
	}

	/**
	 * Convert an authentication exception into an unauthenticated response.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Illuminate\Auth\AuthenticationException $exception
	 * @return \Illuminate\Http\Response
	 */
	protected function unauthenticated($request, AuthenticationException $exception)
	{
		if ($request->expectsJson()) {
			return response()->json(['error' => 'Unauthenticated.'], 401);
		}

		return redirect()->guest('login');
	}
}
