<?php
session_start();
include "../db_connect.php";

// ================= LOGIN =================
if (isset($_POST['login'])) {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND role='admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultUser = $stmt->get_result();

    if ($resultUser->num_rows > 0) {

        $user = $resultUser->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];

            header("Location: index.php");
            exit;

        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "Admin not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<style>
body {
    margin:0;
    font-family: Arial;
    background:#f4f6f9;
}

/* LOGIN */
.login-container {
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(to right,#2c7be5,#00c6ff);
}
.login-box {
    background:white;
    padding:40px;
    width:350px;
    border-radius:10px;
}
input {
    width:100%;
    padding:10px;
    margin-bottom:15px;
}
button {
    width:100%;
    padding:10px;
    background:#2c7be5;
    color:white;
    border:none;
}
.error { color:red; text-align:center; }

/* DASHBOARD */
.sidebar {
    width:220px;
    height:100vh;
    background:#2c3e50;
    position:fixed;
    color:white;
    padding-top:20px;
}
.sidebar h2 {text-align:center;}
.sidebar a {
    display:block;
    padding:10px;
    color:white;
    text-decoration:none;
}
.sidebar a:hover { background:#34495e; }

.main {
    margin-left:220px;
    padding:20px;
}

.header {
    background:white;
    padding:15px;
    margin-bottom:20px;
}

.cards {
    display:flex;
    gap:20px;
    margin-bottom:20px;
}
.card {
    flex:1;
    padding:20px;
    border-radius:10px;
    color:white;
}
.total {background:#3498db;}
.pending {background:#f39c12;}
.resolved {background:#2ecc71;}

.map-box {
    background:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

table {
    width:100%;
    background:white;
    border-collapse:collapse;
}
th, td {
    padding:12px;
    border-bottom:1px solid #ddd;
}
th {
    background:#2c7be5;
    color:white;
}
img {width:70px;}
</style>

</head>

<body>

<?php if (!isset($_SESSION['admin_id'])): ?>

<!-- LOGIN -->
<div class="login-container">
    <div class="login-box">
        <h2>Admin Login</h2>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button name="login">Login</button>
        </form>

        <p style="font-size:12px;text-align:center;">
            admin@gmail.com / 123456
        </p>
    </div>
</div>

<?php else: ?>

<?php
// ================= DATA =================
$total = $conn->query("SELECT COUNT(*) as total FROM reports")->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) as total FROM reports WHERE status='pending'")->fetch_assoc()['total'];
$resolved = $conn->query("SELECT COUNT(*) as total FROM reports WHERE status='resolved'")->fetch_assoc()['total'];

$result = $conn->query("SELECT * FROM reports ORDER BY id DESC");

// MAP DATA
$mapData = [];
$resMap = $conn->query("SELECT * FROM reports WHERE location IS NOT NULL");

while ($row = $resMap->fetch_assoc()) {
    $coords = explode(',', $row['location']);

    if (count($coords) == 2) {
        $mapData[] = [
            "lat" => trim($coords[0]),
            "lng" => trim($coords[1]),
            "desc" => $row['description'],
            "status" => $row['status'],
            "image" => $row['image']
        ];
    }
}
?>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin</h2>
    <a href="#">Dashboard</a>
    <a href="logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<div class="header">
    <h2>Welcome, <?php echo $_SESSION['admin_name']; ?></h2>
</div>

<!-- CARDS -->
<div class="cards">
    <div class="card total">
        <h3>Total Reports</h3>
        <h1><?php echo $total; ?></h1>
    </div>

    <div class="card pending">
        <h3>Pending</h3>
        <h1><?php echo $pending; ?></h1>
    </div>

    <div class="card resolved">
        <h3>Resolved</h3>
        <h1><?php echo $resolved; ?></h1>
    </div>
</div>

<!-- MAP -->
<div class="map-box">
    <h3>Waste Reports Map</h3>
    <div id="map" style="height:400px;"></div>
</div>

<!-- TABLE -->
<h3>Waste Reports</h3>

<table>
<tr>
<th>ID</th>
<th>Description</th>
<th>Location</th>
<th>Image</th>
<th>Status</th>
<th>Update</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['description']; ?></td>
<td><?php echo $row['location']; ?></td>

<td>
<?php if($row['image']!=""){ ?>
<img src="../uploads/<?php echo $row['image']; ?>">
<?php } ?>
</td>

<td><?php echo $row['status']; ?></td>

<td>
<form action="update_status.php" method="POST">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<select name="status">
<option <?php if($row['status']=="pending") echo "selected"; ?>>pending</option>
<option <?php if($row['status']=="in progress") echo "selected"; ?>>in progress</option>
<option <?php if($row['status']=="resolved") echo "selected"; ?>>resolved</option>
</select>

<button type="submit">Update</button>
</form>
</td>
</tr>
<?php } ?>

</table>

</div>

<!-- MAP SCRIPT -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
var map = L.map('map').setView([5.9631, 10.1591], 6);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

var reports = <?php echo json_encode($mapData); ?>;

reports.forEach(function(r){

    var marker = L.marker([r.lat, r.lng]).addTo(map);

    var img = r.image ? `<img src="../uploads/${r.image}" width="100">` : "";

    marker.bindPopup(`
        <b>Description:</b> ${r.desc}<br>
        <b>Status:</b> ${r.status}<br>
        ${img}
    `);

});
</script>

<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

<?php endif; ?>

</body>
</html>