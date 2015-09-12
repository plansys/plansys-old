<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Username</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $model->fullname ?></td>
            <td><?= $model->username ?></td>
            <td><?= $model->email ?></td>
        </tr>
    </tbody>
</table>
<img src="<?= $this->staticUrl("/tes.jpg" ); ?>">