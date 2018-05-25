<?php

/**
 *
 * @author: 洪涛
 * @date: 2018/1/25
 */
class Generate
{
    private $_notes = [];

    public function __construct($notes)
    {
        $this->_notes = $notes;
    }

    public function render($view)
    {
        $projects = $this->_notes;

        $jsNodes = [];

        foreach ($projects as $title => $project) {
            $children = [];
            $i = 0;
            foreach ($project as $note) {
                // 三级
                $methodNotes = [];
                foreach ($note['method_notes'] as $methodNote) {
                    $methodNotes[] = [
                        'name' => $methodNote['api_title'],
                        'id' => $methodNote['unique_id'],
                        //'spread' => true,
                    ];
                }

                // 二级
                $classApiTitle = $note['class_notes']['api_title'];

                $j = 0;
                foreach ($methodNotes as $k => $v) {
                    $methodNotes[$k]['route'] = urlencode($title) . '|' . $i . '|' . $j;
                    $j++;
                }

                $children[] = [
                    'name' => $classApiTitle,
                    'children' => $methodNotes,
                    //'spread' => true,
                ];

                $i++;
            }

            // 一级
            $jsNodes[] = [
                'name' => $title,
                'children' => $children,
                'spread' => true,
            ];
        }
        $json = json_encode($jsNodes);

        require_once $view;
    }
}