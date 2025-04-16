<?php
// Include database connection
include 'db_connect.php';

// UltraMsg API credentials
$instanceId = "instance112319"; 
$token = "razoarhp3k7ze9db"; 
$url = "https://api.ultramsg.com/$instanceId/messages/chat";

// Get the donation details
if(isset($_POST['donor-name']) && isset($_POST['donor-phone'])) {
    // If this is being called directly after form submission
    $donorName = $_POST['donor-name'];
    $donorPhone = $_POST['donor-phone'];
    $foodType = $_POST['food-type'];
    $quantity = $_POST['quantity'];
    $description = $_POST['food-description'];
    $deliveryTime = $_POST['delivery-time'];
    $transport = $_POST['transport'];
    $recipientName = $_POST['recipient-name'];
    
    // Get recipient phone from hidden field
    if(isset($_POST['recipient_phone'])) {
        $recipientPhone = $_POST['recipient_phone'];
    } else {
        // Look up recipient phone from users table
        $recipientQuery = "SELECT phone FROM users WHERE username = ? AND role = 'recipient'";
        $stmt = $conn->prepare($recipientQuery);
        $stmt->bind_param("s", $recipientName);
        $stmt->execute();
        $phoneResult = $stmt->get_result();
        
        if ($phoneResult && $phoneResult->num_rows > 0) {
            $phoneRow = $phoneResult->fetch_assoc();
            $recipientPhone = $phoneRow['phone'];
        } else {
            die("Error: Recipient phone number not found");
        }
    }
    
    // Look up donor email in users table (if needed)
    $donorQuery = "SELECT email FROM users WHERE phone = ? AND role = 'donor'";
    $stmt = $conn->prepare($donorQuery);
    $stmt->bind_param("s", $donorPhone);
    $stmt->execute();
    $emailResult = $stmt->get_result();
    $donorEmail = "";
    
    if ($emailResult && $emailResult->num_rows > 0) {
        $emailRow = $emailResult->fetch_assoc();
        $donorEmail = $emailRow['email'];
    }
} else {
    // If no direct form data, get the most recent donation from database
    $query = "SELECT * FROM donations ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $donorName = $row['donor_name'];
        $donorPhone = $row['donor_phone'];
        $foodType = $row['food_type'];
        $quantity = $row['quantity'];
        $description = $row['food_description'];
        $deliveryTime = $row['delivery_time'];
        $transport = $row['transport'];
        $recipientName = $row['recipient_name'];
        
        // Look up recipient phone from users table
        $recipientQuery = "SELECT phone FROM users WHERE username = ? AND role = 'recipient'";
        $stmt = $conn->prepare($recipientQuery);
        $stmt->bind_param("s", $recipientName);
        $stmt->execute();
        $phoneResult = $stmt->get_result();
        
        if ($phoneResult && $phoneResult->num_rows > 0) {
            $phoneRow = $phoneResult->fetch_assoc();
            $recipientPhone = $phoneRow['phone'];
        } else {
            die("Error: Recipient phone number not found");
        }
    } else {
        die("Error: No donations found");
    }
}

// Format phone numbers correctly
$donorPhone = formatPhoneNumber($donorPhone);
$recipientPhone = formatPhoneNumber($recipientPhone);

// 1. Message to recipient
$recipientMessage = "🍽️ *New Donation Alert!* 🍽️\n\n";
$recipientMessage .= "*Hello $recipientName,*\n\n";
$recipientMessage .= "*You have received a new donation with the following details:*\n";
$recipientMessage .= "• Donor: $donorName\n";
$recipientMessage .= "• Food Type: $foodType\n";
$recipientMessage .= "• Quantity: $quantity servings\n";
$recipientMessage .= "• Description: $description\n";
$recipientMessage .= "• Delivery Time: $deliveryTime\n";
$recipientMessage .= "• Transport Method: $transport\n\n";
$recipientMessage .= "Contact donor at: $donorPhone\n\n";
$recipientMessage .= "Thank you for participating in MealConnect!";

// 2. Message to donor
$donorMessage = "✅ *Donation Confirmation* ✅\n\n";
$donorMessage .= "*Hello $donorName,*\n\n";
$donorMessage .= "Your donation has been successfully recorded. Thank you for your generosity!\n\n";
$donorMessage .= "*Donation Details:*\n";
$donorMessage .= "• Food Type: $foodType\n";
$donorMessage .= "• Quantity: $quantity servings\n";
$donorMessage .= "• Description: $description\n";
$donorMessage .= "• Delivery Time: $deliveryTime\n";
$donorMessage .= "• Transport Method: $transport\n\n";
$donorMessage .= "*Recipient Details:*\n";
$donorMessage .= "• Name: $recipientName\n";
$donorMessage .= "• Contact: $recipientPhone\n\n";
$donorMessage .= "Thank you for making a difference with MealConnect!";

// Send message to recipient
$recipientResult = sendWhatsAppMessage($url, $token, $recipientPhone, $recipientMessage);

// Send message to donor
$donorResult = sendWhatsAppMessage($url, $token, $donorPhone, $donorMessage);

// Output results
echo "<div style='margin: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 8px;'>";

if(json_decode($recipientResult)->sent == 'true') {
    echo "<p style='color: green;'>✓ WhatsApp notification sent successfully to recipient!</p>";
} else {
    echo "<p style='color: red;'>✗ Error sending WhatsApp notification to recipient: " . $recipientResult . "</p>";
}

if(json_decode($donorResult)->sent == 'true') {
    echo "<p style='color: green;'>✓ WhatsApp confirmation sent successfully to donor!</p>";
} else {
    echo "<p style='color: red;'>✗ Error sending WhatsApp confirmation to donor: " . $donorResult . "</p>";
}

echo "</div>";

// Helper function to format phone numbers
function formatPhoneNumber($phone) {
    // Remove any non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Ensure it has the +91 country code (for India)
    if (substr($phone, 0, 2) !== '91') {
        $phone = '91' . $phone;
    }
    
    // Add the + sign
    if (substr($phone, 0, 1) !== '+') {
        $phone = '+' . $phone;
    }
    
    return $phone;
}

// Helper function to send WhatsApp messages
function sendWhatsAppMessage($url, $token, $to, $body) {
    $data = [
        'token' => $token,
        'to' => $to,
        'body' => $body
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}
?>