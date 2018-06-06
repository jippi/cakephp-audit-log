<?php

namespace AuditLog\View\Helper;

use Cake\View\Helper;

/**
 * AuditLog helper
 */
class AuditLogHelper extends Helper
{
    public $helpers = ['Text'];

    public function getEvent($item)
    {
        switch (strtolower($item->event)) {
            case 'create'    :
                return '<span class="label label-success">create</span>';
            case 'edit'        :
                return '<span class="label label-warning">edit</span>';
            case 'delete'    :
                return '<span class="label label-danger">delete</span>';
            default            :
                return '<span class="label label-default">' . $item->event . '</span>';
        }
    }

    public function getSourceId($item)
    {
        return $item->source_id;
    }

    public function getSource($item)
    {
        return $item->source_id;
    }

    public function outputValue($value)
    {
        if (is_array($value)) {
            $output = '<dl class="dl-horizontal">';
            foreach ($value as $key => $val) {
                $output .= sprintf(
                    '<dt>%s<dt/><dd>%s</dd>',
                    $key,
                    $this->outputValue($val)
                );
            }

            return $output . '</dl>';
        }
        if ($value === NULL) {
            return 'NULL';
        }

        if ($value === '') {
            return '(EMPTY)';
        }

        return h($this->Text->truncate($value, 50));
    }

    public function outputHtmlValue($value)
    {
        if (is_array($value)) {
            $output = '<dl class="dl-horizontal">';
            foreach ($value as $key => $val) {
                $output .= sprintf(
                    '<dt>%s<dt/><dd>%s</dd>',
                    $key,
                    $this->outputValue($val)
                );
            }

            return $output . '</dl>';
        }

        return sprintf(
            '<span title="%s">%s</span>',
            $value,
            $this->outputValue($value)
        );
    }

    public function getIdentifier($item)
    {
        if (empty($this->_View->viewVars['model'])) {
            $name = $item['entity_id'];
        } else {
            $model = $this->_View->viewVars['model'];
            $displayField = $this->_View->viewVars['displayField'];

            $name = $item[$model][$displayField];
        }

        return $this->Text->truncate($name, 50);
    }

    public function getDiff($field, $new, $old)
    {
        $config = [
            'ignoreNewLines' => true,
            'ignoreWhitespace' => true,
            'ignoreCase' => true,
        ];

        $diff = new \Diff((array)$old, (array)$new, $config);

        return $diff->render(new \Diff_Renderer_Html_SideBySide());
    }

}
