<table style='width:100%;margin-bottom:5px;'>
    <tr>
        <?php for ($i = 1; $i <= $this->totalColumns; $i++): ?> 
            <td style="width:<?= $this->columnWidth ?>%;padding:5px;
                <?= $this->showBorder == 'Yes' && $i != 1 ? "border-left:1px solid #ececeb" : "" ?>">
                <?= $this->renderColumn($i); ?>
            </td>
        <?php endfor; ?>
    </tr>
</table>