<?php
ob_start();
include 'dbconnect.php';
include 'cart_count.php';

if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['userID'];
// Fetch the application ID for the logged-in user
$appQuery = "SELECT application_id FROM merchant_applications WHERE user_id = ?";
$stmtApp = $connection->prepare($appQuery);
$stmtApp->bind_param('s', $user_id);
$stmtApp->execute();
$appResult = $stmtApp->get_result();

if ($appResult && $appResult->num_rows > 0) {
    $appRow = $appResult->fetch_assoc();
    $application_id = $appRow['application_id'];
    
    // Store application_id in session
    $_SESSION['application_id'] = $application_id;
} else {
    die('Application ID not found for this user.');
}

?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="heading-with-count" id="page-heading">Add New Product</h4>
        </div>
    </div>
    
    <div class="add-product-container">
        <form id="product-form" action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4">
                    <div class="image-upload-container">
                        <div class="image-preview">
                            <div id="image-preview">
                                <i class="ri-image-add-line"></i>
                                <span>Upload Product Image</span>
                            </div>
                            <img src="" alt="Product Image" class="prod-img" id="product_image" style="display: none;">
                        </div>
                        <input type="file" id="image-upload" name="product_image" accept="image/*" style="display: none;" required>
                        <button type="button" class="btn btn-outline-primary w-100 mt-3" id="image-btn">
                            <i class="ri-upload-2-line"></i> Choose Image
                        </button>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label for="product_name">Product Name</label>
                        <input type="text" name="product_name" class="form-control" placeholder="Enter product name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="stock">Stock</label>
                                <input type="number" name="stock" class="form-control" placeholder="Available quantity" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="product_category">Category</label>
                                <select name="product_category" class="form-select" required>
                                    <option value="">Select category</option>
                                    <option value="Rugs">Rugs</option>
                                    <option value="Tables">Tables</option>
                                    <option value="Dining">Dining</option>
                                    <option value="Lighting">Lighting</option>
                                    <option value="Storage">Storage</option>
                                    <option value="Furniture">Furniture</option>
                                    <option value="Kitchen">Kitchen</option>
                                    <option value="Decor">Decors</option>
                                    <option value="Coasters">Coasters</option>
                                    <option value="Trays">Trays</option>
                                    <option value="Accessories">Accessories</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="product_desc">Product Description</label>
                        <textarea name="product_desc" class="form-control" rows="3" placeholder="Describe your product" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="material">Materials Used</label>
                        <input type="text" name="material" class="form-control" placeholder="Materials Used" required>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label>Colors</label>
                        <div class="input-group">
                            <input type="text" name="color[]" class="form-control" placeholder="Add a color">
                            <button type="button" class="btn btn-primary" id="add-color">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                        <div id="color-container"></div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Sizes</label>
                                <div class="input-group">
                                    <input type="text" name="size[]" class="form-control" style="flex: 1;" placeholder="Add a size">
                                </div>
                                <div id="size-container"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Prices</label>
                                <div class="input-group">
                                    <input type="number" name="price[]" class="form-control" placeholder="Enter price">
                                    <button type="button" class="btn btn-primary" id="add-size-price">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                                <div id="price-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12" id="form-buttons">
                        <!--=============== ADD PRODUCT BUTTON ===============-->
                        <button type="submit" class="btn btn-primary w-100" id="add_product" name="add_product">
                            <i class="ri-add-line"></i> Add Product
                        </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image upload functionality
    const imageUpload = document.getElementById('image-upload');
    const imageBtn = document.getElementById('image-btn');
    const productImage = document.getElementById('product_image');
    const imagePreview = document.getElementById('image-preview');

    // Add click event for image button
    if (imageBtn) {
        imageBtn.addEventListener('click', function() {
            imageUpload.click();
        });
    }

    // Add change event for image upload
    if (imageUpload) {
        imageUpload.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    productImage.src = e.target.result;
                    productImage.style.display = 'block';
                    imagePreview.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Add color button functionality
    const addColorBtn = document.getElementById('add-color');
    const colorContainer = document.getElementById('color-container');

    if (addColorBtn) {
        addColorBtn.addEventListener('click', function() {
            const newColorInput = document.createElement('div');
            newColorInput.classList.add('input-group', 'mt-2');
            newColorInput.innerHTML = `
                <input type="text" name="color[]" class="form-control" placeholder="Add a color">
                <button type="button" class="btn btn-danger remove-color">
                    <i class="ri-delete-bin-line"></i>
                </button>
            `;
            colorContainer.appendChild(newColorInput);

            // Add event listener to the remove button
            newColorInput.querySelector('.remove-color').addEventListener('click', function() {
                colorContainer.removeChild(newColorInput);
            });
        });
    }

    // Add size/price button functionality
    const addSizePriceBtn = document.getElementById('add-size-price');
    const sizeContainer = document.getElementById('size-container');
    const priceContainer = document.getElementById('price-container');

    if (addSizePriceBtn) {
        addSizePriceBtn.addEventListener('click', function() {
            const newSizeInput = document.createElement('div');
            newSizeInput.classList.add('input-group', 'mt-2');
            newSizeInput.innerHTML = `
                <input type="text" name="size[]" class="form-control" style="flex: 1;" placeholder="Add a size">
            `;
            sizeContainer.appendChild(newSizeInput);

            const newPriceInput = document.createElement('div');
            newPriceInput.classList.add('input-group', 'mt-2');
            newPriceInput.innerHTML = `
                <input type="number" name="price[]" class="form-control" placeholder="Enter price">
                <button type="button" class="btn btn-danger remove-size-price">
                    <i class="ri-delete-bin-line"></i>
                </button>
            `;
            priceContainer.appendChild(newPriceInput);

            // Add event listener to the remove button
            newPriceInput.querySelector('.remove-size-price').addEventListener('click', function() {
                sizeContainer.removeChild(newSizeInput);
                priceContainer.removeChild(newPriceInput);
            });
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include 'mersidebar.php';
?> 