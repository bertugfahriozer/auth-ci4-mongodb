<?= $this->extend('Modules\Backend\Views\base') ?>

<?= $this->section('title') ?>
<<<<<<< HEAD
<?=lang('Backend.')?>Ofis Çalışan Listesi
<?= $this->endSection() ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="/be-assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
=======
<?=lang('Backend.userList')?>
<?= $this->endSection() ?>
<?= $this->section('head') ?>
<?=link_tag("be-assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css")?>
>>>>>>> dev
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row pb-3 border-bottom">
            <div class="col-sm-6">
<<<<<<< HEAD
                <h1>Ofis Çalışan Listesi</h1>
=======
                <h1><?=lang('Backend.userList')?></h1>
>>>>>>> dev
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"><a href="<?= route_to('create_user') ?>"
                                                         class="btn btn-outline-success"><i
<<<<<<< HEAD
                                class="fas fa-user-plus"></i> Yeni Kullanıcı Ekle</a></ol>
=======
                                class="fas fa-user-plus"></i> <?=lang('Backend.addUser')?></a></ol>
>>>>>>> dev
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="card card-outline card-shl">
        <!-- /.card-header -->
        <div class="card-body">
            <?= view('Modules\Auth\Views\_message_block') ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
<<<<<<< HEAD
                        <th>Ad Soyad</th>
                        <th>E-mail</th>
                        <th style="width: 40px">Durumu</th>
                        <th style="width: 150px" class="text-center">Yetkisi</th>
                        <th style="width: 500px" class="text-center">#İşlemler</th>
=======
                        <th><?=lang('Backend.fullName')?></th>
                        <th><?=lang('Backend.email')?></th>
                        <th style="width: 40px"><?=lang('Backend.status')?></th>
                        <th style="width: 150px" class="text-center"><?=lang('Backend.authority')?></th>
                        <th style="width: 500px" class="text-center"><?=lang('Backend.transactions')?></th>
>>>>>>> dev
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($userLists as $userList) : ?>
                        <tr>
                            <td><?= $userList->firstname ?> <?= $userList->sirname ?></td>
                            <td><?= $userList->email ?></td>
                            <td class="text-center">
                                <?= $userList->status ?>
                            </td>
                            <td class="text-center"><?= $userList->groupInfo->name ?></td>
                            <td>
                                <a href="<?= route_to('update_user', $userList->_id) ?>"
<<<<<<< HEAD
                                   class="btn btn-outline-info btn-sm"><i class="fas fa-edit"></i> Düzenle</a>
                                <?php if ($userList->status == 'banned'): ?>
                                    <button class="btn btn-outline-dark btn-sm"
                                            data-target="#inblackList<?= $userList->_id ?>" data-toggle="modal"><i
                                                class="fas fa-user-slash"></i> Kara Listede
=======
                                   class="btn btn-outline-info btn-sm"><?=lang('Backend.update')?></a>
                                <?php if ($userList->status == 'banned'): ?>
                                    <button class="btn btn-outline-dark btn-sm"
                                            data-target="#inblackList<?= $userList->_id ?>" data-toggle="modal"><i
                                                class="fas fa-user-slash"></i> <?=lang('Backend.inBlackList')?>
>>>>>>> dev
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-outline-dark btn-sm"
                                            data-target="#blackList<?= $userList->_id ?>" data-toggle="modal"><i
<<<<<<< HEAD
                                                class="fas fa-user-slash"></i> Kara Liste
=======
                                                class="fas fa-user-slash"></i> <?=lang('Backend.blackList')?>
>>>>>>> dev
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-outline-dark btn-sm <?php
                                if(!empty($userList->reset_expires)) {
                                    $time = $timeClass::parse($userList->reset_expires);
<<<<<<< HEAD
                                    echo var_dump(time() > $time->getTimestamp());
                                    if (time() < $time->getTimestamp())
                                        echo 'disabled';
                                    }?>" id="fpwd" data-uid="<?= $userList->_id ?>"><i class="fas fa-key"></i> Şifre Sıfırla</button>
                                <a href="<?= route_to('user_perms', $userList->_id) ?>"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-sitemap"></i> Özel Yetki
                                </a>
                                <a class="btn btn-outline-danger btn-sm" href="<?=route_to('user_del',$userList->_id)?>"><i class="fas fa-user-minus"></i> sil</a>
=======
                                    if (time() < $time->getTimestamp())
                                        echo 'disabled';
                                    }?>" id="fpwd" data-uid="<?= $userList->_id ?>"><?=lang('Backend.resetPassword')?></button>
                                <a href="<?= route_to('user_perms', $userList->_id) ?>"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-sitemap"></i> <?=lang('Backend.spacialAuth')?>
                                </a>
                                <a class="btn btn-outline-danger btn-sm" href="<?=route_to('user_del',$userList->_id)?>"><?=lang('Backend.delete')?></a>
>>>>>>> dev
                            </td>
                            <?php if ($userList->status == 'banned'): ?>
                                <div class="modal fade" id="inblackList<?= $userList->_id ?>" tabindex="-1"
                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
<<<<<<< HEAD
                                                <h5 class="modal-title" id="exampleModalLabel">Kara Liste</h5>
=======
                                                <h5 class="modal-title" id="exampleModalLabel"><?=lang('Backend.blackList')?></h5>
>>>>>>> dev
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
<<<<<<< HEAD
                                                    <div class="col-12">
                                                        <label for="">Kara listeye eklenme sebebi</label>
=======
                                                    <div class="col-md-12">
                                                        <label for=""><?=lang('Backend.whyAddedBlakList')?></label>
>>>>>>> dev
                                                        <div>
                                                            <?= $userList->inBlackList->notes ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <form action="<?= route_to('removeFromBlacklist') ?>" method="post"
                                                  data-uid="<?= $userList->_id ?>">
                                                <?= csrf_field() ?>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">
<<<<<<< HEAD
                                                        Vazgeç
                                                    </button>
                                                    <button type="submit" class="btn btn-dark"><i
                                                                class="fas fa-user-check"></i>
                                                        Kara Listeden Çıkar ve Aktivasyon Maili Gönder
=======
                                                        <?=lang('Backend.cancel')?>
                                                    </button>
                                                    <button type="submit" class="btn btn-dark"><i
                                                                class="fas fa-user-check"></i>
                                                        <?=lang('Backend.removeFromBlackList')?>
>>>>>>> dev
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="modal fade" id="blackList<?= $userList->_id ?>" tabindex="-1"
                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Kara Liste</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="<?= route_to('blackList') ?>" method="post"
                                                  data-uid="<?= $userList->_id ?>">
                                                <?= csrf_field() ?>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="">Kara listeye eklenme sebebi</label>
                                                            <textarea name="note" id="" cols="30" rows="10"
                                                                      class="form-control"
                                                                      placeholder="Kara listeye eklenme sebebini yazınız..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">
                                                        Vazgeç
                                                    </button>
                                                    <button type="submit" class="btn btn-dark"><i
                                                                class="fas fa-user-slash"></i>
                                                        Kara Listeye Al
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.card-body -->
        <?php if ($paginator->getNumPages() > 1): ?>
        <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                    <?php if ($paginator->getPrevUrl()): ?>
                        <li class="page-item"><a class="page-link" href="<?php echo $paginator->getPrevUrl(); ?>">&laquo;</a></li>
                    <?php endif; ?>

                    <?php foreach ($paginator->getPages() as $page): ?>
                        <?php if ($page['url']): ?>
                            <li class="page-item <?php echo $page['isCurrent'] ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo $page['url']; ?>"><?php echo $page['num']; ?></a>
                            </li>
                        <?php else: ?>
                            <li class="disabled page-item"><span><?php echo $page['num']; ?></span></li>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($paginator->getNextUrl()): ?>
                        <li class="page-item"><a class="page-link" href="<?php echo $paginator->getNextUrl(); ?>">&raquo;</a></li>
                    <?php endif; ?>
                </ul>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- /.content -->
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<!-- SweetAlert2 -->
<<<<<<< HEAD
<script src="/be-assets/plugins/sweetalert2/sweetalert2.min.js"></script>
=======
<?=script_tag("be-assets/plugins/sweetalert2/sweetalert2.min.js")?>
>>>>>>> dev

<script>
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    $(".modal form").on("submit", function (event) {
        event.preventDefault();

        var formValues = $(this).serialize();
        var url = $(this).attr('action');
        formValues += '&uid=' + $(this).data('uid');

        $.post(url, formValues, function (data) {
            if (data.result == true) {
                Toast.fire({icon: data.error.type, title: data.error.message}).then(function () {
                    location.reload();
                });
            } else
                Toast.fire({icon: 'warning', title: 'İşlem Başarısız !'}).then(function () {
                    $('.modal').modal('toggle');
                });
        }, 'json');
    });

    $('#fpwd').on('click',function (){
        $('#fpwd').addClass('disabled');
        $('#fpwd').addClass('disabled');
       $.post("<?=route_to('forceResetPassword')?>", {uid:$(this).data('uid'),<?=csrf_token()?>:"<?=csrf_hash()?>"}, function (data){
            if (data.result == true) {
                Toast.fire({icon: data.error.type, title: data.error.message}).then(function () {
                    location.reload();
                });
            } else
                Toast.fire({icon: 'warning', title: 'İşlem Başarısız !'}).then(function () {
                    $('.modal').modal('toggle');
                });
        }, 'json');
    });
</script>
<?= $this->endSection() ?>
