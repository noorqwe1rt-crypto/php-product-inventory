<?php
session_start();

// ----------------------------
// Part 1: Initial Products Data
// ----------------------------
$products = [
    [
        'id' => 1,
        'name' => 'Laptop',
        'description' => 'High performance laptop for professionals.',
        'price' => 1200.50,
        'category' => 'Electronics'
    ],
    [
        'id' => 2,
        'name' => 'Office Chair',
        'description' => 'Ergonomic chair with lumbar support.',
        'price' => 250.00,
        'category' => 'Furniture'
    ]
];

// Categories for dropdown
$categories = ["Electronics", "Furniture", "Clothing", "Books", "Other"];

$errors = [];
$submittedData = [
    'name' => '',
    'description' => '',
    'price' => '',
    'category' => ''
];
$successMessage = '';

// ----------------------------
// Part 2: Handle Form Submission
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedData['name'] = htmlspecialchars(trim($_POST['name'] ?? ''));
    $submittedData['description'] = htmlspecialchars(trim($_POST['description'] ?? ''));
    $submittedData['price'] = htmlspecialchars(trim($_POST['price'] ?? ''));
    $submittedData['category'] = htmlspecialchars(trim($_POST['category'] ?? ''));

    // Validation
    if ($submittedData['name'] === '') {
        $errors['name'] = "Product name is required.";
    }
    if ($submittedData['description'] === '') {
        $errors['description'] = "Description is required.";
    }
    if ($submittedData['price'] === '' || !is_numeric($submittedData['price']) || $submittedData['price'] <= 0) {
        $errors['price'] = "Valid price is required.";
    }
    if ($submittedData['category'] === '' || !in_array($submittedData['category'], $categories)) {
        $errors['category'] = "Please select a valid category.";
    }

    // If valid → Add to products
    if (empty($errors)) {
        $newId = end($products)['id'] + 1;
        $products[] = [
            'id' => $newId,
            'name' => $submittedData['name'],
            'description' => $submittedData['description'],
            'price' => (float)$submittedData['price'],
            'category' => $submittedData['category']
        ];

        $successMessage = "Product added successfully!";
        $_SESSION['success'] = $successMessage;
        $submittedData = ['name' => '', 'description' => '', 'price' => '', 'category' => ''];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4 text-center">📦 Product Inventory</h1>

    <!-- Success Alert -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Error Alert -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">Please fix the errors below.</div>
    <?php endif; ?>

    <div class="row">
        <!-- Product Table -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Product List</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?= $p['id']; ?></td>
                                    <td><?= htmlspecialchars($p['name']); ?></td>
                                    <td><?= htmlspecialchars($p['description']); ?></td>
                                    <td>$<?= number_format($p['price'], 2); ?></td>
                                    <td><?= htmlspecialchars($p['category']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Product Form -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Add New Product</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control <?php if(isset($errors['name'])) echo 'is-invalid'; ?>" value="<?= $submittedData['name']; ?>">
                            <?php if(isset($errors['name'])): ?><div class="invalid-feedback"><?= $errors['name']; ?></div><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control <?php if(isset($errors['description'])) echo 'is-invalid'; ?>"><?= $submittedData['description']; ?></textarea>
                            <?php if(isset($errors['description'])): ?><div class="invalid-feedback"><?= $errors['description']; ?></div><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price ($)</label>
                            <input type="text" name="price" class="form-control <?php if(isset($errors['price'])) echo 'is-invalid'; ?>" value="<?= $submittedData['price']; ?>">
                            <?php if(isset($errors['price'])): ?><div class="invalid-feedback"><?= $errors['price']; ?></div><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select <?php if(isset($errors['category'])) echo 'is-invalid'; ?>">
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat; ?>" <?php if($submittedData['category'] === $cat) echo 'selected'; ?>><?= $cat; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if(isset($errors['category'])): ?><div class="invalid-feedback"><?= $errors['category']; ?></div><?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Add Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
