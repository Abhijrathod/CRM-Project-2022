<?php
function format_num($number){
	$decimals = 0;
	$num_ex = explode('.',$number);
	$decimals = isset($num_ex[1]) ? strlen($num_ex[1]) : 0 ;
	return number_format($number,$decimals);
}
$from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-d",strtotime(date('Y-m-d')." -1 week"));
$to = isset($_GET['to']) ? $_GET['to'] : date("Y-m-d");
?>
<style>
	th.p-0, td.p-0{
		padding: 0 !important;
	}
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Working Trial Balance</h3>
		<div class="card-tools">
		</div>
	</div>
	<div class="card-body">
        <div class="callout border-primary shadow rounded-0">
            <h4 class="text-muted">Filter Date</h4>
            <form action="" id="filter">
            <div class="row align-items-end">
                <div class="col-md-4 form-group">
                    <label for="from" class="control-label">Date From</label>
                    <input type="date" id="from" name="from" value="<?= $from ?>" class="form-control form-control-sm rounded-0">
                </div>
                <div class="col-md-4 form-group">
                    <label for="to" class="control-label">Date To</label>
                    <input type="date" id="to" name="to" value="<?= $to ?>" class="form-control form-control-sm rounded-0">
                </div>
                <div class="col-md-4 form-group">
                    <button class="btn btn-default bg-gradient-navy btn-flat btn-sm"><i class="fa fa-filter"></i> Filter</button>
			        <button class="btn btn-default border btn-flat btn-sm" id="print" type="button"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
            </form>
        </div>
        <div class="container-fluid" id="outprint">
            <h3 class="text-center"><b><?= $_settings->info('name') ?></b></h3>
            <h4 class="text-center"><b>Working Trial Balance</b></h4>
            <?php if($from == $to): ?>
            <p class="m-0 text-center"><?= date("M d, Y" , strtotime($from)) ?></p>
            <?php else: ?>
            <p class="m-0 text-center"><?= date("M d, Y" , strtotime($from)). ' - '.date("M d, Y" , strtotime($to)) ?></p>
            <?php endif; ?>
            <hr>
			<table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Date</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Ref. Code.</th>
                        <th class="text-center">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $balance = 0;
                    $journal = $conn->query("SELECT * FROM `journal_entries` where date(journal_date) BETWEEN '{$from}' and '{$to}'");
                    $journal_arr = [];
                    while($row = $journal->fetch_assoc()){
                        $journal_arr[$row['id']] = $row;
                    }
                    $accounts = $conn->query("SELECT * FROM `account_list`  where id in (SELECT account_id FROM `journal_items` where journal_id in (SELECT id FROM `journal_entries` where date(journal_date) BETWEEN '{$from}' and '{$to}' ))");
                    while($arow = $accounts->fetch_assoc()):
                        $items = $conn->query("SELECT j.*,g.type FROM `journal_items` j inner join group_list g on j.group_id = g.id where j.account_id = '{$arow['id']}' and j.journal_id in (SELECT id FROM `journal_entries` where date(journal_date) BETWEEN '{$from}' and '{$to}' )");
                    ?>
                    <tr>
                        <th colspan="4"><?= $arow['name'] ?></th>
                    </tr>
                    <?php 
                    while($irow = $items->fetch_assoc()): 
                        if($irow['type'] == 1)
                            $balance += $irow['amount'];
                        else    
                            $balance -= $irow['amount'];
                    ?>
                        <tr>
                            <td><?= isset($journal_arr[$irow['journal_id']]) ? date("M d, Y",strtotime($journal_arr[$irow['journal_id']]['journal_date'])) : "" ?></td>
                            <td><?= isset($journal_arr[$irow['journal_id']]) ? $journal_arr[$irow['journal_id']]['description'] : "" ?></td>
                            <td><?= isset($journal_arr[$irow['journal_id']]) ? $journal_arr[$irow['journal_id']]['code'] : "" ?></td>
                            <td class="text-right"><?= $irow['type'] == 1 ? format_num($irow['amount']) : '-'.(format_num($irow['amount']))  ?></td>
                        </tr>
                    <?php endwhile; ?>
				<?php endwhile; ?>
                </tbody>
                <tfoot>
                    <th colspan="3" class="text-center">Total</th>
                    <th class="text-right"><?= format_num($balance) ?></th>
                </tfoot>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
        $('#filter').submit(function(e){
            e.preventDefault()
            location.href="./?page=reports/working_trial_balance&"+$(this).serialize();
        })
        $('#print').click(function(){
            start_loader()
            var _h = $('head').clone();
            var _p = $('#outprint').clone();
            var el = $('<div>')
            _h.find('title').text('Working Trial Balance - Print View')
            _h.append('<style>html,body{ min-height: unset !important;}</style>')
            el.append(_h)
            el.append(_p)
             var nw = window.open("","_blank","width=900,height=700,top=50,left=250")
             nw.document.write(el.html())
             nw.document.close()
             setTimeout(() => {
                 nw.print()
                 setTimeout(() => {
                     nw.close()
                     end_loader()
                 }, 200);
             }, 500);
        })
		
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
	})
	
</script>