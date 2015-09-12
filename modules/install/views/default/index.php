<div class="install-pane">
    <div class="install-pane-head">
        <img src="<?= $this->staticUrl("/img/logo.png"); ?>" alt="Logo Plansys" />
    </div>
    
    <?php if (!isset($msg)): ?>
        <table class="table table-bordered table-condensed install-check <?= !(Installer::getError()) ? '' : 'error' ?>">
            <?php foreach (Installer::getCheckList() as $group => $item): ?>
                <tr class="<?= Installer::getError($group) ? 'danger' : 'success'; ?>">
                    <td>
                        <a href="#" ><b><?= $group ?></b>
                            <div class="sub <?= Installer::getError($group) ? 'error' : ''; ?>" >
                                <table class="table table-condensed">
                                    <?php foreach ($item as $k => $i): ?>
                                        <tr class="<?= Installer::getError($group, $k) ? 'danger' : 'success'; ?>">
                                            <td><?= $i['title']; ?>

                                                <?php
                                                if (Installer::getError($group, $k)) {
                                                    echo '<pre style="margin:0px;font-size:11px;">' . Installer::getError($group, $k) . '</pre>';
                                                }
                                                ?>

                                            </td>
                                            <td class="install-status">
                                                <i class="fa <?= Installer::getError($group, $k) ? 'fa-times' : 'fa-check'; ?>"></i>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </a>
                    </td>
                    <td class="install-status">
                        <i class="fa <?= Installer::getError($group) ? 'fa-times' : 'fa-check'; ?>"></i>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php if (!(Installer::getError())): ?>
            <div class="install-passed"><?= Setting::t("All Requirement Passed"); ?></div>

            <a href="<?= Yii::app()->controller->createUrl('/install/default/db'); ?>" class="btn btn-success">
                <?= Setting::t("Next step"); ?> <i class="fa fa-arrow-right"></i>
            </a>
        <?php else: ?>
            <div class="install-failed">
                <?= Setting::t("Please fix error(s) above to continue ..."); ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <br/><br/>
        <div class="alert alert-danger install-error">
            <div class="install-error-head">
                <i class="fa fa-warning"></i> <?= Setting::t("Error while initializing plansys"); ?>
            </div>
            <?php
            echo $msg;
            ?>
        </div>
    <?php endif; ?>
</div>