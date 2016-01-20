<?php
Yii::import('system.cli.commands.MigrateCommand');
class InstallDbCommand extends MigrateCommand {
    public $migrationTable = "p_migration";
}