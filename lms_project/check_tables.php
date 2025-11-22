<?php
include "db.php";

$result = mysqli_query($conn, "SHOW TABLES");
$tables = [];
while ($row = mysqli_fetch_array($result)) {
    $tables[] = $row[0];
}

echo "Tables in lms_db:\n";
foreach ($tables as $table) {
    echo "- $table\n";
}
?>
