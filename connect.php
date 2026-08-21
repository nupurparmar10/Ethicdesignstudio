<?php
$con = mysqli_connect("localhost", "root", "", "u622759878_ethicstore");
mysqli_set_charset($con, 'utf8');
date_default_timezone_set("Asia/Kolkata");

// Prevent browser caching for security (specifically back-button login bypass)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$home=$about=$customer_service=$store=$product_type=false;

/**
 * Sanitize and validate input values for use across the application.
 *
 * @param mixed  $value   The raw input value.
 * @param string $type    Expected input type: int|float|email|url|alnum|alpha|string|bool.
 * @param array  $options Optional settings like max_length, min, max, pattern.
 * @return mixed|null     Sanitized value, or null when validation fails.
 */
function sanitize_input($value, $type = 'string', array $options = [])
{
    if (is_array($value)) {
        $result = [];
        foreach ($value as $key => $item) {
            $result[$key] = sanitize_input($item, $type, $options);
        }
        return $result;
    }

    if ($value === null) {
        return null;
    }

    $value = trim((string)$value);

    switch ($type) {
        case 'int':
            $opts = ['options' => ['min_range' => $options['min'] ?? PHP_INT_MIN, 'max_range' => $options['max'] ?? PHP_INT_MAX]];
            $int = filter_var($value, FILTER_VALIDATE_INT, $opts);
            return ($int === false) ? null : $int;

        case 'float':
            $float = filter_var($value, FILTER_VALIDATE_FLOAT);
            return ($float === false) ? null : $float;

        case 'bool':
            $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            return $bool;

        case 'email':
            $email = filter_var($value, FILTER_VALIDATE_EMAIL);
            return ($email === false) ? null : $email;

        case 'url':
            $url = filter_var($value, FILTER_VALIDATE_URL);
            return ($url === false) ? null : $url;

        case 'alnum':
            if (preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                return $value;
            }
            return null;

        case 'alpha':
            if (preg_match('/^[a-zA-Z]+$/', $value)) {
                return $value;
            }
            return null;

        case 'string':
        default:
        $forbid_tags = ['script'];
        if (isset($options['forbid_tags']) && is_array($options['forbid_tags'])) {
            $forbid_tags = array_unique(array_merge($forbid_tags, $options['forbid_tags']));
        }

        foreach ($forbid_tags as $tag) {

                $tag = preg_quote($tag, '/');

                // Remove complete tag with content
                $value = preg_replace(
                    '/<\s*' . $tag . '\b[^>]*>.*?<\s*\/\s*' . $tag . '\s*>/is',
                    '',
                    $value
                );

                // Remove self-closing or standalone tags
                $value = preg_replace(
                    '/<\s*\/?\s*' . $tag . '\b[^>]*>/i',
                    '',
                    $value
                );
            }

        // Custom regex validation
        if (isset($options['pattern']) && !preg_match($options['pattern'], $value)) {
            return null;
        }

        // Max length check
        if (isset($options['max_length']) && mb_strlen($value, 'UTF-8') > $options['max_length']) {
            return null;
        }

        // Min length check
        if (isset($options['min_length']) && mb_strlen($value, 'UTF-8') < $options['min_length']) {
            return null;
        }

        return trim($value);
    }
}

/**
 * Retrieve and sanitize a request value from GET, POST or REQUEST.
 *
 * @param string $key     Request key.
 * @param string $type    Expected type.
 * @param mixed  $default Default value when the key is missing or invalid.
 * @param string $source  "get", "post", or "request".
 * @param array  $options Optional validation options.
 * @return mixed
 */
function get_input(string $key, string $type = 'string', $default = null, string $source = 'request', array $options = [])
{
    switch (strtolower($source)) {
        case 'get':
            $value = $_GET[$key] ?? null;
            break;
        case 'post':
            $value = $_POST[$key] ?? null;
            break;
        default:
            $value = $_REQUEST[$key] ?? null;
    }

    $sanitized = sanitize_input($value, $type, $options);
    return ($sanitized === null) ? $default : $sanitized;
}

if (empty($_SESSION['csrf_token'])) 
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (get_input('search_product', 'string', null, 'request') !== null)
{
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) 
    {
        die('Invalid CSRF token');
    }
    header("Location:search_product?text=$_REQUEST[text]");
}
if (get_input('newletter_submit', 'string', null, 'request') !== null)
{
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) 
    {
        die('Invalid CSRF token');
    }
    $honeypot = get_input('email_address_check', 'string', '', 'post', ['max_length' => 255]);
    if ($honeypot !== '') {
        return;
    }

    $email = get_input('EMAIL', 'email', null, 'post');
    if ($email === null) {
        echo "<script>alert('Invalid email format');</script>";
        return;
    }

    $check = mysqli_prepare($con, "SELECT n_id FROM newsletter WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        echo "<script>alert('Already subscribed');</script>";
        mysqli_stmt_close($check);
    } else {
        mysqli_stmt_close($check);

        $insert = mysqli_prepare($con, "INSERT INTO newsletter (email) VALUES (?)");
        mysqli_stmt_bind_param($insert, "s", $email);

        if (mysqli_stmt_execute($insert)) {
            echo "<script>alert('Subscription done');</script>";
        } else {
            echo "<script>alert('Subscription failed, please try again');</script>";
        }

        mysqli_stmt_close($insert);
    }
}
$shiprocket_email = "nupurparmar1012@gmail.com";
$shiprocket_password = "&FHT4nfpvTFlWMvsGuJdvqQ@SuipA#Tb";
if(isset($_REQUEST['search_btn']))
{
    echo $_REQUEST['search_text'].'<BR>'.$_REQUEST['search_option'];
    die;
}

function get_valid_shiprocket_token() 
{
    global $shiprocket_email, $shiprocket_password;
    $cache_file = __DIR__ . '/shiprocket_token.json';
    
    // Check if cache file exists and is valid
    if (file_exists($cache_file)) {
        $cache_data = json_decode(file_get_contents($cache_file), true);
        if ($cache_data && isset($cache_data['token']) && isset($cache_data['expires_at'])) {
            // Buffer of 1 hour (3600 seconds)
            if (time() < ($cache_data['expires_at'] - 3600)) {
                return $cache_data['token'];
            }
        }
    }
    
    // Generate new token
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            "email" => $shiprocket_email,
            "password" => $shiprocket_password
        ]),
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($response, true);
    
    if (empty($result['token'])) {
        return null;
    }
    
    // Save to cache file. Shiprocket token usually valid for 10 days (864000 secs).
    $cache_data = [
        'token' => $result['token'],
        'expires_at' => time() + (9 * 24 * 60 * 60) // 9 days
    ];
    file_put_contents($cache_file, json_encode($cache_data));
    
    return $result['token'];
}
?>
