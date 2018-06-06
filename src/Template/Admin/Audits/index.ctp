<?php
/**
 * @var \App\View\AppView $this
 * @var \AuditLog\Model\Table\AuditsTable[] $audits
 */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo __('Audits') ?>
    </div>
    <div class="panel-body">
        <table class="table table-striped" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('event'); ?></th>
                <th><?php echo $this->Paginator->sort('source_id', 'By'); ?></th>
                <th><?php echo $this->Paginator->sort('model', 'Resource'); ?></th>
                <th><?php echo $this->Paginator->sort('entity_id', 'Identifier'); ?></th>
                <th><?php echo $this->Paginator->sort('delta_count', 'Changes'); ?></th>
                <th><?php echo $this->Paginator->sort('created'); ?></th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($audits as $audit): ?>
                <tr>
                    <td><?php echo $this->AuditLog->getEvent($audit); ?></td>
                    <td>
                        <?php
                        echo $this->Html->link($this->AuditLog->getSource($audit), [
                            'action' => 'index',
                            '?' => [
                                'source_id' => $audit->source_id
                            ]
                        ]);
                        ?>&nbsp;
                    </td>
                    <td>
                        <?php
                        echo $this->Html->link(__($audit->model), [
                            'action' => 'index',
                            '?' => [
                                'model' => $audit->model
                            ]
                        ]);
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $this->Html->link($this->AuditLog->getIdentifier($audit), [
                            'action' => 'index',
                            '?' => [
                                'model' => $audit->model,
                                'entity_id' => $audit->entity_id
                            ]
                        ]);
                        ?>
                    </td>
                    <td><?php echo $this->Number->format($audit->delta_count) ?></td>
                    <td>
                        <span title="<?php echo $audit->created; ?>">
                            <?php echo $this->Time->timeAgoInWords($audit->created) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <?php
                        echo $this->Html->link(
                            __('View'),
                            [
                                'action' => 'view',
                                $audit->id
                            ],
                            [
                                'title' => __('View'),
                                'class' => 'btn btn-default glyphicon glyphicon-eye-open'
                            ]
                        );
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="paginator">
            <?php echo $this->Paginator->numbers([
                'prev' => true,
                'next' => true
            ]) ?>
            <p><?php echo $this->Paginator->counter() ?></p>
        </div>
    </div>
</div>
