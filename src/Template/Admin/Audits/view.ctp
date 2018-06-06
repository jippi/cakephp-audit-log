<?php
/**
 * @var \App\View\AppView $this
 * @var \AuditLog\Model\Entity\Audit $audit
 */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo __('Audit Log') ?>
    </div>
    <div class="panel-body">
        <dl class="dl-horizontal">
            <dt><?php echo __('Id'); ?></dt>
            <dd><?php echo h($audit->id) ?></dd>

            <dt><?php echo __('Event type'); ?></dt>
            <dd><?php echo h($audit->event) ?></dd>

            <dt><?php echo __('Model'); ?></dt>
            <dd><?php echo h($audit->model) ?></dd>

            <dt><?php echo __('Model id'); ?></dt>
            <dd><?php echo h($audit->entity_id) ?></dd>

            <dt><?php echo __('Description'); ?></dt>
            <dd><?php echo h($audit->description) ?></dd>

            <dt><?php echo __('Source Id'); ?></dt>
            <dd><?php echo h($audit->source_id) ?></dd>

            <dt><?php echo __('Source Ip'); ?></dt>
            <dd><?php echo h($audit->source_ip) ?></dd>

            <dt><?php echo __('Source Url'); ?></dt>
            <dd><?php echo h($audit->source_url) ?></dd>

            <dt><?php echo __('Deltas'); ?></dt>
            <dd><?php echo
                $this->Number->format($audit->delta_count)
                ?></dd>

            <dt><?php echo __('Created'); ?></dt>
            <dd><?php echo $audit->created ?></dd>
        </dl>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <i class="halflings-icon list"></i><span class="break"></span><?php echo __('Categorize Logs'); ?>
    </div>
    <div class="panel-body">
        <?php if (!empty($audit->audit_deltas)): ?>
            <dl class="dl-horizontal">
                <?php foreach ($audit->audit_deltas as $auditDeltas): ?>
                    <dt><?php echo __('Field'); ?></dt>
                    <dd><?php echo h($auditDeltas->property_name) ?></dd>
                    <dt><?php echo __('Diff') ?></dt>
                    <dd><?php echo $this->AuditLog->getDiff(
                            $auditDeltas->property_name,
                            $auditDeltas->old_value,
                            $auditDeltas->new_value
                        ) ?>
                    </dd>
                <?php endforeach; ?>
            </dl>
        <?php endif; ?>
    </div>
</div>
<style type="text/css">
    del {
        background-color: #f2dede;
    }

    ins {
        color: #3c763d;
        background-color: #dff0d8;
        text-decoration: none;
        border: 1px solid #d6e9c6;
    }

    table.Differences {
        width: 100%;
        font-weight: normal;
    }

    table.Differences th,
    table.Differences td,
    .table tbody tbody {
        border-top: none;
        font-weight: normal;
    }

    td.Left {
        width: 40%;
        margin-right: 0px;
    }

    td.Right {
        width: 40%;
        margin-left: 5px;
    }

    tbody.ChangeInsert {
        background-color: #dff0d8;
    }

    tbody.ChangeDelete {
        background-color: #f2dede;
    }

    tbody.ChangeReplace {
        background-color: #fcf8e3;
    }

    tbody.ChangeInsert th,
    tbody.ChangeDelete th,
    tbody.ChangeReplace th {
        width: 2%;
        background-color: #eee;
        text-align: center;
    }

    tbody.ChangeInsert td,
    tbody.ChangeDelete td,
    tbody.ChangeReplace td {
        width: 48%;
    }

</style>
