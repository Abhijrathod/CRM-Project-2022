<style>
    #banner-img{
        width:100%;
        height:40vh;
        object-fit:cover;
        object-position:center center;
    }
</style>
<h1>Welcome to <?php echo $_settings->info('name') ?> </h1>
<hr class="border-border bg-primary">
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-4">
        <div class="info-box bg-gradient-light shadow">
            <span class="info-box-icon bg-gradient-navy elevation-1"><i class="fas fa-table"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Active Account Groups</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `group_list` where delete_flag = 0 and status = 1 ")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-4">
        <div class="info-box bg-gradient-light shadow">
            <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-th-list"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Active Accounts</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `account_list` where delete_flag= 0 and status = 1 ")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-4">
        <div class="info-box bg-gradient-light shadow">
            <span class="info-box-icon bg-gradient-info elevation-1"><i class="fas fa-pen-square"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Journal Entries</span>
            <span class="info-box-number text-right">
                <?php 
                    echo $conn->query("SELECT * FROM `journal_entries` ")->num_rows;
                ?>
            </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
</div>
<hr class="border-border bg-primary">
<div class="row">
    <div class="col-md-12">
        <img src="<?= validate_image($_settings->info('cover')) ?>" alt="Website Page" id="banner-img" class="w-100">
    </div>
</div>
