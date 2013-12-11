<?php
 Class securityhelper{
    function sec_session_start() {
//        $session_name = 'shadow_session'; // Set a custom session name
//        $secure = false; // Set to true if using https.
//        $httponly = true; // This stops javascript being able to access the session id.

//        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
//        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
//        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
//        session_name($session_name); // Sets the session name to the one set above.
//        session_start(); // Start the php session
        session_regenerate_id(); // regenerated the session, delete the old one.
    }
    function login($login, $password, $mysqli) {
       // Using prepared Statements means that SQL injection is not possible.
       if ($stmt = $mysqli->prepare("select sgo_int_usuario, sgo_vch_clave from tbl_sgo_usuario where sgo_vch_usuario = ? LIMIT 1")) {
          $stmt->bind_param('s', $login); // Bind "$login" to parameter.
          $stmt->execute(); // Execute the prepared query.
          $stmt->store_result();
          $stmt->bind_result($user_id, $db_password); // get variables from result.
          $stmt->fetch();
          if(strlen($password)<=15) $password = hash('sha512', $password); // hash the password with the unique salt.

          if($stmt->num_rows == 1) { // If the user exists
             // We check if the account is locked from too many login attempts
             if($this->checkbrute($user_id, $mysqli) == true) {
                // Account is locked
                // Send an email to user saying their account is locked
                return -1;
             }
             else {
                 if($db_password == $password) { // Check if the password in the database matches the password the user submitted.
                    // Password is correct!
                     $_SESSION['validate_string'] = hash('sha512', $password.$_SERVER['HTTP_USER_AGENT']);
                     // Login successful.
                       return $user_id;
                 } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO tbl_sgo_usuario_intentos_login (sgo_int_usuario, sgo_dat_fecha) VALUES ('$user_id', '$now')");
                    return 0;
                 }
              }
          } else {
             // No user exists.
             return 0;
          }
       }
    }
    function checkbrute($user_id, $mysqli) {
       $now = time(); // Get timestamp of current time
       $valid_attempts = $now - (2 * 60 * 60); // All login attempts are counted from the past 2 hours.

       if ($stmt = $mysqli->prepare("SELECT sgo_dat_fecha FROM tbl_sgo_usuario_intentos_login WHERE sgo_int_usuario = ? AND sgo_dat_fecha > '$valid_attempts'")) {
          $stmt->bind_param('i', $user_id);
          // Execute the prepared query.
          $stmt->execute();
          $stmt->store_result();
          // If there has been more than 5 failed logins
          if($stmt->num_rows > 5) {
             return true;
          } else {
             return false;
          }
       }
    }
    function login_check($mysqli) {
       // Check if all session variables are set
       if(isset($_SESSION['usuario'], $_SESSION['validate_string'])) {
         $user_id = $_SESSION['usuario'][0];
         $login_string = $_SESSION['validate_string'];

         $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.

         if ($stmt = $mysqli->prepare("SELECT sgo_vch_clave FROM tbl_sgo_usuario WHERE sgo_int_usuario = ? LIMIT 1")) {
            $stmt->bind_param('i', $user_id); // Bind "$user_id" to parameter.
            $stmt->execute(); // Execute the prepared query.
            $stmt->store_result();

            if($stmt->num_rows == 1) { // If the user exists
               $stmt->bind_result($password); // get variables from result.
               $stmt->fetch();
               $login_check = hash('sha512', $password.$user_browser);
               if($login_check == $login_string) {
                  // Logged In!!!!
                  return true;
               } else {
                  // Not logged in
                  return false;
               }
            } else {
                // Not logged in
                return false;
            }
         } else {
            // Not logged in
            return false;
         }
       } else {
         // Not logged in
         return false;
       }
    }
 }
?>