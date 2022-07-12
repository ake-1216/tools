<?php

namespace Ake\Tools\Laravel\Trait;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteDependencyResolverTrait as DepResolver;

#自定义路由用到，控制器中调用其他控制器
trait CallActionTrait
{
    use DepResolver;

    private $container;

    public function getUri(Request $request)
    {
        return explode('?', $request->getRequestUri())[0];
    }

    #调用别的控制器中的方法
    public function callControllerMethod(string $class, string $method, array $routeParameters = [])
    {
        #指定容器,不指定,如果有参数传递时,会报错
        $this->container = app();

        #获取控制器完整路径
        $controller = $this->namespace . '\\' . $class;
        try {
            #实例化控制器
            $instance = app()->make($controller);
            #获取并组装参数
            $parameters = $this->resolveClassMethodDependencies($routeParameters, $instance, $method);
            #调用
            return $instance->callAction($method, $parameters);
        }catch (\Exception $exception){
            return abort(404);
        }
    }
}
