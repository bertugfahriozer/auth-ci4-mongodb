<?= $this->extend('Modules\Backend\Views\base') ?>
<?= $this->section('title') ?>
<?= lang('Backend.'.$title->pagename) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= lang('Backend.'.$title->pagename) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"></ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-lg-3 col-md-3">
            <!-- small card -->
            <div class="small-box bg-light shadow">
                <div class="inner">
                    <h3><?=$dashboard->pageCount->count?></h3>

                    <p>Sayfalar</p>
                </div>
                <div class="icon">
                    <?=$dashboard->pageCount->icon?>
                </div>
                <a href="<?=route_to('pages',1)?>" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>

<?= $this->endSection() ?>
