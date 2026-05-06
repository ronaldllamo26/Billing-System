<?php
require_once('../config/db_connect.php');
include('../includes/header.php');
include('../includes/sidebar.php');

// Fetch all inventory items
$stmt = $pdo->query("SELECT * FROM inventory ORDER BY created_at DESC");
$items = $stmt->fetchAll();
?>

<div id="content-wrapper">
    <header class="app-toolbar shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold">Inventory Management</h5>
            <div class="vr"></div>
            <button class="btn btn-sm btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i data-lucide="plus-circle" style="width: 14px;"></i> Add New Item
            </button>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark border">Total Items: <?php echo count($items); ?></span>
        </div>
    </header>

    <div class="app-canvas" style="display: block; overflow-y: auto;">
        <div class="station-container p-0 overflow-hidden">
            <table class="table table-hover mb-0" style="font-size: 13px;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Preview</th>
                        <th class="py-3 border-0">Item Name</th>
                        <th class="py-3 border-0">Category</th>
                        <th class="py-3 border-0">Price</th>
                        <th class="py-3 border-0">Stock</th>
                        <th class="py-3 text-end pe-4 border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="ps-4 align-middle">
                            <img src="../assets/img/<?php echo $item['item_image']; ?>" 
                                 class="rounded-3 shadow-sm" style="width: 45px; height: 45px; object-fit: cover;"
                                 onerror="this.src='https://via.placeholder.com/45?text=Food'">
                        </td>
                        <td class="fw-bold align-middle"><?php echo $item['item_name']; ?></td>
                        <td class="align-middle"><span class="badge bg-light text-dark border"><?php echo $item['category']; ?></span></td>
                        <td class="fw-bold align-middle text-primary">₱<?php echo number_format($item['price'], 2); ?></td>
                        <td class="align-middle">
                            <?php if($item['stock_quantity'] <= 5): ?>
                                <span class="text-danger fw-bold"><?php echo $item['stock_quantity']; ?> (Low)</span>
                            <?php else: ?>
                                <span class="text-success fw-bold"><?php echo $item['stock_quantity']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4 align-middle">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-light border" 
                                        onclick="openEditModal(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                    <i data-lucide="edit-3" style="width: 14px;"></i>
                                </button>
                                <button class="btn btn-sm btn-light border text-danger" 
                                        onclick="if(confirm('Are you sure you want to delete this item?')) window.location.href='../actions/delete_inventory.php?id=<?php echo $item['id']; ?>'">
                                    <i data-lucide="trash-2" style="width: 14px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Add New Paninda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../actions/save_inventory.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Product Name</label>
                        <input type="text" name="item_name" class="form-control bg-light border-0 p-3" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Price (₱)</label>
                            <input type="number" name="price" step="0.01" class="form-control bg-light border-0 p-3" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Initial Stock</label>
                            <input type="number" name="stock" class="form-control bg-light border-0 p-3" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Category</label>
                        <select name="category" class="form-select bg-light border-0 p-3">
                            <option value="Food">Food</option>
                            <option value="Drinks">Drinks</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Product Image</label>
                        <input type="file" name="item_image" class="form-control bg-light border-0 p-3" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-primary w-100 p-3 rounded-3 fw-bold">SAVE PRODUCT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Edit Paninda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../actions/update_inventory.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Product Name</label>
                        <input type="text" name="item_name" id="edit-name" class="form-control bg-light border-0 p-3" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Price (₱)</label>
                            <input type="number" name="price" id="edit-price" step="0.01" class="form-control bg-light border-0 p-3" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Stock Quantity</label>
                            <input type="number" name="stock" id="edit-stock" class="form-control bg-light border-0 p-3" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Category</label>
                        <select name="category" id="edit-category" class="form-select bg-light border-0 p-3">
                            <option value="Food">Food</option>
                            <option value="Drinks">Drinks</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Update Product Image (Optional)</label>
                        <input type="file" name="item_image" class="form-control bg-light border-0 p-3" accept="image/*">
                        <small class="text-muted" style="font-size: 10px;">Leave blank to keep the current image.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-success w-100 p-3 rounded-3 fw-bold">UPDATE PRODUCT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(item) {
    document.getElementById('edit-id').value = item.id;
    document.getElementById('edit-name').value = item.item_name;
    document.getElementById('edit-price').value = item.price;
    document.getElementById('edit-stock').value = item.stock_quantity;
    document.getElementById('edit-category').value = item.category;
    
    var editModal = new bootstrap.Modal(document.getElementById('editItemModal'));
    editModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>

<?php include('../includes/footer.php'); ?>
