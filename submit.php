<?php
include "connection.php";

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $installation_order = mysqli_real_escape_string($conn, $_POST['orderNo']);
    $model_name = mysqli_real_escape_string($conn, $_POST['modelName']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobileNumber']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $serial_number = mysqli_real_escape_string($conn, $_POST['serialNumber']);
    $purchase_date = mysqli_real_escape_string($conn, $_POST['purchaseDate']);
    $invoice_pdf = $_FILES['invoice']['name'];
    $warranty_pdf = $_FILES['waranty']['name'];

    
    $sql = "INSERT INTO registrations (installation_order, model_name, name, email, mobile_number, address, city, state, pincode, serial_number, purchase_date, invoice_pdf, warranty_pdf)
            VALUES ('$installation_order', '$model_name', '$name', '$email', '$mobile_number', '$address', '$city', '$state', '$pincode', '$serial_number', '$purchase_date', '$invoice_pdf', '$warranty_pdf')";

    if (mysqli_query($conn, $sql)) {
       
        $upload_dir = 'uploads/';
        $invoice_temp_file = $_FILES['invoice']['tmp_name'];
        $warranty_temp_file = $_FILES['waranty']['tmp_name'];

        $invoice_destination = $upload_dir . $invoice_pdf;
        $warranty_destination = $upload_dir . $warranty_pdf;

        if (move_uploaded_file($invoice_temp_file, $invoice_destination) &&
            move_uploaded_file($warranty_temp_file, $warranty_destination)) {
            
            $to = 'hr@unbundl.com';
            $subject = 'Warranty Registration Form Submission';
            $message = 'Installation Order No: ' . $installation_order . "\n";
            $message .= 'Model Name: ' . $model_name . "\n";
            

            $headers = 'From: hari7706shukla@gmail.com' . $email . "\r\n" .
                'Reply-To: harigamingstudio@gmail.com' . $email . "\r\n";

            if (mail($to, $subject, $message, $headers)) {
                
                echo "Thank you for sharing the documents with us. Our team will verify the details and get back to you within 7 working days. FFIPL reserves the right to reject the warranty application if the registration terms & conditions are not met. Please refer to the product’s user manual for detailed warranty terms & conditions.";
            } else {
                echo "Error sending email.";
            }
        } else {
            echo "Error moving files.";
        }
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    
    mysqli_close($conn);
}
?>
