<?php
header('Content-Type: application/json');
include 'includes/db.php';

try {
    // Abfrage der genehmigten Radiosender zusammen mit den Owner-Daten
    $stmt = $conn->prepare("
        SELECT 
            s.station_id AS id, 
            s.name, 
            s.stream_url AS streamUrl, 
            s.logo_url AS logoUrl, 
            o.name AS ownerName, 
            o.slug AS ownerSlug
        FROM 
            stations s
        LEFT JOIN 
            owner_station so ON s.station_id = so.station_id
        LEFT JOIN 
            radio_owners o ON so.owner_id = o.owner_id
        WHERE 
            s.status = 'approved'
    ");
    
    $stmt->execute();
    $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Überprüfen, ob Stationen gefunden wurden
    if (!$stations) {
        echo json_encode(['error' => 'No stations found']);
        exit();
    }

    // Sende die Stationen im JSON-Format zurück
    echo json_encode($stations);

} catch (PDOException $e) {
    // Fehlerbehandlung
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>