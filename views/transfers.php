<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content"><div class="panel_s"><div class="panel-body">
    <div class="tw-flex tw-items-center tw-justify-between"><h4><?php echo html_escape($title); ?></h4><a href="<?php echo admin_url('kt_inventory/transfer'); ?>" class="btn btn-primary"><?php echo _l('kt_inventory_create_transfer'); ?></a></div>
    <hr class="hr-panel-heading" />
    <div class="table-responsive"><table class="table table-striped"><thead><tr><th><?php echo _l('kt_inventory_code'); ?></th><th><?php echo _l('kt_inventory_from_warehouse'); ?></th><th><?php echo _l('kt_inventory_to_warehouse'); ?></th><th><?php echo _l('kt_inventory_date'); ?></th><th><?php echo _l('status'); ?></th><th><?php echo _l('kt_inventory_actions'); ?></th></tr></thead><tbody>
    <?php foreach ($transfers as $row) { ?><tr><td><?php echo html_escape($row['transfer_code']); ?></td><td><?php echo html_escape($row['from_warehouse_name']); ?></td><td><?php echo html_escape($row['to_warehouse_name']); ?></td><td><?php echo html_escape(_d($row['transfer_date'])); ?></td><td><span class="label label-<?php echo kt_inventory_status_badge_class($row['status']); ?>"><?php echo _l('kt_inventory_status_' . $row['status']); ?></span></td><td class="kt-inventory-table-actions"><a href="<?php echo admin_url('kt_inventory/transfer/' . $row['id']); ?>" class="btn btn-default btn-sm"><?php echo _l('kt_inventory_edit'); ?></a><?php if ($row['status'] === 'draft') { ?><a href="<?php echo admin_url('kt_inventory/post_transfer/' . $row['id']); ?>" class="btn btn-success btn-sm"><?php echo _l('kt_inventory_post'); ?></a><a href="<?php echo admin_url('kt_inventory/cancel_transfer/' . $row['id']); ?>" class="btn btn-warning btn-sm"><?php echo _l('kt_inventory_cancel'); ?></a><?php } ?></td></tr><?php } ?>
    <?php if (empty($transfers)) { ?><tr><td colspan="6"><?php echo _l('kt_inventory_no_records'); ?></td></tr><?php } ?>
    </tbody></table></div>
</div></div></div></div>
<?php init_tail(); ?>
