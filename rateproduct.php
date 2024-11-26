<?php 
    include 'dbconnect.php';
    include 'cart_count.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
       
    }
    $user_id = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
    

    if(isset($_GET['order_id'])){
        $order_id = $_GET['order_id'];

        $select = "SELECT
                    o.product_id,
                    o.order_id, 
                    p.product_img, 
                    p.product_name

                FROM 
                    orders o
                JOIN 
                    merchant_products p ON o.product_id = p.product_id
                WHERE 
                    o.order_id = '$order_id' AND o.status = 'order received'";

                    $result = mysqli_query($connection, $select);

                    if(!$result){
                        die("query Failed".mysqli_error($connection));
                    }else{
                        $row = mysqli_fetch_assoc($result);

                    }

                }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: An Online Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/rateproduct.css">
    <link rel="stylesheet" href="css/navigation.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
        <?php include 'nav.php'; ?>
        
    <form action="process_feedback.php" method="post">
        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
        <!--=============== PRODUCT CONTAINER ===============-->
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="product-image-container">
                        <img id="product-image" class="product-image" src="<?php echo 'uploads/' . basename($row['product_img']); ?>" alt="Product Image">
                    </div>
                </div>
                <div class="col-md-9 product-details mt-5">
                    <h6>Product Name</h6>
                    <p><?php echo $row['product_name']; ?></p>
                </div>
            </div>
            <!--=============== END OF PRODUCT CONTAINER ===============-->
            
            <!--=============== PRODUCT REVIEW HEADER ===============-->
            <div class="review-header">
                Product Review
            </div>

           <!--=============== RATINGS AND FEEDBACK FORM ===============-->
           <div class="row">
                <div class="col-md-3">
                    <h6>Product Quality</h6> <!--=============== PRODUCT QUALITY ===============-->
                </div>
                <div class="col-md-9">
                    <div class="rating"> 
                        <input type="radio" name="quality-rating" value="5" id="quality-5"><label for="quality-5">☆</label>
                        <input type="radio" name="quality-rating" value="4" id="quality-4"><label for="quality-4">☆</label> 
                        <input type="radio" name="quality-rating" value="3" id="quality-3"><label for="quality-3">☆</label>
                        <input type="radio" name="quality-rating" value="2" id="quality-2"><label for="quality-2">☆</label>
                        <input type="radio" name="quality-rating" value="1" id="quality-1"><label for="quality-1">☆</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <h6>Product Price</h6> <!--=============== PRODUCT PRICE ===============-->
                </div>
                <div class="col-md-9">
                    <div class="rating"> 
                        <input type="radio" name="price-rating" value="5" id="price-5"><label for="price-5">☆</label> 
                        <input type="radio" name="price-rating" value="4" id="price-4"><label for="price-4">☆</label> 
                        <input type="radio" name="price-rating" value="3" id="price-3"><label for="price-3">☆</label> 
                        <input type="radio" name="price-rating" value="2" id="price-2"><label for="price-2">☆</label> 
                        <input type="radio" name="price-rating" value="1" id="price-1"><label for="price-1">☆</label> 
                    </div>
                </div>
            </div>

            <div class="row"> 
                <div class="col-md-3">
                    <h6>Seller Service</h6> <!--=============== SELLER SERVICE ===============-->
                </div>
                <div class="col-md-9">
                    <div class="rating"> 
                        <input type="radio" name="service-rating" value="5" id="service-5"><label for="service-5">☆</label>
                        <input type="radio" name="service-rating" value="4" id="service-4"><label for="service-4">☆</label>  
                        <input type="radio" name="service-rating" value="3" id="service-3"><label for="service-3">☆</label> 
                        <input type="radio" name="service-rating" value="2" id="service-2"><label for="service-2">☆</label> 
                        <input type="radio" name="service-rating" value="1" id="service-1"><label for="service-1">☆</label> 
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mt-3">
                    <textarea class="form-control" rows="3" id="notes" name="notes" placeholder="Enter your feedback here..."></textarea>
                </div>
            </div>

            <!--=============== SUBMIT FEEDBACK BUTTON ===============-->
            <div class="row mt-3">
                <div class="col-md-12">
                    <input type="submit" class="btn btn-success" id="submit-btn" name="submit" value="Submit Feedback">
                </div>
            </div>
            <!--=============== END OF SUBMIT FEEDBACK BUTTON ===============-->
        </div>
    </form>
    <!--=============== END OF FORM ===============-->
    <?php include 'footer.php'; ?>
    
<script src="cart.js"></script>
</body>
</html>