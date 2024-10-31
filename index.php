<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
// Use the Dotenv class
use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['WEATHER_API_KEY']; 
$location = $_POST['location'];
$apiUrl = "https://api.weatherapi.com/v1/current.json?key=$apiKey&q=$location&aqi=no";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Weather Report</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="weather-card">
      <h1 class="title">Weather Report</h1>
      <form action="" method="POST" class="weather-form">
        <input type="text" placeholder="Enter your City..." name="location" class="text-input" required>
        <input type="email" placeholder="Enter your Email ID" name="email" class="text-input" required>
        <button type="submit" class="submit-btn">Get Weather</button>
      </form>
    </div>
    <div>
        <?php
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            } else {
                $weatherData = json_decode($response, true);
                
                // Display the weather information
                if ($weatherData && isset($weatherData['current'])) {
                    echo '<div class="weather-box">';
                    echo "<h1>Current Weather in " . htmlspecialchars($location) . ":</h1><br><br>";
                    echo "<p>Temperature: " . $weatherData['current']["temp_c"] . "°C</p>";
                    echo "<p>Humidity: " . $weatherData['current']["humidity"] . "</p>";
                    echo "<p>Cloud: " . $weatherData['current']["cloud"] . "</p>";
                    echo "</div>";
                }
            }
            
            // Close the cURL session
            curl_close($ch);

          
          //##### Groq AI ########

          $apiUrl = "https://api.groq.com/openai/v1/chat/completions";
          $apiKey = $_ENV['GROQ_API_KEY'];
          $weatherCurrent = $weatherData['current'];
          $email = $_POST['email'];
          // Prepare the data to be sent in the request
          $data = [
              "messages" => [
                  [
                      "role" => "user",
                      "content" => "Write an email paragraph about the weather without any introductory text, headings, or concluding phrases. Start directly with 'Dear Sir/Madam,' and include only the weather data of $location, specifying the temperature as " . $weatherCurrent['temp_c'] . "°C, humidity as " . $weatherCurrent['humidity'] . "%, and cloud cover as " . $weatherCurrent['cloud'] . "%. Format the email in HTML with inline CSS within a <p> tag. Sign off with 'Best regards, Aamir.'"
                  ]
              ],
              "model" => "llama3-8b-8192"
          ];

          // Initialize cURL
          $ch = curl_init($apiUrl);

          // Set cURL options
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_POST, true); 
          curl_setopt($ch, CURLOPT_HTTPHEADER, [
              "Authorization: Bearer $apiKey",
              "Content-Type: application/json"
          ]);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 

          $response = curl_exec($ch);

          // Check for cURL errors
          if (curl_errno($ch)) {
              echo 'cURL error: ' . curl_error($ch);
          } else {
              $responseData = json_decode($response, true);
              // echo json_encode($responseData['choices'][0]['message']['content']); 

              if (isset($responseData['choices'][0]['message']['content'])) {
                $content = $responseData['choices'][0]['message']['content'];
                $mail = new PHPMailer(true);

                //HTML code for sending in Email
$htmlContent = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f4f4f4;
                            margin: 0;
                            padding: 0;
                            color: #333;
                        }
                        .container {
                            width: 100%;
                            max-width: 600px;
                            margin: 0 auto;
                            background-color: #ffffff;
                            border: 1px solid #dddddd;
                            border-radius: 8px;
                            overflow: hidden;
                        }
                        .header {
                            background-color: #4a90e2;
                            color: white;
                            padding: 20px;
                            text-align: center;
                        }
                        .content {
                            padding: 20px;
                        }
                        .footer {
                            background-color: #f4f4f4;
                            color: #777;
                            text-align: center;
                            padding: 10px;
                            font-size: 12px;
                        }
                        .report-section {
                            margin: 20px 0;
                        }
                        .report-section h2 {
                            margin-bottom: 10px;
                            color: #4a90e2;
                        }
                        .report-item {
                            display: flex;
                            justify-content: space-between;
                            margin-bottom: 8px;
                        }
                        .report-item div {
                            font-weight: bold;
                        }
                        .icon {
                            width: 50px;
                            height: 50px;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <!-- Header Section -->
                        <div class="header">
                            <h1>Todays Weather Report</h1>
                            <p>Stay prepared with the latest weather update</p>
                        </div>

                        <!-- Content Section -->
                        <div class="content">
                            <div class="report-section">
                                <h2>Location: '.$location.'</h2>
                                <div class="report-item">
                                    <div>Condition:</div>
                                    <div>
                                        '.$weatherCurrent['condition']['text'].'
                                    </div>
                                </div>
                                <div class="report-item">
                                    <div>Temperature:</div>
                                    <div>'.$weatherCurrent['temp_c'].'&deg;</div>
                                </div>
                                <div class="report-item">
                                    <div>Cloud:</div>
                                    <div>'.$weatherCurrent['cloud'].'%</div>
                                </div>
                                <div class="report-item">
                                    <div>Humidity:</div>
                                    <div>'.$weatherCurrent['humidity'].'%</div>
                                </div>
                                <div class="report-item">
                                    <div>Wind Speed:</div>
                                    <div>'.$weatherCurrent['wind_kph'].'</div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Section -->
                        <div class="footer">
                            <p>Weather data provided by WeatherAPI. This is an automated message; please do not reply.</p>
                            <p>&copy; 2024 Weather Report Service</p>
                        </div>
                    </div>
                    <br> <br>
                </body>
                </html>
                ';
                try {
                    // SMTP server settings for sending mail
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; 
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['MAIL_USERNAME'];
                    $mail->Password = $_ENV['MAIL_PASSWORD'];
                    $mail->SMTPSecure = 'tls'; 
                    $mail->Port = 587;

                    // Email settings
                    $mail->setFrom($_ENV['MAIL_USERNAME'], 'Aamir');
                    $mail->addAddress($email, 'Recipient Aamir'); 
                    $mail->isHTML(true); 
                    $mail->Subject = 'Weather Report';
                    $mail->Body    = $htmlContent . $responseData['choices'][0]['message']['content'];
                    $mail->AltBody = 'This is a weather report';

                    $mail->send();
                    echo '<h3>Email has been sent</h3>';
                } catch (Exception $e) {
                    if($mail->ErrorInfo == "Invalid address: (to):"){
                      echo 'Please insert Email';
                    } else {
                      echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                    }
                }
            } else {
                echo 'Content not found in the response.';
            }
          }

          // Close cURL
          curl_close($ch);

        ?>
    </div>
  </div>
</body>
</html>
