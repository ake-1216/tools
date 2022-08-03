<?php

namespace Ake\Tools\Test;

class Paginator
{
    #总记录数
    protected $itemCount;

    #每页多少条记录
    protected $perPageCount;

    #当前页页码
    protected $currentPage;

    #分页按钮中间显示的数量
    protected $pageRange;

    #当前页面链接
    protected $baseUrl;

    #页码变量
    protected $pageKey;

    #总页数
    protected $totalPage;

    public function __construct($total, $perPage = 10, $pageRange = 5)
    {
        #设置页码变量
        $this->setPageKey('page');

        #设置总数量
        $this->setItemCount($total);

        #设置每页多少个数量
        $this->setPerPageCount($perPage);

        #设置当前页面页码
        $this->setCurrentPage();

        $this->setPageRange($pageRange);

        # 获取请求的url
        $this->setBaseUrl($_SERVER['REQUEST_URI']);
    }

    #设置页码变量
    public function setPageKey($pageKey)
    {
        $this->pageKey = $pageKey;

        return $this;
    }

    #设置总数量
    public function setItemCount($count)
    {
        $this->itemCount = $count;

        return $this;
    }

    #设置每页多少个数量
    public function setPerPageCount($count)
    {
        $this->perPageCount = $count;
        return $this;
    }

    #设置总页数
    public function setTotalPage()
    {
        $this->totalPage = (int) ceil($this->getItemCount()/$this->getPerPageCount());
        return $this;
    }

    #获取总页数
    public function getTotalPage()
    {
        return $this->totalPage ? : $this->setTotalPage() && $this->totalPage;
    }

    #获取每页多少个数量
    public function getPerPageCount()
    {
        return $this->perPageCount;
    }

    #设置当前页页码
    public function setCurrentPage()
    {
        $page = (int) isset($_GET[$this->pageKey])? $_GET[$this->pageKey] : 1 ;

        $this->currentPage = $page <= $this->getTotalPage() ? $page : $this->getLastPage();

        return $this;
    }

    #设置按钮数量
    public function setPageRange($range)
    {
        $this->pageRange = $range;

        return $this;
    }

    #设置 url
    public function setBaseUrl($url)
    {
        $template = '';

        $urls = parse_url($url);
        $template .= empty($urls['scheme']) ? '' : $urls['scheme'] . '://';
        $template .= empty($urls['host']) ? '' : $urls['host'];
        $template .= empty($urls['path']) ? '' : $urls['path'];

        if (isset($urls['query'])) {
            parse_str($urls['query'], $queries);
            $queries[$this->pageKey] = '..page..';
        } else {
            $queries = array($this->pageKey => '..page..');
        }
        $template .= '?' . http_build_query($queries);

        $this->baseUrl = $template;
    }

    #获取当前 url
    public function getPageUrl($page)
    {
        return str_replace('..page..', $page, $this->baseUrl);
    }

    #分页按钮中间显示的数量
    public function getPageRange()
    {
        return $this->pageRange
            ;
    }

    #获取当前页页码
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    #获取首页页面
    public function getFirstPage()
    {
        return 1;
    }

    #获取最后尾页页码
    public function getLastPage()
    {
        return ceil($this->itemCount / $this->perPageCount);
    }

    #获取上一页页码
    public function getPreviousPage()
    {
        return $diff = ($this->getCurrentPage() - 1) > 0 ? : $this->getFirstPage();
    }

    #获取下一页页码
    public function getNextPage()
    {
        return $this->getCurrentPage() < $this->getLastPage() ? $this->getCurrentPage() + 1 : $this->getLastPage();
    }

    #获取 sql 分页起始位
    public function getOffsetCount()
    {
        return ($this->getCurrentPage() - 1) * $this->perPageCount;
    }

    #获取总数量
    public function getItemCount()
    {
        return $this->itemCount;
    }

    public function getPages()
    {
        $previousRange = floor($this->getPageRange() / 2); // 获取按钮前半部分数量
        $nextRange = $this->getPageRange() - $previousRange - 1; // 获取按钮前后部分数量

        $start = $this->getCurrentPage() - $previousRange;
        $start = $start <= 0 ? 1 : $start;  // 计算出中间按钮的初始值

        $end = $this->getCurrentPage() + $nextRange;

        $end = $end > $this->getLastPage() ? $this->getLastPage() : $end; // 计算出末尾按钮的初始值

        if ($this->getLastPage() - $this->getPageRange() <= $this->getFirstPage() - 1){ // 当首页和尾页在页面显示范围内
            $start = $this->getFirstPage();
            $end = $this->getLastPage();
        } elseif ($this->getLastPage() - $previousRange < $this->getCurrentPage()) { // 当 当前页在尾页的后半部分
            $start = $end - $this->getPageRange() + 1;
        } elseif ($this->getCurrentPage() < $this->getFirstPage() + $previousRange) { // 当 当前页在首页的前半部分
            $end = $start + $this->getPageRange() - 1;
        }

        $pages = range($start, $end);

        return $pages;
    }

//    public static function toArray($request, $total, $perPage = 20, $pageRange = 5)
//    {
//        $paginator = new self($request, $total, $perPage, $pageRange);
//
//        return [
//            'firstPage' => $paginator->getFirstPage(), // 首页
//            'currentPage' => $paginator->getCurrentPage(), // 当前页
//            'firstPageUrl' => $paginator->getPageUrl($paginator->getFirstPage()),  // 首页路由
//            'previousPageUrl' => $paginator->getPageUrl($paginator->getPreviousPage()),
//            'pages' => $paginator->getPages(),
//            'pageUrls' => array_map(function ($page) use ($paginator) { return $paginator->getPageUrl($page); }, $paginator->getPages()),
//            'lastPageUrl' => $paginator->getPageUrl($paginator->getLastPage()),
//            'lastPage' => $paginator->getLastPage(),
//            'nextPageUrl' => $paginator->getPageUrl($paginator->getNextPage()),
//        ];
//    }
}