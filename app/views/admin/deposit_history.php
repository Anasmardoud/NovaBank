<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1>Deposit History</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Deposit ID</th>
                <th>Account ID</th>
                <th>Admin ID</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deposits as $deposit): ?>
                <tr>
                    <td><?= htmlspecialchars($deposit['deposit_id']) ?></td>
                    <td><?= htmlspecialchars($deposit['account_id']) ?></td>
                    <td><?= htmlspecialchars($deposit['admin_id']) ?></td>
                    <td><?= htmlspecialchars($deposit['amount']) ?></td>
                    <td><?= htmlspecialchars($deposit['created_at']) ?></td>
                    <td><?= htmlspecialchars($deposit['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>