<?php

/**
 * 解析接口文档注释类
 */
class ParseDocNote
{
    private $_uniqueNamespace;
    private $_tmpDir = '';
    private $_apiDirs = [];

    public function __construct($apiDirs)
    {
        $this->_apiDirs = $apiDirs;
        $this->_tmpDir = dirname(__DIR__) . '/tmp/';
    }

    /**
     * 读取注释
     */
    public function getNotes()
    {
        $notes = [];
        $newNotes = [];
        foreach ($this->_apiDirs as $title => $apiDir) {
            $trees = [];
            $this->listTrees($apiDir, $trees, 'php', true);
            $newTrees['fu'] = $trees; // 一级目录
            $this->processTrees($apiDir, $newTrees, $notes);
            $newNotes[$title] = $notes;
            unset($notes); // 置空
        }

        return $newNotes;
    }

    public function processTrees($apiDir, $trees, &$notes = [])
    {
        foreach ($trees as $k => $tree) {
            if (isset($tree['files'])) {
                foreach ($tree['files'] as $file) {
                    $this->_uniqueNamespace = 'a' . md5($file);
                    $notes[] = $this->parse($apiDir, $file);
                }
            }

            if (isset($tree['directories'])) {
                $this->processTrees($apiDir, $tree['directories'], $notes);
            }
        }
    }

    private function parse($apiDir, $file)
    {
        // 创建临时目录
        $saveTmpPath = $this->_tmpDir . md5($apiDir) . '/';
        @mkdir($saveTmpPath, 0777);

        $classContent = $this->filterClassContent(file_get_contents($file));
        file_put_contents($saveTmpPath . basename($file), $classContent);

        require_once $saveTmpPath . basename($file);

        if (!class_exists($this->_uniqueNamespace . '\\' . basename($file, '.php'))) {
            return false;
        }

        $reflection = new \ReflectionClass($this->_uniqueNamespace . '\\' . basename($file, '.php'));
        $classComment = $reflection->getDocComment();
        $classNotes = $this->parseNote($classComment);

        $methods = $reflection->getMethods();

        /**
         * @var \ReflectionMethod $method
         */
        $methodNotes = [];
        foreach ($methods as $method) {
            $methodComment = $method->getDocComment();

            $apiReturn = $this->processApiReturn($methodComment);
            $methodComment = $this->parseNote($methodComment);

            $methodComment['apiReturn'] = $apiReturn;

            $methodNotes[] = $methodComment;
        }

        return ['class_notes' => $classNotes, 'method_notes' => $methodNotes];
    }

    /**
     * 解析注释，返回以@后面的字符串为键的数组
     *
     * @param $docComment
     * @return array
     */
    private function parseNote($docComment)
    {
        $notes = [];
        preg_match_all("/\*.*/", $docComment, $matches);

        foreach ($matches[0] as $item) {
            if (!preg_match('/@([a-zA-Z0-9]*)/', $item, $result)) {
                continue;
            }

            $item = trim(str_replace(['*', $result[0]], '', $item));

            $notes[$result[1]][] = $item;
        }

        return $notes;
    }

    /**
     * 特殊处理apiReturn注释
     *
     * @param $comment
     * @return mixed
     */
    private function processApiReturn($comment)
    {
        if (($start = strpos($comment, '@apiReturn')) !== false) {
            //$comment = str_replace('@apiReturn', '', substr($comment, $start));
            preg_match("/@apiReturn[\s\S]*\}/", $comment, $match);
            $comment = str_replace('*', '', $match[0]);
            $comment = str_replace('@apiReturn', '', $comment);
        }

        return $comment;
    }

    private function filterClassContent($fileContent)
    {
        $fileContent = $this->filterNamespace($fileContent);
        $fileContent = $this->filterExtend($fileContent);
        $fileContent = $this->filterByTag($fileContent);

        return $fileContent;
    }

    private function filterNamespace($fileContent)
    {
        // 加入命名空间，防止类名相同导致的重复定义错误
        $fileContent = preg_replace("/[^\$]namespace\s+[a-zA-z0-9]+;?/", '', $fileContent);
        $namespace = "\r\nnamespace {$this->_uniqueNamespace};\r\n";
        $fileContent = str_replace('<?php', "<?php {$namespace}", $fileContent);

        return $fileContent;
    }

    private function filterExtend($fileContent)
    {
        if (($start = strpos($fileContent, 'extends')) !== false) {
            $end = strpos($fileContent, '{');
            $length = $end - $start;
            $fileContent = substr_replace($fileContent, '', $start, $length);
            return $fileContent;
        }

        return $fileContent;
    }

    private function filterByTag($fileContent)
    {
        if (($start = strpos($fileContent, 'class_start')) !== false) {
            $end = strpos($fileContent, 'class_end');
            if ($end === false) {
                die("缺少class_end标签");
            }
            $length = $end - $start;
            $fileContent = substr($fileContent, $start, $length);
            $fileContent = str_replace('class_start', '', $fileContent);

            return "<?php " . $fileContent . "\r\n" . '?>';
        }

        return $fileContent;
    }

    /**
     * 返回某个目录下所有文件的树形结构
     *
     * @param string $directoryName 目录名
     * @param array $tree 树形数组
     * @param string $extension 要取的文件后缀
     * @param boolean $isFullPath 是否返回文件的完整路径
     */
    private function listTrees($directoryName, &$tree = [], $extension = '', $isFullPath = false)
    {
        $iterator = new \DirectoryIterator($directoryName);
        while ($iterator->valid()) {
            $file = $iterator->current();

            if ($file->isDot()) {
                $iterator->next();
                continue;
            }

            if ($file->isDir()) {
                $this->listTrees($directoryName . $file->getFilename() . '/', $tree['directories'][$file->getFilename()], $extension, $isFullPath);
            } else {
                if ($file->getExtension() != $extension) {
                    $iterator->next();
                    continue;
                }

                $tree['files'][] = $isFullPath ? $directoryName . $file->getFilename() : $file->getFilename();
            }

            $iterator->next();
        }
    }
}