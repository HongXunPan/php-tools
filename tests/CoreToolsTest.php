<?php

use HongXunPan\Tools\Backtrace\BacktraceHelper;
use HongXunPan\Tools\Cli\Progress;
use HongXunPan\Tools\File\GetDirFiles;
use HongXunPan\Tools\Performance\Performance;
use HongXunPan\Tools\TreeAndList\List2Tree;
use HongXunPan\Tools\TreeAndList\Tree2List;
use HongXunPan\Tools\Validate\Validator;

function testValidatorRepairs()
{
    $result = Validator::validate(
        ['status' => 'invalid', 'nullable' => null],
        ['status' => 'required|in:["enabled"]', 'nullable' => 'notnull']
    );

    assertSameValue(2, $result['count'], '校验错误数量不正确');
    assertTrueValue(!isset($result['validated_data']['status']), '后续规则失败的字段不应进入 validated_data');
    assertTrueValue(!isset($result['validated_data']['nullable']), '空值字段不应进入 validated_data');

    $timeResult = Validator::validate(
        ['created_at' => '2026-07-25 12:30:00'],
        ['created_at' => 'timeFormat:Y-m-d H:i:s']
    );
    assertSameValue(0, $timeResult['count'], '包含冒号的规则参数解析失败');
}

function testFileScanner()
{
    $directory = makeTestDirectory('files');
    mkdir($directory . DIRECTORY_SEPARATOR . 'nested', 0777, true);
    file_put_contents($directory . DIRECTORY_SEPARATOR . 'root.txt', 'root');
    file_put_contents($directory . DIRECTORY_SEPARATOR . 'nested' . DIRECTORY_SEPARATOR . 'child.txt', 'child');

    try {
        $all = GetDirFiles::getFilesByPath($directory);
        assertSameValue(2, count($all), '全量目录扫描结果错误');

        $files = GetDirFiles::getFilesByPath($directory, false, GetDirFiles::GET_TYPE_FILE_ONLY);
        assertSameValue(2, count($files), '仅文件扫描没有递归返回全部文件');
        assertSameValue(GetDirFiles::TYPE_FILE, $files[0]['type'], '仅文件扫描返回了目录');

        $directories = GetDirFiles::getFilesByPath($directory, false, GetDirFiles::GET_TYPE_DIR_ONLY);
        assertSameValue(1, count($directories), '仅目录扫描结果错误');
        assertSameValue('nested', $directories[0]['name'], '目录名称错误');
    } finally {
        removeTestDirectory($directory);
    }
}

function testTreeConversion()
{
    $list = [
        ['id' => 1, 'parent_id' => 0, 'name' => '根'],
        ['id' => 2, 'parent_id' => 1, 'name' => '子'],
        ['id' => 3, 'parent_id' => 2, 'name' => '孙'],
    ];
    $builder = new List2Tree();
    $tree = $builder->buildTree($list)->tree;

    assertSameValue(1, count($tree), '树根数量错误');
    assertSameValue(2, $tree[0]['children'][0]['id'], '子节点挂载错误');
    assertSameValue(3, $tree[0]['children'][0]['children'][0]['id'], '孙节点挂载错误');

    $listBuilder = new Tree2List();
    $flat = $listBuilder->buildByChildren($tree)->list;
    assertSameValue(3, count($flat), '树转列表数量错误');
    assertSameValue(2, $flat[2]['parent'], '树转列表父节点错误');
}

function testBacktraceAndPerformance()
{
    $caller = BacktraceHelper::getCaller(
        [
            ['file' => '/project/vendor/package.php'],
            ['file' => '/project/app/service.php'],
        ]
    );
    assertSameValue('/project/app/service.php', $caller['file'], '回溯忽略路径失效');

    $diff = Performance::diffPerformance(
        [
            'time' => 1.0,
            'memory' => 100,
            'peak_memory' => 200,
            'cpu' => ['ru_utime.tv_usec' => 10],
            'files' => 1,
        ],
        [
            'time' => 1.5,
            'memory' => 200,
            'peak_memory' => 300,
            'cpu' => ['ru_utime.tv_usec' => 25],
            'files' => 3,
        ]
    );
    assertSameValue(0.5, $diff['time'], '耗时差值错误');
    assertSameValue(15, $diff['cpu']['ru_utime.tv_usec'], 'CPU 差值错误');
    assertSameValue(2, $diff['files'], '加载文件数量差值错误');
}

function testProgressBoundary()
{
    ob_start();
    Progress::echoCliProgress(1, 0);
    $output = ob_get_clean();

    assertTrueValue(strpos($output, '0.00%') !== false, '总数为零时进度输出错误');
}

return [
    'Validator 逻辑修复' => 'testValidatorRepairs',
    '目录扫描' => 'testFileScanner',
    '树与列表转换' => 'testTreeConversion',
    '回溯与性能计算' => 'testBacktraceAndPerformance',
    'CLI 进度边界' => 'testProgressBoundary',
];
