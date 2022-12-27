<?php

namespace Ake\Tools\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class DcatMake extends GeneratorCommand
{
    #命令
    protected $signature = 'ake:make';

    #描述
    protected $description = 'dcat后台make命令，用来创建控制器，仓库，翻译文件等';

    protected $arr = [
        '控制器', '数据仓库', '翻译文件'
    ];

    #创建类型
    protected $genre;

    #创建文件名
    protected $base_name;

    public function handle()
    {
        $type = $this->choice('请选择要创建的文件类型', $this->arr,0);
        #设置成功提示文字
        $this->type = $type;
        #循环改为下标
        $type = array_search($type, $this->arr);
        $this->genre = $type;
        $name = $this->ask('文件名字为？');
        $this->base_name = $name;
        return parent::handle();
    }

    #构建class
    protected function qualifyClass($name)
    {
        #如果是创建翻译文件，则直接返回名字
        if ($this->genre == 2) {
            return $name;
        }
        return parent::qualifyClass($name);
    }

    #生成内容
    public function buildClass($name)
    {
        $stub = parent::buildClass($name);

        if ($this->genre == 1) {
            $stub = $this->replaceModel($stub, $name);
        }

        $base = $this->ask('复制文件名字？（不填为不复制）');

        return $base ? $this->replaceCopy($base) : $stub;
    }

    #替换model
    private function replaceModel($stub, $model)
    {
        $modelClass = $this->buildModel($model);

        $replace = [
            'DummyFullModelClass' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{model}}' => $modelClass,
        ];

        return str_replace(
            array_keys($replace), array_values($replace), $stub
        );
    }

    #构建model名
    private function buildModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new \InvalidArgumentException('模型名称包含无效字符。');
        }
        $model = str_replace('App\Admin\Repositories\\', '', $model);
        return $this->qualifyModel($model);
    }

    #判断是否是复制
    protected function replaceCopy($name)
    {
        $path = $this->copyPath($name);
        return $this->files->get($path);
    }

    #获取默认的namespace
    protected function getDefaultNamespace($name = '')
    {
        switch ($this->genre){
            case 0:
                $path = 'App\Admin\Controllers';
                break;
            case 1:
                $path = 'App\Admin\Repositories';
                break;
            case 2:
                $path = 'resources\lang\zh_CN';
                break;
            default:
                throw new \InvalidArgumentException('数据错误');
        }
        return $path;
    }

    #复制地址
    protected function copyPath($name = '')
    {
        switch ($this->genre){
            case 0:
                $path = $this->path('app/Admin/Controllers/' . $name . 'Controller.php');
                break;
            case 1:
                $path = $this->path('app/Admin/Repositories/' . $name . '.php');
                break;
            case 2:
                $path = $this->path('resources/lang/zh_CN/' . $name . '.php');
                break;
            default:
                throw new \InvalidArgumentException('数据错误');
        }
        return str_replace("\\", "/" , $path);
    }

    #存根文件地址
    protected function getStub()
    {
        switch ($this->genre){
            case 0:
                $path = $this->stubPath('stubs/admin/controller.stub');
                break;
            case 1:
                $path  = $this->stubPath('stubs/admin/repository.stub');
                break;
            case 2:
                $path = $this->stubPath('stubs/admin/lang.stub');
                break;
            default:
                throw new \InvalidArgumentException('数据错误');
        }
        return $path;
    }

    #获取创建名字
    protected function getNameInput()
    {
        return trim($this->base_name);
    }

    #获取地址
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        switch ($this->genre){
            case 0:
                $path = $this->laravel['path'].'/'.str_replace('\\', '/', $name).'Controller.php';
                break;
            case 1:
                $path = $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
                break;
            case 2:
                $path = $this->path('resources/lang/zh_CN/' . $name . '.php');
                break;
            default:
                throw new \InvalidArgumentException('数据错误');
        }
        return $path;
    }

    private function path($path)
    {
        return base_path($path);
    }

    #生成完整地址
    private function stubPath($path)
    {
        return __DIR__ . '/' . $path;
    }
}
