<?php

$version = "1.0.3";

// Database connection
$dbPath = __DIR__ . '/database.sqlite';
if (!file_exists($dbPath)) {
    // $pdo = new PDO('sqlite:' . $dbPath);
    $pdo = new PDO("sqlite:database.sqlite");

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS patients (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            name          TEXT    NOT NULL,
            age           INTEGER NOT NULL,
            address       TEXT    NOT NULL,
            gender        TEXT    NOT NULL,
            phone         TEXT,
            dob           TEXT,
            compliant     TEXT,
            treatments    TEXT,
            investigation TEXT
        )
    ");
} else {
    $pdo = new PDO('sqlite:' . $dbPath);
}
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get the action from the URL
$method = $_SERVER['REQUEST_METHOD'];

// Set the response header to JSON
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

try {
    switch ($method) {
        case 'POST':
            // Create/Add a new patient
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('INSERT INTO patients (name, age, dob, gender, address, phone, compliant, treatments, investigation) VALUES (:name, :age, :dob, :gender, :address, :phone, :compliant, :treatments, :investigation)');
            $stmt->execute([
                ':name'          => $data['name'],
                ':age'           => $data['age'] ?? 0,
                ':dob'           => $data['dob'],
                ':gender'        => $data['gender'],
                ':address'       => $data['address'] ?? "",
                ':phone'         => $data['phone'] ?? "",

                ':compliant'     => $data['compliant'] ?? "",
                ':treatments'    => $data['treatments'] ?? "",
                ':investigation' => $data['investigation'] ?? "",
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'GET':
            // Fetch all patients
            $stmt = $pdo->query('SELECT * FROM patients');
            $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($patients);
            break;

        case 'PUT':
            // Update an existing patient
            $id = $_GET['id'] ?? 0;
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('UPDATE patients SET name = :name, age = :age, dob = :dob, gender = :gender, compliant = :compliant, treatments = :treatments, investigation = :investigation, address = :address, phone = :phone WHERE id = :id');
            $stmt->execute([
                ':name'          => $data['name'],
                ':age'           => $data['age'],
                ':gender'        => $data['gender'],
                ':address'       => $data['address'],
                ':phone'         => $data['phone'],
                ':compliant'     => $data['compliant'] ?? "",
                ':treatments'    => $data['treatments'] ?? "", 
                ':investigation' => $data['investigation'] ?? "",
                ':dob' => $data['dob'] ?? "",
                ':id' => $id,
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            // Delete a patient
            $id = $_GET['id'] ?? 0;
            $stmt = $pdo->prepare('DELETE FROM patients WHERE id = :id');
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true]);
            break;

        case 'OPTIONS':
            $config = [
                'version' => $version,
                'dbPath' => $dbPath,
                'dbExists' => file_exists($dbPath),
                'dbSize' => file_exists($dbPath) ? filesize($dbPath) : 0,
                'db:tables' => $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN),
                'db:patients' => $pdo->query("SELECT * FROM patients")->fetchAll(PDO::FETCH_ASSOC),
            ];

            echo json_encode(['config' => $config]);
            break;

        default:
            // Invalid action
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
