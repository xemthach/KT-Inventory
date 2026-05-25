<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"><div class="content">
    <div class="panel_s"><div class="panel-body">
        <h4><?php echo html_escape($title); ?></h4>
        <?php echo form_open(admin_url('kt_inventory/reports'), ['method' => 'get']); ?>
        <div class="row">
            <div class="col-md-3"><select name="warehouse_id" class="form-control selectpicker"><option value=""><?php echo _l('kt_inventory_warehouse'); ?></option><?php foreach ($warehouses as $warehouse) { ?><option value="<?php echo (int) $warehouse['id']; ?>" <?php echo ((int) ($filters['warehouse_id'] ?? 0) === (int) $warehouse['id']) ? 'selected' : ''; ?>><?php echo html_escape($warehouse['warehouse_name']); ?></option><?php } ?></select></div>
            <div class="col-md-3"><input type="date" name="date_from" value="<?php echo html_escape($filters['date_from'] ?? ''); ?>" class="form-control"></div>
            <div class="col-md-3"><input type="date" name="date_to" value="<?php echo html_escape($filters['date_to'] ?? ''); ?>" class="form-control"></div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary"><?php echo _l('kt_inventory_filters'); ?></button></div>
        </div>
        <?php echo form_close(); ?>
    </div></div>

    <div class="panel_s"><div class="panel-body">
        <h4><?php echo _l('kt_inventory_reports_current_stock'); ?></h4>
        <div class="table-responsive"><table class="table table-striped"><thead><tr><th><?php echo _l('kt_inventory_warehouse'); ?></th><th><?php echo _l('kt_inventory_sku'); ?></th><th><?php echo _l('kt_inventory_name'); ?></th><th><?php echo _l('kt_inventory_available_stock'); ?></th></tr></thead><tbody>
        <?php foreach ($balances as $row) { ?><tr><td><?php echo html_escape($row['warehouse_name']); ?></td><td><?php echo html_escape($row['sku']); ?></td><td><?php echo html_escape($row['name']); ?></td><td><?php echo html_escape($row['available_quantity']); ?></td></tr><?php } ?>
        <?php if (empty($balances)) { ?><tr><td colspan="4"><?php echo _l('kt_inventory_no_records'); ?></td></tr><?php } ?>
        </tbody></table></div>
    </div></div>

    <div class="panel_s"><div class="panel-body">
        <h4><?php echo _l('kt_inventory_reports_low_stock'); ?></h4>
        <div class="table-responsive"><table class="table table-striped"><thead><tr><th><?php echo _l('kt_inventory_warehouse'); ?></th><th><?php echo _l('kt_inventory_sku'); ?></th><th><?php echo _l('kt_inventory_name'); ?></th><th><?php echo _l('kt_inventory_available_stock'); ?></th><th><?php echo _l('kt_inventory_min_stock'); ?></th></tr></thead><tbody>
        <?php foreach ($low_stock_rows as $row) { ?><tr><td><?php echo html_escape($row['warehouse_name']); ?></td><td><?php echo html_escape($row['sku']); ?></td><td><?php echo html_escape($row['name']); ?></td><td><?php echo html_escape($row['available_quantity']); ?></td><td><?php echo html_escape($row['min_stock']); ?></td></tr><?php } ?>
        <?php if (empty($low_stock_rows)) { ?><tr><td colspan="5"><?php echo _l('kt_inventory_no_records'); ?></td></tr><?php } ?>
        </tbody></table></div>
    </div></div>

    <div class="panel_s"><div class="panel-body">
        <h4><?php echo _l('kt_inventory_reports_movements'); ?></h4>
        <div class="table-responsive"><table class="table table-striped"><thead><tr><th><?php echo _l('kt_inventory_sku'); ?></th><th><?php echo _l('kt_inventory_name'); ?></th><th>In</th><th>Out</th><th>Adjustment</th><th>Transfer In</th><th>Transfer Out</th></tr></thead><tbody>
        <?php foreach ($movement_rows as $row) { ?><tr><td><?php echo html_escape($row['sku']); ?></td><td><?php echo html_escape($row['name']); ?></td><td><?php echo html_escape($row['qty_in']); ?></td><td><?php echo html_escape($row['qty_out']); ?></td><td><?php echo html_escape($row['qty_adjustment']); ?></td><td><?php echo html_escape($row['qty_transfer_in']); ?></td><td><?php echo html_escape($row['qty_transfer_out']); ?></td></tr><?php } ?>
        <?php if (empty($movement_rows)) { ?><tr><td colspan="7"><?php echo _l('kt_inventory_no_records'); ?></td></tr><?php } ?>
        </tbody></table></div>
    </div></div>

    <!-- Phase 3: Batch Expiry Date Warnings -->
    <div class="panel_s"><div class="panel-body">
        <h4 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Cảnh báo Hạn dùng Lô hàng (Expiry Date Warning)</h4>
        <p class="text-muted">Danh sách các lô thuốc đã hết hạn hoặc sắp hết hạn trong vòng 6 tháng tới (chỉ hiển thị các lô còn tồn kho).</p>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="active">
                        <th><?php echo _l('kt_inventory_warehouse'); ?></th>
                        <th><?php echo _l('kt_inventory_sku'); ?></th>
                        <th><?php echo _l('kt_inventory_name'); ?></th>
                        <th><?php echo _l('kt_inventory_lot_number'); ?></th>
                        <th><?php echo _l('kt_inventory_expiry_date'); ?></th>
                        <th>Tồn kho</th>
                        <th>Trạng thái QC</th>
                        <th>Số ngày còn lại</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($expiry_alerts as $row) {
                    $days = (int)$row['days_left'];
                    $tr_class = '';
                    $status_text = '';
                    if ($days <= 0) {
                        $tr_class = 'danger';
                        $status_text = 'Đã hết hạn';
                    } elseif ($days <= 90) {
                        $tr_class = 'warning';
                        $status_text = 'Hết hạn dưới 3 tháng (' . $days . ' ngày)';
                    } else {
                        $tr_class = 'info';
                        $status_text = 'Hết hạn dưới 6 tháng (' . $days . ' ngày)';
                    }
                    
                    $qc_class = 'label-info';
                    if ($row['qc_status'] === 'released') {
                        $qc_class = 'label-success';
                    } elseif ($row['qc_status'] === 'blocked') {
                        $qc_class = 'label-danger';
                    }
                ?>
                    <tr class="<?php echo $tr_class; ?>">
                        <td><?php echo html_escape($row['warehouse_name']); ?></td>
                        <td><?php echo html_escape($row['sku']); ?></td>
                        <td><?php echo html_escape($row['item_name']); ?></td>
                        <td><strong><?php echo html_escape($row['lot_number']); ?></strong></td>
                        <td><?php echo html_escape($row['expiry_date']); ?></td>
                        <td><?php echo html_escape($row['available_qty']); ?></td>
                        <td><span class="label <?php echo $qc_class; ?>"><?php echo _l('kt_inventory_qc_' . $row['qc_status']); ?></span></td>
                        <td><strong><?php echo $status_text; ?></strong></td>
                    </tr>
                <?php } ?>
                <?php if (empty($expiry_alerts)) { ?>
                    <tr><td colspan="8" class="text-center text-success">Không có lô hàng nào hết hạn hoặc sắp hết hạn trong 6 tháng tới.</td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div></div>

    <!-- Phase 3: Product Recall & Traceability -->
    <div class="panel_s"><div class="panel-body">
        <h4><i class="fa fa-history"></i> Báo cáo Truy vết Thu hồi Thuốc (Product Recall & Traceability)</h4>
        <p class="text-muted">Nhập Số lô thuốc cụ thể để truy vết lịch sử Nhập - Xuất - Chuyển - Điều chỉnh kho và Tồn kho hiện tại.</p>
        
        <?php echo form_open(admin_url('kt_inventory/reports'), ['method' => 'get', 'class' => 'form-inline mbot20']); ?>
        <div class="form-group">
            <input type="text" name="recall_lot" placeholder="Nhập Số lô thuốc..." value="<?php echo html_escape($filters['recall_lot'] ?? ''); ?>" class="form-control" style="min-width:300px;">
        </div>
        <button type="submit" class="btn btn-info">Tìm kiếm truy vết</button>
        <?php echo form_close(); ?>

        <?php if (isset($recall_data)) { ?>
            <hr />
            <h3>Kết quả truy vết Số lô: <span class="text-danger"><?php echo html_escape($recall_data['lot_number']); ?></span></h3>
            
            <!-- 1. Stock Location -->
            <div class="row mtop20">
                <div class="col-md-12">
                    <h5><strong>1. Vị trí tồn kho hiện tại (Current Location & Balances)</strong></h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="active">
                                <th>Kho hàng</th>
                                <th>SKU</th>
                                <th>Tên thuốc</th>
                                <th>Số lượng tồn</th>
                                <th>Trạng thái QC</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recall_data['balances'] as $row) {
                            $qc_class = $row['qc_status'] === 'released' ? 'label-success' : ($row['qc_status'] === 'blocked' ? 'label-danger' : 'label-info');
                        ?>
                            <tr>
                                <td><?php echo html_escape($row['warehouse_name']); ?></td>
                                <td><?php echo html_escape($row['sku']); ?></td>
                                <td><?php echo html_escape($row['item_name']); ?></td>
                                <td><strong><?php echo html_escape($row['stock_qty']); ?></strong></td>
                                <td><span class="label <?php echo $qc_class; ?>"><?php echo _l('kt_inventory_qc_' . $row['qc_status']); ?></span></td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($recall_data['balances'])) { ?>
                            <tr><td colspan="5" class="text-center text-muted">Lô thuốc này hiện không còn tồn kho thực tế.</td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Goods Receipts -->
            <div class="row mtop20">
                <div class="col-md-12">
                    <h5><strong>2. Lịch sử Nhập kho (Receipts / Imports Source)</strong></h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="active">
                                <th>Ngày nhập</th>
                                <th>Mã phiếu nhập</th>
                                <th>Nhà cung cấp</th>
                                <th>Kho nhận</th>
                                <th>Số lượng</th>
                                <th>Giá nhập</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recall_data['receipts'] as $row) { ?>
                            <tr>
                                <td><?php echo html_escape($row['receipt_date']); ?></td>
                                <td><a href="<?php echo admin_url('kt_inventory/receipt/' . $row['receipt_code']); ?>" target="_blank"><?php echo html_escape($row['receipt_code']); ?></a></td>
                                <td><?php echo html_escape($row['supplier_name']); ?></td>
                                <td><?php echo html_escape($row['warehouse_name']); ?></td>
                                <td><?php echo html_escape($row['quantity']); ?></td>
                                <td><?php echo html_escape($row['unit_cost']); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($recall_data['receipts'])) { ?>
                            <tr><td colspan="6" class="text-center text-muted">Không tìm thấy phiếu nhập kho nào cho lô này.</td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Goods Issues -->
            <div class="row mtop20">
                <div class="col-md-12">
                    <h5><strong>3. Lịch sử Xuất bán cho Khách hàng (Issues / Sales Destinations)</strong></h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="active">
                                <th>Ngày xuất</th>
                                <th>Mã phiếu xuất</th>
                                <th>Khách hàng</th>
                                <th>Hóa đơn liên kết</th>
                                <th>Kho xuất</th>
                                <th>Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recall_data['issues'] as $row) { ?>
                            <tr>
                                <td><?php echo html_escape($row['issue_date']); ?></td>
                                <td><a href="<?php echo admin_url('kt_inventory/issue/' . $row['issue_code']); ?>" target="_blank"><?php echo html_escape($row['issue_code']); ?></a></td>
                                <td><?php echo html_escape($row['customer_name'] ?: 'Khách hàng vãng lai'); ?></td>
                                <td><?php echo $row['invoice_number'] ? '<a href="' . admin_url('invoices/list_invoices/' . $row['invoice_number']) . '" target="_blank">#' . $row['invoice_number'] . '</a>' : 'N/A'; ?></td>
                                <td><?php echo html_escape($row['warehouse_name']); ?></td>
                                <td><strong class="text-danger"><?php echo html_escape($row['quantity']); ?></strong></td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($recall_data['issues'])) { ?>
                            <tr><td colspan="6" class="text-center text-success">Lô thuốc này chưa được xuất bán cho khách hàng nào.</td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Transfers & Adjustments -->
            <div class="row mtop20">
                <div class="col-md-6">
                    <h5><strong>4. Lịch sử Chuyển kho (Transfers)</strong></h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="active">
                                <th>Ngày chuyển</th>
                                <th>Mã chuyển</th>
                                <th>Từ kho</th>
                                <th>Đến kho</th>
                                <th>Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recall_data['transfers'] as $row) { ?>
                            <tr>
                                <td><?php echo html_escape($row['transfer_date']); ?></td>
                                <td><?php echo html_escape($row['transfer_code']); ?></td>
                                <td><?php echo html_escape($row['from_warehouse_name']); ?></td>
                                <td><?php echo html_escape($row['to_warehouse_name']); ?></td>
                                <td><?php echo html_escape($row['quantity']); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($recall_data['transfers'])) { ?>
                            <tr><td colspan="5" class="text-center text-muted">Không phát sinh chuyển kho.</td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5><strong>5. Lịch sử Điều chỉnh tồn (Adjustments)</strong></h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="active">
                                <th>Ngày chỉnh</th>
                                <th>Mã điều chỉnh</th>
                                <th>Lý do</th>
                                <th>Số lượng biến động</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recall_data['adjustments'] as $row) { ?>
                            <tr>
                                <td><?php echo html_escape($row['adjustment_date']); ?></td>
                                <td><?php echo html_escape($row['adjustment_code']); ?></td>
                                <td><?php echo html_escape($row['reason'] ?: $row['note']); ?></td>
                                <td><?php echo ($row['difference_quantity'] > 0 ? '+' : '') . html_escape($row['difference_quantity']); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if (empty($recall_data['adjustments'])) { ?>
                            <tr><td colspan="4" class="text-center text-muted">Không phát sinh điều chỉnh kho.</td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    </div></div>
</div></div>
<?php init_tail(); ?>
