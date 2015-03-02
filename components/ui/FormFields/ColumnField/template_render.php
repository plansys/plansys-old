<table <?= $this->expandAttributes($this->options) ?>>
    <tr valign="top">
        <?php for ($i = 1; $i <= $this->totalColumns; $i++): ?> 
            <td style="width:<?= $this->columnWidth ?>%;
                <?= $this->showBorder == 'Yes' && $i != 1 ? "border-left:1px solid #ececeb" : "" ?>">
                <?= $this->renderColumn($i); ?>
            </td>
        <?php endfor; ?>
    </tr>
</table>