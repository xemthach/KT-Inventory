<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if ($edit_batch) { ?>
                            <h4><?php echo html_escape(_l('kt_inventory_batch_edit')); ?></h4>
                            <p class="text-muted"><?php echo html_escape($edit_batch['item_name']); ?> (SKU: <?php echo html_escape($edit_batch['sku']); ?>)</p>
                            <hr class="hr-10" />
                            
                            <?php echo form_open(admin_url('kt_inventory/batches/' . $edit_batch['id'])); ?>
                            
                            <div class="form-group">
                                <label for="lot_number" class="control-label"><?php echo _l('kt_inventory_lot_number'); ?></label>
                                <input type="text" id="lot_number" class="form-control" value="<?php echo html_escape($edit_batch['lot_number']); ?>" readonly disabled />
                            </div>

                            <div class="form-group">
                                <label for="qc_status" class="control-label"><?php echo _l('kt_inventory_qc_status'); ?></label>
                                <select name="qc_status" id="qc_status" class="form-control selectpicker" required>
                                    <?php foreach ($qc_statuses as $key => $label) { ?>
                                        <option value="<?php echo html_escape($key); ?>" <?php echo $edit_batch['qc_status'] === $key ? 'selected' : ''; ?>>
                                            <?php echo html_escape($label); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <?php echo render_date_input('expiry_date', 'kt_inventory_expiry_date', $edit_batch['expiry_date'] ?? ''); ?>
                            <?php echo render_date_input('manufacturing_date', 'kt_inventory_manufacturing_date', $edit_batch['manufacturing_date'] ?? ''); ?>

                            <button type="submit" class="btn btn-primary"><?php echo _l('kt_inventory_batch_save'); ?></button>
                            <a href="<?php echo admin_url('kt_inventory/batches'); ?>" class="btn btn-default"><?php echo _l('kt_inventory_cancel'); ?></a>
                            
                            <?php echo form_close(); ?>
                        <?php } else { ?>
                            <div class="text-center padding-30">
                                <i class="fa-solid fa-file-invoice text-muted" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                                <p class="text-muted">Chọn một Lô hàng từ bảng bên phải để bắt đầu kiểm duyệt chất lượng (QC) hoặc thay đổi hạn dùng.</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo html_escape($title); ?></h4>
                        <hr class="hr-10" />
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('kt_inventory_item'); ?></th>
                                        <th><?php echo _l('kt_inventory_lot_number'); ?></th>
                                        <th><?php echo _l('kt_inventory_expiry_date'); ?></th>
                                        <th><?php echo _l('kt_inventory_qc_status'); ?></th>
                                        <th>Tổng Tồn</th>
                                        <th><?php echo _l('kt_inventory_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($batches as $batch) { ?>
                                        <tr>
                                            <td>
                                                <span class="font-medium"><?php echo html_escape($batch['item_name']); ?></span>
                                                <div class="text-muted text-xs">SKU: <?php echo html_escape($batch['sku']); ?></div>
                                            </td>
                                            <td class="bold font-medium"><?php echo html_escape($batch['lot_number']); ?></td>
                                            <td>
                                                <?php 
                                                if (!empty($batch['expiry_date'])) {
                                                    $is_expired = strtotime($batch['expiry_date']) < time();
                                                    $is_near_expiry = strtotime($batch['expiry_date']) < strtotime('+6 months') && !$is_expired;
                                                    
                                                    $date_class = '';
                                                    if ($is_expired) {
                                                        $date_class = 'text-danger bold';
                                                    } elseif ($is_near_expiry) {
                                                        $date_class = 'text-warning bold';
                                                    }
                                                    
                                                    echo '<span class="' . $date_class . '">' . _d($batch['expiry_date']) . '</span>';
                                                    if ($is_expired) {
                                                        echo ' <span class="label label-danger">Hết hạn</span>';
                                                    } elseif ($is_near_expiry) {
                                                        echo ' <span class="label label-warning">Cận date</span>';
                                                    }
                                                } else {
                                                    echo '<span class="text-muted">N/A</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $qc_class = 'default';
                                                $qc_label = $batch['qc_status'];
                                                if ($batch['qc_status'] === 'released') {
                                                    $qc_class = 'success';
                                                    $qc_label = 'Released';
                                                } elseif ($batch['qc_status'] === 'quarantine') {
                                                    $qc_class = 'warning';
                                                    $qc_label = 'Quarantine';
                                                } elseif ($batch['qc_status'] === 'blocked') {
                                                    $qc_class = 'danger';
                                                    $qc_label = 'Blocked';
                                                }
                                                echo '<span class="label label-' . $qc_class . '">' . $qc_label . '</span>';
                                                ?>
                                            </td>
                                            <td class="bold"><?php echo html_escape(app_format_number($batch['total_quantity'])) . ' ' . html_escape($batch['unit']); ?></td>
                                            <td>
                                                <a href="<?php echo admin_url('kt_inventory/batches/' . $batch['id']); ?>" class="btn btn-default btn-xs">
                                                    <i class="fa fa-edit"></i> <?php echo _l('kt_inventory_edit'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php if (empty($batches)) { ?>
                                        <tr><td colspan="6" class="text-center"><?php echo _l('kt_inventory_no_records'); ?></td></tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
