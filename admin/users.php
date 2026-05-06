<?php
require_once('../config/db_connect.php');
include('../includes/header.php');
include('../includes/sidebar.php');

// Fetch members based on view (Active vs Archived)
$view_archived = isset($_GET['view']) && $_GET['view'] == 'archived' ? 1 : 0;
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'member' AND is_archived = ? ORDER BY created_at DESC");
$stmt->execute([$view_archived]);
$users = $stmt->fetchAll();
?>

<div id="content-wrapper">
    <header class="app-toolbar shadow-sm px-4">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold"><?php echo $view_archived ? 'Archived Records' : 'Member Accounts'; ?></h5>
            <div class="vr"></div>
            <?php if(!$view_archived): ?>
                <button class="btn btn-sm btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i data-lucide="user-plus" style="width: 14px;"></i> Register Member
                </button>
            <?php else: ?>
                <a href="users.php" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2">
                    <i data-lucide="users" style="width: 14px;"></i> Active List
                </a>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center gap-3">
            <?php if(!$view_archived): ?>
                <a href="users.php?view=archived" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2">
                    <i data-lucide="archive" style="width: 14px;"></i> View Archive
                </a>
            <?php endif; ?>
            <span class="badge bg-light text-dark border">Total: <?php echo count($users); ?></span>
        </div>
    </header>

    <div class="app-canvas" style="display: block; overflow-y: auto;">
        <?php if(isset($_GET['archived'])): ?>
            <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">Member has been moved to <b>Archive</b>.</div>
        <?php endif; ?>
        <?php if(isset($_GET['restored'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">Member has been <b>Restored</b> to active list.</div>
        <?php endif; ?>
        <div class="station-container p-0 overflow-hidden">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Member Name</th>
                        <th class="py-3 border-0">Status</th>
                        <th class="py-3 border-0">Balance</th>
                        <th class="py-3 border-0">Member Since</th>
                        <th class="py-3 text-end pe-4 border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="ps-4 align-middle">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($user['full_name'] ?: 'No Name'); ?></span>
                                <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                            </div>
                        </td>
                        <td class="align-middle text-muted" style="font-size: 12px;">
                            <span class="badge <?php echo $user['membership_type'] == 'VIP' ? 'bg-danger' : 'bg-primary'; ?> rounded-pill">
                                <?php echo $user['membership_type']; ?> MEMBER
                            </span>
                        </td>
                        <td class="align-middle fw-bold text-success">₱<?php echo number_format($user['balance'], 2); ?></td>
                        <td class="align-middle text-muted" style="font-size: 12px;">
                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td class="align-middle text-end pe-4">
                            <?php if(!$view_archived): ?>
                                <button class="btn btn-sm btn-light border-0 rounded-3 me-1" title="Top-up" onclick="openTopupModal(<?php echo $user['id']; ?>, '<?php echo $user['username']; ?>')">
                                    <i data-lucide="plus-circle" style="width: 16px; color: #10b981;"></i>
                                </button>
                                <a href="../actions/reset_password.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-light border-0 rounded-3 me-1" title="Reset Password" onclick="return confirm('Reset password to 123456?')">
                                    <i data-lucide="key" style="width: 16px; color: #f59e0b;"></i>
                                </a >
                                <a href="../actions/archive_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-light border-0 rounded-3" title="Archive User" onclick="return confirm('Move this member to archive?')">
                                    <i data-lucide="archive" style="width: 16px; color: #64748b;"></i>
                                </a>
                            <?php else: ?>
                                <a href="../actions/archive_user.php?id=<?php echo $user['id']; ?>&restore=1" class="btn btn-sm btn-success fw-bold rounded-pill px-3" style="font-size: 10px;">
                                    RESTORE MEMBER
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Register User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Register New Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../actions/register_member.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Full Name</label>
                        <input type="text" name="full_name" class="form-control bg-light border-0 p-3" placeholder="e.g. Juan Dela Cruz" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Desired Username</label>
                        <input type="text" name="username" class="form-control bg-light border-0 p-3" placeholder="e.g. GamerX123" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Password</label>
                        <input type="password" name="password" class="form-control bg-light border-0 p-3" value="123456" required>
                        <small class="text-muted">Default: 123456</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Initial Top-up (₱)</label>
                            <input type="number" name="balance" class="form-control bg-light border-0 p-3" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Membership Type</label>
                            <select name="membership_type" class="form-select bg-light border-0 p-3">
                                <option value="Regular">Regular Member</option>
                                <option value="VIP">VIP Member</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 p-3 rounded-3 fw-bold">CREATE MEMBER ACCOUNT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Top-up Modal -->
<div class="modal fade" id="topupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Account Top-up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../actions/topup_member.php" method="POST">
                <input type="hidden" name="user_id" id="topup-user-id">
                <div class="modal-body p-4">
                    <p class="text-muted">Adding balance to: <strong id="topup-username" class="text-dark"></strong></p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Amount to Add (₱)</label>
                        <input type="number" name="amount" class="form-control bg-light border-0 p-3" placeholder="50.00" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-success w-100 p-3 rounded-3 fw-bold">CONFIRM TOP-UP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTopupModal(id, username) {
    document.getElementById('topup-user-id').value = id;
    document.getElementById('topup-username').innerText = username;
    new bootstrap.Modal(document.getElementById('topupModal')).show();
}

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});
</script>

<?php include('../includes/footer.php'); ?>
