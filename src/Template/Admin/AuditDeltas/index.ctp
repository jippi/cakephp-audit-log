<?php
/**
 * @var \App\View\AppView $this
 * @var \AuditLog\Model\Entity\AuditDelta[] $auditDeltas
 */
?>
<div class="panel panel-default">
  <div class="panel-heading">
    <?= __('Audit Log')?>
  </div>
  <div class="panel-body">
    <table class="table table-striped table-condensed bootstrap-datatable datatable">
        <thead>
        <tr>
            <th><?= $this->Paginator->sort('Audits.created');?></th>
            <th>Resource</th>
            <th><?= $this->Paginator->sort('AuditDeltas.property_name');?></th>
            <th><?= $this->Paginator->sort('AuditDeltas.old_value');?></th>
            <th><?= $this->Paginator->sort('AuditDeltas.new_value');?></th>
        </tr>
        </thead>
        <tbody>
    <?php foreach ($auditDeltas as $delta): ?>
        <tr>
            <td class='center'><?=(
                $delta->audit->created
            )?>&nbsp;</td>
            <td class='center'><?= $this->Html->link(
                $delta->audit->model . ' # ' . $delta->audit->entity_id,
                [
                    'controller' => 'audits',
                    'action' => 'index',
                    '?' => [
                        'model' => $delta->audit->model,
                        'entity_id' => $delta->audit->entity_id
                    ]
                ]
            ); ?>&nbsp;</td>
            <td class='center'><?=
                h($delta->property_name)
            ?>&nbsp;</td>
            <td class='center'><?=
                $this->AuditLog->outputValue(
                    $delta->old_value
                )
            ?>&nbsp;</td>
            <td class='center'><?=
                $this->AuditLog->outputHtmlValue(
                    $delta->new_value
                )
            ?>&nbsp;</td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
    <?= $this->Paginator->numbers([
        'prev' => true,
        'next' => true
    ]) ?>
    <p><?= $this->Paginator->counter() ?></p>
    </div>
  </div>
</div>
