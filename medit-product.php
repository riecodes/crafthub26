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
if (isset($_GET['product_id'])){
    $product_id = $_GET['product_id'];

    $selectproduct = "SELECT * FROM merchant_products WHERE product_id = '$product_id'";
    $result = mysqli_query($connection, $selectproduct);

    if(!$result){
        die("Query Failed: " . mysqli_error($connection));
    } else {
        $row = mysqli_fetch_assoc($result);
    }

    $selectcolor = "SELECT * FROM product_color WHERE product_id = '$product_id'";
    $color = $connection->query($selectcolor);

    $selectsize = "SELECT * FROM product_sizes WHERE product_id = '$product_id'";
    $sizes = $connection->query($selectsize);
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="heading-with-count">Edit Product</h4>
        </div>
    </div>
    
    <div class="add-product-container">
    <form id="product-form" action="edit_product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" id="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
        
        <div class="row">
            <div class="col-md-4">
                <div class="image-upload-container">
                    <div class="image-preview">
                        <div id="image-preview">                         
                        </div>
                        <?php if (!empty($row['product_img'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['product_img']); ?>" alt="Product Image" class="prod-img" id="product_image" style="display: block;">
                        <?php endif; ?>
                    </div>
                    <input type="file" id="image-upload" name="product_image" accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-outline-primary w-100 mt-3" id="image-btn">
                        <i class="ri-upload-2-line"></i> Choose Image
                    </button>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group mb-3">
                    <label for="product_name">Product Name</label>
                    <input type="text" name="product_name" class="form-control" placeholder="Enter product name" required value="<?php echo htmlspecialchars($row['product_name']); ?>">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="stock">Stock</label>
                            <input type="number" name="stock" class="form-control" placeholder="Available quantity" required value="<?php echo htmlspecialchars($row['stock']); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="product_category">Category</label>
                            <select name="product_category" class="form-select" required>
                                <option value="">Select category</option>
                                <?php
                                $categories = ["Rugs", "Tables", "Dining", "Lighting", "Storage", "Furniture", "Kitchen", "Decor", "Coasters", "Trays", "Accessories", "Others"];
                                foreach ($categories as $category) {
                                    $selected = ($row['category'] == $category) ? 'selected' : '';
                                    echo "<option value='$category' $selected>$category</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="product_desc">Product Description</label>
                    <textarea name="product_desc" class="form-control" rows="3" placeholder="Describe your product" required><?php echo htmlspecialchars($row['product_desc']); ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="material">Materials Used</label>
                    <input type="text" name="material" class="form-control" placeholder="Materials Used" value="<?php echo htmlspecialchars($row['material']); ?>" required>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label>Colors</label>
                    <div id="color-container">
                        <?php 
                        // Fetch colors data from the database
                        while($colors = mysqli_fetch_assoc($color)) {
                        ?>
                        <div class="input-group mt-2">
                            <input type="hidden" name="color_id[]" value="<?php echo $colors['color_id']; ?>">
                            <input type="text" name="color[]" class="form-control" value="<?php echo htmlspecialchars($colors['color']); ?>" placeholder="Add a color">
                            <button type="button" class="btn btn-danger remove-color">Remove</button>
                        </div>
                        <?php 
                        }
                        ?>
                    </div>
                    <button type="button" class="btn btn-primary mt-2" id="add-color"><i class="ri-add-line"></i></button>
                </div>
            </div>

            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Sizes</label>
                            <div id="size-container">
                                <?php 
                                // Fetch sizes data from the database
                                while($size = mysqli_fetch_assoc($sizes)) {
                                ?>
                                <div class="input-group mt-2">
                                    <input type="hidden" name="size_id[]" value="<?php echo $size['size_id']; ?>">
                                    <input type="text" name="size[]" class="form-control" value="<?php echo htmlspecialchars($size['sizes']); ?>" placeholder="Add a size">
                                </div>
                                <?php 
                                }
                                ?>
                            </div>
                            <button type="button" class="btn btn-primary mt-2" id="add-size"><i class="ri-add-line"></i></button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Prices</label>
                            <div id="price-container">
                                <?php 
                                // Use the same sizes data for price input since they are linked
                                mysqli_data_seek($sizes, 0); // Reset the sizes result pointer
                                while($size = mysqli_fetch_assoc($sizes)) {
                                ?>
                                <div class="input-group mt-2">
                                    <input type="hidden" name="size_id[]" value="<?php echo $size['size_id']; ?>">
                                    <input type="number" name="price[]" class="form-control" value="<?php echo htmlspecialchars($size['price']); ?>" placeholder="Enter price" min="0" step="0.01">
                                    <button type="button" class="btn btn-danger remove-size-price">Remove</button>
                                </div>
                                <?php 
                                }
                                ?>
                            </div>
                            <button type="button" class="btn btn-primary mt-2" id="add-size-price"><i class="ri-add-line"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12" id="form-buttons">
                <div class="d-flex gap-2 w-100">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <button type="button" class="btn btn-danger w-100" id="delete">
                        <i class="ri-delete-bin-line"></i> Delete Product
                    </button>
                    <button type="submit" class="btn btn-success w-100" iid="add_product" name="edit_product">
                        <i class="ri-save-line"></i> Save Changes
                    </button>
                </div>
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
                <input type="hidden" name="color_id[]" value="">
                <input type="text" name="color[]" class="form-control" placeholder="Add a color">
                <button type="button" class="btn btn-danger remove-color">Remove</button>
            `;
            colorContainer.appendChild(newColorInput);

            // Add event listener to the remove button
            newColorInput.querySelector('.remove-color').addEventListener('click', function() {
                colorContainer.removeChild(newColorInput);
            });
        });
    }

    // Add event listeners to existing remove color buttons
    document.querySelectorAll('.remove-color').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.input-group').remove();
        });
    });

    // Add size/price button functionality
    const addSizePriceBtn = document.getElementById('add-size-price');
    const sizeContainer = document.getElementById('size-container');
    const priceContainer = document.getElementById('price-container');

    if (addSizePriceBtn) {
        addSizePriceBtn.addEventListener('click', function() {
            const newSizeInput = document.createElement('div');
            newSizeInput.classList.add('input-group', 'mt-2');
            newSizeInput.innerHTML = `
                <input type="hidden" name="size_id[]" value="">
                <input type="text" name="size[]" class="form-control" placeholder="Add a size">
            `;
            sizeContainer.appendChild(newSizeInput);

            const newPriceInput = document.createElement('div');
            newPriceInput.classList.add('input-group', 'mt-2');
            newPriceInput.innerHTML = `
                <input type="number" name="price[]" class="form-control" placeholder="Enter price" min="0" step="0.01">
                <button type="button" class="btn btn-danger remove-size-price">Remove</button>
            `;
            priceContainer.appendChild(newPriceInput);

            // Add event listener to the remove button
            newPriceInput.querySelector('.remove-size-price').addEventListener('click', function() {
                sizeContainer.removeChild(newSizeInput);
                priceContainer.removeChild(newPriceInput);
            });
        });
    }

    // Add event listeners to existing remove size-price buttons
    document.querySelectorAll('.remove-size-price').forEach(button => {
        button.addEventListener('click', function() {
            const index = Array.from(priceContainer.children).indexOf(this.closest('.input-group'));
            if (index !== -1 && sizeContainer.children[index]) {
                sizeContainer.children[index].remove();
                this.closest('.input-group').remove();
            }
        });
    });

    // Delete button functionality
    const deleteBtn = document.getElementById('delete');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this product?')) {
                const productId = document.querySelector('input[name="product_id"]').value;
                window.location.href = 'delete_product.php?product_id=' + productId;
            }
        });
    }

    // Form submission handling
    const form = document.getElementById('product-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Add any validation if needed
            return true; // Allow form submission
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include 'mersidebar.php';
?> 