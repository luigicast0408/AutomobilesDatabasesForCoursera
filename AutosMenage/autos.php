<?php
require_once "PDO/pdo.php";
session_start();

if (!isset($_SESSION['name'])) {
    die("Name parameter missing");
}

if (isset($_POST['logout'])) {
    header("Location: ../index.php");
    return;
}

if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    if (strlen($_POST['make']) < 1) {
        $_SESSION['error'] = "Make is required";
    } elseif (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = "Mileage and year must be numeric";
    } else {
        $stmt = $pdo->prepare('INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)');
        $stmt->execute(array(
            ':mk' => htmlentities($_POST['make']),
            ':yr' => htmlentities($_POST['year']),
            ':mi' => htmlentities($_POST['mileage'])
        ));
        $_SESSION['success'] = "Record inserted";
        header("Location: autos.php");
        return;
    }
}

$stmt = $pdo->query("SELECT make, year, mileage FROM autos ORDER BY make");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Name</title>
</head>
<body>
<h1>Tracking Autos for <?= htmlentities($_SESSION['name']); ?></h1>

<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . '</p>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<p style="color: green;">' . htmlentities($_SESSION['success']) . '</p>';
    unset($_SESSION['success']);
}
?>

<form method="post">
    <p>Make: <input type="text" name="make" size="60"></p>
    <p>Year: <input type="text" name="year"></p>
    <p>Mileage: <input type="text" name="mileage"></p>
    <input type="submit" value="Add">
    <input type="submit" name="logout" value="Logout">
</form>

<h2>Automobiles</h2>
<ul>
    <?php
    foreach ($rows as $row) {
        echo '<li>';
        echo htmlentities($row['make']) . ' ' . htmlentities($row['year']) . ' / ' . htmlentities($row['mileage']);
        echo '</li>';
    }
    ?>
</ul>
</body>
</html>
