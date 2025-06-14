<?php

if (isset($_REQUEST['Submit'])) {
    
    $id = $_REQUEST['id'];

    if (!ctype_digit($id)) {
        die('Invalid ID');
    }

    switch ($_DVWA['SQLI_DB']) {
        case MYSQL:
            
            $stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ?");
            if (!$stmt) {
                error_log("MySQL prepare failed: " . mysqli_error($GLOBALS["___mysqli_ston"]));
                die('Database error');
            }

            mysqli_stmt_bind_param($stmt, 's', $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            if (!$result) {
                error_log("MySQL execute failed: " . mysqli_error($GLOBALS["___mysqli_ston"]));
                die('Database error');
            }

            while ($row = mysqli_fetch_assoc($result)) {
                $first = htmlspecialchars($row["first_name"], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $last  = htmlspecialchars($row["last_name"], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                $html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
            }

            mysqli_stmt_close($stmt);
            mysqli_close($GLOBALS["___mysqli_ston"]);
            break;

        case SQLITE:
            global $sqlite_db_connection;

            $stmt = $sqlite_db_connection->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id');
            if (!$stmt) {
                error_log("SQLite prepare failed: " . $sqlite_db_connection->lastErrorMsg());
                die('Database error');
            }

            $stmt->bindValue(':id', $id, SQLITE3_TEXT);
            $results = $stmt->execute();

            if (!$results) {
                error_log("SQLite execute failed: " . $sqlite_db_connection->lastErrorMsg());
                die('Database error');
            }

            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $first = htmlspecialchars($row['first_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $last  = htmlspecialchars($row['last_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                $html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
            }
            $results->finalize();
            break;
    }
}

?>