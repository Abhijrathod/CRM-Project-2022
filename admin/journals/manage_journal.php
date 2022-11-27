<?php 
require_once('./../../config.php');
$account_arr = [];
$group_arr = [];
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `journal_entries` where id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
}
?>
<?php
function format_num($number){
	$decimals = 0;
	$num_ex = explode('.',$number);
	$decimals = isset($num_ex[1]) ? strlen($num_ex[1]) : 0 ;
	return number_format($number,$decimals);
}
?>
<div class="container-fluid">
    <form action="" id="journal-form">
        <input type="hidden" name="id" value="<?= isset($id) ? $id :'' ?>">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="journal_date" class="control-label">Entry Date</label>
                <input type="date" id="journal_date" name="journal_date" class="form-control form-control-sm form-control-border rounded-0" value="<?= isset($journal_date) ? $journal_date : date("Y-m-d") ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="description" class="control-label">Entry Description</label>
                <textarea rows="2" id="description" name="description" class="form-control form-control-sm rounded-0" required><?= isset($description) ? $description : "" ?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="account_id" class="control-label">Account</label>
                <select id="account_id" class="from-control form-control-sm form-control-border select2">
                    <option value="" disabled selected></option>
                    <?php 
                    $accounts = $conn->query("SELECT * FROM `account_list` where delete_flag = 0 and `status` = 1 order by `name` asc ");
                    while($row = $accounts->fetch_assoc()):
                        unset($row['description']);
                        $account_arr[$row['id']] = $row;
                    ?>
                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="group_id" class="control-label">Account Group</label>
                <select id="group_id" class="from-control form-control-sm form-control-border select2">
                    <option value="" disabled selected></option>
                    <?php 
                    $groups = $conn->query("SELECT * FROM `group_list` where delete_flag = 0 and `status` = 1 order by `name` asc ");
                    while($row = $groups->fetch_assoc()):
                        unset($row['description']);
                        $group_arr[$row['id']] = $row;
                    ?>
                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="row align-items-end">
            <div class="form-group col-md-6">
                <label for="amount" class="control-label">Amount</label>
                <input type="number" step="any" id="amount" class="form-control form-control-sm form-control-border text-right">
            </div>
            <div class="form-group col-md-6">
                <button class="btn btn-default bg-gradient-navy btn-flat btn-sm" id="add_to_list" type="button"><i class="fa fa-plus"></i> Add Account</button>
            </div>
        </div>
        <table id="account_list" class="table table-stripped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="35%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
            </colgroup>
            <thead>
                <tr class="bg-gradient-primary">
                    <th class="text-center"></th>
                    <th class="text-center">Account</th>
                    <th class="text-center">Group</th>
                    <th class="text-center">Debit</th>
                    <th class="text-center">Credit</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(isset($id)):
                $jitems = $conn->query("SELECT j.*,a.name as account, g.name as `group`, g.type FROM `journal_items` j inner join account_list a on j.account_id = a.id inner join group_list g on j.group_id = g.id where journal_id = '{$id}'");
                while($row = $jitems->fetch_assoc()):
                ?>
                <tr>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline btn-danger btn-flat delete-row" type="button"><i class="fa fa-times"></i></button>
                    </td>
                    <td class="">
                        <input type="hidden" name="account_id[]" value="<?= $row['account_id'] ?>">
                        <input type="hidden" name="group_id[]" value="<?= $row['group_id'] ?>">
                        <input type="hidden" name="amount[]" value="<?= $row['amount'] ?>">
                        <span class="account"><?= $row['account'] ?></span>
                    </td>
                    <td class="group"><?= $row['group'] ?></td>
                    <td class="debit_amount text-right"><?= $row['type'] == 1 ? format_num($row['amount']) : '' ?></td>
                    <td class="credit_amount text-right"><?= $row['type'] == 2 ? format_num($row['amount']) : '' ?></td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="bg-gradient-secondary">
                    <tr>
                        <th colspan="3" class="text-center">Total</th>
                        <th class="text-right total_debit">0.00</th>
                        <th class="text-right total_credit">0.00</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-center"></th>
                        <th colspan="3" class="text-center total-balance">0</th>
                    </tr>
                </tr>
            </tfoot>
        </table>
    </form>
</div>
<noscript id="item-clone">
<tr>
    <td class="text-center">
        <button class="btn btn-sm btn-outline btn-danger btn-flat delete-row" type="button"><i class="fa fa-times"></i></button>
    </td>
    <td class="">
        <input type="hidden" name="account_id[]" value="">
        <input type="hidden" name="group_id[]" value="">
        <input type="hidden" name="amount[]" value="">
        <span class="account"></span>
    </td>
    <td class="group"></td>
    <td class="debit_amount text-right"></td>
    <td class="credit_amount text-right"></td>
</tr>
</noscript>
<script>
    var account = $.parseJSON('<?= json_encode($account_arr) ?>')
    var group = $.parseJSON('<?= json_encode($group_arr) ?>')

    function cal_tb(){
        var debit = 0;
        var credit = 0;
        $('#account_list tbody tr').each(function(){
            if($(this).find('.debit_amount').text() != "")
                debit += parseFloat(($(this).find('.debit_amount').text()).replace(/,/gi,''));
            if($(this).find('.credit_amount').text() != "")
                credit += parseFloat(($(this).find('.credit_amount').text()).replace(/,/gi,''));
        })
        $('#account_list').find('.total_debit').text(parseFloat(debit).toLocaleString('en-US',{style:'decimal'}))
        $('#account_list').find('.total_credit').text(parseFloat(credit).toLocaleString('en-US',{style:'decimal'}))
        $('#account_list').find('.total-balance').text(parseFloat(debit - credit).toLocaleString('en-US',{style:'decimal'}))
    }
    $(function(){
        if('<?= isset($id) ?>' == 1){
            cal_tb()
        }
        $('#account_list th,#account_list td').addClass('align-middle px-2 py-1')
        $('#uni_modal').on('shown.bs.modal',function(){
            $('.select2').select2({
                placeholder:"Please select here",
                width:"100%",
                dropdownParent:$('#uni_modal')
            })
        })
        $('#uni_modal').trigger('shown.bs.modal')
        $('#add_to_list').click(function(){
            var account_id = $('#account_id').val()
            var group_id = $('#group_id').val()
            var amount = $('#amount').val()
            var account_data = !!account[account_id] ? account[account_id] : {};
            var group_data = !!group[group_id] ? group[group_id] : {};
            var tr = $($('noscript#item-clone').html()).clone()
            tr.find('input[name="account_id[]"]').val(account_id)
            tr.find('input[name="group_id[]"]').val(group_id)
            tr.find('input[name="amount[]"]').val(amount)
            tr.find('.account').text(!!account_data.name ? account_data.name : "N/A")
            tr.find('.group').text(!!group_data.name ? group_data.name : "N/A")
            if(!!group_data.type && group_data.type == 1)
                tr.find('.debit_amount').text(parseFloat(amount).toLocaleString('en-US',{style:'decimal'}))
            else
                tr.find('.credit_amount').text(parseFloat(amount).toLocaleString('en-US',{style:'decimal'}))
                $('#account_list').append(tr)
                tr.find('.delete-row').click(function(){
                    $(this).closest('tr').remove()
                    cal_tb()
                })
            cal_tb()
            $('#account_id').val('').trigger('change')
            $('#group_id').val('').trigger('change')
            $('#amount').val('').trigger('change')
        })
        $('#uni_modal #journal-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            if($('#account_list tbody tr').length <=0){
                el.addClass('alert-danger').text(" Account Table is empty.")
                _this.prepend(el)
                el.show('slow')
                $('#uni_modal').scrollTop(0)
                return false;
            }
            if($('#account_list tfoot .total-balance').text() != '0'){
                el.addClass('alert-danger').text(" Trial Balance is not equal.")
                _this.prepend(el)
                el.show('slow')
                $('#uni_modal').scrollTop(0)
                return false;
            }
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_journal",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload();
                    }else if(!!resp.msg){
                        el.addClass("alert-danger")
                        el.text(resp.msg)
                        _this.prepend(el)
                    }else{
                        el.addClass("alert-danger")
                        el.text("An error occurred due to unknown reason.")
                        _this.prepend(el)
                    }
                    el.show('slow')
                    $('html,body,.modal').animate({scrollTop:0},'fast')
                    end_loader();
                }
            })
        })
    })
</script>